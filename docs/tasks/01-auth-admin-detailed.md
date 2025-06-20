# Authentication & Admin System Tasks - Laravel 12 + Livewire

## **OVERVIEW**

Task breakdown untuk implementasi sistem authentication lengkap pada WhatsApp SaaS menggunakan **Laravel 12 + Livewire** tanpa Filament. Sistem akan memisahkan akses admin dan user dengan role-based permissions.

**Current Analysis:**
- ✅ Laravel 12 dengan Livewire 3.x
- ✅ Spatie Permission package installed
- ✅ Laravel Sanctum untuk API auth
- ❌ **NO FILAMENT** - Build custom admin dengan pure Livewire
- ❌ No multi-guard authentication
- ❌ No role-based access implemented
- ❌ No admin interface built

---

## **AUTHENTICATION STRATEGY**

### **Current State:**
```
/login → Dashboard (semua user sama)
/register → Dashboard (no role differentiation)
```

### **Target Implementation:**
```
Public:
├── /login → User Login
├── /register → User Registration
└── /admin/login → Admin Login

Protected Routes:
├── /dashboard → User Dashboard (role: user)
├── /admin/dashboard → Admin Dashboard (role: admin)
└── /api/* → API Routes (Sanctum auth)
```

---

# **LEVEL 1: FOUNDATION (CRITICAL)**

## **Task 1.1: Basic Authentication Enhancement**
**Priority**: HIGHEST | **Complexity**: BASIC | **Time**: 4-6 hours

### **1.1.1: Laravel Breeze Integration (Optional)**
**Target**: Decide antara Breeze atau custom auth

**Option A - Laravel Breeze:**
```bash
composer require laravel/breeze
php artisan breeze:install blade
npm install && npm run build
```

**Option B - Custom Auth (Recommended untuk kontrol penuh):**
- Build login/register forms dengan Livewire
- Custom authentication logic
- Better integration dengan existing code

**Expected Output:**
- [ ] Login form di `/login`
- [ ] Register form di `/register` 
- [ ] Password reset functionality
- [ ] Clean logout functionality
- [ ] Session management

**Files:**
- `app/Livewire/Auth/Login.php`
- `app/Livewire/Auth/Register.php`
- `resources/views/livewire/auth/login.blade.php`
- `resources/views/livewire/auth/register.blade.php`

---

### **1.1.2: Multi-Guard Configuration**
**Target**: Setup separate authentication guards

**Implementation:**
```php
// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => null,
    ],
],
```

**Expected Output:**
- [ ] Admin login di `/admin/login`
- [ ] User login di `/login`
- [ ] Separate session management
- [ ] Guard-specific redirects

**Files:**
- `config/auth.php` (modify)
- `app/Http/Middleware/AuthenticateAdmin.php` (create)
- `app/Http/Controllers/Auth/AdminLoginController.php` (create)
- `routes/admin.php` (create)

---

## **Task 1.2: Role & Permission Setup (Spatie)**
**Priority**: HIGH | **Complexity**: INTERMEDIATE | **Time**: 6-8 hours

### **1.2.1: Roles & Permissions Definition**
**Target**: Setup role hierarchy dengan Spatie Permission

**Roles:**
- `super_admin` - Full system access
- `admin` - Admin dashboard access
- `user` - Regular user dashboard only

**Permissions:**
```php
// User Management
'view_users', 'create_users', 'edit_users', 'delete_users',

// Dashboard & Analytics  
'view_admin_dashboard', 'view_user_dashboard', 'view_analytics',

// WhatsApp Management
'manage_whatsapp_accounts', 'view_whatsapp_accounts',

// Campaign Management
'create_campaigns', 'edit_campaigns', 'delete_campaigns', 'execute_campaigns',

// Contact Management
'manage_contacts', 'import_contacts', 'export_contacts',

// System Settings
'manage_system_settings', 'view_system_logs',
```

**Expected Output:**
- [ ] Roles defined dengan proper hierarchy
- [ ] Permissions assigned to roles
- [ ] Default role assignment saat register
- [ ] Migration untuk roles dan permissions

**Files:**
- `database/seeders/RolePermissionSeeder.php`
- `app/Models/User.php` (add HasRoles trait)
- Migration untuk default data

---

### **1.2.2: Middleware & Route Protection**
**Target**: Protect routes berdasarkan roles dan permissions

**Implementation:**
```php
// routes/web.php
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('permission:view_user_dashboard');
    // ... other user routes
});

// routes/admin.php  
Route::middleware(['auth:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('permission:view_admin_dashboard');
    // ... other admin routes
});
```

