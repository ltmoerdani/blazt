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

        <title>{{ $title ?? 'Reset Password - Blazt Admin' }}</title>

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
                    <h1 class="blazt-login-title">Reset Password</h1>
                    <p class="blazt-login-subtitle">Enter your email address and we'll send you a link to reset your password</p>

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

                    <form method="POST" action="{{ filament()->getRequestPasswordResetUrl() }}" class="blazt-form">
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
                                placeholder="Enter your email address"
                                required
                                autofocus
                                autocomplete="email"
                                aria-describedby="@error('email') email-error @enderror email-description"
                            >
                            <div id="email-description" class="text-sm text-gray-600 mt-1">
                                We'll send a password reset link to this email address
                            </div>
                            @error('email')
                                <div id="email-error" class="text-red-600 text-sm mt-1" role="alert">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="blazt-submit-btn"
                            id="reset-submit"
                            aria-describedby="reset-submit-description"
                        >
                            Send Reset Link
                        </button>
                        <div id="reset-submit-description" class="sr-only">
                            Submit the form to receive a password reset link via email
                        </div>

                        <!-- Back to Login -->
                        <div style="text-align: center; margin-top: 1.5rem;">
                            <a href="{{ filament()->getLoginUrl() }}" class="blazt-forgot-link">
                                ← Back to Login
                            </a>
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
    </body>
</html>
