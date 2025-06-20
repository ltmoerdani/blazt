const { 
    default: makeWASocket, 
    DisconnectReason, 
    useMultiFileAuthState,
    delay,
    Browsers
} = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const fs = require('fs').promises;
const path = require('path');
const logger = require('../utils/logger');
const ApiClient = require('../utils/api-client');

class BaileysHandlerV3 {
    constructor() {
        this.sessions = new Map();
        this.qrCodes = new Map();
        this.apiClient = new ApiClient();
        this.sessionsDir = path.join(__dirname, '../../storage/app/whatsapp-sessions');
        this.qrDir = path.join(__dirname, '../../storage/app/public/qr-codes');
        this.qrTimers = new Map(); // Track QR expiration timers
        this.connectionAttempts = new Map(); // Track connection attempts per account
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
        logger.info('[V3] Enhanced WhatsApp handler initialized');
    }

    async connectAccount(accountId, phoneNumber) {
        try {
            logger.info(`[V3] Starting connection for account: ${accountId} (${phoneNumber})`);

            // Force cleanup everything for this account
            await this.forceCleanupAccount(accountId);

            // Reset connection attempts
            this.connectionAttempts.set(accountId, 0);

            const sessionDir = path.join(this.sessionsDir, `v3_${accountId.toString()}`);
            
            // Complete session directory cleanup
            try {
                await fs.rm(sessionDir, { recursive: true, force: true });
                await delay(1000); // Wait for filesystem
                await fs.mkdir(sessionDir, { recursive: true });
                logger.info(`[V3] Fresh session directory created for account ${accountId}`);
            } catch (e) {
                logger.warn(`[V3] Session cleanup warning:`, e.message);
            }

            const { state, saveCreds } = await useMultiFileAuthState(sessionDir);

            // Enhanced socket configuration
            const sock = makeWASocket({
                auth: state,
                printQRInTerminal: false,
                logger: logger,
                browser: Browsers.macOS('Chrome'), // Use stable browser definition
                defaultQueryTimeoutMs: 20000,
                connectTimeoutMs: 20000,
                qrTimeout: 30000, // 30 seconds QR timeout
                markOnlineOnConnect: false,
                syncFullHistory: false,
                shouldSyncHistoryMessage: () => false,
                fireInitQueries: false,
                emitOwnEvents: false,
                retryRequestDelayMs: 500,
                maxMsgRetryCount: 2,
                getMessage: async (key) => ({ conversation: '' }),
                version: [2, 3000, 1015901307], // Stable WhatsApp Web version
            });

            return await this.setupConnectionHandlers(sock, accountId, phoneNumber, saveCreds);

        } catch (error) {
            logger.error(`[V3] Connection setup error for account ${accountId}:`, error);
            await this.forceCleanupAccount(accountId);
            throw error;
        }
    }

    async setupConnectionHandlers(sock, accountId, phoneNumber, saveCreds) {
        const sessionId = `v3_session_${accountId}_${Date.now()}`;
        let qrCount = 0;
        const maxQrAttempts = 5;

        return new Promise((resolve, reject) => {
            sock.ev.on('connection.update', async (update) => {
                const { connection, lastDisconnect, qr } = update;

                if (qr && qrCount < maxQrAttempts) {
                    await this.handleQRGeneration(qr, accountId, sessionId, ++qrCount, maxQrAttempts);
                }

                if (connection === 'close') {
                    await this.handleConnectionClose(lastDisconnect, accountId, phoneNumber, sessionId, resolve, reject);
                } else if (connection === 'open') {
                    await this.handleConnectionOpen(sock, accountId, sessionId, resolve);
                } else if (connection === 'connecting') {
                    logger.info(`[V3] Account ${accountId} is connecting...`);
                }
            });

            sock.ev.on('creds.update', saveCreds);

            // Store socket temporarily
            this.sessions.set(`temp_${accountId}`, sock);

            // Set connection timeout
            setTimeout(() => {
                if (!this.sessions.has(accountId)) {
                    logger.warn(`[V3] Connection timeout for account ${accountId}`);
                    this.forceCleanupAccount(accountId);
                    resolve({ success: false, status: 'timeout', version: 'v3' });
                }
            }, 60000); // 60 second timeout

            // Initial resolve for API response
            resolve({ success: true, status: 'connecting', version: 'v3' });
        });
    }

