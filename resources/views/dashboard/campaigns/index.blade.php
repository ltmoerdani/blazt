<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaigns</title>
</head>
<body>
    <h1>Campaigns</h1>
    <a href="{{ route('dashboard.campaigns.create') }}">Create New Campaign</a>

    @if ($campaigns->isEmpty())
        <p>No campaigns found.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Scheduled At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($campaigns as $campaign)
                    <tr>
                        <td>{{ $campaign->name }}</td>
                        <td>{{ $campaign->status }}</td>
                        <td>{{ $campaign->scheduled_at ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('dashboard.campaigns.show', $campaign->id) }}">View</a> |
                            <a href="{{ route('dashboard.campaigns.edit', $campaign->id) }}">Edit</a> |
                            <form action="{{ route('dashboard.campaigns.destroy', $campaign->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                            @if ($campaign->status === 'draft' || $campaign->status === 'scheduled')
                                <form action="{{ route('dashboard.campaigns.execute', $campaign->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to execute this campaign?')">Execute Now</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $campaigns->links() }}
    @endif
</body>
</html> 