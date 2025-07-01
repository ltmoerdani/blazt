# Dokumentasi Desain dan Layout Halaman Autentikasi
**Blazt WhatsApp Campaign Management System**

## Overview
Dokumentasi ini berisi spesifikasi detail dan rinci untuk desain halaman Login, Register, dan Forgot Password yang dapat diduplikasi dengan presisi 1:1. Semua elemen visual, dimensi, warna, dan interaksi telah didokumentasikan secara komprehensif.

---

## 1. STRUKTUR LAYOUT UMUM (Guest Layout)

### 1.1 HTML Structure
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="...">
    <title>Blazt</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
</head>
<body class="font-sans antialiased bg-white">
    <div class="direction-ltr">
        <div class="flex h-screen justify-center">
            <div class="flex justify-center">
                <div class="w-[20em] mt-40">
                    <!-- Logo Section -->
                    <!-- Form Content -->
                </div>
            </div>
        </div>
    </div>
</body>
</html>
```

### 1.2 Layout Properties
- **Body Background**: `bg-white` (putih solid)
- **Container**: Flexbox centered, full screen height
- **Form Container Width**: `w-[20em]` (320px)
- **Top Margin**: `mt-40` (160px dari atas)
- **Font Family**: Figtree (400, 500, 600 weights)
- **Text Anti-aliasing**: Enabled
- **Direction**: Left-to-right (`direction-ltr`)

---

## 2. LOGO SECTION

### 2.1 Logo Structure
```html
<div class="flex justify-center mb-5">
    <a href="/">
        <div class="flex items-center space-x-2">
            <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
                </svg>
            </div>
            <span class="text-xl font-semibold text-gray-900">Blazt</span>
        </div>
    </a>
</div>
```

### 2.2 Logo Properties
- **Container**: `flex justify-center mb-5` (centered, margin bottom 20px)
- **Icon Container**: `w-6 h-6` (24x24px), `bg-green-600` (#059669), `rounded-full`
- **SVG Icon**: Lightning bolt, `w-4 h-4` (16x16px), `text-white`
- **Text**: "Blazt", `text-xl` (20px), `font-semibold`, `text-gray-900`
- **Spacing**: `space-x-2` (8px gap between icon and text)
- **Link**: Clickable area around entire logo, links to "/"

---

## 3. HALAMAN LOGIN

### 3.1 HTML Structure
```html
<h1 class="text-2xl text-center">Login to your account</h1>
<div class="text-center text-sm text-slate-500">
    Don't have an account? 
    <a class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500" href="/register">
        Create one here
    </a>
</div>

<!-- Session Status Alert -->
@if (session('status'))
    <div class="mb-6 p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="/login" class="mt-5">
    <!-- CSRF Token -->
    
    <div class="mt-5 space-y-4">
        <!-- Email Field -->
        <div class="col-span-3">
            <label for="email" class="block text-sm leading-6 text-gray-900">Email</label>
            <div>
                <input id="email"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    type="email"
                    name="email"
                    value=""
                    required
                    autofocus
                    autocomplete="username" />
            </div>
            <!-- Error messages here -->
        </div>

        <!-- Password Field -->
        <div class="col-span-3">
            <label for="password" class="block text-sm leading-6 text-gray-900">Password</label>
            <div>
                <input id="password"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password" />
            </div>
            <!-- Error messages here -->
        </div>
    </div>

    <!-- Remember Me & Forgot Password -->
    <div class="flex items-center justify-between mt-5">
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input id="remember"
                    aria-describedby="remember"
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800">
            </div>
            <div class="ml-3 text-sm">
                <label for="remember" class="text-gray-500 dark:text-gray-300">Remember me</label>
            </div>
        </div>
        <a class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500" href="/forgot-password">
            Forgot password?
        </a>
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
        <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">
            Login to your account
        </button>
    </div>
