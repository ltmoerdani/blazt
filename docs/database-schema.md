## **Konsep Optimasi Database**

### **Prinsip Dasar Optimasi:**
- **Normalisasi yang Tepat**: Balance antara normalisasi dan denormalisasi untuk performa
- **Indexing Strategy**: Index yang strategis untuk query patterns
- **Partitioning**: Pembagian data untuk menghindari bottleneck
- **Read Optimization**: Optimasi untuk query yang sering dijalankan
- **Write Optimization**: Batch operations dan async processing

---

## **Struktur Database Utama**

### **1. Core User Tables**

#### **users (Tabel Master User)**
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    uuid CHAR(36) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    subscription_plan ENUM('trial', 'starter', 'pro', 'enterprise') DEFAULT 'trial',
    subscription_status ENUM('active', 'expired', 'suspended') DEFAULT 'active',
    subscription_expires_at TIMESTAMP NULL,
    timezone VARCHAR(50) DEFAULT 'UTC',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_subscription_status (subscription_status),
    INDEX idx_subscription_plan (subscription_plan),
    INDEX idx_created_at (created_at)
);
```

#### **user_limits (Limit per User)**
```sql
CREATE TABLE user_limits (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    messages_daily_limit INT DEFAULT 1000,
    messages_monthly_limit INT DEFAULT 30000,
    ai_requests_daily_limit INT DEFAULT 100,
    whatsapp_accounts_limit INT DEFAULT 1,
    contacts_limit INT DEFAULT 10000,
    campaigns_limit INT DEFAULT 50,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_limit (user_id)
);
```

### **2. WhatsApp Management Tables**

#### **whatsapp_accounts (Akun WhatsApp per User)**
```sql
CREATE TABLE whatsapp_accounts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) UNIQUE NOT NULL,
    display_name VARCHAR(255),
    status ENUM('disconnected', 'connecting', 'connected', 'banned') DEFAULT 'disconnected',
    session_data JSON,
    qr_code_path VARCHAR(255),
    last_connected_at TIMESTAMP NULL,
    health_check_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_phone_status (phone_number, status),
    INDEX idx_health_check (health_check_at)
);
```

### **3. Contact Management Tables**

#### **contacts (Optimized untuk Scale)**
```sql
CREATE TABLE contacts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    name VARCHAR(255),
    group_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_message_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_contact (user_id, phone_number),
    INDEX idx_user_active (user_id, is_active),
    INDEX idx_group_active (group_id, is_active),
    INDEX idx_last_message (last_message_at)
);
```

#### **contact_groups (Grouping untuk Segmentasi)**
```sql
CREATE TABLE contact_groups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    contact_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);
```

### **4. Campaign Tables**

#### **campaigns (Master Campaign)**
```sql
CREATE TABLE campaigns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    whatsapp_account_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    template_content TEXT NOT NULL,
    target_type ENUM('all', 'group', 'custom') DEFAULT 'all',
    target_group_id BIGINT UNSIGNED NULL,
    status ENUM('draft', 'scheduled', 'running', 'completed', 'failed') DEFAULT 'draft',
    scheduled_at TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    total_contacts INT DEFAULT 0,
    messages_sent INT DEFAULT 0,
    messages_delivered INT DEFAULT 0,
    messages_failed INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (whatsapp_account_id) REFERENCES whatsapp_accounts(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_scheduled (scheduled_at),
    INDEX idx_status_updated (status, updated_at)
);
```

### **5. Message Tables (Partitioned untuk Performance)**

#### **messages (Partitioned by Month)**
```sql
CREATE TABLE messages (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    campaign_id BIGINT UNSIGNED NULL,
    whatsapp_account_id BIGINT UNSIGNED NOT NULL,
    contact_id BIGINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    message_content TEXT NOT NULL,
    media_path VARCHAR(255) NULL,
    status ENUM('queued', 'sending', 'sent', 'delivered', 'read', 'failed') DEFAULT 'queued',
    error_message TEXT NULL,
    sent_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_status_created (user_id, status, created_at),
    INDEX idx_campaign_status (campaign_id, status),
    INDEX idx_contact_created (contact_id, created_at),
    INDEX idx_phone_created (phone_number, created_at)
) PARTITION BY RANGE (YEAR(created_at) * 100 + MONTH(created_at)) (
    PARTITION p202401 VALUES LESS THAN (202402),
    PARTITION p202402 VALUES LESS THAN (202403),
    PARTITION p202403 VALUES LESS THAN (202404),
    -- Auto-partitioning akan menambah partition baru setiap bulan
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### **6. Analytics Tables (Denormalized untuk Speed)**

#### **daily_analytics (Pre-aggregated Data)**
```sql
CREATE TABLE daily_analytics (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    date DATE NOT NULL,
    messages_sent INT DEFAULT 0,
    messages_delivered INT DEFAULT 0,
    messages_failed INT DEFAULT 0,
    campaigns_created INT DEFAULT 0,
    campaigns_completed INT DEFAULT 0,
    ai_requests_used INT DEFAULT 0,
    unique_contacts_messaged INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_date (user_id, date),
    INDEX idx_date (date),
    INDEX idx_user_date_range (user_id, date)
);
```

#### **campaign_analytics (Real-time Campaign Stats)**
```sql
CREATE TABLE campaign_analytics (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    campaign_id BIGINT UNSIGNED NOT NULL,
    total_contacts INT DEFAULT 0,
    messages_queued INT DEFAULT 0,
    messages_sent INT DEFAULT 0,
    messages_delivered INT DEFAULT 0,
    messages_read INT DEFAULT 0,
    messages_failed INT DEFAULT 0,
    delivery_rate DECIMAL(5,2) DEFAULT 0,
    read_rate DECIMAL(5,2) DEFAULT 0,
    avg_delivery_time INT DEFAULT 0, -- seconds
    last_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    UNIQUE KEY unique_campaign (campaign_id)
);
```

### **7. Usage Tracking (untuk Billing)**

#### **usage_logs (Partitioned by Month)**
```sql
CREATE TABLE usage_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    usage_type ENUM('message', 'ai_request', 'api_call') NOT NULL,
    quantity INT DEFAULT 1,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_type_created (user_id, usage_type, created_at),
    INDEX idx_created_at (created_at)
) PARTITION BY RANGE (YEAR(created_at) * 100 + MONTH(created_at)) (
    PARTITION p202401 VALUES LESS THAN (202402),
    PARTITION p202402 VALUES LESS THAN (202403),
    -- Auto-partitioning
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

---

## **Strategi Optimasi untuk 25K Users, 500K Transaksi**

### **1. Index Strategy**

#### **Composite Indexes untuk Query Patterns:**
```sql
-- Query: Cari campaign user dengan status tertentu
ALTER TABLE campaigns ADD INDEX idx_user_status_updated (user_id, status, updated_at);

-- Query: Tracking message delivery per campaign
ALTER TABLE messages ADD INDEX idx_campaign_status_created (campaign_id, status, created_at);

-- Query: Analytics per user per periode
ALTER TABLE daily_analytics ADD INDEX idx_user_date_range (user_id, date);

-- Query: Contact aktif per user
ALTER TABLE contacts ADD INDEX idx_user_active_updated (user_id, is_active, updated_at);
```

### **2. Partitioning Strategy**

#### **Time-based Partitioning:**
- **messages**: Partisi per bulan untuk menghindari table yang terlalu besar
- **usage_logs**: Partisi per bulan untuk billing calculation yang cepat
- **Auto-partition**: Script untuk membuat partisi baru secara otomatis

#### **Keuntungan Partitioning:**
- Query hanya scan partisi yang relevan
- Maintenance (backup, cleanup) per partisi
- Better concurrent access
- Archive old data dengan mudah

### **3. Denormalization untuk Performance**

#### **Pre-calculated Fields:**
- **campaigns.messages_sent**: Update real-time dari triggers
- **contact_groups.contact_count**: Maintain count untuk avoid COUNT() queries
- **campaign_analytics**: Denormalized analytics untuk dashboard cepat

#### **Redundant Data yang Strategis:**
- **messages.phone_number**: Duplicate dari contacts untuk query tanpa JOIN
- **daily_analytics**: Pre-aggregated data untuk dashboard

### **4. Connection Pooling & Configuration**

#### **MySQL Configuration untuk High Load:**
```sql
-- Connection settings
max_connections = 500
max_user_connections = 50

-- Buffer settings
innodb_buffer_pool_size = 70% of RAM
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2

-- Query cache (untuk read-heavy workload)
query_cache_type = 1
query_cache_size = 256M

-- Connection timeout
wait_timeout = 300
interactive_timeout = 300
```

### **5. Read/Write Splitting**

#### **Master-Slave Replication:**
- **Master**: Semua write operations (campaigns, messages, contacts)
- **Slave**: Read operations (analytics, dashboard, reports)
- **Laravel Configuration**: Automatic read/write splitting

#### **Query Distribution:**
- **Dashboard queries**: Slave database
- **Campaign execution**: Master database
- **Analytics**: Slave database dengan slight delay acceptable
- **Real-time operations**: Master database

---

## **Caching Strategy**

### **1. Redis untuk Hot Data**

#### **Cache Structure:**
```
user:123:limits -> User limits data (TTL: 1 hour)
campaign:456:stats -> Real-time campaign statistics (TTL: 5 minutes)
user:123:daily_usage:2024-01-15 -> Daily usage count (TTL: 24 hours)
whatsapp:account:789:status -> WhatsApp connection status (TTL: 1 minute)
```

#### **Cache Patterns:**
- **Write-through**: Update database dan cache bersamaan
- **Cache-aside**: Load dari cache, fallback ke database
- **Write-behind**: Update cache dulu, database async

### **2. Application-level Caching**

#### **Laravel Cache untuk Static Data:**
- Subscription plans dan limits
- User permissions dan roles
- Template variables dan configurations
- Contact groups dan metadata

---

## **Database Monitoring & Maintenance**

### **1. Performance Monitoring**

#### **Key Metrics to Track:**
- Query response time per table
- Index usage statistics
- Lock wait time dan deadlocks
- Connection pool utilization
- Partition scan efficiency

#### **Automated Monitoring:**
```sql
-- Slow query monitoring
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 1;

-- Performance schema monitoring
SELECT * FROM performance_schema.events_statements_summary_by_digest 
WHERE avg_timer_wait > 1000000000;
```

### **2. Maintenance Strategy**

#### **Daily Maintenance:**
- Partition maintenance (create new, drop old)
- Index statistics update
- Cache warming untuk hot data
- Backup verification

#### **Weekly Maintenance:**
- Table optimization dan defragmentation
- Archive old partitions
- Performance analysis dan tuning
- Capacity planning review

### **3. Backup Strategy**

#### **Multi-layer Backup:**
- **Real-time**: Binary log replication ke slave
- **Daily**: Full database dump dengan compression
- **Weekly**: Incremental backup dengan point-in-time recovery
- **Monthly**: Archive ke cold storage

---

## **Estimasi Resource untuk 25K Users, 500K Transaksi**

### **Storage Estimation:**
- **Users data**: ~5MB
- **Contacts (avg 200/user)**: ~5GB
- **Messages (monthly)**: ~50GB per month
- **Analytics**: ~2GB per month
- **Total first year**: ~650GB

### **Memory Requirements:**
- **InnoDB Buffer Pool**: 16-32GB
- **Connection overhead**: 2-4GB
- **OS dan applications**: 4-8GB
- **Total RAM**: 32-64GB recommended

### **CPU Requirements:**
- **Database server**: 8-16 cores
- **Concurrent connections**: 200-500 active
- **Query processing**: Medium to high CPU load
- **Recommendation**: Modern multi-core processor

### **Network:**
- **Database queries**: ~10-50Mbps
- **Replication**: ~5-20Mbps
- **Backup**: ~100-500Mbps (burst)

Dengan desain database ini, aplikasi bisa handle 25K users dan 500K transaksi dengan performance yang optimal dan resource yang efficient. 