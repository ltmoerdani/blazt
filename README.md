<div align="center">
  <h1>🚀 Blazt - WhatsApp Business SaaS Starter Pack</h1>
  <p><strong>Complete Laravel + Node.js solution for WhatsApp Business automation with AI chatbot, bulk messaging, campaign management, and subscription billing</strong></p>
  
  <p>
    <img src="https://img.shields.io/badge/Laravel-11.x-red?style=for-the-badge&logo=laravel" alt="Laravel">
    <img src="https://img.shields.io/badge/Node.js-20.x-green?style=for-the-badge&logo=node.js" alt="Node.js">
    <img src="https://img.shields.io/badge/MySQL-8.0-blue?style=for-the-badge&logo=mysql" alt="MySQL">
    <img src="https://img.shields.io/badge/WhatsApp-Business-25D366?style=for-the-badge&logo=whatsapp" alt="WhatsApp">
  </p>
</div>

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Architecture](#-architecture)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [API Documentation](#-api-documentation)
- [Usage Examples](#-usage-examples)
- [Development](#-development)
- [Production Deployment](#-production-deployment)
- [Contributing](#-contributing)

## 🎯 Overview

**Blazt** adalah starter pack SaaS yang lengkap untuk membangun platform WhatsApp Business automation. Dibangun dengan Laravel (TALL stack) dan Node.js, Blazt menyediakan semua fitur yang dibutuhkan untuk memulai bisnis SaaS WhatsApp marketing:

### What Makes Blazt Special?

- ✅ **Production-Ready**: Telah diuji dan siap untuk production deployment
- ✅ **Multi-Tenant**: Mendukung multiple WhatsApp accounts per user
- ✅ **AI-Powered**: Integrasi dengan OpenAI, Claude, Gemini, dan model lokal
- ✅ **Scalable Architecture**: Domain-Driven Design dengan proper separation
- ✅ **Complete Billing**: Subscription management dengan usage-based pricing
- ✅ **Real-time Analytics**: Comprehensive reporting dan dashboard
- ✅ **Developer Friendly**: Dokumentasi lengkap dan code yang clean

### Tech Stack

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Frontend**: Alpine.js, Tailwind CSS, Livewire (TALL Stack)
- **WhatsApp Integration**: Node.js + Baileys library
- **Database**: MySQL 8.0+ dengan partitioning
- **Queue**: Redis/Database dengan Laravel Queue
- **AI Integration**: OpenAI, Anthropic Claude, Google Gemini
- **Payment**: Stripe integration ready
- **Analytics**: Custom analytics engine dengan real-time reporting

## � Features

### 📱 WhatsApp Business Integration
- **Multi-Account Management**: Kelola beberapa nomor WhatsApp dalam satu dashboard
- **QR Code Authentication**: Scan QR code untuk koneksi otomatis ke WhatsApp Web
- **Message Status Tracking**: Real-time tracking untuk sent, delivered, read, failed
- **Media Support**: Kirim gambar, video, dokumen, dan audio dengan mudah
- **Auto Reconnection**: Automatic reconnection jika koneksi terputus
- **Session Management**: Persistent session dengan backup otomatis

### 🤖 AI Chatbot Multi-Provider
- **Multiple AI Providers**: 
  - OpenAI GPT-3.5/4 (untuk response yang natural)
  - Anthropic Claude (untuk analisis mendalam)
  - Google Gemini (untuk multimodal AI)
  - Local AI Models (untuk privacy dan cost efficiency)
- **Context-Aware Conversations**: Memahami konteks percakapan sebelumnya
- **Custom Prompts**: Kustomisasi personality dan behavior bot
- **Auto-Reply Configuration**: Setup auto-reply berdasarkan keywords atau conditions
- **Conversation Analytics**: Track performa dan tingkat kepuasan customer

### 📢 Campaign Management
- **Template-Based Messaging**: Buat template pesan yang bisa digunakan ulang
- **Contact Segmentation**: Group kontaktil berdasarkan kategori, tag, atau custom fields
- **Bulk Messaging**: Kirim pesan ke ribuan kontakis secara bersamaan
- **Scheduled Campaigns**: Jadwalkan kampanye untuk waktu tertentu
- **A/B Testing**: Test beberapa versi pesan untuk optimasi
- **Drip Campaigns**: Setup automated follow-up sequences
- **Performance Tracking**: Monitor open rate, response rate, dan conversion

### 📊 Analytics & Reporting
- **Real-time Dashboard**: Monitor semua metrics dalam real-time
- **Message Analytics**: Track delivery rate, read rate, response rate
- **Campaign Performance**: ROI tracking, conversion analytics
- **User Engagement**: Heat maps, engagement patterns, peak hours
- **Revenue Analytics**: Track revenue per campaign, per contact, per time period
- **Custom Reports**: Generate custom reports dengan date range dan filters
- **Export Functionality**: Export data ke CSV, Excel, PDF

### 💰 Subscription & Billing
- **Tiered Plans**: Basic, Pro, Enterprise dengan fitur berbeda
- **Usage-Based Billing**: Charge berdasarkan jumlah pesan yang dikirim
- **Credit System**: Top-up credit atau monthly allowance
- **Automated Billing**: Integration dengan Stripe untuk payment otomatis
- **Invoice Generation**: Automatic invoice generation dan email
- **Usage Limits**: Enforce limits berdasarkan subscription plan
- **Upgrade/Downgrade**: Seamless plan changes dengan proration

### 👥 Contact Management
- **Import/Export**: Bulk import dari CSV, Excel, atau API
- **Custom Fields**: Tambahkan custom fields untuk data tambahan
- **Tagging System**: Tag kontaktis untuk segmentasi yang better
- **Duplicate Detection**: Automatic detection dan merge duplicate contacts
- **Contact History**: Track semua interaksi dengan setiap kontak
- **GDPR Compliance**: Data export dan delete untuk compliance

### 🔒 Security & Privacy
- **Role-Based Access**: Admin, Manager, Agent dengan permissions berbeda
- **API Authentication**: Secure API dengan bearer tokens
- **Data Encryption**: Sensitive data diencrypt di database
- **Audit Logging**: Track semua user actions untuk security
- **Rate Limiting**: Protect API dari abuse dan spam
- **Webhook Security**: Signed webhooks untuk secure communication

## 🏗 Architecture

Blazt menggunakan **Domain-Driven Design (DDD)** architecture untuk maintainability dan scalability:

```
app/
├── Domain/                 # Business logic layer
│   ├── AI/                # AI chatbot domain
│   │   ├── Models/        # AI-related models
│   │   ├── Services/      # ChatbotService, ProviderManager
│   │   └── Jobs/          # ProcessAIResponseJob
│   ├── WhatsApp/          # WhatsApp integration domain
│   │   ├── Models/        # WhatsAppAccount, Message
│   │   ├── Services/      # WhatsAppService, MessageSender
│   │   └── Jobs/          # SendBulkMessageJob, ProcessIncomingMessageJob
│   ├── Campaign/          # Campaign management domain
│   │   ├── Models/        # Campaign, Template
│   │   └── Services/      # CampaignService
│   ├── Contact/           # Contact management domain
│   │   ├── Models/        # Contact, ContactGroup
│   │   └── Services/      # ContactService
│   ├── Analytics/         # Analytics domain
│   │   ├── Models/        # Analytics, Report
│   │   └── Services/      # AnalyticsService
│   └── User/              # User management domain
│       ├── Models/        # User, Subscription
│       └── Services/      # UserService, BillingService
├── Http/
│   └── Controllers/
│       ├── API/           # REST API controllers
│       └── Webhook/       # Webhook controllers
├── Infrastructure/        # Infrastructure layer
│   └── Repositories/      # Data access layer
└── Interfaces/            # Contracts and interfaces
```

### Database Schema

Database menggunakan **18 tabel utama** dengan partitioning untuk performance:

**Core Tables:**
- `users` - User accounts dengan UUID primary key
- `whatsapp_accounts` - WhatsApp business accounts
- `contacts` - Contact database dengan segmentation
- `contact_groups` - Contact grouping system

**Messaging Tables:**
- `messages` - All messages (partitioned by month)
- `campaigns` - Campaign definitions
- `templates` - Message templates
- `sessions` - WhatsApp session management

**AI Tables:**
- `ai_conversations` - Conversation history
- `ai_responses` - AI response logs
- `ai_training_data` - Training data for custom models

**Analytics Tables:**
- `analytics` - Core analytics data
- `campaign_analytics` - Campaign-specific metrics
- `usage_logs` - Usage tracking for billing

**Billing Tables:**
- `subscriptions` - User subscription plans
- `subscription_plans` - Available plans
- `usage_limits` - Plan limits and quotas
- `invoices` - Billing invoices

### Service Layer

Setiap domain memiliki service layer yang handle business logic:

- **WhatsAppService**: Handle koneksi dan operasi WhatsApp
- **MessageSender**: Handle sending messages dengan queue
- **ChatbotService**: Handle AI chatbot responses
- **CampaignService**: Handle campaign creation dan execution
- **AnalyticsService**: Handle data collection dan reporting
- **BillingService**: Handle subscription dan billing logic

## 💻 Requirements

### System Requirements
- **PHP**: 8.2 atau lebih tinggi
- **Node.js**: 18.x atau lebih tinggi (20.x recommended)
- **MySQL**: 8.0 atau lebih tinggi
- **Redis**: 6.x atau lebih tinggi (optional, untuk cache dan queue)
- **Composer**: Latest version
- **NPM**: Latest version

### PHP Extensions
```bash
# Required PHP extensions
php -m | grep -E "(curl|mbstring|openssl|pdo|pdo_mysql|tokenizer|xml|json|gd|zip)"
```

### Server Requirements (Production)
- **RAM**: Minimum 2GB (4GB recommended)
- **Storage**: Minimum 10GB SSD
- **CPU**: 2 cores minimum
- **Bandwidth**: Unlimited atau minimal 1TB/month

## 🚀 Installation

### Step 1: Clone Repository
```bash
git clone https://github.com/your-username/blazt.git
cd blazt
```

### Step 2: Install PHP Dependencies
```bash
composer install
```

### Step 3: Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup

**Create MySQL Database:**
```sql
CREATE DATABASE blazt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Update .env file:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blazt
DB_USERNAME=root
DB_PASSWORD=your_password
```

**Run Migrations and Seed Data:**
```bash
# Run migrations (creates all 18 tables)
php artisan migrate

# Seed demo data (optional but recommended)
php artisan db:seed --class=DemoSeeder
```

### Step 5: Node.js WhatsApp Service

**Install Node.js Dependencies:**
```bash
cd node-scripts
npm install
```

**Setup Node.js Environment:**
```bash
cp .env.example .env
```

**Configure node-scripts/.env:**
```env
PORT=3001
LARAVEL_API_URL=http://localhost:8000
LARAVEL_API_TOKEN=your-api-token
WEBHOOK_SECRET=your-webhook-secret-key
SESSION_PATH=./sessions
QR_PATH=./qr-codes
LOG_LEVEL=info
```

### Step 6: Start Services

**Terminal 1 - Laravel Application:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Terminal 2 - WhatsApp Node.js Service:**
```bash
cd node-scripts
npm start
```

**Terminal 3 - Queue Worker:**
```bash
php artisan queue:work --tries=3 --timeout=300
```

### Step 7: Verify Installation

**Check Laravel Health:**
```bash
curl http://localhost:8000/api/v1/health
# Expected: {"status":"ok","timestamp":"2024-01-01T00:00:00.000000Z"}
```

**Check Node.js Service:**
```bash
curl http://localhost:3001/health
# Expected: {"status":"ok","service":"whatsapp","uptime":123}
```

**Check Database Connection:**
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

## ⚙️ Configuration

### Laravel Configuration (.env)

```env
# Application
APP_NAME="Blazt"
APP_ENV=local
APP_KEY=base64:generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blazt
DB_USERNAME=root
DB_PASSWORD=

# Queue Configuration
QUEUE_CONNECTION=database
# For production, use Redis:
# QUEUE_CONNECTION=redis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# WhatsApp Node.js Service
WHATSAPP_NODE_URL=http://localhost:3001
WHATSAPP_WEBHOOK_SECRET=your-webhook-secret-key
WHATSAPP_SESSION_TIMEOUT=300000
WHATSAPP_QR_TIMEOUT=60000

# AI Provider APIs (Optional)
OPENAI_API_KEY=sk-your-openai-api-key
OPENAI_MODEL=gpt-3.5-turbo
ANTHROPIC_API_KEY=your-claude-api-key
ANTHROPIC_MODEL=claude-3-sonnet-20240229
GOOGLE_API_KEY=your-gemini-api-key
GOOGLE_MODEL=gemini-pro

# Payment Gateway (Stripe)
STRIPE_KEY=pk_test_your-stripe-publishable-key
STRIPE_SECRET=sk_test_your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=whsec_your-webhook-secret

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@blazt.app"
MAIL_FROM_NAME="${APP_NAME}"

# Cache
CACHE_DRIVER=file
# For production:
# CACHE_DRIVER=redis

# Session
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

### AI Provider Configuration

File: `config/ai-providers.php`
```php
return [
    'default' => env('AI_DEFAULT_PROVIDER', 'openai'),
    
    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-3.5-turbo'),
            'max_tokens' => 1000,
            'temperature' => 0.7,
        ],
        'anthropic' => [
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-sonnet-20240229'),
            'max_tokens' => 1000,
        ],
        // ... more providers
    ],
];
```

### Subscription Plans Configuration

File: `config/subscription.php`
```php
return [
    'plans' => [
        'basic' => [
            'name' => 'Basic Plan',
            'price' => 29.99,
            'currency' => 'USD',
            'interval' => 'month',
            'limits' => [
                'messages_per_month' => 1000,
                'whatsapp_accounts' => 1,
                'contacts' => 5000,
                'campaigns_per_month' => 10,
            ],
        ],
        'pro' => [
            'name' => 'Pro Plan', 
            'price' => 99.99,
            'currency' => 'USD',
            'interval' => 'month',
            'limits' => [
                'messages_per_month' => 10000,
                'whatsapp_accounts' => 5,
                'contacts' => 50000,
                'campaigns_per_month' => 100,
            ],
        ],
        // ... more plans
    ],
];
```

### Demo Data Overview

Setelah menjalankan `DemoSeeder`, anda akan memiliki:

**Demo User Account:**
- Email: `demo@blazt.app`
- Password: `password`
- Plan: Basic

**WhatsApp Account:**
- Phone: `+628123456789`
- Status: `connected`
- Account Name: `Demo Business`

**Contact Groups:**
- `Personal` (2 contacts)
- `Business` (2 contacts)

**Sample Contacts:**
- John Doe (`+628111111111`) - Personal group
- Jane Smith (`+628222222222`) - Personal group  
- PT ABC (`+628333333333`) - Business group
- CV XYZ (`+628444444444`) - Business group

**Sample Campaign:**
- Name: `Welcome Campaign`
- Status: `completed`
- Message: `Welcome to our service! Thanks for joining us.`
- Sent to: 4 contacts
- Template: `welcome`

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication
```bash
# All API requests require Bearer token
curl -H "Authorization: Bearer YOUR_API_TOKEN" \
     -H "Content-Type: application/json" \
     http://localhost:8000/api/v1/health
```

### Core Endpoints

#### 🔍 Health Check
```bash
GET /api/v1/health
```
**Response:**
```json
{
  "status": "ok",
  "timestamp": "2024-01-01T00:00:00.000000Z",
  "version": "1.0.0",
  "services": {
    "database": "ok",
    "whatsapp": "ok",
    "queue": "ok"
  }
}
```

#### 📱 WhatsApp Management

**List WhatsApp Accounts:**
```bash
GET /api/v1/whatsapp/accounts
```

**Create WhatsApp Account:**
```bash
POST /api/v1/whatsapp/accounts
Content-Type: application/json

{
  "phone": "+628123456789",
  "name": "My Business Account",
  "webhook_url": "https://yourapp.com/webhook"
}
```

**Get QR Code for Authentication:**
```bash
GET /api/v1/whatsapp/accounts/{id}/qr
```

**Send Single Message:**
```bash
POST /api/v1/whatsapp/send
Content-Type: application/json

{
  "account_id": "uuid-account-id",
  "to": "+628123456789",
  "message": "Hello from Blazt!",
  "type": "text"
}
```

**Send Media Message:**
```bash
POST /api/v1/whatsapp/send-media
Content-Type: multipart/form-data

{
  "account_id": "uuid-account-id",
  "to": "+628123456789",
  "caption": "Check this image",
  "media": file_upload,
  "type": "image"
}
```

#### 👥 Contact Management

**List Contacts:**
```bash
GET /api/v1/contacts?page=1&limit=50&group=business
```

**Create Contact:**
```bash
POST /api/v1/contacts
Content-Type: application/json

{
  "name": "John Doe",
  "phone": "+628123456789", 
  "email": "john@example.com",
  "group_id": "uuid-group-id",
  "custom_fields": {
    "company": "ABC Corp",
    "position": "Manager"
  }
}
```

**Import Contacts (Bulk):**
```bash
POST /api/v1/contacts/import
Content-Type: multipart/form-data

{
  "file": contacts.csv,
  "group_id": "uuid-group-id",
  "skip_duplicates": true
}
```

#### 📢 Campaign Management

**List Campaigns:**
```bash
GET /api/v1/campaigns?status=active&page=1
```

**Create Campaign:**
```bash
POST /api/v1/campaigns
Content-Type: application/json

{
  "name": "Summer Sale 2024",
  "template_id": "uuid-template-id",
  "contact_groups": ["uuid-group-1", "uuid-group-2"],
  "schedule_at": "2024-07-01T10:00:00Z",
  "variables": {
    "discount": "30%",
    "valid_until": "July 31st"
  }
}
```

**Start Campaign:**
```bash
POST /api/v1/campaigns/{id}/start
```

**Get Campaign Analytics:**
```bash
GET /api/v1/campaigns/{id}/analytics
```

**Response:**
```json
{
  "campaign_id": "uuid",
  "status": "completed",
  "total_contacts": 1000,
  "messages_sent": 950,
  "messages_delivered": 900,
  "messages_read": 450,
  "messages_failed": 50,
  "responses_received": 123,
  "conversion_rate": 12.3,
  "cost": 47.50
}
```

#### 🤖 AI Chatbot

**Configure AI Settings:**
```bash
POST /api/v1/ai/settings
Content-Type: application/json

{
  "account_id": "uuid-account-id",
  "provider": "openai",
  "model": "gpt-3.5-turbo",
  "auto_reply": true,
  "prompt": "You are a helpful customer service assistant...",
  "max_tokens": 500,
  "temperature": 0.7
}
```

**Train AI with Custom Data:**
```bash
POST /api/v1/ai/training
Content-Type: application/json

{
  "account_id": "uuid-account-id",
  "training_data": [
    {
      "question": "What are your business hours?",
      "answer": "We are open Monday to Friday, 9 AM to 6 PM."
    }
  ]
}
```

**Get Conversation History:**
```bash
GET /api/v1/ai/conversations/{contact_id}?limit=50
```

#### 📊 Analytics

**Get Dashboard Data:**
```bash
GET /api/v1/analytics/dashboard?period=30d
```

**Response:**
```json
{
  "period": "30d",
  "messages": {
    "total_sent": 15420,
    "total_delivered": 14890,
    "total_read": 8934,
    "delivery_rate": 96.5,
    "read_rate": 60.0
  },
  "campaigns": {
    "total_campaigns": 23,
    "active_campaigns": 5,
    "completed_campaigns": 18,
    "avg_conversion_rate": 8.7
  },
  "revenue": {
    "total_revenue": 2847.50,
    "recurring_revenue": 1680.00,
    "usage_revenue": 1167.50
  }
}
```

**Get Message Analytics:**
```bash
GET /api/v1/analytics/messages?start_date=2024-01-01&end_date=2024-01-31&group_by=day
```

**Get Campaign Performance:**
```bash
GET /api/v1/analytics/campaigns/{id}/performance
```

#### 💰 Subscription & Billing

**Get Current Subscription:**
```bash
GET /api/v1/subscription
```

**Upgrade Subscription:**
```bash
POST /api/v1/subscription/upgrade
Content-Type: application/json

{
  "plan": "pro",
  "payment_method": "stripe_token_here"
}
```

**Get Usage Stats:**
```bash
GET /api/v1/subscription/usage?period=current_month
```

**Response:**
```json
{
  "period": "2024-01",
  "plan": "basic",
  "limits": {
    "messages_per_month": 1000,
    "whatsapp_accounts": 1,
    "contacts": 5000
  },
  "usage": {
    "messages_sent": 750,
    "whatsapp_accounts": 1,
    "contacts": 2340
  },
  "remaining": {
    "messages": 250,
    "whatsapp_accounts": 0,
    "contacts": 2660
  }
}
```

### Webhook Endpoints

#### WhatsApp Webhooks
```bash
POST /api/v1/webhooks/whatsapp
```

**Incoming Message Webhook:**
```json
{
  "type": "message",
  "account_id": "uuid",
  "from": "+628123456789",
  "message": {
    "id": "msg_id",
    "type": "text",
    "text": "Hello!",
    "timestamp": "2024-01-01T00:00:00Z"
  }
}
```

**Message Status Webhook:**
```json
{
  "type": "status",
  "account_id": "uuid", 
  "message_id": "msg_id",
  "status": "delivered",
  "timestamp": "2024-01-01T00:00:00Z"
}
```

#### Payment Webhooks (Stripe)
```bash
POST /api/v1/webhooks/stripe
```

### Error Responses

**Standard Error Format:**
```json
{
  "error": true,
  "message": "Resource not found",
  "code": "RESOURCE_NOT_FOUND",
  "details": {
    "resource": "contact",
    "id": "invalid-uuid"
  }
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Rate Limited
- `500` - Internal Server Error

### Rate Limiting

API endpoints are rate limited:
- **General API**: 60 requests per minute
- **Send Message**: 30 requests per minute
- **Bulk Operations**: 10 requests per minute

Rate limit headers:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1609459200
```
## 💡 Usage Examples

### Quick Start Guide

**1. Login to Demo Account**
```bash
# Use demo credentials
Email: demo@blazt.app
Password: password
```

**2. Connect WhatsApp Account**
```bash
# Get QR code
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost:8000/api/v1/whatsapp/accounts/uuid/qr

# Scan QR code with WhatsApp Business app
# Account will automatically connect
```

**3. Import Contacts**
```bash
# Create CSV file with headers: name, phone, email, group
curl -X POST \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -F "file=@contacts.csv" \
     -F "group_id=uuid-group-id" \
     http://localhost:8000/api/v1/contacts/import
```

**4. Create and Send Campaign**
```bash
# Create campaign
curl -X POST \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{
       "name": "Flash Sale",
       "message": "🔥 Flash Sale! 50% off all items. Use code: FLASH50",
       "contact_groups": ["uuid-group-id"],
       "schedule_at": "2024-07-01T10:00:00Z"
     }' \
     http://localhost:8000/api/v1/campaigns

# Start campaign immediately
curl -X POST \
     -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost:8000/api/v1/campaigns/uuid/start
```

### Advanced Usage Examples

**1. Setup AI Chatbot**
```php
// In your controller or service
use App\Domain\AI\Services\ChatbotService;

$chatbot = app(ChatbotService::class);

// Configure AI settings
$chatbot->updateSettings($accountId, [
    'provider' => 'openai',
    'model' => 'gpt-3.5-turbo',
    'auto_reply' => true,
    'prompt' => 'You are a helpful customer service assistant for an e-commerce store. Be friendly and helpful.',
    'max_tokens' => 500,
    'temperature' => 0.7,
]);

// Generate response for incoming message
$response = $chatbot->generateResponse(
    accountId: $accountId,
    message: 'What are your business hours?',
    context: $conversationHistory
);
```

**2. Send Bulk Messages with Queue**
```php
use App\Domain\WhatsApp\Jobs\SendBulkMessageJob;

// Dispatch bulk message job
SendBulkMessageJob::dispatch([
    'account_id' => $accountId,
    'contacts' => [
        ['phone' => '+628111111111', 'name' => 'John'],
        ['phone' => '+628222222222', 'name' => 'Jane'],
    ],
    'message' => 'Hello {{name}}, we have exciting news for you!',
    'type' => 'text',
    'variables' => ['name'] // Template variables
]);
```

**3. Track Message Status**
```php
use App\Domain\WhatsApp\Models\Message;

// Get message status
$message = Message::find($messageId);
echo $message->status; // sent, delivered, read, failed

// Get delivery analytics for campaign
$campaign = Campaign::with(['messages' => function($query) {
    $query->selectRaw('status, COUNT(*) as count')
          ->groupBy('status');
}])->find($campaignId);

foreach ($campaign->messages as $statusGroup) {
    echo "{$statusGroup->status}: {$statusGroup->count} messages\n";
}
```

**4. Custom Analytics Report**
```php
use App\Domain\Analytics\Services\AnalyticsService;

$analytics = app(AnalyticsService::class);

// Get custom report
$report = $analytics->generateReport([
    'start_date' => '2024-01-01',
    'end_date' => '2024-01-31',
    'metrics' => ['messages_sent', 'delivery_rate', 'response_rate'],
    'group_by' => 'day',
    'filters' => [
        'campaign_type' => 'promotional',
        'contact_group' => 'business',
    ]
]);

// Export to CSV
$analytics->exportToCSV($report, 'monthly_report.csv');
```

**5. Webhook Integration**
```php
// Handle incoming WhatsApp message
// File: app/Http/Controllers/Webhook/WhatsAppWebhookController.php

public function handle(Request $request)
{
    $data = $request->all();
    
    if ($data['type'] === 'message') {
        // Process incoming message
        ProcessIncomingMessageJob::dispatch([
            'account_id' => $data['account_id'],
            'from' => $data['from'],
            'message' => $data['message'],
            'timestamp' => $data['timestamp'],
        ]);
        
        // Trigger AI auto-reply if enabled
        if ($this->shouldAutoReply($data['account_id'])) {
            ProcessAIResponseJob::dispatch([
                'account_id' => $data['account_id'],
                'contact_phone' => $data['from'],
                'incoming_message' => $data['message']['text'],
            ]);
        }
    }
    
    return response()->json(['status' => 'ok']);
}
```

### Frontend Integration Examples

**1. JavaScript SDK Usage**
```javascript
// Blazt JavaScript SDK (create this for easier integration)
class BlaztSDK {
    constructor(apiUrl, token) {
        this.apiUrl = apiUrl;
        this.token = token;
    }
    
    async sendMessage(accountId, to, message) {
        const response = await fetch(`${this.apiUrl}/api/v1/whatsapp/send`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.token}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ account_id: accountId, to, message, type: 'text' })
        });
        return response.json();
    }
    
    async getCampaignAnalytics(campaignId) {
        const response = await fetch(`${this.apiUrl}/api/v1/campaigns/${campaignId}/analytics`, {
            headers: { 'Authorization': `Bearer ${this.token}` }
        });
        return response.json();
    }
}

