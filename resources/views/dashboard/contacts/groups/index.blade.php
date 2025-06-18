<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Groups</title>
</head>
<body>
    <h1>Contact Groups</h1>
    <a href="{{ route('dashboard.contacts.groups.create') }}">Create New Group</a>

    @if ($groups->isEmpty())
        <p>No contact groups found.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->description ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('dashboard.contacts.groups.show', $group->id) }}">View</a> |
                            <a href="{{ route('dashboard.contacts.groups.edit', $group->id) }}">Edit</a> |
                            <form action="{{ route('dashboard.contacts.groups.destroy', $group->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $groups->links() }}
    @endif

    <a href="{{ route('dashboard.contacts.index') }}">Back to Contacts</a>
</body>
</html> 