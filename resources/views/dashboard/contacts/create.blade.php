<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Contact</title>
</head>
<body>
    <h1>Create New Contact</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.contacts.store') }}" method="POST">
        @csrf
        <div>
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" required>
            @error('phone_number')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="name">Name (optional):</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="group_id">Contact Group (optional):</label>
            <select name="group_id" id="group_id">
                <option value="">None</option>
                @foreach ($contactGroups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
            @error('group_id')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="is_active">Is Active:</label>
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}>
            @error('is_active')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Create Contact</button>
    </form>

    <a href="{{ route('dashboard.contacts.index') }}">Back to Contacts</a>
</body>
</html> 