// Usage
const blazt = new BlaztSDK('http://localhost:8000', 'your-api-token');

// Send message
await blazt.sendMessage('account-uuid', '+628123456789', 'Hello from web app!');

// Get analytics
const analytics = await blazt.getCampaignAnalytics('campaign-uuid');
console.log(`Delivery rate: ${analytics.delivery_rate}%`);
```

**2. Real-time Dashboard with WebSocket**
```javascript
// Real-time updates (you can implement WebSocket later)
const eventSource = new EventSource('/api/v1/stream/analytics');

eventSource.onmessage = function(event) {
    const data = JSON.parse(event.data);
    
    // Update dashboard metrics
    document.getElementById('messages-sent').textContent = data.messages_sent;
    document.getElementById('delivery-rate').textContent = `${data.delivery_rate}%`;
    
    // Update charts
    updateChart('deliveryChart', data.delivery_chart_data);
};
```

### Testing Examples

**1. Feature Test Example**
```php
// tests/Feature/WhatsAppTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Domain\WhatsApp\Models\WhatsAppAccount;

class WhatsAppTest extends TestCase
{
    public function test_user_can_send_message()
    {
        $user = User::factory()->create();
        $account = WhatsAppAccount::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)
            ->postJson('/api/v1/whatsapp/send', [
                'account_id' => $account->id,
                'to' => '+628123456789',
                'message' => 'Test message',
                'type' => 'text'
            ]);
            
