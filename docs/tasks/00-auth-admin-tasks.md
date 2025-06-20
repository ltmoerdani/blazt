# Authentication & Admin System - Task Breakdown

## **OVERVIEW**

Implementasi sistem authentication lengkap untuk WhatsApp SaaS dengan pemisahan admin dan user dashboard menggunakan **Laravel 12 + Livewire** tanpa Filament.

**Current Status Analysis:**
- ✅ Basic Laravel Authentication (web guard dengan Sanctum)
- ✅ User Dashboard (`/dashboard`) dengan Livewire
- ✅ Spatie Permission package installed
- ❌ **No Filament** - akan build custom admin dengan Livewire
- ❌ No separate admin authentication system
- ❌ No role-based access control implemented 
- ✅ Spatie Permission Package
- ✅ Laravel Sanctum for API authentication
- ❌ **No Filament** - Need custom admin built with Livewire
- ⚠️ **Gap**: Multi-level authentication system tidak ada
- ⚠️ **Gap**: Admin permission management belum implemented 
- ⚠️ **Gap**: User role differentiation tidak clear

---

## **AUTHENTICATION ARCHITECTURE**

### **Current Setup (Laravel 12 + Livewire):**
```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Public Site   │───▶│   Auth System    │───▶│  User Dashboard │
│   (welcome.php) │    │   (Single Guard)  │    │   (Livewire)   │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │                          │
                              ▼                          ▼
                    ┌──────────────────┐       ┌─────────────────┐
                    │     Guards       │       │     Roles       │
                    │                  │       │                 │
                    │ • web (session)  │       │ • (not setup)   │
                    │ • sanctum (api)  │       │ • all users     │
                    │                  │       │   same access   │
                    └──────────────────┘       └─────────────────┘
```

### **Target Architecture (Multi-Guard + Livewire Admin):**
```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Public Site   │───▶│  Multi-Guard     │───▶│  Role-Based     │
│                 │    │  Auth System     │    │  Dashboard      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │                          │
                              ▼                          ▼
                    ┌──────────────────┐       ┌─────────────────┐
                    │     Guards       │       │   Livewire      │
                    │                  │       │   Components    │
                    │ • web (user)     │       │                 │
                    │ • admin          │       │ • User Dashboard│
                    │ • sanctum (api)  │       │ • Admin Panel   │
                    │                  │       │ • Analytics     │
                    └──────────────────┘       └─────────────────┘
                              │                          │
                              ▼                          ▼
                    ┌──────────────────┐       ┌─────────────────┐
                    │   Route Groups   │       │   Permissions   │
                    │                  │       │                 │
                    │ • /login         │       │ • manage_users  │
                    │ • /admin/login   │       │ • view_analytics│
                    │ • /dashboard     │       │ • send_campaigns│
                    │ • /admin/panel   │       │ • manage_system │
                    └──────────────────┘       └─────────────────┘
```

---

# **LEVEL 1: AUTH FOUNDATION DENGAN LIVEWIRE**

## **Task 1.1: Multi-Guard Authentication Setup (No Filament)**

### **Description** 
Membangun sistem multi-guard authentication dari scratch menggunakan Laravel 12 + Livewire untuk memisahkan admin dan user authentication, menggantikan kebutuhan Filament.

### **Sub-tasks**

#### **1.1.1: Create Admin Guard Configuration**
**Files to create/modify:**
- `config/auth.php` - Add admin guard
- `app/Http/Middleware/AuthenticateAdmin.php` (create)
- `routes/admin.php` (create)

**Implementation Details:**
```php
// config/auth.php - Update guards untuk multi-guard
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session', 
        'provider' => 'users', // Same table, different middleware check
    ],
],
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Domain\User\Models\User::class,
    ],
    'admins' => [
        'driver' => 'eloquent',
        'model' => App\Domain\User\Models\User::class,
    ],
],
```

#### **1.1.2: Create User Role Seeder**
**File:** `database/seeders/UserRoleSeeder.php`

**Implementation:**
```php
// Create roles: super_admin, admin, user
// Create permissions: manage_users, view_analytics, send_campaigns, etc.
// Assign roles to default admin user
```

#### **1.1.3: Update Authentication Controllers**
**Files to modify:**
- `app/Http/Controllers/Auth/LoginController.php` (enhance)
- `app/Http/Controllers/Admin/AuthController.php` (create)

### **Acceptance Criteria**
- [ ] Admin dapat login melalui `/admin` dengan guard terpisah
- [ ] User dapat login melalui `/login` dengan guard terpisah
- [ ] Session isolation antara admin dan user
- [ ] Proper logout functionality untuk kedua guards
- [ ] Role-based access control berfungsi

### **Expected Output**
```
Visual Result:
- Admin login page di /admin dengan styling Filament
- User login page di /login dengan styling dashboard
- Proper role indicators di UI

Data Result:
- 3 roles di database: super_admin, admin, user
- 10+ permissions untuk granular access control
- Default admin user dengan super_admin role
```

