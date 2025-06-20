<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test user model compatibility
echo "Testing User Model Compatibility...\n";

try {
    // Test App\Models\User
    $appUser = \App\Models\User::first();
    if (!$appUser) {
        echo "No users found in App\Models\User\n";
        exit(1);
    }
    
    echo "App User ID: " . $appUser->id . "\n";
    echo "App User Email: " . $appUser->email . "\n";
    
    // Test App\Domain\User\Models\User
    $domainUser = \App\Domain\User\Models\User::find($appUser->id);
    if (!$domainUser) {
        echo "Could not find corresponding domain user\n";
        exit(1);
    }
    
    echo "Domain User ID: " . $domainUser->id . "\n";
    echo "Domain User Email: " . $domainUser->email . "\n";
    
    // Test WhatsApp accounts relationship
    echo "Testing WhatsApp accounts relationship...\n";
    $whatsappAccounts = $domainUser->whatsappAccounts;
    echo "WhatsApp accounts count: " . $whatsappAccounts->count() . "\n";
    
    echo "✅ User model compatibility test passed!\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
