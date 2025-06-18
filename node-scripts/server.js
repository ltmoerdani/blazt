const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
require('dotenv').config();

const WhatsAppHandler = require('./whatsapp/baileys-handler');
const logger = require('./utils/logger');

const app = express();
const PORT = process.env.PORT || 3001;

// Middleware
app.use(helmet());
app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: true, limit: '50mb' }));

// Initialize WhatsApp handler
const whatsappHandler = new WhatsAppHandler();

// Routes
app.get('/health', (req, res) => {
    res.json({ 
        status: 'OK', 
        timestamp: new Date().toISOString(),
        uptime: process.uptime()
    });
});

// Connect WhatsApp account
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
