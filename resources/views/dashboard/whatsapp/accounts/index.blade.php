<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp Accounts</title>
</head>
<body>
    <h1>WhatsApp Accounts</h1>
    <a href="{{ route('dashboard.whatsapp-accounts.create') }}">Add New WhatsApp Account</a>

    @if ($accounts->isEmpty())
        <p>No WhatsApp accounts found.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>Phone Number</th>
                    <th>Display Name</th>
                    <th>Status</th>
                    <th>Last Connected</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <td>{{ $account->phone_number }}</td>
                        <td>{{ $account->display_name ?? 'N/A' }}</td>
                        <td>{{ $account->status }}</td>
                        <td>{{ $account->last_connected_at ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('dashboard.whatsapp-accounts.show', $account->id) }}">View</a> |
                            <a href="{{ route('dashboard.whatsapp-accounts.edit', $account->id) }}">Edit</a> |
                            <form action="{{ route('dashboard.whatsapp-accounts.destroy', $account->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                            @if ($account->status === 'disconnected' || $account->status === 'banned')
                                <form action="{{ route('dashboard.whatsapp-accounts.connect', $account->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit">Connect</button>
                                </form>
                            @elseif ($account->status === 'connected')
                                <form action="{{ route('dashboard.whatsapp-accounts.disconnect', $account->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit">Disconnect</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $accounts->links() }}
    @endif
</body>
</html>