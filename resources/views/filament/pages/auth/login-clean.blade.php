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

        <title>{{ $title ?? 'Login to your account - Blazt' }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/blazt-auth-clean.css'])
    </head>

    <body>
        <div class="blazt-auth-container">
            <!-- Brand Logo -->
            <div class="blazt-brand">
                <div class="blazt-logo">
                    <span class="blazt-logo-icon">⚡</span>
                </div>
                <div class="blazt-brand-name">Blazt</div>
            </div>

            <!-- Form Container -->
            <div class="blazt-form-container">
                <h1 class="blazt-form-title">Login to your account</h1>
                <p class="blazt-form-subtitle">Don't have an account? Create one here</p>

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="blazt-message blazt-error" role="alert">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- Success Messages -->
                @if (session('status'))
                    <div class="blazt-message blazt-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ filament()->getLoginUrl() }}" class="blazt-form">
                    @csrf

                    <!-- Email Field -->
                    <div class="blazt-form-group">
                        <label for="email" class="blazt-label">Email</label>
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
                        >
                    </div>

                    <!-- Password Field -->
                    <div class="blazt-form-group">
                        <label for="password" class="blazt-label">Password</label>
                        <div class="blazt-password-container">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="blazt-input @error('password') border-red-500 @enderror"
                                placeholder="Enter your password"
                                required
                                autocomplete="current-password"
                            >
                            <button
                                type="button"
                                class="blazt-password-toggle"
                                onclick="togglePassword()"
                                aria-label="Toggle password visibility"
                            >
                                <span id="password-toggle-text">Show</span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me and Forgot Password Row -->
                    <div class="blazt-form-row">
                        <div class="blazt-checkbox-container">
                            <input
                                type="checkbox"
                                id="remember"
                                name="remember"
                                value="1"
                                class="blazt-checkbox"
                                {{ old('remember') ? 'checked' : '' }}
                            >
                            <label for="remember" class="blazt-checkbox-label">Remember me</label>
                        </div>
                        <a href="{{ route('filament.admin.auth.password-reset.request') }}" class="blazt-forgot-link">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="blazt-submit-btn">
                        Login to your account
                    </button>
                </form>
            </div>
        </div>

        <script>
            function togglePassword() {
                const passwordInput = document.getElementById('password');
                const toggleText = document.getElementById('password-toggle-text');
                const toggleButton = document.querySelector('.blazt-password-toggle');
                
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
        </script>
    </body>
</html>
