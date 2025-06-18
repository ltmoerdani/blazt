<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Campaign</title>
</head>
<body>
    <h1>Edit Campaign: {{ $campaign->name }}</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dashboard.campaigns.update', $campaign->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Campaign Name:</label>
            <input type="text" name="name" id="name" value="{{ old('name', $campaign->name) }}" required>
            @error('name')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="template_content">Message Template:</label>
            <textarea name="template_content" id="template_content" rows="5" required>{{ old('template_content', $campaign->template_content) }}</textarea>
            @error('template_content')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="target_type">Target Type:</label>
            <select name="target_type" id="target_type" required>
                <option value="all" {{ old('target_type', $campaign->target_type) == 'all' ? 'selected' : '' }}>All Contacts</option>
                <option value="group" {{ old('target_type', $campaign->target_type) == 'group' ? 'selected' : '' }}>Specific Group</option>
                <option value="custom" {{ old('target_type') == 'custom' ? 'selected' : '' }}>Custom List (Not implemented)</option>
            </select>
            @error('target_type')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div id="target_group_div" style="display: {{ old('target_type', $campaign->target_type) == 'group' ? 'block' : 'none' }};">
            <label for="target_group_id">Contact Group:</label>
            <select name="target_group_id" id="target_group_id">
                <option value="">Select a Group</option>
                {{-- Assuming $contactGroups is passed from controller --}}
                {{-- @foreach ($contactGroups as $group)
                    <option value="{{ $group->id }}" {{ old('target_group_id', $campaign->target_group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach --}}
            </select>
            @error('target_group_id')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="scheduled_at">Scheduled At (optional):</label>
            <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at', $campaign->scheduled_at ? \Carbon\Carbon::parse($campaign->scheduled_at)->format('Y-m-d\\TH:i') : '') }}">
            <small>Leave blank to send immediately.</small>
            @error('scheduled_at')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <div>
            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="draft" {{ old('status', $campaign->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="scheduled" {{ old('status', $campaign->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="running" {{ old('status', $campaign->status) == 'running' ? 'selected' : '' }}>Running</option>
                <option value="completed" {{ old('status', $campaign->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="failed" {{ old('status', $campaign->status) == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
            @error('status')
                <span style="color: red;">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit">Update Campaign</button>
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