        $response->assertStatus(200)
                 ->assertJson(['status' => 'queued']);
                 
        $this->assertDatabaseHas('messages', [
            'account_id' => $account->id,
            'to' => '+628123456789',
            'message' => 'Test message',
        ]);
    }
}
```

**2. Unit Test Example**
```php
// tests/Unit/ChatbotServiceTest.php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Domain\AI\Services\ChatbotService;

class ChatbotServiceTest extends TestCase
{
    public function test_generates_ai_response()
    {
        $chatbot = new ChatbotService();
        
        // Mock AI provider response
        $this->mock(\App\Domain\AI\Services\ProviderManager::class)
             ->shouldReceive('generateResponse')
             ->once()
             ->andReturn('Hello! How can I help you today?');
             
        $response = $chatbot->generateResponse(
            accountId: 'test-account',
            message: 'Hello',
            context: []
        );
        
        $this->assertEquals('Hello! How can I help you today?', $response);
    }
}
```

## 🛠 Development

### Local Development Setup

**1. Development Tools**
```bash
# Install development dependencies
composer install --dev

# Install Node.js development tools
cd node-scripts && npm install --save-dev nodemon

# Install Laravel debugging tools
composer require --dev laravel/telescope
php artisan telescope:install
php artisan migrate
```

**2. Code Quality Tools**

**PHP Code Style (Laravel Pint):**
```bash
# Check code style
./vendor/bin/pint --test