**Expected Output:**
- [ ] Route protection dengan middleware
- [ ] Role-based access control
- [ ] Automatic permission checking
- [ ] Proper error handling untuk unauthorized access

**Files:**
- `app/Http/Middleware/CheckRole.php`
- `app/Http/Middleware/CheckPermission.php`
- `routes/web.php` (update)
- `routes/admin.php` (create)

---

# **LEVEL 2: ADMIN DASHBOARD (CUSTOM LIVEWIRE)**

## **Task 2.1: Admin Layout & Navigation**
**Priority**: HIGH | **Complexity**: INTERMEDIATE | **Time**: 8-10 hours

### **2.1.1: Admin Layout Structure**
**Target**: Build admin layout dari scratch dengan Livewire

**Layout Structure:**
```
resources/views/admin/
├── layouts/
│   ├── app.blade.php (main admin layout)
│   └── partials/
│       ├── sidebar.blade.php
│       ├── header.blade.php
│       └── breadcrumb.blade.php
├── dashboard/
│   └── index.blade.php
└── components/
    ├── stats-card.blade.php
    └── data-table.blade.php
```

**Expected Output:**
- [ ] Responsive admin layout
- [ ] Collapsible sidebar navigation
- [ ] Header dengan user dropdown
- [ ] Breadcrumb navigation
- [ ] Dark/light theme toggle

**Files:**
- `resources/views/admin/layouts/app.blade.php`
- `resources/views/admin/layouts/partials/sidebar.blade.php`
- `app/Livewire/Admin/Components/ThemeToggle.php`

---

### **2.1.2: Admin Dashboard Homepage**
**Target**: Dashboard dengan overview statistics

**Dashboard Components:**
- User count (total, active, new this month)
- Campaign statistics (total, active, completed)
- Message statistics (sent today, this month)
- WhatsApp account status
- Recent activity feed

**Expected Output:**
- [ ] Real-time stats cards
- [ ] Charts untuk trends
- [ ] Recent activity table
- [ ] Quick action buttons

**Files:**
- `app/Livewire/Admin/Dashboard.php`
- `app/Livewire/Admin/Components/StatsOverview.php`
- `app/Services/AdminAnalyticsService.php`

---

## **Task 2.2: User Management Interface**
**Priority**: HIGH | **Complexity**: INTERMEDIATE | **Time**: 8-10 hours

### **2.2.1: User Listing & Search**
**Target**: Advanced user management dengan Livewire

**Features:**
- Searchable user table
- Filter by role, status, registration date
- Sortable columns
- Pagination
- Bulk actions (activate, deactivate, delete)

**Expected Output:**
- [ ] Responsive user table
- [ ] Real-time search
- [ ] Advanced filtering
- [ ] Bulk action implementation
- [ ] Export functionality

**Files:**
- `app/Livewire/Admin/UserManagement.php`
- `resources/views/livewire/admin/user-management.blade.php`
- `app/Livewire/Admin/Components/UserTable.php`

---

### **2.2.2: User Detail & Edit**
**Target**: User detail view dengan inline editing

**Features:**
- User profile information
- Role and permission management
- Activity log
- Login history
- Account status management

**Expected Output:**
- [ ] User detail modal/page
- [ ] Inline role editing
- [ ] Activity timeline
- [ ] Account status controls

**Files:**
- `app/Livewire/Admin/UserDetail.php`
- `app/Livewire/Admin/Components/UserEditForm.php`
- `app/Models/UserActivity.php`

---

## **Task 2.3: System Settings Management**
**Priority**: MEDIUM | **Complexity**: ADVANCED | **Time**: 10-12 hours

### **2.3.1: Settings Interface**
**Target**: System configuration dengan tab-based interface

**Setting Categories:**
- General (app name, timezone, language)
- Email (SMTP settings, templates)
- WhatsApp (API configurations)
- AI (provider settings, API keys)
- Security (2FA, rate limiting)

**Expected Output:**
- [ ] Tab-based settings interface
- [ ] Form validation
- [ ] Setting persistence
- [ ] Configuration preview

**Files:**
- `app/Livewire/Admin/SystemSettings.php`
- `app/Models/SystemSetting.php`
- `app/Services/SettingsService.php`

---

### **2.3.2: System Monitoring**
**Target**: System health dan performance monitoring

**Monitoring Features:**
- System health checks
- Queue status monitoring
- Database performance
- Storage usage
- Error logs

