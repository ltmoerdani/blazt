<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
</head>
<body>
    <h1>Settings</h1>

    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h2>Profile Information</h2>
    <form action="{{ route('dashboard.settings.updateProfile') }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="timezone">Timezone:</label>
            <input type="text" name="timezone" id="timezone" value="{{ old('timezone', $user->timezone) }}">
            @error('timezone')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Update Profile</button>
    </form>

    <h2>Update Password</h2>
    <form action="{{ route('dashboard.settings.updatePassword') }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>
            @error('current_password')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="password">New Password:</label>
            <input type="password" name="password" id="password" required>
            @error('password')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="password_confirmation">Confirm New Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <button type="submit">Update Password</button>
    </form>

    <h2>AI Configuration</h2>
    <form action="{{ route('dashboard.settings.updateAIConfiguration') }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="provider">AI Provider:</label>
            <select name="provider" id="provider" required>
                <option value="openai" {{ old('provider', $aiConfig->provider ?? '') == 'openai' ? 'selected' : '' }}>OpenAI</option>
                <option value="deepseek" {{ old('provider', $aiConfig->provider ?? '') == 'deepseek' ? 'selected' : '' }}>DeepSeek</option>
                <option value="claude" {{ old('provider', $aiConfig->provider ?? '') == 'claude' ? 'selected' : '' }}>Claude</option>
            </select>
            @error('provider')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="api_key">API Key:</label>
            <input type="text" name="api_key" id="api_key" value="{{ old('api_key', $aiConfig->api_key ?? '') }}" required>
            @error('api_key')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="is_active">Is Active:</label>
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $aiConfig->is_active ?? false) ? 'checked' : '' }}>
            @error('is_active')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Update AI Configuration</button>
    </form>

    <h2>User Limits</h2>
    @if ($userLimits)
        <div>
            <strong>Max WhatsApp Accounts:</strong> {{ $userLimits->max_whatsapp_accounts }}
        </div>
        <div>
            <strong>Max Contacts:</strong> {{ $userLimits->max_contacts }}
        </div>
        <div>
            <strong>Max Campaigns:</strong> {{ $userLimits->max_campaigns }}
        </div>
        <div>
            <strong>Max Messages Per Month:</strong> {{ $userLimits->max_messages_per_month }}
        </div>
        <div>
            <strong>AI Usage Limit:</strong> {{ $userLimits->ai_usage_limit }}
        </div>
    @else
        <p>No user limits configured.</p>
    @endif

    <a href="{{ route('dashboard.index') }}">Back to Dashboard</a>
</body>
</html> 