# Fix code style automatically
./vendor/bin/pint
```

**Static Analysis (PHPStan):**
```bash
# Analyze code for bugs
./vendor/bin/phpstan analyse --level=5 app/

# Generate baseline for existing issues
./vendor/bin/phpstan analyse --generate-baseline
```

**3. Testing**

**Run All Tests:**
```bash
# Run PHP tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=WhatsAppTest
```

**Create New Tests:**
```bash
# Create feature test
php artisan make:test WhatsAppIntegrationTest

# Create unit test
php artisan make:test --unit ChatbotServiceTest
```

**4. Database Development**

**Create Migration:**
```bash
php artisan make:migration create_custom_table --create=custom_table
```

**Create Seeder:**
```bash
php artisan make:seeder CustomSeeder
```

**Database Commands:**
```bash
# Fresh migrate with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# Check migration status
php artisan migrate:status
```

**5. Queue Development**

**Monitor Queue:**
```bash
# Watch queue jobs in real-time
php artisan queue:work --verbose

# Process failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

**Create Custom Job:**
```bash
php artisan make:job ProcessCustomTask
```

### Development Workflow

**1. Branch Strategy**
```bash
# Feature development
git checkout -b feature/new-ai-provider
git commit -m "feat: add new AI provider support"
git push origin feature/new-ai-provider

# Bug fixes
git checkout -b fix/whatsapp-connection-issue
git commit -m "fix: resolve WhatsApp connection timeout"
```

