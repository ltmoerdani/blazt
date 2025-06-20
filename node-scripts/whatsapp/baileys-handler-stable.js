const { 
    default: makeWASocket, 
    DisconnectReason, 
    useMultiFileAuthState,
    delay,
    Browsers,
    fetchLatestBaileysVersion
} = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const fs = require('fs').promises;
const path = require('path');
const logger = require('../utils/logger');
const ApiClient = require('../utils/api-client');

class BaileysHandlerStable {
    constructor() {
        this.sessions = new Map();
        this.qrCodes = new Map();
        this.apiClient = new ApiClient();
        this.sessionsDir = path.join(__dirname, '../../storage/app/whatsapp-sessions');
        this.qrDir = path.join(__dirname, '../../storage/app/public/qr-codes');
    }

    async ensureDirectories() {
        try {
            await fs.mkdir(this.sessionsDir, { recursive: true });
            await fs.mkdir(this.qrDir, { recursive: true });
        } catch (error) {
            logger.error('Error creating directories:', error);
        }
    }

    async init() {
        await this.ensureDirectories();
        logger.info('[STABLE] Stable WhatsApp handler initialized');
    }

    async connectAccount(accountId, phoneNumber) {
        try {
            logger.info(`[STABLE] Connecting account: ${accountId} (${phoneNumber})`);

            // Cleanup first
            await this.cleanupAccount(accountId);

            const sessionDir = path.join(this.sessionsDir, `stable_${accountId.toString()}`);
            await fs.rm(sessionDir, { recursive: true, force: true });
            await fs.mkdir(sessionDir, { recursive: true });

            const { state, saveCreds } = await useMultiFileAuthState(sessionDir);

            // Get latest version info
            const { version, isLatest } = await fetchLatestBaileysVersion();
            logger.info(`[STABLE] Using Baileys version: ${version.join('.')}, Latest: ${isLatest}`);

            // Minimal configuration for stability
            const sock = makeWASocket({
                auth: state,
                logger: logger,
                browser: ['WhatsApp Web', '', ''], // Minimal browser info
                version: version,
                printQRInTerminal: false,
                
                // Conservative timeouts
                defaultQueryTimeoutMs: 10000,
                connectTimeoutMs: 10000,
                
                // Minimal features for stability
                markOnlineOnConnect: false,
                syncFullHistory: false,
                shouldSyncHistoryMessage: () => false,
                fireInitQueries: false,
                emitOwnEvents: false,
                
                // Simple message getter
                getMessage: async () => ({ conversation: 'Hi' }),
            });

            const sessionId = `stable_session_${accountId}_${Date.now()}`;

            sock.ev.on('connection.update', async (update) => {
                const { connection, lastDisconnect, qr } = update;
                
                if (qr) {
                    await this.generateAndSaveQR(qr, accountId, sessionId);
                }

                if (connection === 'close') {
                    const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                    logger.info(`[STABLE] Connection closed for account ${accountId}. Will reconnect: ${shouldReconnect}`);
                    
                    if (shouldReconnect) {
                        // Simple retry after delay
                        setTimeout(() => {
                            this.connectAccount(accountId, phoneNumber);
                        }, 10000);
                    }
                } else if (connection === 'open') {
                    logger.info(`[STABLE] ✅ Connected successfully: account ${accountId}`);
                    this.sessions.set(accountId, sock);
                    this.qrCodes.delete(accountId);
                    await this.apiClient.notifySessionStatus(accountId, 'connected', sessionId);
                }
            });

            sock.ev.on('creds.update', saveCreds);

            this.sessions.set(`temp_${accountId}`, sock);

            return { success: true, status: 'connecting', version: 'stable' };

        } catch (error) {
            logger.error(`[STABLE] Error for account ${accountId}:`, error);
            await this.cleanupAccount(accountId);
            throw error;
        }
    }

    async generateAndSaveQR(qr, accountId, sessionId) {
        try {
            logger.info(`[STABLE] Generating QR for account ${accountId}`);
            
            const qrCodeDataURL = await QRCode.toDataURL(qr, {
                errorCorrectionLevel: 'M',
                width: 256,
                margin: 2
            });

            this.qrCodes.set(accountId, qrCodeDataURL);

            // Save to file
            const qrCodePath = path.join(this.qrDir, `stable_${accountId}.png`);
            const base64Data = qrCodeDataURL.replace(/^data:image\/png;base64,/, '');
            await fs.writeFile(qrCodePath, base64Data, 'base64');

            // Notify Laravel
            await this.apiClient.notifyQRGenerated(accountId, qrCodeDataURL, sessionId);
            
            logger.info(`[STABLE] QR generated and saved for account ${accountId}`);
        } catch (error) {
            logger.error(`[STABLE] QR generation error:`, error);
        }
    }

    async cleanupAccount(accountId) {
        try {
            ['temp_', ''].forEach(prefix => {
                const key = `${prefix}${accountId}`;
                const sock = this.sessions.get(key);
                if (sock) {
                    try { sock.end(); } catch (e) { /* ignore */ }
                    this.sessions.delete(key);
                }
            });
            
            this.qrCodes.delete(accountId);
            logger.info(`[STABLE] Cleaned up account ${accountId}`);
        } catch (error) {
            logger.warn(`[STABLE] Cleanup error:`, error.message);
        }
    }

    async disconnectAccount(accountId) {
        await this.cleanupAccount(accountId);
        return { success: true, status: 'disconnected', version: 'stable' };
    }

    getQRCode(accountId) {
        return this.qrCodes.get(accountId) || null;
    }

    async getAccountStatus(accountId) {
        const sock = this.sessions.get(accountId);
        return {
            status: sock?.user ? 'connected' : 'disconnected',
            connected: !!sock?.user,
            user: sock?.user || null,
            version: 'stable'
        };
    }
}

module.exports = BaileysHandlerStable;