</form>
```

### 3.2 Element Properties

#### 3.2.1 Heading
- **Text**: "Login to your account"
- **Class**: `text-2xl text-center`
- **Font Size**: 24px
- **Alignment**: Center
- **Color**: Default (black)

#### 3.2.2 Subtitle
- **Text**: "Don't have an account? Create one here"
- **Container**: `text-center text-sm text-slate-500`
- **Font Size**: 14px
- **Color**: #64748b (slate-500)
- **Link Color**: #059669 (primary-600)
- **Link Hover**: `border-gray-500` (underline on hover)

#### 3.2.3 Session Status Alert
- **Background**: `bg-green-50` (#f0fdf4)
- **Text Color**: `text-green-600` (#059669)
- **Border**: `border-green-200` (#bbf7d0)
- **Padding**: `p-3` (12px all sides)
- **Margin**: `mb-6` (24px bottom)
- **Border Radius**: `rounded` (4px)

#### 3.2.4 Form Container
- **Method**: POST
- **Action**: "/login"
- **Top Margin**: `mt-5` (20px)
- **Fields Container**: `mt-5 space-y-4` (20px top, 16px gap between fields)

#### 3.2.5 Input Fields
**Label Properties:**
- **Class**: `block text-sm leading-6 text-gray-900`
- **Font Size**: 14px
- **Line Height**: 24px
- **Color**: #111827 (gray-900)

**Input Properties:**
- **Class**: `block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300`
- **Width**: 100%
- **Padding**: 6px vertical, 16px horizontal
- **Border**: None (using ring instead)
- **Ring**: 1px solid #d1d5db (gray-300)
- **Border Radius**: 6px
- **Text Color**: #111827 (gray-900)
- **Placeholder Color**: #9ca3af (gray-400)
- **Shadow**: Small shadow
- **Focus**: Remove outline, maintain ring

#### 3.2.6 Remember Me Section
- **Container**: `flex items-center justify-between mt-5`
- **Checkbox**: `w-4 h-4 border border-gray-300 rounded bg-gray-50`
- **Checkbox Focus**: `focus:ring-3 focus:ring-primary-300`
- **Label**: `text-gray-500 dark:text-gray-300`, margin left 12px

#### 3.2.7 Submit Button
- **Text**: "Login to your account"
- **Class**: `rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full`
- **Background**: #059669 (primary)
- **Hover**: #047857 (darker green)
- **Padding**: 12px vertical, 12px horizontal
- **Width**: 100%
- **Text Color**: White
- **Font Size**: 14px
- **Border Radius**: 6px
- **Shadow**: Small shadow
- **Top Margin**: `mt-6` (24px)

---

## 4. HALAMAN REGISTER

### 4.1 HTML Structure
```html
<h1 class="text-2xl text-center">Create your account</h1>
<div class="text-center text-sm text-slate-500">
    Already have an account? 
    <a class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500" href="/login">
        Sign in here
    </a>
</div>

<form method="POST" action="/register" class="mt-5">
    <!-- CSRF Token -->
    
    <div class="mt-5 space-y-4">
        <!-- Name Field -->
        <div class="col-span-3">
            <label for="name" class="block text-sm leading-6 text-gray-900">Name</label>
            <div>
                <input id="name"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    type="text"
                    name="name"
                    value=""
                    required
                    autofocus
                    autocomplete="name" />
            </div>
            <!-- Error messages here -->
        </div>

        <!-- Email Field -->
        <div class="col-span-3">
            <label for="email" class="block text-sm leading-6 text-gray-900">Email</label>
            <div>
                <input id="email"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    type="email"
                    name="email"
                    value=""
                    required
                    autocomplete="username" />
            </div>
            <!-- Error messages here -->
        </div>

        <!-- Timezone Field -->
        <div class="col-span-3">
            <label for="timezone" class="block text-sm leading-6 text-gray-900">Timezone</label>
            <div>
                <select id="timezone"
                    name="timezone"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    required>
                    <option value="">Select your timezone</option>
                    <option value="UTC">UTC</option>
                    <option value="America/New_York">Eastern Time</option>
                    <option value="America/Chicago">Central Time</option>
                    <option value="America/Denver">Mountain Time</option>
                    <option value="America/Los_Angeles">Pacific Time</option>
                    <option value="Europe/London">London</option>
                    <option value="Europe/Paris">Paris</option>
                    <option value="Asia/Jakarta">Jakarta</option>
                    <option value="Asia/Tokyo">Tokyo</option>
                </select>
            </div>
            <!-- Error messages here -->
        </div>

        <!-- Password Field -->
        <div class="col-span-3">
            <label for="password" class="block text-sm leading-6 text-gray-900">Password</label>
            <div>
                <input id="password"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password" />
            </div>
            <!-- Error messages here -->
        </div>

        <!-- Confirm Password Field -->
        <div class="col-span-3">
            <label for="password_confirmation" class="block text-sm leading-6 text-gray-900">Confirm Password</label>
            <div>
                <input id="password_confirmation"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password" />
            </div>
            <!-- Error messages here -->
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
        <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">
            Create your account
        </button>
    </div>