**2. Code Review Checklist**
- [ ] All tests pass (`php artisan test`)
- [ ] Code style compliance (`./vendor/bin/pint --test`)
- [ ] No static analysis errors (`./vendor/bin/phpstan analyse`)
- [ ] Documentation updated
- [ ] Migration files reviewed
- [ ] Environment variables documented

**3. Performance Monitoring**

**Laravel Telescope:**
```bash
# View at http://localhost:8000/telescope
# Monitor:
# - Database queries
# - Queue jobs
# - API requests
# - Mail sent
# - Cache hits/misses
```

**Database Query Optimization:**
```bash
# Enable query log
DB::enableQueryLog();

# Get executed queries
$queries = DB::getQueryLog();
dd($queries);
```

### API Development

**1. Create New API Endpoint**
```bash
# Create controller
php artisan make:controller API/CustomController --api

# Add routes in routes/api.php
Route::apiResource('custom', CustomController::class);
```

**2. API Versioning**
```php
// routes/api.php - Version 2 API
Route::prefix('v2')->group(function () {
    Route::apiResource('whatsapp', WhatsAppV2Controller::class);
});
```

**3. API Documentation**
```bash
# Install API documentation tools
composer require --dev darkaonline/l5-swagger
php artisan l5-swagger:generate

# Access at http://localhost:8000/api/documentation
```

