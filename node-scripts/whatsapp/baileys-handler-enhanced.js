const { 
    default: makeWASocket, 
    DisconnectReason, 
    useMultiFileAuthState,
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore,
    Browsers
} = require('@whiskeysockets/baileys');
const QRCode = require('qrcode');
const fs = require('fs').promises;
const path = require('path');
const { v4: uuidv4 } = require('uuid');
const P = require('pino');
const logger = require('../utils/logger');
const ApiClient = require('../utils/api-client');

// Create API client instance
const apiClient = new ApiClient();

class EnhancedWhatsAppHandler {
    constructor() {
        this.connections = new Map();
        this.qrCodes = new Map();
        this.connectionAttempts = new Map();
        this.maxRetries = 3;
        this.qrTimeout = 60000; // 60 seconds
        this.reconnectDelay = 5000; // 5 seconds
        
        // Enhanced browser configuration for better WhatsApp Web compatibility
        this.browserConfig = [
            'WhatsApp-SaaS', // App name
            'Chrome', // Browser name  
            '121.0.0.0' // Version
        ];
    }

    async init() {
        try {
            // Ensure auth directory exists
            const authDir = path.join(__dirname, '../auth');
            await fs.mkdir(authDir, { recursive: true });
            
            logger.info('Enhanced WhatsApp handler initialized');
        } catch (error) {
            logger.error('Failed to initialize Enhanced WhatsApp handler:', error);
        }
    }

    async connectAccount(accountId, phoneNumber) {
        try {
            logger.info(`Starting enhanced connection for account ${accountId} (${phoneNumber})`);
            
            // Clean up any existing connection
            await this.disconnectAccount(accountId);
            
            // Reset connection attempts
            this.connectionAttempts.set(accountId, 0);
            
            return await this.establishConnection(accountId, phoneNumber);
        } catch (error) {
            logger.error(`Enhanced connection failed for account ${accountId}:`, error);
            throw error;
        }
    }

    async establishConnection(accountId, phoneNumber, isRetry = false) {
        const authDir = path.join(__dirname, '../auth', accountId);
        
        try {
            // Create auth directory
            await fs.mkdir(authDir, { recursive: true });
            
            // Get latest Baileys version for compatibility
            const { version, isLatest } = await fetchLatestBaileysVersion();
            logger.info(`Using Baileys version ${version.join('.')}, isLatest: ${isLatest}`);
            
            // Setup auth state
            const { state, saveCreds } = await useMultiFileAuthState(authDir);
            
            // Create socket with enhanced configuration
            const socket = makeWASocket({
                version,
                auth: {
                    creds: state.creds,
                    keys: makeCacheableSignalKeyStore(state.keys, P().child({}))
                },
                browser: this.browserConfig,
                logger: P({ level: 'silent' }), // Reduce Baileys logging
                printQRInTerminal: false,
                generateHighQualityLinkPreview: true,
                markOnlineOnConnect: false, // Don't show as online immediately
                syncFullHistory: false, // Faster connection
                defaultQueryTimeoutMs: 30000, // 30 second timeout
                connectTimeoutMs: 30000, // 30 second connection timeout
                keepAliveIntervalMs: 30000, // Keep alive every 30 seconds
                // Enhanced connection options for stability
                options: {
                    phoneNumber: phoneNumber,
                    skipOfflineChats: true,
                    skipUnreadCount: true
                }
            });

            // Store connection
            this.connections.set(accountId, socket);
            
            // Setup connection event handlers
            await this.setupConnectionHandlers(socket, accountId, phoneNumber, saveCreds);
            
            return { success: true, message: 'Connection initiated' };
            
        } catch (error) {
            logger.error(`Failed to establish connection for account ${accountId}:`, error);
            
            // Cleanup on failure
            await this.cleanupConnection(accountId);
            
            throw error;
        }
    }

