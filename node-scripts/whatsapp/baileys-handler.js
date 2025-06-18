const { 
    default: makeWASocket, 
    DisconnectReason, 
    useMultiFileAuthState,
    delay,
    downloadMediaMessage
} = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const fs = require('fs').promises;
const path = require('path');
const logger = require('../utils/logger');
const ApiClient = require('../utils/api-client');

class BaileysHandler {
    constructor() {
        this.sessions = new Map(); // Store active sessions
        this.qrCodes = new Map(); // Store QR codes
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
            logger.info(`Connecting WhatsApp account: ${accountId} (${phoneNumber})`);

            if (this.sessions.has(accountId)) {
                logger.info(`Account ${accountId} already connected`);
                return { success: true, status: 'already_connected' };
            }

            const sessionDir = path.join(this.sessionsDir, accountId.toString());
            await fs.mkdir(sessionDir, { recursive: true });

            const { state, saveCreds } = await useMultiFileAuthState(sessionDir);

            const sock = makeWASocket({
                auth: state,
                printQRInTerminal: false,
                logger: logger,
                browser: ['Blazt WhatsApp Bot', 'Chrome', '1.0.0'],
                defaultQueryTimeoutMs: 60000,
            });

            // Handle QR code generation
            sock.ev.on('connection.update', async (update) => {
                const { connection, lastDisconnect, qr } = update;

                if (qr) {
                    try {
                        const qrCodeDataURL = await QRCode.toDataURL(qr);
                        const qrCodePath = path.join(this.qrDir, `${accountId}.png`);
                        
                        // Save QR code as base64 data URL
                        this.qrCodes.set(accountId, qrCodeDataURL);
                        
                        // Also save as PNG file
                        const base64Data = qrCodeDataURL.replace(/^data:image\/png;base64,/, '');
                        await fs.writeFile(qrCodePath, base64Data, 'base64');

                        logger.info(`QR code generated for account ${accountId}`);
                        
                        // Notify Laravel app about QR code
                        await this.apiClient.notifyQRGenerated(accountId, qrCodeDataURL);
                    } catch (error) {
                        logger.error('Error generating QR code:', error);
                    }
                }

                if (connection === 'close') {
                    const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                    
                    logger.info(`Connection closed for account ${accountId}. Reconnecting: ${shouldReconnect}`);
                    
                    if (shouldReconnect) {
                        // Attempt to reconnect after delay
                        setTimeout(() => {
                            this.connectAccount(accountId, phoneNumber);
                        }, 5000);
                    } else {
                        // Clean up session
                        this.sessions.delete(accountId);
                        this.qrCodes.delete(accountId);
                        
                        // Notify Laravel app about disconnection
                        await this.apiClient.notifySessionStatus(accountId, 'disconnected');
                    }
                } else if (connection === 'open') {
                    logger.info(`WhatsApp connected successfully for account ${accountId}`);
                    
                    // Remove QR code since we're connected
                    this.qrCodes.delete(accountId);
                    
                    // Notify Laravel app about successful connection
                    await this.apiClient.notifySessionStatus(accountId, 'connected');
                }
            });

            // Handle credential updates
            sock.ev.on('creds.update', saveCreds);

            // Handle incoming messages
            sock.ev.on('messages.upsert', async (m) => {
                try {
                    const messages = m.messages || [];
                    
                    for (const message of messages) {
                        if (message.key.fromMe) continue; // Skip outgoing messages
                        
                        await this.handleIncomingMessage(accountId, message);
                    }
                } catch (error) {
                    logger.error('Error handling incoming messages:', error);
                }
            });

            // Handle message status updates
            sock.ev.on('messages.update', async (updates) => {
                try {
                    for (const update of updates) {
                        await this.handleMessageUpdate(accountId, update);
                    }
                } catch (error) {
                    logger.error('Error handling message updates:', error);
                }
            });

            this.sessions.set(accountId, sock);
            
            return { success: true, status: 'connecting' };
        } catch (error) {
            logger.error(`Error connecting account ${accountId}:`, error);
            return { success: false, error: error.message };
        }
    }

    async disconnectAccount(accountId) {
        try {
            const sock = this.sessions.get(accountId);
            
            if (sock) {
                await sock.logout();
                this.sessions.delete(accountId);
            }
            
            this.qrCodes.delete(accountId);
            
            logger.info(`Account ${accountId} disconnected`);
            return { success: true };
        } catch (error) {
            logger.error(`Error disconnecting account ${accountId}:`, error);
            return { success: false, error: error.message };
        }
    }

    async sendMessage(accountId, phoneNumber, message, messageId, mediaUrl = null) {
        try {
            const sock = this.sessions.get(accountId);
            
            if (!sock) {
                throw new Error(`Account ${accountId} not connected`);
            }

            // Format phone number (ensure it has country code)
            const formattedNumber = this.formatPhoneNumber(phoneNumber);
            const jid = `${formattedNumber}@s.whatsapp.net`;

            let result;
            
            if (mediaUrl) {
                // Send media message
                result = await sock.sendMessage(jid, {
                    image: { url: mediaUrl },
                    caption: message
                });
            } else {
                // Send text message
                result = await sock.sendMessage(jid, { text: message });
            }

            logger.info(`Message sent successfully to ${phoneNumber} from account ${accountId}`);
            
            // Notify Laravel about successful send
            if (messageId) {
                await this.apiClient.notifyMessageStatus(messageId, 'sent', result.key.id);
            }

            return { 
                success: true, 
                messageId: result.key.id,
                timestamp: result.messageTimestamp 
            };
        } catch (error) {
            logger.error(`Error sending message from account ${accountId}:`, error);
            
            // Notify Laravel about failed send
            if (messageId) {
                await this.apiClient.notifyMessageStatus(messageId, 'failed', null, error.message);
            }
            
            return { success: false, error: error.message };
        }
    }

    async getAccountStatus(accountId) {
        const sock = this.sessions.get(accountId);
        const hasQR = this.qrCodes.has(accountId);
        if (sock?.user) {
            return { 
                status: 'connected', 
                phoneNumber: sock.user.id.split(':')[0],
                user: sock.user 
            };
        } else if (hasQR) {
            return { status: 'qr_required' };
        } else if (sock) {
            return { status: 'connecting' };
        } else {
            return { status: 'disconnected' };
        }
    }

    async getQRCode(accountId) {
        return this.qrCodes.get(accountId) || null;
    }

    async listAccounts() {
        const accounts = [];
        
        for (const [accountId] of this.sessions) {
            const status = await this.getAccountStatus(accountId);
            accounts.push({ accountId, ...status });
        }
        
        return accounts;
    }

    async handleIncomingMessage(accountId, message) {
        try {
            const messageData = {
                whatsapp_account_id: accountId,
                id: message.key?.id,
                from: message.key?.remoteJid?.split('@')[0],
                timestamp: message.messageTimestamp,
                type: 'text'
            };
            if (message.message?.conversation) {
                messageData.body = message.message.conversation;
            } else if (message.message?.extendedTextMessage?.text) {
                messageData.body = message.message.extendedTextMessage.text;
            } else if (message.message?.imageMessage) {
                messageData.type = 'image';
                messageData.body = message.message.imageMessage.caption || '';
                // Download and save media (basic implementation)
                // const buffer = await downloadMediaMessage(message, 'buffer', {});
                // Save buffer to disk or process as needed
            } else if (message.message?.videoMessage) {
                messageData.type = 'video';
                messageData.body = message.message.videoMessage.caption || '';
            } else if (message.message?.audioMessage) {
                messageData.type = 'audio';
                messageData.body = '';
            } else if (message.message?.documentMessage) {
                messageData.type = 'document';
                messageData.body = message.message.documentMessage.caption || '';
            }
            await this.apiClient.sendIncomingMessage(messageData);
            logger.info(`Incoming message processed for account ${accountId}`);
        } catch (error) {
            logger.error('Error handling incoming message:', error);
        }
    }

    async handleMessageUpdate(accountId, update) {
        try {
            const { key, update: messageUpdate } = update;
            
            if (messageUpdate.status) {
                // Map WhatsApp status to our status
                let status = 'sent';
                if (messageUpdate.status === 3) status = 'delivered';
                if (messageUpdate.status === 4) status = 'read';
                
                await this.apiClient.notifyMessageStatus(key.id, status);
            }
        } catch (error) {
            logger.error('Error handling message update:', error);
        }
    }

    formatPhoneNumber(phoneNumber) {
        // Remove any non-digit characters
        let formatted = phoneNumber.replace(/\D/g, '');
        
        // Add country code if not present (default to Indonesia)
        if (!formatted.startsWith('62') && formatted.startsWith('0')) {
            formatted = '62' + formatted.slice(1);
        } else if (!formatted.startsWith('62') && !formatted.startsWith('+')) {
            formatted = '62' + formatted;
        }
        
        return formatted;
    }

    async cleanup() {
        logger.info('Cleaning up WhatsApp sessions...');
        for (const accountId of this.sessions.keys()) {
            try {
                await this.sessions.get(accountId).end();
                logger.info(`Session ${accountId} ended`);
            } catch (error) {
                logger.error(`Error ending session ${accountId}:`, error);
            }
        }
        this.sessions.clear();
        this.qrCodes.clear();
    }
}

module.exports = BaileysHandler;