### Frontend Development (Optional)

**1. TALL Stack Setup**
```bash
# Install Livewire
composer require livewire/livewire

# Install Alpine.js & Tailwind (already included)
npm install

# Build assets
npm run dev

# Watch for changes
npm run dev --watch
```

**2. Create Livewire Component**
```bash
php artisan make:livewire Dashboard
# Creates:
# - app/Livewire/Dashboard.php
# - resources/views/livewire/dashboard.blade.php
```

### Environment-Specific Configuration

**Development (.env.local)**
```env
APP_ENV=local
APP_DEBUG=true
LOG_LEVEL=debug

# Database
DB_DATABASE=blazt_dev

# Queue (use sync for immediate processing)
QUEUE_CONNECTION=sync

# Mail (use log driver for development)
MAIL_MAILER=log

# WhatsApp (development instance)
WHATSAPP_NODE_URL=http://localhost:3001
```

**Testing (.env.testing)**
```env
APP_ENV=testing
DB_DATABASE=blazt_test
QUEUE_CONNECTION=sync
MAIL_MAILER=array
```

**Staging (.env.staging)**
```env
APP_ENV=staging
APP_DEBUG=false
LOG_LEVEL=info

# Use staging database
DB_DATABASE=blazt_staging

# Use Redis for queue
QUEUE_CONNECTION=redis
```

### Debugging Tools

**1. Laravel Debugbar**
```bash
composer require --dev barryvdh/laravel-debugbar
```

**2. Ray (Premium Debugging)**
```bash
composer require --dev spatie/ray
```

**3. Xdebug Configuration**
```ini
; php.ini
[xdebug]
xdebug.mode=develop,debug
xdebug.start_with_request=yes
xdebug.client_port=9003
```

### Performance Optimization

**1. Optimize Laravel**
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader
```

**2. Database Optimization**
```sql
-- Add indexes for better performance
CREATE INDEX idx_messages_account_created ON messages(account_id, created_at);
CREATE INDEX idx_campaigns_user_status ON campaigns(user_id, status);
CREATE INDEX idx_contacts_phone ON contacts(phone);
```

**3. Queue Optimization**
```bash
# Use Redis for better queue performance
composer require predis/predis

# Configure Redis in .env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Node.js Development

**1. Development Mode**
```bash
cd node-scripts

# Use nodemon for auto-restart
npm install -g nodemon
nodemon server.js

# Or use npm script
npm run dev
```

**2. Node.js Testing**
```bash
# Install testing framework
npm install --save-dev jest supertest

# Run tests
npm test
```

**3. Node.js Debugging**
```bash
# Debug mode
node --inspect server.js

# Use Chrome DevTools at chrome://inspect
```

## 🚀 Production Deployment

### Server Requirements

**Minimum Production Server:**
- **CPU**: 2 cores (4 cores recommended)
- **RAM**: 4GB (8GB recommended)
- **Storage**: 50GB SSD
- **OS**: Ubuntu 20.04 LTS or CentOS 8
- **PHP**: 8.2 or higher
- **Node.js**: 18.x or higher
- **MySQL**: 8.0 or higher
- **Redis**: 6.x or higher (recommended)

### Step 1: Server Setup

**1. Install Dependencies (Ubuntu):**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd php8.2-json php8.2-bcmath

# Install MySQL
sudo apt install mysql-server
sudo mysql_secure_installation

# Install Redis
sudo apt install redis-server

# Install Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs

# Install Composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

**2. Create Database:**
```sql
CREATE DATABASE blazt_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'blazt_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON blazt_production.* TO 'blazt_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 2: Application Deployment

**1. Clone and Setup Application:**
```bash
# Clone repository
cd /var/www
sudo git clone https://github.com/your-username/blazt.git
sudo chown -R www-data:www-data blazt
cd blazt

# Install dependencies
composer install --optimize-autoloader --no-dev
cd node-scripts && npm install --production
```

**2. Environment Configuration:**
```bash
# Copy production environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Set proper permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

**3. Production Environment (.env):**
```env
APP_NAME="Blazt"
APP_ENV=production
APP_KEY=base64:generated-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blazt_production
DB_USERNAME=blazt_user
DB_PASSWORD=secure_password_here

# Cache & Session (Redis)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# WhatsApp Node.js Service
WHATSAPP_NODE_URL=http://localhost:3001
WHATSAPP_WEBHOOK_SECRET=production-webhook-secret

# AI Providers
OPENAI_API_KEY=your-production-openai-key
ANTHROPIC_API_KEY=your-production-claude-key
GOOGLE_API_KEY=your-production-gemini-key

# Mail (Production SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Payment (Live Stripe)
STRIPE_KEY=pk_live_your-stripe-publishable-key
STRIPE_SECRET=sk_live_your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=whsec_your-live-webhook-secret

# Security
SESSION_SECURE_COOKIE=true
SANCTUM_STATEFUL_DOMAINS=yourdomain.com
```

**4. Database Migration:**
```bash
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder
```

### Step 3: Web Server Configuration

