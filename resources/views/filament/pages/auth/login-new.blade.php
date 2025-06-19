<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi min-h-screen',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Login - Blazt Admin' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/blazt-auth.css'])
    </head>

    <body class="fi-body fi-panel-app">
        <div class="blazt-container">
            <!-- Header -->
            <header class="blazt-header">
                <div class="blazt-logo">⚡</div>
                <div>
                    <span class="blazt-brand-text">BLAZT</span>
                    <span class="blazt-admin-text">Admin</span>
                </div>
            </header>

            <!-- Main Content -->
            <main class="blazt-main">
                <div class="blazt-login-container">
                    <h1 class="blazt-login-title">Welcome Back</h1>
                    <p class="blazt-login-subtitle">Sign in to your admin account</p>

                    <!-- Error Messages -->
                    @if ($errors->any())
                        <div class="blazt-error" role="alert">
                            <ul style="margin: 0; padding: 0; list-style: none;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Success Messages -->
                    @if (session('status'))
                        <div class="blazt-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ filament()->getLoginUrl() }}" class="blazt-form">
                        @csrf

                        <!-- Email Field -->
                        <div class="blazt-form-group">
                            <label for="email" class="blazt-label">
                                Email Address
                                <span style="color: #dc2626;">*</span>
                            </label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="blazt-input @error('email') border-red-500 @enderror"
                                placeholder="Enter your email"
                                required
                                autofocus
                                autocomplete="email"
                                aria-describedby="@error('email') email-error @enderror"
                            >
                            @error('email')
                                <div id="email-error" class="text-red-600 text-sm mt-1" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="blazt-form-group">
                            <label for="password" class="blazt-label">
                                Password
                                <span style="color: #dc2626;">*</span>
                            </label>
                            <div class="blazt-input-with-icon">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="blazt-input @error('password') border-red-500 @enderror"
                                    placeholder="Enter your password"
                                    required
                                    autocomplete="current-password"
                                    aria-describedby="@error('password') password-error @enderror password-toggle"
                                >
                                <button
                                    type="button"
                                    class="blazt-password-toggle"
                                    id="password-toggle"
                                    onclick="togglePassword()"
                                    aria-label="Toggle password visibility"
                                    tabindex="0"
                                >
                                    <span id="password-toggle-text">Show</span>
                                </button>
                            </div>
                            @error('password')
                                <div id="password-error" class="text-red-600 text-sm mt-1" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="blazt-checkbox-group">
                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                                value="1"
                                class="blazt-checkbox"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="blazt-checkbox-label">
                                Remember me
                            </label>
                        </div>

                        <!-- Forgot Password Link -->
                        <div class="blazt-forgot-password">
                            <a href="{{ filament()->getRequestPasswordResetUrl() }}" class="blazt-forgot-link">
                                Forgot your password?
                            </a>
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="blazt-submit-btn"
                            id="login-submit"
                            aria-describedby="login-submit-description"
                        >
                            Sign In
                        </button>
                        <div id="login-submit-description" class="sr-only">
                            Submit the login form to access your admin account
                        </div>
                    </form>
                </div>
            </main>

            <!-- Footer -->
            <footer class="blazt-footer">
                <div class="blazt-footer-content">
                    <div class="blazt-footer-text">
                        © {{ date('Y') }} Blazt. All rights reserved.
                    </div>
                    <div class="blazt-footer-links">
                        <a href="#" class="blazt-footer-link">Privacy Policy</a>
                        <a href="#" class="blazt-footer-link">Terms of Service</a>
                        <a href="#" class="blazt-footer-link">Support</a>
                    </div>
                </div>
            </footer>
        </div>

        <script>
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const toggleText = document.getElementById('password-toggle-text');
                const toggleButton = document.getElementById('password-toggle');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggleText.textContent = 'Hide';
                    toggleButton.setAttribute('aria-label', 'Hide password');
                } else {
                    passwordInput.type = 'password';
                    toggleText.textContent = 'Show';
                    toggleButton.setAttribute('aria-label', 'Show password');
                }
            }

            // Keyboard accessibility for password toggle
            document.getElementById('password-toggle').addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    togglePassword();
                }
            });
        </script>
    </body>
</html>