    async handleQRGeneration(qr, accountId, sessionId, qrCount, maxQrAttempts) {
        try {
            logger.info(`[V3] Generating QR code for account ${accountId} (${qrCount}/${maxQrAttempts})`);
            
            // Clear any existing QR timer
            if (this.qrTimers.has(accountId)) {
                clearTimeout(this.qrTimers.get(accountId));
            }

            const qrCodeDataURL = await QRCode.toDataURL(qr, {
                errorCorrectionLevel: 'L',
                type: 'image/png',
                quality: 0.95,
                margin: 4,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                },
                width: 512
            });

            // Store QR code
            this.qrCodes.set(accountId, {
                data: qrCodeDataURL,
                generated: Date.now(),
                count: qrCount
            });

            // Save to file
            const qrCodePath = path.join(this.qrDir, `v3_${accountId}.png`);
            const base64Data = qrCodeDataURL.replace(/^data:image\/png;base64,/, '');
            await fs.writeFile(qrCodePath, base64Data, 'base64');

            // Notify Laravel
            await this.apiClient.notifyQRGenerated(accountId, qrCodeDataURL, sessionId);

            logger.info(`[V3] QR code generated and saved for account ${accountId}`);

            // Set QR expiration timer (20 seconds - before WhatsApp timeout)
            const timer = setTimeout(async () => {
                logger.info(`[V3] QR code expired for account ${accountId}, will generate new one`);
                this.qrCodes.delete(accountId);
            }, 20000);

            this.qrTimers.set(accountId, timer);

        } catch (error) {
            logger.error(`[V3] QR generation error for account ${accountId}:`, error);
        }
    }

    async handleConnectionClose(lastDisconnect, accountId, phoneNumber, sessionId, resolve, reject) {
        const statusCode = lastDisconnect?.error?.output?.statusCode;
        const reason = Object.keys(DisconnectReason).find(key => DisconnectReason[key] === statusCode) || 'unknown';
        
        logger.info(`[V3] Connection closed for account ${accountId}. Reason: ${reason} (${statusCode})`);

        const currentAttempts = this.connectionAttempts.get(accountId) || 0;
        const shouldReconnect = statusCode !== DisconnectReason.loggedOut && 
                               statusCode !== DisconnectReason.forbidden && 
                               currentAttempts < 3;

        if (shouldReconnect) {
            this.connectionAttempts.set(accountId, currentAttempts + 1);
            logger.info(`[V3] Retrying connection for account ${accountId} (attempt ${currentAttempts + 1}/3)`);
            
            // Wait before retry
            setTimeout(async () => {
                try {
                    await this.connectAccount(accountId, phoneNumber);
                } catch (error) {
                    logger.error(`[V3] Retry failed for account ${accountId}:`, error);
                }
            }, 5000);
        } else {
            await this.forceCleanupAccount(accountId);
            await this.apiClient.notifySessionStatus(accountId, 'disconnected', sessionId);
            logger.info(`[V3] Giving up on account ${accountId} after ${currentAttempts} attempts`);
        }
    }

    async handleConnectionOpen(sock, accountId, sessionId, resolve) {
        logger.info(`[V3] ✅ WhatsApp connected successfully for account ${accountId}`);
        
        // Move from temp to permanent storage
        this.sessions.delete(`temp_${accountId}`);
        this.sessions.set(accountId, sock);
        
        // Clear QR code and timers
        this.qrCodes.delete(accountId);
        if (this.qrTimers.has(accountId)) {
            clearTimeout(this.qrTimers.get(accountId));
            this.qrTimers.delete(accountId);
        }
        
        // Reset connection attempts
        this.connectionAttempts.delete(accountId);
        
        // Notify Laravel
        await this.apiClient.notifySessionStatus(accountId, 'connected', sessionId);
    }

    async forceCleanupAccount(accountId) {
        try {
            // Clear timers
            if (this.qrTimers.has(accountId)) {
                clearTimeout(this.qrTimers.get(accountId));
                this.qrTimers.delete(accountId);
            }

            // Close sockets
            const tempSock = this.sessions.get(`temp_${accountId}`);
            const sock = this.sessions.get(accountId);
            
            if (tempSock) {
                try { tempSock.end(); } catch (e) { /* ignore */ }
                this.sessions.delete(`temp_${accountId}`);
            }
            
            if (sock) {
                try { sock.end(); } catch (e) { /* ignore */ }
                this.sessions.delete(accountId);
            }
            
            // Clear QR codes
            this.qrCodes.delete(accountId);
            
            logger.info(`[V3] Force cleanup completed for account ${accountId}`);
        } catch (error) {
            logger.warn(`[V3] Cleanup error for account ${accountId}:`, error.message);
        }
    }

    async disconnectAccount(accountId) {
        logger.info(`[V3] Disconnecting account ${accountId}`);
        await this.forceCleanupAccount(accountId);
        return { success: true, status: 'disconnected', version: 'v3' };
    }

    getQRCode(accountId) {
        const qrInfo = this.qrCodes.get(accountId);
        return qrInfo ? qrInfo.data : null;
    }

    async getAccountStatus(accountId) {
        const sock = this.sessions.get(accountId);
        const qrInfo = this.qrCodes.get(accountId);
        
        return {
            status: sock?.user ? 'connected' : (qrInfo ? 'waiting_qr_scan' : 'disconnected'),
            connected: !!sock?.user,
            user: sock?.user || null,
            qr_available: !!qrInfo,
            qr_age: qrInfo ? Math.floor((Date.now() - qrInfo.generated) / 1000) : null,
            version: 'v3'
        };
    }
}

module.exports = BaileysHandlerV3;
