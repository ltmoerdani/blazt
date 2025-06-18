<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add WhatsApp Account</title>
</head>
<body>
    <h1>Add New WhatsApp Account</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.whatsapp-accounts.store') }}" method="POST">
        @csrf
        <div>
            <label for="phone_number">Phone Number (e.g., 628123456789):</label>
            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required>
            @error('phone_number')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="display_name">Display Name (optional):</label>
            <input type="text" name="display_name" id="display_name" value="{{ old('display_name') }}">
            @error('display_name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Add Account</button>
    </form>

    <a href="{{ route('dashboard.whatsapp-accounts.index') }}">Back to WhatsApp Accounts</a>
</body>
</html> 