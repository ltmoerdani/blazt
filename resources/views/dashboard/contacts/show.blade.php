<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Details</title>
</head>
<body>
    <h1>Contact Details: {{ $contact->name ?? $contact->phone_number }}</h1>

    <div>
        <strong>ID:</strong> {{ $contact->id }}
    </div>
    <div>
        <strong>User ID:</strong> {{ $contact->user_id }}
    </div>
    <div>
        <strong>Phone Number:</strong> {{ $contact->phone_number }}
    </div>
    <div>
        <strong>Name:</strong> {{ $contact->name ?? 'N/A' }}
    </div>
    <div>
        <strong>Contact Group:</strong> {{ $contact->contactGroup->name ?? 'None' }}
    </div>
    <div>
        <strong>Is Active:</strong> {{ $contact->is_active ? 'Yes' : 'No' }}
    </div>
    <div>
        <strong>Created At:</strong> {{ $contact->created_at }}
    </div>
    <div>
        <strong>Updated At:</strong> {{ $contact->updated_at }}
    </div>

    <a href="{{ route('dashboard.contacts.edit', $contact->id) }}">Edit Contact</a> |
    <form action="{{ route('dashboard.contacts.destroy', $contact->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Are you sure?')">Delete Contact</button>
    </form>

    <a href="{{ route('dashboard.contacts.index') }}">Back to Contacts</a>
</body>
</html> 