<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacts</title>
</head>
<body>
    <h1>Contacts</h1>
    <a href="{{ route('dashboard.contacts.create') }}">Add New Contact</a> | 
    <a href="{{ route('dashboard.contacts.groups.index') }}">Manage Contact Groups</a>

    @if ($contacts->isEmpty())
        <p>No contacts found.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Group</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contacts as $contact)
                    <tr>
                        <td>{{ $contact->name ?? 'N/A' }}</td>
                        <td>{{ $contact->phone_number }}</td>
                        <td>{{ $contact->contactGroup->name ?? 'N/A' }}</td>
                        <td>{{ $contact->is_active ? 'Active' : 'Inactive' }}</td>
                        <td>
                            <a href="{{ route('dashboard.contacts.show', $contact->id) }}">View</a> |
                            <a href="{{ route('dashboard.contacts.edit', $contact->id) }}">Edit</a> |
                            <form action="{{ route('dashboard.contacts.destroy', $contact->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $contacts->links() }}
    @endif

    <h2>Import Contacts</h2>
    <form action="{{ route('dashboard.contacts.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="file">CSV File:</label>
            <input type="file" name="file" id="file" accept=".csv,.txt" required>
            @error('file')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="group_id">Assign to Group (optional):</label>
            <select name="group_id" id="group_id">
                <option value="">None</option>
                {{-- Assuming $contactGroups is passed from controller --}}
                {{-- @foreach ($contactGroups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach --}}
            </select>
            @error('group_id')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Import Contacts</button>
    </form>
</body>
</html> 