---

## **Task 1.2: Enhanced User Model & Permission Integration**

### **Description**
Mengintegrasikan Spatie Permission dengan User model dan menambahkan user type differentiation.

### **Sub-tasks**

#### **1.2.1: Update User Model with Permissions**
**File:** `app/Domain/User/Models/User.php`

**Implementation:**
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    
    // Add user_type field
    protected $fillable = [
        'user_type', // admin, user
        // ... existing fields
    ];
    
    public function isAdmin(): bool
    {
        return in_array($this->user_type, ['admin', 'super_admin']);
    }
    
    public function isSuperAdmin(): bool
    {
        return $this->user_type === 'super_admin';
    }
}
```

#### **1.2.2: Create Migration for User Type**
**File:** `database/migrations/add_user_type_to_users_table.php`

#### **1.2.3: Update User Registration Process**
**Files to modify:**
- `app/Domain/User/Services/UserService.php`
- Registration controllers

### **Acceptance Criteria**
- [ ] User model memiliki field user_type
- [ ] Permission checks berfungsi dengan roles
- [ ] User registration otomatis assign role 'user'
- [ ] Admin creation assign role 'admin' atau 'super_admin'
- [ ] Helper methods isAdmin(), isSuperAdmin() berfungsi

### **Expected Output**
```
Database Result:
- users table memiliki kolom user_type
- Default users memiliki role 'user'
- Admin accounts memiliki role 'admin' atau 'super_admin'

Code Result:
- Permission checks di controllers menggunakan roles
- Middleware authentication berdasarkan user type
```

---

# **LEVEL 2: ADMIN PANEL ENHANCEMENT**

## **Task 2.1: Filament Admin Panel Customization**

### **Description**
Mengenhance Filament admin panel dengan custom resources, widgets, dan permission-based access.

### **Sub-tasks**

#### **2.1.1: Create Admin User Management Resource**
**File:** `app/Filament/Resources/AdminUserResource.php`

**Implementation:**
- CRUD untuk admin users
- Role assignment interface
- Permission management
- User activity logs

#### **2.1.2: Enhance Existing Resources with Permissions**
**Files to modify:**
- `app/Filament/Resources/ContactResource.php`
- `app/Filament/Resources/WhatsAppAccountResource.php`
- `app/Filament/Resources/CampaignResource.php` (create)

#### **2.1.3: Create Admin Dashboard Widgets**
**Files to create:**
- `app/Filament/Widgets/UserStatsWidget.php`
- `app/Filament/Widgets/CampaignStatsWidget.php`
- `app/Filament/Widgets/RevenueWidget.php`
- `app/Filament/Widgets/SystemHealthWidget.php`

### **Acceptance Criteria**
- [ ] Admin dapat manage users melalui Filament interface
- [ ] Role dan permission assignment berfungsi
- [ ] Dashboard menampilkan real-time statistics
- [ ] Access control berdasarkan admin permissions
- [ ] Audit logs untuk admin actions

### **Expected Output**
```
Visual Result:
- Filament admin panel dengan custom navigation
- User management interface dengan role assignment
- Dashboard widgets menampilkan key metrics
- Permission-based menu visibility

Functional Result:
- CRUD operations untuk semua main entities
- Role-based access control di admin panel
- Activity logging untuk admin actions
```

---

## **Task 2.2: User Dashboard Permission Gates**

### **Description**
Implementasi permission gates di user dashboard untuk feature access control.

### **Sub-tasks**

#### **2.2.1: Create Permission Gates**
**File:** `app/Providers/AuthServiceProvider.php`

**Implementation:**
```php
Gate::define('send-campaigns', function ($user) {
    return $user->hasPermissionTo('send_campaigns');
});

Gate::define('manage-contacts', function ($user) {
    return $user->hasPermissionTo('manage_contacts');
});
```

#### **2.2.2: Update Controllers with Gates**
**Files to modify:**
- `app/Http/Controllers/Dashboard/CampaignController.php`
- `app/Http/Controllers/Dashboard/ContactController.php`
- `app/Http/Controllers/Dashboard/WhatsAppAccountController.php`

#### **2.2.3: Update Livewire Components with Permissions**
**Files to modify:**
- `app/Livewire/CampaignsIndex.php`
- `app/Livewire/ContactsManager.php`
- `app/Livewire/WhatsAppAccountsIndex.php`

### **Acceptance Criteria**
- [ ] Permission gates berfungsi di controllers
- [ ] UI elements tersembunyi berdasarkan permissions
- [ ] Error messages appropriate untuk unauthorized access
- [ ] Livewire components respect permissions
- [ ] API endpoints memiliki permission checks

### **Expected Output**
```
Functional Result:
- User hanya dapat mengakses features sesuai permissions
- UI elements (buttons, menus) tersembunyi untuk unauthorized users
- Proper error handling untuk permission violations