</form>
```

### 4.2 Element Properties

#### 4.2.1 Heading
- **Text**: "Create your account"
- **Properties**: Identical to login page

#### 4.2.2 Subtitle
- **Text**: "Already have an account? Sign in here"
- **Properties**: Identical to login page styling
- **Link**: Links to "/login"

#### 4.2.3 Form Fields
**Additional fields compared to login:**

**Name Field:**
- **Label**: "Name"
- **Type**: `text`
- **Autocomplete**: `name`
- **Required**: Yes
- **Autofocus**: Yes

**Timezone Select:**
- **Label**: "Timezone"
- **Type**: `select`
- **Default Option**: "Select your timezone"
- **Options**: 
  - UTC
  - America/New_York (Eastern Time)
  - America/Chicago (Central Time)
  - America/Denver (Mountain Time)
  - America/Los_Angeles (Pacific Time)
  - Europe/London (London)
  - Europe/Paris (Paris)
  - Asia/Jakarta (Jakarta)
  - Asia/Tokyo (Tokyo)

**Password Confirmation:**
- **Label**: "Confirm Password"
- **Type**: `password`
- **Autocomplete**: `new-password`

#### 4.2.4 Submit Button
- **Text**: "Create your account"
- **Properties**: Identical to login button styling

---

## 5. HALAMAN FORGOT PASSWORD

### 5.1 HTML Structure
```html
<h1 class="text-2xl text-center">Forgot your password?</h1>
<div class="text-center text-sm text-slate-500">
    No problem. Just let us know your email address and we will email you a password reset link.
</div>

<!-- Session Status Alert -->
@if (session('status'))
    <div class="mb-6 p-3 text-sm text-green-600 bg-green-50 border border-green-200 rounded">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="/password/email" class="mt-5">
    <!-- CSRF Token -->

    <div class="mt-5 space-y-4">
        <!-- Email Address -->
        <div class="col-span-3">
            <label for="email" class="block text-sm leading-6 text-gray-900">Email</label>
            <div>
                <input id="email"
                    class="block w-full rounded-md border-0 py-1.5 px-4 text-gray-900 shadow-sm outline-none ring-1 ring-inset placeholder:text-gray-400 sm:text-sm sm:leading-6 ring-gray-300"
                    type="email"
                    name="email"
                    value=""
                    required
                    autofocus
                    autocomplete="username" />
            </div>
            <!-- Error messages here -->
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
        <button type="submit" class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full">
            Email Password Reset Link
        </button>
    </div>

    <!-- Footer Link -->
    <div class="text-center mt-5">
        <div class="text-sm text-slate-500">
            Remember your password? 
            <a class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500" href="/login">
                Back to sign in
            </a>
        </div>
    </div>
</form>
```

### 5.2 Element Properties

#### 5.2.1 Heading
- **Text**: "Forgot your password?"
- **Properties**: Identical to other pages

#### 5.2.2 Description
- **Text**: "No problem. Just let us know your email address and we will email you a password reset link."
- **Class**: `text-center text-sm text-slate-500`
- **Properties**: Identical to subtitle styling but longer text

#### 5.2.3 Form
- **Action**: "/password/email"
- **Single Field**: Email only
- **Field Properties**: Identical to login/register email field

#### 5.2.4 Submit Button
- **Text**: "Email Password Reset Link"
- **Properties**: Identical to other pages

#### 5.2.5 Footer Link
- **Text**: "Remember your password? Back to sign in"
- **Container**: `text-center mt-5`
- **Properties**: Identical to other page subtitle styling
- **Link**: Links to "/login"

---

## 6. ERROR HANDLING COMPONENT

### 6.1 HTML Structure
```html
@if ($messages)
    <ul class="text-sm text-red-600 dark:text-red-400 space-y-1">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