**Nginx Configuration:**
```nginx
# /etc/nginx/sites-available/blazt
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/blazt/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/yourdomain.crt;
    ssl_certificate_key /etc/ssl/private/yourdomain.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # API Rate Limiting
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
}

# Rate limiting zone
http {
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
}
```

**Enable Site:**
```bash
sudo ln -s /etc/nginx/sites-available/blazt /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 4: Process Management

**1. Laravel Queue with Supervisor:**
```ini
# /etc/supervisor/conf.d/blazt-queue.conf
[program:blazt-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/blazt/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/blazt/storage/logs/worker.log
stopwaitsecs=3600
```

**2. WhatsApp Node.js Service with PM2:**
```bash
# Install PM2 globally
sudo npm install -g pm2

# Create PM2 ecosystem file
cat > /var/www/blazt/node-scripts/ecosystem.config.js << 'EOF'
module.exports = {
  apps: [{
    name: 'blazt-whatsapp',
    script: './server.js',
    cwd: '/var/www/blazt/node-scripts',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'production',
      PORT: 3001
    },
    error_file: '/var/www/blazt/storage/logs/whatsapp-error.log',
    out_file: '/var/www/blazt/storage/logs/whatsapp-out.log',
    log_file: '/var/www/blazt/storage/logs/whatsapp.log'
  }]
};
EOF

# Start services
cd /var/www/blazt/node-scripts
pm2 start ecosystem.config.js
pm2 startup
pm2 save
```

**3. Start Services:**
```bash
# Start Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start blazt-queue:*

# Verify services
sudo supervisorctl status
pm2 status
```

### Step 5: Scheduled Tasks

**Add to crontab:**
```bash
sudo crontab -e -u www-data

# Add this line:
* * * * * cd /var/www/blazt && php artisan schedule:run >> /dev/null 2>&1
```

### Step 6: SSL Certificate

**Using Let's Encrypt (Recommended):**
```bash
# Install Certbot
sudo snap install core; sudo snap refresh core
sudo snap install --classic certbot
sudo ln -s /snap/bin/certbot /usr/bin/certbot

# Get certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal (already included in cron)
sudo certbot renew --dry-run
```

### Step 7: Monitoring & Logging

**1. Log Configuration:**
```bash
# Create log rotation
sudo tee /etc/logrotate.d/blazt << 'EOF'
/var/www/blazt/storage/logs/*.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
    postrotate
        systemctl reload php8.2-fpm
    endscript
}
EOF
```

**2. Health Check Script:**
```bash
# Create health check script
sudo tee /usr/local/bin/blazt-health-check << 'EOF'
#!/bin/bash

# Check Laravel application
LARAVEL_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/api/v1/health)

# Check Node.js service
NODE_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:3001/health)

# Check MySQL
mysql -u blazt_user -p'secure_password_here' -e "SELECT 1;" blazt_production > /dev/null 2>&1
MYSQL_STATUS=$?

# Check Redis
redis-cli ping > /dev/null 2>&1
REDIS_STATUS=$?

echo "$(date): Laravel=$LARAVEL_STATUS, Node=$NODE_STATUS, MySQL=$MYSQL_STATUS, Redis=$REDIS_STATUS" >> /var/log/blazt-health.log

# Send alert if any service is down
if [ "$LARAVEL_STATUS" != "200" ] || [ "$NODE_STATUS" != "200" ] || [ "$MYSQL_STATUS" != "0" ] || [ "$REDIS_STATUS" != "0" ]; then
    echo "Service down detected!" | mail -s "Blazt Health Alert" admin@yourdomain.com
fi
EOF

chmod +x /usr/local/bin/blazt-health-check

# Add to cron
(crontab -l 2>/dev/null; echo "*/5 * * * * /usr/local/bin/blazt-health-check") | crontab -
```

### Step 8: Backup Strategy

**1. Database Backup:**
```bash
# Create backup script
sudo tee /usr/local/bin/blazt-backup << 'EOF'
#!/bin/bash

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/blazt"
DB_BACKUP="$BACKUP_DIR/db_backup_$TIMESTAMP.sql"
APP_BACKUP="$BACKUP_DIR/app_backup_$TIMESTAMP.tar.gz"

mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u blazt_user -p'secure_password_here' blazt_production > $DB_BACKUP
gzip $DB_BACKUP

# Application backup (excluding vendor and node_modules)
tar -czf $APP_BACKUP -C /var/www blazt --exclude='vendor' --exclude='node_modules' --exclude='storage/logs' --exclude='.git'

# Upload to S3 (optional)
# aws s3 cp $DB_BACKUP.gz s3://your-backup-bucket/
# aws s3 cp $APP_BACKUP s3://your-backup-bucket/

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup completed: $TIMESTAMP"
EOF

chmod +x /usr/local/bin/blazt-backup

# Schedule daily backups
(crontab -l 2>/dev/null; echo "0 2 * * * /usr/local/bin/blazt-backup") | crontab -
```

### Step 9: Security Hardening

**1. Firewall Configuration:**
```bash
# Install and configure UFW
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

**2. Fail2Ban:**
```bash
# Install Fail2Ban
sudo apt install fail2ban

# Configure for Nginx
sudo tee /etc/fail2ban/jail.local << 'EOF'
[nginx-http-auth]
enabled = true

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
action = iptables-multiport[name=ReqLimit, port="http,https", protocol=tcp]
logpath = /var/log/nginx/error.log
findtime = 600
bantime = 7200
maxretry = 10
EOF

sudo systemctl restart fail2ban
```

### Step 10: Performance Optimization

**1. Laravel Optimization:**
```bash
cd /var/www/blazt

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimize composer autoloader
composer install --optimize-autoloader --no-dev
```

**2. Database Optimization:**
```sql
-- Production database tuning
-- Add to MySQL configuration
[mysqld]
innodb_buffer_pool_size = 2G
innodb_log_file_size = 256M
query_cache_size = 64M
query_cache_type = 1
max_connections = 200
```

**3. Redis Configuration:**
```bash
# /etc/redis/redis.conf
maxmemory 1gb
maxmemory-policy allkeys-lru
save 900 1
save 300 10
save 60 10000
```

### Step 11: Deployment Automation