**Expected Output:**
- [ ] Health status dashboard
- [ ] Real-time monitoring
- [ ] Alert system
- [ ] Log viewer

**Files:**
- `app/Livewire/Admin/SystemMonitoring.php`
- `app/Services/SystemHealthService.php`

---

# **LEVEL 3: ADVANCED FEATURES**

## **Task 3.1: API Authentication**
**Priority**: MEDIUM | **Complexity**: ADVANCED | **Time**: 8-10 hours

### **3.1.1: API Token Management**
**Target**: Personal access token management untuk users

**Features:**
- Token generation interface
- Scope management
- Usage tracking
- Token revocation
- Rate limiting per token

**Expected Output:**
- [ ] Token management interface
- [ ] Scope selection
- [ ] Usage analytics
- [ ] Security controls

**Files:**
- `app/Livewire/API/TokenManagement.php`
- `app/Http/Controllers/API/AuthController.php`

---

### **3.1.2: Two-Factor Authentication**
**Target**: Enhanced security dengan 2FA

**Implementation:**
- Google Authenticator support
- SMS backup (optional)
- Recovery codes
- Admin enforcement

**Expected Output:**
- [ ] 2FA setup wizard
- [ ] QR code generation
- [ ] Backup codes
- [ ] Admin controls

**Files:**
- `app/Livewire/Auth/TwoFactorSetup.php`
- `app/Services/TwoFactorService.php`

---

# **LEVEL 4: OPTIMIZATION & TESTING**

## **Task 4.1: Performance Optimization**
**Priority**: MEDIUM | **Complexity**: INTERMEDIATE | **Time**: 6-8 hours

### **4.1.1: Livewire Optimization**
**Target**: Optimize Livewire performance

**Optimizations:**
- Lazy loading components
- Query optimization
- Caching strategies
- Asset optimization

**Expected Output:**
- [ ] Faster page loads
- [ ] Reduced server requests
- [ ] Better user experience

---

## **Task 4.2: Testing Implementation**
**Priority**: HIGH | **Complexity**: INTERMEDIATE | **Time**: 8-10 hours

### **4.2.1: Authentication Tests**
**Target**: Comprehensive testing untuk auth system

**Test Coverage:**
- Login/logout functionality
- Role and permission checks
- Multi-guard authentication
- API authentication

**Expected Output:**
- [ ] >80% test coverage
- [ ] All auth flows tested
- [ ] Security vulnerability checks

**Files:**
- `tests/Feature/AuthenticationTest.php`
- `tests/Feature/AdminAuthTest.php`
- `tests/Unit/RolePermissionTest.php`

---

# **IMPLEMENTATION TIMELINE**

## **Week 1: Foundation** 
- ✅ Task 1.1: Basic Authentication Enhancement
- ✅ Task 1.2: Role & Permission Setup

## **Week 2: Admin Interface**
- ✅ Task 2.1: Admin Layout & Navigation
- ✅ Task 2.2: User Management Interface

## **Week 3: Advanced Features**
- ✅ Task 2.3: System Settings Management
- ✅ Task 3.1: API Authentication

## **Week 4: Security & Testing**
- ✅ Task 3.1.2: Two-Factor Authentication
- ✅ Task 4.1: Performance Optimization
- ✅ Task 4.2: Testing Implementation

---

# **VALIDATION CHECKLIST**

## **Authentication System:**
- [ ] Users dapat login/logout dengan benar
- [ ] Admin dan user memiliki akses terpisah
- [ ] Role-based permissions berfungsi
- [ ] Password reset functionality
- [ ] Session management proper

## **Admin Dashboard:**
- [ ] Admin dashboard hanya accessible oleh admin
- [ ] User management berfungsi lengkap
- [ ] System settings dapat diubah
- [ ] Real-time statistics accurate
- [ ] Responsive di semua device

## **Security:**
- [ ] Multi-guard authentication secure
- [ ] API tokens properly managed
- [ ] 2FA implementation working
- [ ] Rate limiting configured
- [ ] Security headers implemented

## **Performance:**
- [ ] Page load times < 2 seconds
- [ ] Livewire components optimized
- [ ] Database queries efficient
- [ ] Caching properly implemented

---

**Total Estimated Time: 64-82 hours**

**Recommended Approach**: Start dengan Level 1 foundation karena semua level selanjutnya depend pada authentication system yang solid. Build incrementally dan test setiap component sebelum move ke level berikutnya.
