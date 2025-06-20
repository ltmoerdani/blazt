#!/bin/bash

echo "🔍 Admin QR Endpoint Debug Test"
echo "================================"

# Test database status
echo -e "\n📊 Database Status:"
php artisan tinker --execute="
App\Domain\WhatsApp\Models\WhatsAppAccount::select('id', 'phone_number', 'status', 'user_id')
    ->get()
    ->each(function(\$a) { 
        echo \"ID: {\$a->id}, Phone: {\$a->phone_number}, Status: {\$a->status}, User: {\$a->user_id}\n\"; 
    });
"

# Test Enhanced Handler
echo -e "\n📡 Enhanced Handler Status:"
HANDLER_QR=$(curl -s http://localhost:3001/qr-code/3 | jq -r '.success // false')
echo "QR Code available for ID '3': $HANDLER_QR"

# Test Laravel Service
echo -e "\n🌐 Laravel Service Test:"
php artisan tinker --execute="
\$service = app(App\Services\EnhancedWhatsAppService::class);
\$healthy = \$service->isHealthy();
\$qr = \$service->getQRCode('3');
echo \"Service Healthy: \" . (\$healthy ? 'YES' : 'NO') . \"\n\";
echo \"QR Code Available: \" . (\$qr ? 'YES (' . strlen(\$qr) . ' bytes)' : 'NO') . \"\n\";
"

echo -e "\n✅ Debug Complete!"
echo "Next steps:"
echo "1. Login to admin panel: http://localhost:8000/admin"
echo "2. Go to WhatsApp Accounts"
echo "3. Look for account with status 'connecting'"
echo "4. Click 'Show QR Code' button"