**Zero-Downtime Deployment Script:**
```bash
#!/bin/bash
# /usr/local/bin/blazt-deploy

set -e

APP_DIR="/var/www/blazt"
RELEASES_DIR="/var/www/releases"
CURRENT_RELEASE=$(date +%Y%m%d_%H%M%S)
RELEASE_DIR="$RELEASES_DIR/$CURRENT_RELEASE"

echo "Starting deployment: $CURRENT_RELEASE"

# Create release directory
mkdir -p $RELEASE_DIR

# Clone latest code
git clone https://github.com/your-username/blazt.git $RELEASE_DIR
cd $RELEASE_DIR

# Install dependencies
composer install --optimize-autoloader --no-dev
cd node-scripts && npm install --production && cd ..

# Copy environment file
cp $APP_DIR/.env $RELEASE_DIR/.env

# Link storage
ln -nfs $APP_DIR/storage $RELEASE_DIR/storage
ln -nfs $APP_DIR/node-scripts/sessions $RELEASE_DIR/node-scripts/sessions

# Cache Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Switch to new release
ln -nfs $RELEASE_DIR $APP_DIR

# Restart services
sudo supervisorctl restart blazt-queue:*
pm2 restart blazt-whatsapp

# Cleanup old releases (keep last 5)
cd $RELEASES_DIR && ls -t | tail -n +6 | xargs rm -rf

echo "Deployment completed: $CURRENT_RELEASE"
```

### Monitoring & Alerts

**Setup monitoring with Uptime Robot or similar:**
- **Laravel Health**: `https://yourdomain.com/api/v1/health`
- **Node.js Health**: `https://yourdomain.com:3001/health`
- **Database Connection**: Custom endpoint
- **Queue Processing**: Monitor queue depth

**Key Metrics to Monitor:**
- Response time < 500ms
- Error rate < 1%
- Queue depth < 100 jobs
- Memory usage < 80%
- Disk usage < 80%
- SSL certificate expiry

## 🤝 Contributing

We welcome contributions to Blazt! Here's how you can help:

### Getting Started

1. **Fork the Repository**
   ```bash
   git clone https://github.com/your-username/blazt.git
   cd blazt
   git remote add upstream https://github.com/original-repo/blazt.git
   ```

2. **Create Feature Branch**
   ```bash
   git checkout -b feature/amazing-new-feature
   ```

3. **Make Your Changes**
   - Follow PSR-12 coding standards
   - Write tests for new features
   - Update documentation
   - Add type hints where possible

4. **Test Your Changes**
   ```bash
   php artisan test
   ./vendor/bin/pint --test
   ./vendor/bin/phpstan analyse
   ```

5. **Submit Pull Request**
   - Clear description of changes
   - Link to related issues
   - Include screenshots for UI changes

### Development Guidelines

**Code Style:**
- Follow PSR-12 standards
- Use meaningful variable/method names
- Add docblocks for public methods
- Keep methods small and focused

**Testing:**
- Write tests for all new features
- Maintain minimum 80% code coverage
- Use factories for test data
- Mock external services

**Documentation:**
- Update README for new features
- Add inline code comments
- Update API documentation
- Include usage examples

### Areas for Contribution

**🚀 High Priority:**
- Additional AI providers integration
- Advanced analytics dashboards
- Mobile app (React Native/Flutter)
- Advanced automation workflows
- Performance optimizations

**📱 UI/UX Improvements:**
- Modern dashboard design
- Real-time notifications
- Mobile-responsive interface
- Dark mode support
- Accessibility improvements

**🔧 Technical Enhancements:**
- Docker containerization
- Kubernetes deployment manifests
- Advanced caching strategies
- Message encryption
- Audit logging system

**📊 Analytics & Reporting:**
- Advanced reporting engine
- Custom chart types
- Export to multiple formats
- Scheduled report delivery
- Predictive analytics

**🤖 AI & Automation:**
- More AI provider integrations
- Custom model training
- Conversation flow builder
- Smart response suggestions
- Sentiment analysis

### Bug Reports

**Before Reporting:**
- Check existing issues
- Verify on latest version
- Test with minimal reproduction

**Include in Report:**
- Laravel/PHP version
- Browser (if applicable)
- Steps to reproduce
- Expected vs actual behavior
- Error logs/screenshots

### Feature Requests

**Proposal Template:**
- **Problem**: What problem does this solve?
- **Solution**: Proposed implementation
- **Alternatives**: Other approaches considered
- **Impact**: Who benefits from this feature?

### Code Review Process

1. **Automated Checks**: All PRs run automated tests
2. **Peer Review**: At least one approval required
3. **Maintainer Review**: Final review by maintainers
4. **Merge**: Squash and merge after approval

## 📄 License

Blazt is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

```
MIT License

Copyright (c) 2024 Blazt Contributors

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

## 🆘 Support & Community

### Documentation
- **Architecture Guide**: `/docs/architecture.md`
- **Database Schema**: `/docs/database-schema.md`
- **API Reference**: `/docs/api.md`
- **Deployment Guide**: `/docs/deployment.md`

### Community Support
- **GitHub Discussions**: General questions and ideas
- **GitHub Issues**: Bug reports and feature requests
- **Discord Server**: Real-time community chat
- **Telegram Group**: Indonesian developer community

### Professional Support
- **Priority Support**: Email support with 24h response time
- **Custom Development**: Tailored features for your business
- **Training & Consultation**: On-site or remote training
- **White-label Solutions**: Custom branding and deployment

Contact: [support@blazt.app](mailto:support@blazt.app)

### Acknowledgments

**Built With:**
- [Laravel](https://laravel.com) - The PHP Framework
- [Baileys](https://github.com/WhiskeySockets/Baileys) - WhatsApp Web API
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework

**Special Thanks:**
- Laravel community for the amazing framework
- Baileys contributors for WhatsApp integration
- All beta testers and early adopters
- Indonesian SaaS developer community

---

<div align="center">
  <h3>🚀 Start Building Your WhatsApp SaaS Today!</h3>
  <p>
    <a href="#-installation">Get Started</a> •
    <a href="#-api-documentation">API Docs</a> •
    <a href="#-usage-examples">Examples</a> •
    <a href="https://github.com/your-username/blazt/discussions">Community</a>
  </p>
  
  <p><strong>Made with ❤️ for Indonesian SaaS developers</strong></p>
  
  <p>
    <img src="https://img.shields.io/github/stars/your-username/blazt?style=social" alt="GitHub stars">
    <img src="https://img.shields.io/github/forks/your-username/blazt?style=social" alt="GitHub forks">
    <img src="https://img.shields.io/github/watchers/your-username/blazt?style=social" alt="GitHub watchers">
  </p>
</div>
