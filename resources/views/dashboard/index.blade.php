<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome to your Dashboard, {{ $user->name }}!</h1>

    <h2>Summary</h2>
    <p>Total Campaigns: {{ $summary['totalCampaigns'] ?? 0 }}</p>
    <p>Active Campaigns: {{ $summary['activeCampaigns'] ?? 0 }}</p>
    <p>Total Contacts: {{ $summary['totalContacts'] ?? 0 }}</p>
    <p>Total Messages Sent: {{ $summary['totalMessagesSent'] ?? 0 }}</p>
    <p>Total WhatsApp Accounts: {{ $summary['totalWhatsAppAccounts'] ?? 0 }}</p>

    <p>This is your main dashboard page. More features will be added here.</p>

    <h3>Quick Links:</h3>
    <ul>
        <li><a href="{{ route('dashboard.campaigns.index') }}">Manage Campaigns</a></li>
        <li><a href="{{ route('dashboard.contacts.index') }}">Manage Contacts</a></li>
        <li><a href="{{ route('dashboard.whatsapp-accounts.index') }}">Manage WhatsApp Accounts</a></li>
        <li><a href="{{ route('dashboard.settings.index') }}">Settings</a></li>
    </ul>
</body>
</html> 