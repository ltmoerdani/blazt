const { 
    default: makeWASocket, 
    DisconnectReason, 
    useMultiFileAuthState,
    delay,
    downloadMediaMessage,
    Browsers
} = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const fs = require('fs').promises;
const path = require('path');
const logger = require('../utils/logger');
const ApiClient = require('../utils/api-client');

class BaileysHandlerV2 {
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
    }

    async connectAccount(accountId, phoneNumber) {
        try {
            logger.info(`[V2] Connecting WhatsApp account: ${accountId} (${phoneNumber})`);

            // Force cleanup any existing session
            await this.cleanupSession(accountId);

            const sessionDir = path.join(this.sessionsDir, `v2_${accountId.toString()}`);
            await fs.rm(sessionDir, { recursive: true, force: true });
            await fs.mkdir(sessionDir, { recursive: true });

            const { state, saveCreds } = await useMultiFileAuthState(sessionDir);

            // Use more conservative browser settings
            const sock = makeWASocket({
                auth: state,
                printQRInTerminal: false,
                logger: logger,
                browser: Browsers.macOS('Chrome'), // Use built-in browser definition
                defaultQueryTimeoutMs: 30000,
                connectTimeoutMs: 30000,
                qrTimeout: 30000,
                markOnlineOnConnect: false,
                syncFullHistory: false,
                shouldSyncHistoryMessage: () => false,
                fireInitQueries: false,
                emitOwnEvents: false,
                retryRequestDelayMs: 1000,
                maxMsgRetryCount: 3,
                getMessage: async (key) => {
                    return { conversation: '' };
                },
            });

            const sessionId = `v2_session_${accountId}_${Date.now()}`;
            let connectionAttempts = 0;
            const maxAttempts = 3;

            sock.ev.on('connection.update', async (update) => {
                const { connection, lastDisconnect, qr } = update;

                if (qr) {
                    try {
                        connectionAttempts++;
                        logger.info(`[V2] QR generated for account ${accountId} (attempt ${connectionAttempts}/${maxAttempts})`);
                        
                        // Generate high quality QR code
                        const qrCodeDataURL = await QRCode.toDataURL(qr, {
                            errorCorrectionLevel: 'M',
                            type: 'image/png',
                            quality: 0.95,
                            margin: 4,
                            color: {
                                dark: '#000000',
                                light: '#FFFFFF'
                            },
                            width: 400
                        });
                        
                        this.qrCodes.set(accountId, qrCodeDataURL);
                        
                        // Save to file
                        const qrCodePath = path.join(this.qrDir, `v2_${accountId}.png`);
                        const base64Data = qrCodeDataURL.replace(/^data:image\/png;base64,/, '');
                        await fs.writeFile(qrCodePath, base64Data, 'base64');

                        logger.info(`[V2] QR code saved for account ${accountId}, notifying Laravel...`);
                        
                        // Notify Laravel
                        await this.apiClient.notifyQRGenerated(accountId, qrCodeDataURL, sessionId);
                        
                    } catch (error) {
                        logger.error('[V2] Error generating QR code:', error);
                    }
                }

                if (connection === 'close') {
                    const statusCode = lastDisconnect?.error?.output?.statusCode;
                    const shouldReconnect = statusCode !== DisconnectReason.loggedOut && connectionAttempts < maxAttempts;
                    
                    logger.info(`[V2] Connection closed for account ${accountId}. Status: ${statusCode}, Will reconnect: ${shouldReconnect}`);
                    
                    if (shouldReconnect) {
                        logger.info(`[V2] Retrying connection for account ${accountId} in 3 seconds...`);
                        setTimeout(() => {
                            this.connectAccount(accountId, phoneNumber);
                        }, 3000);
                    } else {
                        await this.cleanupSession(accountId);
                        await this.apiClient.notifySessionStatus(accountId, 'disconnected', sessionId);
                    }
                } else if (connection === 'open') {
                    logger.info(`[V2] WhatsApp connected successfully for account ${accountId}`);
                    
                    this.sessions.set(accountId, sock);
                    this.qrCodes.delete(accountId);
                    
                    await this.apiClient.notifySessionStatus(accountId, 'connected', sessionId);
                }
            });

            sock.ev.on('creds.update', saveCreds);

            // Store socket reference temporarily
            this.sessions.set(`temp_${accountId}`, sock);

            return { success: true, status: 'connecting', version: 'v2' };

        } catch (error) {
            logger.error(`[V2] Error connecting account ${accountId}:`, error);
            await this.cleanupSession(accountId);
            throw error;
        }
    }

    async cleanupSession(accountId) {
        try {
            // Close existing socket
            const tempSock = this.sessions.get(`temp_${accountId}`);
            const sock = this.sessions.get(accountId);
            
            if (tempSock) {
                try {
                    tempSock.end();
                } catch (e) { /* ignore */ }
                this.sessions.delete(`temp_${accountId}`);
            }
            
            if (sock) {
                try {
                    sock.end();
                } catch (e) { /* ignore */ }
                this.sessions.delete(accountId);
            }
            
            this.qrCodes.delete(accountId);
            
            logger.info(`[V2] Cleaned up session for account ${accountId}`);
        } catch (error) {
            logger.warn(`[V2] Error during cleanup for account ${accountId}:`, error.message);
        }
    }

    async disconnectAccount(accountId) {
        try {
            logger.info(`[V2] Disconnecting account ${accountId}`);
            await this.cleanupSession(accountId);
            return { success: true, status: 'disconnected' };
        } catch (error) {
            logger.error(`[V2] Error disconnecting account ${accountId}:`, error);
            throw error;
        }
    }

    getQRCode(accountId) {
        return this.qrCodes.get(accountId) || null;
    }

    async getAccountStatus(accountId) {
        const sock = this.sessions.get(accountId);
        
        if (!sock) {
            return { status: 'disconnected', connected: false };
        }

        try {
            const isConnected = sock.user !== undefined;
            return {
                status: isConnected ? 'connected' : 'connecting',
                connected: isConnected,
                user: sock.user || null
            };
        } catch (error) {
            return { status: 'error', connected: false, error: error.message };
        }
    }
}

module.exports = BaileysHandlerV2;
