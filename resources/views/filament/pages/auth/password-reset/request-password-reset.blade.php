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
        <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/blazt-auth-clean.css'])
    </head>

    <body>
        <div class="direction-ltr">
            <div class="flex h-screen justify-center">
                <div class="flex justify-center">
                    <div class="blazt-auth-container">
                        <div class="blazt-brand">
                            <a href="/admin">
                                @if(file_exists(public_path('images/blazt-logo.png')))
                                    <img class="blazt-logo" src="/images/blazt-logo.png" alt="Blazt">
                                @else
                                    <div style="color: #2d8f5f; font-size: 24px; font-weight: 700; text-align: center;">⚡ Blazt</div>
                                @endif
                            </a>
                        </div>

                        <h1 class="blazt-form-title">Reset password</h1>
                        <div class="blazt-form-subtitle">
                            Remembered password?
                            <a href="{{ route('filament.admin.auth.login') }}" class="text-sm text-primary-600 dark:text-primary-500 border-b hover:border-gray-500">Login</a>
                        </div>

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
                            
                            <div class="blazt-form-fields">
                                <!-- Email Field -->
                                <div class="blazt-form-group">
                                    <label for="email" class="blazt-label">Email</label>
                                    <div>
                                        <input
                                            type="email"
                                            id="email"
                                            name="email"
                                            value="{{ old('email') }}"
                                            class="blazt-input @error('email') border-red-500 @enderror"
                                            required
                                            autofocus
                                            autocomplete="email"
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="blazt-submit-container">
                                <button type="submit" class="blazt-submit-btn">
                                    Send password reset link
                                </button>
                            </div>

                            <!-- Back to Login -->
                            <div class="blazt-back-link">
                                <a href="{{ route('filament.admin.auth.login') }}">
                                    ← Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
</html>