    async setupConnectionHandlers(socket, accountId, phoneNumber, saveCreds) {
        let qrGenerated = false;
        let qrTimer = null;
        
        // Connection update handler
        socket.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;
            
            logger.info(`Connection update for ${accountId}:`, {
                connection,
                hasQR: !!qr,
                lastDisconnect: lastDisconnect?.error?.output?.statusCode
            });

            if (qr && !qrGenerated) {
                qrGenerated = true;
                await this.handleQRCode(accountId, phoneNumber, qr);
                
                // Set QR timeout
                qrTimer = setTimeout(async () => {
                    logger.info(`QR code timeout for account ${accountId}, refreshing connection`);
                    await this.refreshConnection(accountId, phoneNumber);
                }, this.qrTimeout);
            }

            if (connection === 'open') {
                logger.info(`✅ WhatsApp connected successfully for account ${accountId}`);
                
                // Clear QR timer
                if (qrTimer) {
                    clearTimeout(qrTimer);
                    qrTimer = null;
                }
                
                // Clear QR code
                this.qrCodes.delete(accountId);
                
                // Reset connection attempts
                this.connectionAttempts.set(accountId, 0);
                
                // Notify Laravel backend
                await this.notifyConnectionStatus(accountId, 'connected', null);
                
                // Get user info
                try {
                    const userInfo = socket.user;
                    if (userInfo) {
                        logger.info(`Connected as: ${userInfo.name || userInfo.id} for account ${accountId}`);
                        await this.notifyUserInfo(accountId, userInfo);
                    }
                } catch (error) {
                    logger.warn(`Could not get user info for account ${accountId}:`, error);
                }
            }

            if (connection === 'close') {
                // Clear QR timer
                if (qrTimer) {
                    clearTimeout(qrTimer);
                    qrTimer = null;
                }
                
                const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                
                logger.info(`Connection closed for account ${accountId}. Status: ${statusCode}, Should reconnect: ${shouldReconnect}`);
                
                if (shouldReconnect) {
                    const attempts = this.connectionAttempts.get(accountId) || 0;
                    
                    if (attempts < this.maxRetries) {
                        this.connectionAttempts.set(accountId, attempts + 1);
                        logger.info(`Attempting reconnection ${attempts + 1}/${this.maxRetries} for account ${accountId}`);
                        
                        setTimeout(async () => {
                            try {
                                await this.establishConnection(accountId, phoneNumber, true);
                            } catch (error) {
                                logger.error(`Reconnection failed for account ${accountId}:`, error);
                            }
                        }, this.reconnectDelay * (attempts + 1)); // Exponential backoff
                    } else {
                        logger.error(`Max reconnection attempts reached for account ${accountId}`);
                        await this.notifyConnectionStatus(accountId, 'failed', 'Max reconnection attempts reached');
                    }
                } else {
                    logger.info(`Account ${accountId} was logged out, cleaning up`);
                    await this.cleanupConnection(accountId);
                    await this.notifyConnectionStatus(accountId, 'disconnected', 'Logged out');
                }
            }
        });

        // Credentials update handler
        socket.ev.on('creds.update', saveCreds);
        
        // Error handler
        socket.ev.on('messaging-history.set', () => {
            logger.info(`Message history loaded for account ${accountId}`);
        });
    }

    async handleQRCode(accountId, phoneNumber, qr) {
        try {
            logger.info(`Generating QR code for account ${accountId}`);
            
            // Generate QR code with enhanced options for better scanning
            const qrCodeDataURL = await QRCode.toDataURL(qr, {
                width: 400,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                },
                errorCorrectionLevel: 'M'
            });
            
            // Store QR code
            this.qrCodes.set(accountId, {
                qr: qrCodeDataURL,
                timestamp: new Date(),
                raw: qr
            });
            
            // Notify Laravel backend
            await this.notifyQRCode(accountId, qrCodeDataURL);
            
            logger.info(`QR code generated and sent to backend for account ${accountId}`);
            
        } catch (error) {
            logger.error(`Failed to generate QR code for account ${accountId}:`, error);
        }
    }

    async refreshConnection(accountId, phoneNumber) {
        try {
            logger.info(`Refreshing connection for account ${accountId}`);
            
            // Disconnect current connection
            await this.disconnectAccount(accountId, false);
            
            // Small delay before reconnecting
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // Establish new connection
            await this.establishConnection(accountId, phoneNumber);
            
        } catch (error) {
            logger.error(`Failed to refresh connection for account ${accountId}:`, error);
        }
    }

    async disconnectAccount(accountId, notify = true) {
        try {
            const socket = this.connections.get(accountId);
            
            if (socket) {
                logger.info(`Disconnecting account ${accountId}`);
                
                try {
                    await socket.logout();
                } catch (error) {
                    logger.warn(`Error during logout for account ${accountId}:`, error);
                }
                
                socket.end();
                this.connections.delete(accountId);
            }
            
            // Clean up QR code
            this.qrCodes.delete(accountId);
            
            // Clean up connection attempts
            this.connectionAttempts.delete(accountId);
            
            // Clean up auth files
            await this.cleanupAuthFiles(accountId);
            
            if (notify) {
                await this.notifyConnectionStatus(accountId, 'disconnected', 'Manual disconnect');
            }
            
            return { success: true, message: 'Account disconnected' };
            
        } catch (error) {
            logger.error(`Failed to disconnect account ${accountId}:`, error);
            throw error;
        }
    }

    async cleanupConnection(accountId) {
        try {
            this.connections.delete(accountId);
            this.qrCodes.delete(accountId);
            this.connectionAttempts.delete(accountId);
            
            logger.info(`Cleaned up connection data for account ${accountId}`);
        } catch (error) {
            logger.error(`Failed to cleanup connection for account ${accountId}:`, error);
        }
    }

    async cleanupAuthFiles(accountId) {
        try {
            const authDir = path.join(__dirname, '../auth', accountId);
            await fs.rmdir(authDir, { recursive: true });
            logger.info(`Cleaned up auth files for account ${accountId}`);
        } catch (error) {
            logger.warn(`Failed to cleanup auth files for account ${accountId}:`, error);
        }
    }

    async notifyQRCode(accountId, qrCode) {
        try {
            await apiClient.notifyQRGenerated(accountId, qrCode);
        } catch (error) {
            logger.error(`Failed to notify QR code for account ${accountId}:`, error);
        }
    }

    async notifyConnectionStatus(accountId, status, message = null) {
        try {
            await apiClient.notifyConnectionStatus(accountId, status, message);
        } catch (error) {
            logger.error(`Failed to notify status for account ${accountId}:`, error);
        }
    }

    async notifyUserInfo(accountId, userInfo) {
        try {
            // Use connection status with user info
            await apiClient.notifyConnectionStatus(accountId, 'connected', JSON.stringify(userInfo));
        } catch (error) {
            logger.error(`Failed to notify user info for account ${accountId}:`, error);
        }
    }

    // Utility methods
    getQRCode(accountId) {
        return this.qrCodes.get(accountId);
    }

    getConnectionStatus(accountId) {
        const socket = this.connections.get(accountId);
        const qr = this.qrCodes.get(accountId);
        
        return {
            connected: socket?.readyState === 1,
            hasQR: !!qr,
            qrCode: qr?.qr || null,
            attempts: this.connectionAttempts.get(accountId) || 0
        };
    }

    getAllConnections() {
        const connections = {};
        for (const [accountId, socket] of this.connections) {
            connections[accountId] = {
                connected: socket?.readyState === 1,
                hasQR: this.qrCodes.has(accountId),
                attempts: this.connectionAttempts.get(accountId) || 0
            };
        }
        return connections;
    }
}

module.exports = EnhancedWhatsAppHandler;
