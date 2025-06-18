const winston = require('winston');
const path = require('path');

// Custom log format
const logFormat = winston.format.combine(
    winston.format.timestamp({ format: 'YYYY-MM-DD HH:mm:ss' }),
    winston.format.errors({ stack: true }),
    winston.format.json(),
    winston.format.printf(({ timestamp, level, message, stack, ...meta }) => {
        let log = `${timestamp} [${level.toUpperCase()}]: ${message}`;
        
        if (Object.keys(meta).length > 0) {
            log += ` ${JSON.stringify(meta)}`;
        }
        
        if (stack) {
            log += `\n${stack}`;
        }
        
        return log;
    })
);

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
