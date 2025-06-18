<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Campaign</title>
</head>
<body>
    <h1>Create New Campaign</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.campaigns.store') }}" method="POST">
        @csrf
        <div>
            <label for="whatsapp_account_id">WhatsApp Account:</label>
            <select name="whatsapp_account_id" id="whatsapp_account_id" required>
                <option value="">Select an Account</option>
                {{-- Assuming $whatsappAccounts is passed from controller with user's accounts --}}
                {{-- @foreach ($whatsappAccounts as $account)
                    <option value="{{ $account->id }}" {{ old('whatsapp_account_id') == $account->id ? 'selected' : '' }}>{{ $account->display_name ?? $account->phone_number }}</option>
                @endforeach --}}
            </select>
            @error('whatsapp_account_id')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="name">Campaign Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="template_content">Message Template:</label>
            <textarea name="template_content" id="template_content" rows="5" required>{{ old('template_content') }}</textarea>
            @error('template_content')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="target_type">Target Type:</label>
            <select name="target_type" id="target_type" required>
                <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>All Contacts</option>
                <option value="group" {{ old('target_type') == 'group' ? 'selected' : '' }}>Specific Group</option>
                <option value="custom" {{ old('target_type') == 'custom' ? 'selected' : '' }}>Custom List (Not implemented)</option>
            </select>
            @error('target_type')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div id="target_group_div" style="display: {{ old('target_type') == 'group' ? 'block' : 'none' }};">
            <label for="target_group_id">Contact Group:</label>
            <select name="target_group_id" id="target_group_id">
                <option value="">Select a Group</option>
                {{-- Assuming $contactGroups is passed from controller --}}
                {{-- @foreach ($contactGroups as $group)
                    <option value="{{ $group->id }}" {{ old('target_group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach --}}
            </select>
            @error('target_group_id')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="scheduled_at">Scheduled At (optional):</label>
            <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}">
            <small>Leave blank to send immediately.</small>
            @error('scheduled_at')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Create Campaign</button>
    </form>

    <script>
        document.getElementById('target_type').addEventListener('change', function() {
            var targetType = this.value;
            var targetGroupDiv = document.getElementById('target_group_div');
            if (targetType === 'group') {
                targetGroupDiv.style.display = 'block';
            } else {
                targetGroupDiv.style.display = 'none';
            }
        });
    </script>
</body>
</html> 