<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Contact Group</title>
</head>
<body>
    <h1>Create New Contact Group</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.contacts.groups.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Group Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="description">Description (optional):</label>
            <textarea name="description" id="description" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Create Group</button>
    </form>

    <a href="{{ route('dashboard.contacts.groups.index') }}">Back to Contact Groups</a>
</body>
</html> 