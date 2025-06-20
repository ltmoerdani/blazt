const winston = require('winston');
const path = require('path');

// Custom log format
// Refactored logFormat.printf to reduce cognitive complexity and fix redundant assignment
const logFormat = winston.format.combine(
    winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }),
    winston.format.errors({ stack: true }),
    winston.format.json(),
    winston.format.printf((info) => {
        // Helper to stringify safely
        function safeStringify(val) {
            if (typeof val === 'string') return val;
            try { return JSON.stringify(val); } catch { return '[object Object]'; }
        }
        // Extract timestamp
        let ts = '';
        if (typeof info.timestamp === 'string') {
            ts = info.timestamp;
        } else if (info.timestamp && typeof info.timestamp.toISOString === 'function') {
            ts = info.timestamp.toISOString();
        }
        // Extract message
        const msg = extractMessage(info.message, safeStringify);
        // Extract stack
        const stk = extractStack(info.stack, safeStringify);
        // Compose log
        let log = `${ts} [${info.level.toUpperCase()}]: ${msg}`;
        if (Object.keys(info).length > 4) {
            const meta = { ...info };
            delete meta.timestamp; delete meta.level; delete meta.message; delete meta.stack;
            log += ` ${safeStringify(meta)}`;
        }
        if (stk) log += `\n${stk}`;
        return log;
    })
);

// Helper to extract message
function extractMessage(message, safeStringify) {
    if (typeof message === 'object' && message !== null) {
        if (message.message && typeof message.message === 'string') {
            return message.message;
        } else {
            return safeStringify(message);
        }
    } else {
        return typeof message === 'string' ? message : safeStringify(message);
    }
}

// Helper to extract stack
function extractStack(stack, safeStringify) {
    if (!stack) return '';
    if (typeof stack === 'object' && stack !== null) {
        if (stack.stack && typeof stack.stack === 'string') {
            return stack.stack;
        } else {
            return safeStringify(stack);
        }
    } else if (typeof stack === 'string') {
        return stack;
    } else {
        return safeStringify(stack);
    }
}

// Create logger instance
const logger = winston.createLogger({
    level: process.env.LOG_LEVEL || 'info',
    format: logFormat,
    transports: [
        // Console transport
        new winston.transports.Console({
            format: winston.format.combine(
                winston.format.colorize(),
                logFormat
            )
        }),
        
        // File transport for all logs
        new winston.transports.File({
            filename: path.join(__dirname, '../../storage/logs/whatsapp-service.log'),
            format: winston.format.json(),
            maxsize: 5242880, // 5MB
            maxFiles: 5
        }),
        
        // Error logs only
        new winston.transports.File({
            filename: path.join(__dirname, '../../storage/logs/whatsapp-errors.log'),
            level: 'error',
            format: winston.format.json(),
            maxsize: 5242880, // 5MB
            maxFiles: 5
        })
    ],
    
    // Handle uncaught exceptions
    exceptionHandlers: [
        new winston.transports.File({
            filename: path.join(__dirname, '../../storage/logs/whatsapp-exceptions.log')
        })
    ],
    
    // Handle unhandled promise rejections
    rejectionHandlers: [
        new winston.transports.File({
            filename: path.join(__dirname, '../../storage/logs/whatsapp-rejections.log')
        })
    ]
});

// Add Baileys-compatible methods
logger.trace = (...args) => logger.debug(...args);
logger.child = () => logger; // Return the same logger instance

// Add request logging method
logger.logRequest = (req, res, responseTime) => {
    logger.info('HTTP Request', {
        method: req.method,
        url: req.url,
        userAgent: req.get('user-agent'),
        ip: req.ip,
        responseTime: `${responseTime}ms`,
        statusCode: res.statusCode
    });
};

// Add error logging method
logger.logError = (error, context = {}) => {
    logger.error('Application Error', {
        message: error.message,
        stack: error.stack,
        ...context
    });
};

module.exports = logger;