```

### 6.2 Error Properties
- **Text Color**: `text-red-600` (#dc2626) in light mode
- **Text Color Dark**: `text-red-400` (#f87171) in dark mode
- **Font Size**: `text-sm` (14px)
- **Spacing**: `space-y-1` (4px gap between errors)
- **Display**: Shows below respective input field
- **Position**: `mt-1` (4px top margin from input)

---

## 7. COLOR SCHEME SPECIFICATION

### 7.1 Primary Colors
```css
.text-primary-600 { color: #059669; }
.text-primary-500 { color: #10b981; }
.bg-primary { background-color: #059669; }
.bg-primary:hover { background-color: #047857; }
```

### 7.2 Gray Scale Colors
- **gray-900**: #111827 (headings, labels)
- **gray-500**: #6b7280 (secondary text)
- **gray-400**: #9ca3af (placeholder text)
- **gray-300**: #d1d5db (borders, rings)
- **slate-500**: #64748b (muted text)

### 7.3 Status Colors
- **Green (Success)**:
  - Background: #f0fdf4 (green-50)
  - Border: #bbf7d0 (green-200)
  - Text: #059669 (green-600)
- **Red (Error)**:
  - Text: #dc2626 (red-600)
  - Text Dark: #f87171 (red-400)

---

## 8. RESPONSIVE BEHAVIOR

### 8.1 Mobile Adaptations
- **Container Width**: `w-[20em]` (320px) remains fixed
- **Font Sizes**: Remain consistent across devices
- **Touch Targets**: All interactive elements maintain minimum 44px touch area
- **Viewport**: `width=device-width, initial-scale=1`

### 8.2 Dark Mode Support
- **Text Colors**: Automatic dark mode variants included
- **Background**: Remains white (no dark mode background)
- **Focus States**: Maintain visibility in both modes

---

## 9. INTERACTION STATES

### 9.1 Focus States
- **Inputs**: Remove outline, maintain ring
- **Buttons**: Subtle glow with primary color
- **Links**: Underline on hover (`hover:border-gray-500`)

### 9.2 Hover States
- **Submit Button**: Darker green background (#047857)
- **Links**: Border bottom appears
- **Checkbox**: Ring color change

### 9.3 Loading States
- **Form Submission**: Button should show loading indicator
- **Input Validation**: Real-time error display

---

## 10. ACCESSIBILITY FEATURES

### 10.1 Labels and IDs
- **All inputs**: Properly labeled with `for` attributes
- **Required fields**: `required` attribute present
- **Autocomplete**: Appropriate values set
- **ARIA**: `aria-describedby` for checkbox

### 10.2 Keyboard Navigation
- **Tab Order**: Natural flow through form
- **Enter Key**: Submits form
- **Space**: Toggles checkbox

### 10.3 Screen Reader Support
- **Semantic HTML**: Proper form structure
- **Error Association**: Errors linked to inputs
- **Status Messages**: Announced when displayed

---

## 11. VALIDATION SPECIFICATIONS

### 11.1 Client-Side Validation
- **Email**: HTML5 email validation
- **Required Fields**: HTML5 required attribute
- **Password Match**: JavaScript validation (register page)

### 11.2 Server-Side Validation
- **Laravel Validation**: Backend validation rules
- **Error Display**: Via `x-input-error` component
- **Session Status**: Success messages via session

---

## 12. IMPLEMENTATION CHECKLIST

### 12.1 HTML Structure
- [ ] Guest layout component implemented
- [ ] Logo with SVG icon and text
- [ ] Form structure with proper methods/actions
- [ ] CSRF token inclusion
- [ ] Proper input IDs and names
- [ ] Accessibility attributes

### 12.2 Styling
- [ ] Tailwind CSS classes applied exactly
- [ ] Custom primary colors defined
- [ ] Font family loaded (Figtree)
- [ ] Responsive behavior working
- [ ] Dark mode classes included

### 12.3 Functionality
- [ ] Form submissions work
- [ ] Validation errors display
- [ ] Success messages show
- [ ] Links navigation correct
- [ ] Remember me functionality
- [ ] Password reset flow

### 12.4 Browser Testing
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari
- [ ] Mobile browsers
- [ ] Different screen sizes

---

## 13. TECHNICAL SPECIFICATIONS

### 13.1 Dependencies
- **Laravel Framework**: 10.x+
- **Tailwind CSS**: 3.x+
- **Figtree Font**: Google Fonts
- **Blade Templates**: Laravel templating
- **Vite**: Asset compilation

### 13.2 File Structure
```
resources/
├── views/
│   ├── components/
│   │   ├── guest-layout.blade.php
│   │   └── input-error.blade.php
│   └── auth/
│       ├── login.blade.php
│       ├── register.blade.php
│       └── forgot-password.blade.php
├── css/
│   └── app.css
└── js/
    └── app.js
```

### 13.3 Routes Required
- `GET /login` → login form
- `POST /login` → authenticate user
- `GET /register` → registration form
- `POST /register` → create user
- `GET /forgot-password` → forgot password form
- `POST /password/email` → send reset link

---

## 14. QUALITY ASSURANCE

### 14.1 Visual Testing
- [ ] Pixel-perfect alignment
- [ ] Consistent spacing
- [ ] Proper color usage
- [ ] Typography consistency
- [ ] Shadow and border accuracy

### 14.2 Functional Testing
- [ ] All form submissions work
- [ ] Validation messages appear
- [ ] Links navigate correctly
- [ ] Sessions maintain state
- [ ] Error handling robust

### 14.3 Performance
- [ ] Fast loading times
- [ ] Optimized images/icons
- [ ] Minimal CSS/JS
- [ ] Proper caching headers

---

**Dokumentasi ini dapat digunakan untuk menduplikasi halaman autentikasi dengan presisi 1:1. Setiap detail visual, fungsional, dan teknis telah didokumentasikan secara komprehensif.**
