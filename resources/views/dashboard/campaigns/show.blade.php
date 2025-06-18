<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Details</title>
</head>
<body>
    <h1>Campaign Details: {{ $campaign->name }}</h1>

    <div>
        <strong>ID:</strong> {{ $campaign->id }}
    </div>
    <div>
        <strong>WhatsApp Account ID:</strong> {{ $campaign->whatsapp_account_id }}
    </div>
    <div>
        <strong>User ID:</strong> {{ $campaign->user_id }}
    </div>
    <div>
        <strong>Name:</strong> {{ $campaign->name }}
    </div>
    <div>
        <strong>Template Content:</strong>
        <pre>{{ $campaign->template_content }}</pre>
    </div>
    <div>
        <strong>Target Type:</strong> {{ $campaign->target_type }}
    </div>
    @if ($campaign->target_group_id)
        <div>
            <strong>Target Group ID:</strong> {{ $campaign->target_group_id }}
        </div>
    @endif
    <div>
        <strong>Status:</strong> {{ $campaign->status }}
    </div>
    <div>
        <strong>Scheduled At:</strong> {{ $campaign->scheduled_at ?? 'N/A' }}
    </div>
    <div>
        <strong>Created At:</strong> {{ $campaign->created_at }}
    </div>
    <div>
        <strong>Updated At:</strong> {{ $campaign->updated_at }}
    </div>

    <a href="{{ route('dashboard.campaigns.edit', $campaign->id) }}">Edit Campaign</a>
    <form action="{{ route('dashboard.campaigns.destroy', $campaign->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Are you sure?')">Delete Campaign</button>
    </form>
    @if ($campaign->status === 'draft' || $campaign->status === 'scheduled')
        <form action="{{ route('dashboard.campaigns.execute', $campaign->id) }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" onclick="return confirm('Are you sure you want to execute this campaign?')">Execute Now</button>
        </form>
    @endif

    <a href="{{ route('dashboard.campaigns.index') }}">Back to Campaigns</a>
</body>
</html> 