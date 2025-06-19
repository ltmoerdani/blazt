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

        <title>{{ $title ?? 'Reset password - Blazt' }}</title>

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
                <h1 class="blazt-form-title">Reset password</h1>
                <p class="blazt-form-subtitle">Remembered password? Login</p>

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

                <form method="POST" action="{{ filament()->getRequestPasswordResetUrl() }}" class="blazt-form">
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
                            placeholder="Enter your email address"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="blazt-submit-btn">
                        Send password reset link
                    </button>

                    <!-- Back to Login -->
                    <div class="blazt-back-link">
                        <a href="{{ route('filament.admin.auth.login') }}">
                            ← Back to Login
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
