const axios = require('axios');
const logger = require('./logger');

class ApiClient {
    constructor() {
        this.baseUrl = process.env.LARAVEL_APP_URL || 'http://localhost:8000';
        this.webhookSecret = process.env.WEBHOOK_SECRET || 'blazt-webhook-secret';
        
        // Create axios instance with default config
        this.client = axios.create({
            baseURL: this.baseUrl,
            timeout: 30000,
            headers: {
                'Content-Type': 'application/json',
                'X-Webhook-Secret': this.webhookSecret,
                'User-Agent': 'Blazt-WhatsApp-Service/1.0'
            }
        });

        // Add request interceptor for logging
        this.client.interceptors.request.use(
            (config) => {
                logger.info('API Request', {
                    method: config.method.toUpperCase(),
                    url: config.url,
                    data: config.data ? 'present' : 'none'
                });
                return config;
            },
            (error) => {
                logger.error('API Request Error', { error: error.message });
                return Promise.reject(new Error(error.message));
            }
        );

        // Add response interceptor for logging
        this.client.interceptors.response.use(
            (response) => {
                logger.info('API Response', {
                    status: response.status,
                    url: response.config.url
                });
                return response;
            },
            (error) => {
                logger.error('API Response Error', {
                    status: error.response?.status,
                    url: error.config?.url,
                    message: error.message
                });
                return Promise.reject(new Error(error.message));
            }
        );
    }

    async sendIncomingMessage(messageData) {
        try {
            const response = await this.client.post('/api/webhooks/whatsapp/message-received', {
                ...messageData,
                timestamp: new Date().toISOString()
            });
            
            return response.data;
        } catch (error) {
            logger.error('Failed to send incoming message to Laravel:', {
                error: error.message,
                messageData
            });
            throw error;
        }
    }

    async notifyMessageStatus(messageId, status, whatsappMessageId = null, errorMessage = null) {
        try {
            const response = await this.client.post('/api/webhooks/whatsapp/message-status', {
                message_id: messageId,
                status: status,
                whatsapp_message_id: whatsappMessageId,
                error_message: errorMessage,
                timestamp: new Date().toISOString()
            });
            
            return response.data;
        } catch (error) {
            logger.error('Failed to notify message status to Laravel:', {
                error: error.message,
                messageId,
                status
            });
            // Don't throw error here to avoid breaking the flow
        }
    }

    async notifySessionStatus(accountId, status, userData = null) {
        try {
            const response = await this.client.post('/api/webhooks/whatsapp/session-status', {
                account_id: accountId,
                status: status,
                user_data: userData,
                timestamp: new Date().toISOString()
            });
            
            return response.data;
        } catch (error) {
            logger.error('Failed to notify session status to Laravel:', {
                error: error.message,
                accountId,
                status
            });
            // Don't throw error here to avoid breaking the flow
        }
    }

    async notifyQRGenerated(accountId, qrCodeData) {
        try {
            const response = await this.client.post('/api/webhooks/whatsapp/qr-generated', {
                account_id: accountId,
                qr_code: qrCodeData,
                timestamp: new Date().toISOString()
            });
            
            return response.data;
        } catch (error) {
            logger.error('Failed to notify QR generation to Laravel:', {
                error: error.message,
                accountId
            });
            // Don't throw error here to avoid breaking the flow
        }
    }

    async healthCheck() {
        try {
            const response = await this.client.get('/api/health');
            return response.data;
        } catch (error) {
            logger.error('Laravel health check failed:', {
                error: error.message
            });
            throw error;
        }
    }

    async getUserConfiguration(userId) {
        try {
            const response = await this.client.get(`/api/users/${userId}/configuration`);
            return response.data;
        } catch (error) {
            logger.error('Failed to get user configuration:', {
                error: error.message,
                userId
            });
            throw error;
        }
    }

    async reportError(accountId, error, context = {}) {
        try {
            await this.client.post('/api/webhooks/whatsapp/error', {
                account_id: accountId,
                error_message: error.message,
                error_stack: error.stack,
                context: context,
                timestamp: new Date().toISOString()
            });
        } catch (reportError) {
            logger.error('Failed to report error to Laravel:', {
                error: reportError.message,
                originalError: error.message
            });
        }
    }
}

module.exports = ApiClient;
