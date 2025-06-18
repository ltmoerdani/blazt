<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit WhatsApp Account</title>
</head>
<body>
    <h1>Edit WhatsApp Account: {{ $whatsAppAccount->display_name ?? $whatsAppAccount->phone_number }}</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.whatsapp-accounts.update', $whatsAppAccount->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $whatsAppAccount->phone_number) }}" required>
            @error('phone_number')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="display_name">Display Name (optional):</label>
            <input type="text" name="display_name" id="display_name" value="{{ old('display_name', $whatsAppAccount->display_name) }}">
            @error('display_name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="disconnected" {{ old('status', $whatsAppAccount->status) == 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                <option value="connecting" {{ old('status', $whatsAppAccount->status) == 'connecting' ? 'selected' : '' }}>Connecting</option>
                <option value="connected" {{ old('status', $whatsAppAccount->status) == 'connected' ? 'selected' : '' }}>Connected</option>
                <option value="banned" {{ old('status', $whatsAppAccount->status) == 'banned' ? 'selected' : '' }}>Banned</option>
            </select>
            @error('status')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Update Account</button>
    </form>

    <a href="{{ route('dashboard.whatsapp-accounts.show', $whatsAppAccount->id) }}">Back to Account Details</a> |
    <a href="{{ route('dashboard.whatsapp-accounts.index') }}">Back to WhatsApp Accounts List</a>
</body>
</html> 