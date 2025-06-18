<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\AnalyticsController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\Webhook\WhatsAppWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint
Route::get('/v1/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Blazt WhatsApp SaaS API is running',
        'timestamp' => now()->toISOString(),
        'services' => [
            'database' => 'connected',
            'whatsapp_service' => 'running',
            'queue' => 'active'
        ]
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Contact API Routes
    Route::apiResource('contacts', ContactController::class);
    Route::get('contact-groups', [ContactController::class, 'indexGroups']);
    Route::post('contact-groups', [ContactController::class, 'storeGroup']);
    Route::get(CONTACT_GROUP_ROUTE, [ContactController::class, 'showGroup']);
    Route::put(CONTACT_GROUP_ROUTE, [ContactController::class, 'updateGroup']);
    Route::delete(CONTACT_GROUP_ROUTE, [ContactController::class, 'destroyGroup']);
    Route::post('contacts/import', [ContactController::class, 'importContacts']);

    // Analytics API Routes
    Route::get('analytics/dashboard-summary', [AnalyticsController::class, 'getDashboardSummary']);
    Route::get('analytics/campaign-performance/{campaignId}', [AnalyticsController::class, 'getCampaignPerformance']);

    // Payment API Routes
    Route::post('payment/checkout-session', [PaymentController::class, 'createCheckoutSession']);
    Route::get('payment/subscription', [PaymentController::class, 'getUserSubscription']);
    Route::post('payment/cancel-subscription', [PaymentController::class, 'cancelSubscription']);
    Route::post('payment/update-payment-method', [PaymentController::class, 'updatePaymentMethod']);

    // WhatsApp API Routes
    Route::prefix('whatsapp')->group(function () {
        Route::get('accounts', [\App\Http\Controllers\API\WhatsAppController::class, 'index']);
        Route::post('accounts', [\App\Http\Controllers\API\WhatsAppController::class, 'store']);
        Route::get(ACCOUNTS_ACCOUNT_ROUTE, [\App\Http\Controllers\API\WhatsAppController::class, 'show']);
        Route::put(ACCOUNTS_ACCOUNT_ROUTE, [\App\Http\Controllers\API\WhatsAppController::class, 'update']);
        Route::delete(ACCOUNTS_ACCOUNT_ROUTE, [\App\Http\Controllers\API\WhatsAppController::class, 'destroy']);
        Route::post('accounts/{account}/connect', [\App\Http\Controllers\API\WhatsAppController::class, 'connect']);
        Route::post('accounts/{account}/disconnect', [\App\Http\Controllers\API\WhatsAppController::class, 'disconnect']);
        Route::get('accounts/{account}/qr-code', [\App\Http\Controllers\API\WhatsAppController::class, 'getQRCode']);
        Route::post('send-message', [\App\Http\Controllers\API\WhatsAppController::class, 'sendMessage']);
    });

    // Campaign API Routes
    Route::prefix('campaigns')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\CampaignController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\API\CampaignController::class, 'store']);
        Route::get(CAMPAIGN_ROUTE, [\App\Http\Controllers\API\CampaignController::class, 'show']);
        Route::put(CAMPAIGN_ROUTE, [\App\Http\Controllers\API\CampaignController::class, 'update']);
        Route::delete(CAMPAIGN_ROUTE, [\App\Http\Controllers\API\CampaignController::class, 'destroy']);
        Route::post(CAMPAIGN_ROUTE . '/execute', [\App\Http\Controllers\API\CampaignController::class, 'execute']);
        Route::get(CAMPAIGN_ROUTE . '/analytics', [\App\Http\Controllers\API\CampaignController::class, 'analytics']);
    });
});

// Webhook Routes (Publicly Accessible)
Route::prefix('webhooks')->group(function () {
    Route::prefix('whatsapp')->group(function () {
        Route::post('message-received', [WhatsAppWebhookController::class, 'messageReceived']);
        Route::post('message-status', [WhatsAppWebhookController::class, 'messageStatus']);
        Route::post('session-status', [WhatsAppWebhookController::class, 'sessionStatus']);
        Route::post('qr-generated', [WhatsAppWebhookController::class, 'qrGenerated']);
        Route::post('error', [WhatsAppWebhookController::class, 'errorReport']);
    });
    
    Route::prefix('payment')->group(function () {
        Route::post('stripe', [\App\Http\Controllers\Webhook\PaymentWebhookController::class, 'stripe']);
        Route::post('midtrans', [\App\Http\Controllers\Webhook\PaymentWebhookController::class, 'midtrans']);
    });
});

// Health Check Routes
Route::get('health', function () {
    return response()->json([
        'status' => 'OK',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0',
        'environment' => app()->environment(),
    ]);
});

// Define route path constants to avoid duplication
const CONTACT_GROUP_ROUTE = 'contact-groups/{group}';
const ACCOUNTS_ACCOUNT_ROUTE = 'accounts/{account}';
const CAMPAIGN_ROUTE = '{campaign}';
