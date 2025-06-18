<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Account Details</title>
</head>
<body>
    <h1>WhatsApp Account Details: {{ $whatsAppAccount->display_name ?? $whatsAppAccount->phone_number }}</h1>

    <div>
        <strong>ID:</strong> {{ $whatsAppAccount->id }}
    </div>
    <div>
        <strong>User ID:</strong> {{ $whatsAppAccount->user_id }}
    </div>
    <div>
        <strong>Phone Number:</strong> {{ $whatsAppAccount->phone_number }}
    </div>
    <div>
        <strong>Display Name:</strong> {{ $whatsAppAccount->display_name ?? 'N/A' }}
    </div>
    <div>
        <strong>Status:</strong> {{ $whatsAppAccount->status }}
    </div>
    <div>
        <strong>Last Connected:</strong> {{ $whatsAppAccount->last_connected_at ?? 'N/A' }}
    </div>
    <div>
        <strong>Health Check At:</strong> {{ $whatsAppAccount->health_check_at ?? 'N/A' }}
    </div>
    @if ($whatsAppAccount->qr_code_path)
        <div>
            <strong>QR Code:</strong><br>
            <img src="{{ $whatsAppAccount->qr_code_path }}" alt="QR Code" style="max-width: 200px;">
        </div>
    @endif
    <div>
        <strong>Created At:</strong> {{ $whatsAppAccount->created_at }}
    </div>
    <div>
        <strong>Updated At:</strong> {{ $whatsAppAccount->updated_at }}
    </div>

    <a href="{{ route('dashboard.whatsapp-accounts.edit', $whatsAppAccount->id) }}">Edit Account</a> |
    <form action="{{ route('dashboard.whatsapp-accounts.destroy', $whatsAppAccount->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Are you sure?')">Delete Account</button>
    </form>

    @if ($whatsAppAccount->status === 'disconnected' || $whatsAppAccount->status === 'banned')
        <form action="{{ route('dashboard.whatsapp-accounts.connect', $whatsAppAccount->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Connect Account</button>
        </form>
    @elseif ($whatsAppAccount->status === 'connected')
        <form action="{{ route('dashboard.whatsapp-accounts.disconnect', $whatsAppAccount->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit">Disconnect Account</button>
        </form>
    @endif

    <a href="{{ route('dashboard.whatsapp-accounts.index') }}">Back to WhatsApp Accounts</a>
</body>
</html>