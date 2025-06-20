const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
require('dotenv').config();

const EnhancedWhatsAppHandler = require('./whatsapp/baileys-handler-enhanced');
const logger = require('./utils/logger');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(helmet());
app.use(cors());
app.use(express.json({ limit: '50mb' }));
app.use(express.urlencoded({ extended: true, limit: '50mb' }));

// Initialize Enhanced WhatsApp handler
const whatsappHandler = new EnhancedWhatsAppHandler();

// Initialize handler
(async () => {
    await whatsappHandler.init();
    logger.info('Enhanced WhatsApp handler initialized');
})();

// Routes
app.get('/health', (req, res) => {
    res.json({ 
        status: 'OK', 
        timestamp: new Date().toISOString(),
        uptime: process.uptime(),
        handler: 'Enhanced',
        connections: whatsappHandler.getAllConnections()
    });
});

// Connect WhatsApp account (Enhanced)
app.post('/connect-account', async (req, res) => {
    try {
        const { accountId, phoneNumber } = req.body;
        
        if (!accountId || !phoneNumber) {
            return res.status(400).json({ 
                success: false, 
                error: 'Account ID and phone number are required' 
            });
        }

        logger.info(`Connection request for account ${accountId} (${phoneNumber})`);
        const result = await whatsappHandler.connectAccount(accountId, phoneNumber);
        res.json(result);
        
    } catch (error) {
        logger.error('Connect account error:', error);
        res.status(500).json({ 
            success: false, 
            error: error.message 
        });
    }
});

// Disconnect WhatsApp account
app.post('/disconnect-account', async (req, res) => {
    try {
        const { accountId } = req.body;
        
        if (!accountId) {
            return res.status(400).json({ 
                success: false, 
                error: 'Account ID is required' 
            });
        }

        logger.info(`Disconnect request for account ${accountId}`);
        const result = await whatsappHandler.disconnectAccount(accountId);
        res.json(result);
        
    } catch (error) {
        logger.error('Disconnect account error:', error);
        res.status(500).json({ 
            success: false, 
            error: error.message 
        });
    }
});

// Get QR code for account
app.get('/qr-code/:accountId', (req, res) => {
    try {
        const { accountId } = req.params;
        const qrData = whatsappHandler.getQRCode(accountId);
        
        if (!qrData) {
            return res.status(404).json({ 
                success: false, 
                error: 'QR code not found for this account' 
            });
        }
        
        res.json({
            success: true,
            qr_code: qrData.qr,
            timestamp: qrData.timestamp,
            account_id: accountId
        });
        
    } catch (error) {
        logger.error('Get QR code error:', error);
        res.status(500).json({ 
            success: false, 
            error: error.message 
        });
    }
});

// Get connection status for account
app.get('/status/:accountId', (req, res) => {
    try {
        const { accountId } = req.params;
        const status = whatsappHandler.getConnectionStatus(accountId);
        
        res.json({
            success: true,
            account_id: accountId,
            ...status
        });
        
    } catch (error) {
        logger.error('Get status error:', error);
        res.status(500).json({ 
            success: false, 
            error: error.message 
        });
    }
});

// Get all connections status
app.get('/connections', (req, res) => {
    try {
        const connections = whatsappHandler.getAllConnections();
        res.json({
            success: true,
            connections: connections
        });
        
    } catch (error) {
        logger.error('Get connections error:', error);
        res.status(500).json({ 
            success: false, 
            error: error.message 
        });
    }
});

// Refresh connection for account
app.post('/refresh-connection', async (req, res) => {
    try {
        const { accountId, phoneNumber } = req.body;
        
        if (!accountId || !phoneNumber) {
            return res.status(400).json({ 
                success: false, 
                error: 'Account ID and phone number are required' 
            });
        }

        logger.info(`Refresh connection request for account ${accountId}`);
        await whatsappHandler.refreshConnection(accountId, phoneNumber);
        
        res.json({
            success: true,
            message: 'Connection refresh initiated'
        });
        
    } catch (error) {
        logger.error('Refresh connection error:', error);
        res.status(500).json({ 
            success: false, 
            error: error.message 
        });
    }
});

// Error handling middleware
app.use((error, req, res, next) => {
    logger.error('Unhandled error:', error);
    res.status(500).json({ 
        success: false, 
        error: 'Internal server error' 
    });
});

// 404 handler
app.use((req, res) => {
    res.status(404).json({ 
        success: false, 
        error: 'Endpoint not found' 
    });
});

// Graceful shutdown
process.on('SIGTERM', async () => {
    logger.info('SIGTERM received, shutting down gracefully');
    process.exit(0);
});

process.on('SIGINT', async () => {
    logger.info('SIGINT received, shutting down gracefully');
    process.exit(0);
});

// Start server
app.listen(PORT, '0.0.0.0', () => {
    logger.info(`Enhanced WhatsApp Service listening on port ${PORT}`);
    logger.info(`Health check: http://localhost:${PORT}/health`);
});

module.exports = app;
