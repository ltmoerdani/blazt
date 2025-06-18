<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Group Details</title>
</head>
<body>
    <h1>Contact Group Details: {{ $group->name }}</h1>

    <div>
        <strong>ID:</strong> {{ $group->id }}
    </div>
    <div>
        <strong>User ID:</strong> {{ $group->user_id }}
    </div>
    <div>
        <strong>Name:</strong> {{ $group->name }}
    </div>
    <div>
        <strong>Description:</strong> {{ $group->description ?? 'N/A' }}
    </div>
    <div>
        <strong>Total Contacts:</strong> {{ $group->contacts->count() }}
    </div>
    <div>
        <strong>Created At:</strong> {{ $group->created_at }}
    </div>
    <div>
        <strong>Updated At:</strong> {{ $group->updated_at }}
    </div>

    <h2>Contacts in this Group</h2>
    @if ($group->contacts->isEmpty())
        <p>No contacts in this group.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($group->contacts as $contact)
                    <tr>
                        <td>{{ $contact->name ?? 'N/A' }}</td>
                        <td>{{ $contact->phone_number }}</td>
                        <td>{{ $contact->is_active ? 'Active' : 'Inactive' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('dashboard.contacts.groups.edit', $group->id) }}">Edit Group</a> |
    <form action="{{ route('dashboard.contacts.groups.destroy', $group->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Are you sure?')">Delete Group</button>
    </form>

    <a href="{{ route('dashboard.contacts.groups.index') }}">Back to Contact Groups</a>
</body>
</html> 