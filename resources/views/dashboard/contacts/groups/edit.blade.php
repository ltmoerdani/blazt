<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact Group</title>
</head>
<body>
    <h1>Edit Contact Group: {{ $group->name }}</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.contacts.groups.update', $group->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Group Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="description">Description (optional):</label>
            <textarea name="description" id="description" rows="3">{{ old('description', $group->description) }}</textarea>
            @error('description')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Update Group</button>
    </form>

    <a href="{{ route('dashboard.contacts.groups.show', $group->id) }}">Back to Group Details</a> |
    <a href="{{ route('dashboard.contacts.groups.index') }}">Back to Contact Groups List</a>
</body>
</html> 