Security Result:
- All sensitive operations protected by permissions
- Proper authorization checks di backend
```

---

# **LEVEL 3: ADVANCED AUTHENTICATION FEATURES**

## **Task 3.1: Multi-Tenant User Isolation**

### **Description**
Implementasi tenant isolation untuk memastikan users hanya dapat mengakses data mereka sendiri.

### **Sub-tasks**

#### **3.1.1: Create Tenant Middleware**
**File:** `app/Http/Middleware/EnsureTenantAccess.php`

#### **3.1.2: Update Models with User Scoping**
**Files to modify:**
- Add global scopes ke Contact, Campaign, WhatsAppAccount models
- Update relationships untuk proper isolation

#### **3.1.3: API Authentication Enhancement**
**Files to modify:**
- `routes/api.php` - Add tenant middleware
- API controllers untuk proper user scoping

### **Acceptance Criteria**
- [ ] Users tidak dapat mengakses data user lain
- [ ] Global scopes applied ke semua tenant-specific models
- [ ] API endpoints memiliki proper tenant isolation
- [ ] Admin dapat mengakses semua tenant data
- [ ] Proper error handling untuk cross-tenant access attempts

---

## **Task 3.2: Two-Factor Authentication (Optional)**

### **Description**
Implementasi 2FA untuk enhanced security di admin panel.

### **Sub-tasks**

#### **3.2.1: Install and Configure 2FA Package**
**Package:** `pragmarx/google2fa-laravel`

#### **3.2.2: Create 2FA Setup Interface**
**Files to create:**
- 2FA setup page in admin panel
- QR code generation
- Backup codes

#### **3.2.3: Integrate 2FA with Login Process**
**Files to modify:**
- Admin login flow
- 2FA verification middleware

### **Acceptance Criteria**
- [ ] Admin dapat enable/disable 2FA
- [ ] QR code generation untuk Google Authenticator
- [ ] Backup codes generation
- [ ] 2FA verification di login process
- [ ] Recovery options jika device hilang

---

# **LEVEL 4: MONITORING & SECURITY**

## **Task 4.1: User Activity Logging**

### **Description**
Implementasi comprehensive logging untuk user activities dan admin actions.

### **Sub-tasks**

#### **4.1.1: Create Activity Log System**
**Package:** `spatie/laravel-activitylog`

#### **4.1.2: Log Critical User Actions**
- Campaign executions
- Contact imports
- WhatsApp account connections
- Settings changes

#### **4.1.3: Admin Activity Dashboard**
- Real-time activity feeds
- Filterable activity logs
- Export functionality

### **Acceptance Criteria**
- [ ] All critical actions logged with context
- [ ] Admin dapat view activity logs
- [ ] Logs include user, action, timestamp, IP address
- [ ] Search dan filter functionality
- [ ] Export logs untuk compliance

---

## **Task 4.2: Security Enhancements**

### **Description**
Implementasi additional security measures untuk production environment.

### **Sub-tasks**

#### **4.2.1: Rate Limiting Enhancement**
- Login attempt limiting
- API rate limiting per user
- Campaign execution throttling

#### **4.2.2: Session Management**
- Session timeout configuration
- Concurrent session limits
- Force logout functionality

#### **4.2.3: Security Headers**
- CSP (Content Security Policy)
- HSTS headers
- X-Frame-Options

### **Acceptance Criteria**
- [ ] Rate limiting active untuk login attempts
- [ ] Session management properly configured
- [ ] Security headers implemented
- [ ] HTTPS enforcement
- [ ] Password policy enforcement

---

# **IMPLEMENTATION ROADMAP**

## **Phase 1 (Week 1): Foundation**
- Task 1.1: Multi-Guard Authentication Setup
- Task 1.2: Enhanced User Model & Permission Integration

## **Phase 2 (Week 2): Admin Panel**
- Task 2.1: Filament Admin Panel Customization
- Task 2.2: User Dashboard Permission Gates

## **Phase 3 (Week 3): Advanced Features**
- Task 3.1: Multi-Tenant User Isolation
- Task 3.2: Two-Factor Authentication (Optional)

## **Phase 4 (Week 4): Security & Monitoring**
- Task 4.1: User Activity Logging
- Task 4.2: Security Enhancements

---

# **SUCCESS METRICS**

## **Security Metrics:**
- [ ] Zero cross-tenant data access incidents
- [ ] All admin actions properly logged
- [ ] Permission system 100% coverage
- [ ] Rate limiting prevents abuse

## **Functionality Metrics:**
- [ ] Admin panel response time < 2 seconds
- [ ] User dashboard load time < 3 seconds
- [ ] Authentication success rate > 99%
- [ ] Permission checks latency < 100ms

## **User Experience Metrics:**
- [ ] Clear role-based UI differentiation
- [ ] Intuitive admin interface
- [ ] Proper error messages for permission violations
- [ ] Smooth login/logout experience

---

**Estimasi Total Development Time: 3-4 weeks**  
**Team Required: 2-3 developers**  
**Priority: Critical (Foundation untuk seluruh system)**
