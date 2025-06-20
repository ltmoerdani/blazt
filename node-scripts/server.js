const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
require('dotenv').config();

const WhatsAppHandler = require('./whatsapp/baileys-handler');
const WhatsAppHandlerV2 = require('./whatsapp/baileys-handler-v2');
const WhatsAppHandlerV3 = require('./whatsapp/baileys-handler-v3');
const WhatsAppHandlerStable = require('./whatsapp/baileys-handler-stable');
const logger = require('./utils/logger');

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(helmet());
app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: true, limit: '50mb' }));

// Initialize WhatsApp handlers
const whatsappHandler = new WhatsAppHandler();
const whatsappHandlerV2 = new WhatsAppHandlerV2();
const whatsappHandlerV3 = new WhatsAppHandlerV3();
const whatsappHandlerStable = new WhatsAppHandlerStable();

// Initialize all handlers
(async () => {
    await whatsappHandler.init();
    await whatsappHandlerV2.init();
    await whatsappHandlerV3.init();
    await whatsappHandlerStable.init();
    logger.info('All WhatsApp handlers initialized (V1, V2, V3, Stable)');
})();

// Routes
app.get('/health', (req, res) => {
    res.json({ 
        status: 'OK', 
        timestamp: new Date().toISOString(),
        uptime: process.uptime()
    });
});

// Connect WhatsApp account (Stable - Minimal Config)
app.post('/connect-account-stable', async (req, res) => {
    try {
        const { accountId, phoneNumber } = req.body;
        
        if (!accountId || !phoneNumber) {
            return res.status(400).json({ 
                error: 'Account ID and phone number are required' 
            });
        }

        const result = await whatsappHandlerStable.connectAccount(accountId, phoneNumber);
        res.json(result);
    } catch (error) {
        logger.error('Error connecting account (Stable):', error);
        res.status(500).json({ error: error.message });
    }
});

// Connect WhatsApp account (V3 - Enhanced Session Management)
app.post('/connect-account-v3', async (req, res) => {
    try {
        const { accountId, phoneNumber } = req.body;
        
        if (!accountId || !phoneNumber) {
            return res.status(400).json({ 
                error: 'Account ID and phone number are required' 
            });
        }

        const result = await whatsappHandlerV3.connectAccount(accountId, phoneNumber);
        res.json(result);
    } catch (error) {
        logger.error('Error connecting account (V3):', error);
        res.status(500).json({ error: error.message });
    }
});

// Get account status (V3)
app.get('/account-status-v3/:accountId', async (req, res) => {
    try {
        const { accountId } = req.params;
        const status = await whatsappHandlerV3.getAccountStatus(accountId);
        res.json(status);
    } catch (error) {
        logger.error('Error getting account status (V3):', error);
        res.status(500).json({ error: error.message });
    }
});

// Force cleanup account (V3)
app.post('/cleanup-account-v3', async (req, res) => {
    try {
        const { accountId } = req.body;
        
        if (!accountId) {
            return res.status(400).json({ 
                error: 'Account ID is required' 
            });
        }

        await whatsappHandlerV3.forceCleanupAccount(accountId);
        res.json({ success: true, message: 'Account cleanup completed' });
    } catch (error) {
        logger.error('Error cleaning up account (V3):', error);
        res.status(500).json({ error: error.message });
    }
});
app.post('/connect-account-v2', async (req, res) => {
    try {
        const { accountId, phoneNumber } = req.body;
        
        if (!accountId || !phoneNumber) {
            return res.status(400).json({ 
                error: 'Account ID and phone number are required' 
            });
        }

        const result = await whatsappHandlerV2.connectAccount(accountId, phoneNumber);
        res.json(result);
    } catch (error) {
        logger.error('Error connecting account (V2):', error);
        res.status(500).json({ error: error.message });
    }
});

// Connect WhatsApp account (Original)
app.post('/connect-account', async (req, res) => {
    try {
        const { accountId, phoneNumber } = req.body;
        
        if (!accountId || !phoneNumber) {
            return res.status(400).json({ 
                error: 'Account ID and phone number are required' 
            });
        }

        const result = await whatsappHandler.connectAccount(accountId, phoneNumber);
        res.json(result);
    } catch (error) {
        logger.error('Error connecting account:', error);
        res.status(500).json({ error: error.message });
    }
});

// Disconnect WhatsApp account
app.post('/disconnect-account', async (req, res) => {
    try {
        const { accountId } = req.body;
        
        if (!accountId) {
            return res.status(400).json({ 
                error: 'Account ID is required' 
            });
        }

        const result = await whatsappHandler.disconnectAccount(accountId);
        res.json(result);
    } catch (error) {
        logger.error('Error disconnecting account:', error);
        res.status(500).json({ error: error.message });
    }
});

// Get account status
app.get('/account-status/:accountId', async (req, res) => {
    try {
        const { accountId } = req.params;
        const status = await whatsappHandler.getAccountStatus(accountId);
        res.json(status);
    } catch (error) {
        logger.error('Error getting account status:', error);
        res.status(500).json({ error: error.message });
    }
});

// Send message
app.post('/send-message', async (req, res) => {
    try {
        const { account_id, phone_number, message, message_id, media_url } = req.body;
        
        if (!account_id || !phone_number || !message) {
            return res.status(400).json({ 
                error: 'Account ID, phone number, and message are required' 
            });
        }

        const result = await whatsappHandler.sendMessage(
            account_id, 
            phone_number, 
            message, 
            message_id,
            media_url
        );
        
        res.json(result);
    } catch (error) {
        logger.error('Error sending message:', error);
        res.status(500).json({ error: error.message });
    }
});

// Get QR code for account
app.get('/qr-code/:accountId', async (req, res) => {
    try {
        const { accountId } = req.params;
        const qrData = await whatsappHandler.getQRCode(accountId);
        
        if (qrData) {
            res.json({ qr_code: qrData });
        } else {
            res.status(404).json({ error: 'QR code not available' });
        }
    } catch (error) {
        logger.error('Error getting QR code:', error);
        res.status(500).json({ error: error.message });
    }
});

// List connected accounts
app.get('/accounts', async (req, res) => {
    try {
        const accounts = await whatsappHandler.listAccounts();
        res.json({ accounts });
    } catch (error) {
        logger.error('Error listing accounts:', error);
        res.status(500).json({ error: error.message });
    }
});

// Error handling middleware
app.use((error, req, res, next) => {
    logger.error('Unhandled error:', error);
    res.status(500).json({ 
        error: 'Internal server error',
        message: process.env.NODE_ENV === 'development' ? error.message : undefined
    });
});

// Start server
app.listen(PORT, () => {
    logger.info(`Blazt WhatsApp Service running on port ${PORT}`);
    logger.info(`Environment: ${process.env.NODE_ENV || 'development'}`);
});

// Graceful shutdown
process.on('SIGINT', async () => {
    logger.info('Shutting down gracefully...');
    await whatsappHandler.cleanup();
    process.exit(0);
});

process.on('SIGTERM', async () => {
    logger.info('Shutting down gracefully...');
    await whatsappHandler.cleanup();
    process.exit(0);
});
