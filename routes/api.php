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
    Route::get('contact-groups/{group}', [ContactController::class, 'showGroup']);
    Route::put('contact-groups/{group}', [ContactController::class, 'updateGroup']);
    Route::delete('contact-groups/{group}', [ContactController::class, 'destroyGroup']);
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
        Route::get('accounts/{account}', [\App\Http\Controllers\API\WhatsAppController::class, 'show']);
        Route::put('accounts/{account}', [\App\Http\Controllers\API\WhatsAppController::class, 'update']);
        Route::delete('accounts/{account}', [\App\Http\Controllers\API\WhatsAppController::class, 'destroy']);
        Route::post('accounts/{account}/connect', [\App\Http\Controllers\API\WhatsAppController::class, 'connect']);
        Route::post('accounts/{account}/disconnect', [\App\Http\Controllers\API\WhatsAppController::class, 'disconnect']);
        Route::get('accounts/{account}/qr-code', [\App\Http\Controllers\API\WhatsAppController::class, 'getQRCode']);
        Route::post('send-message', [\App\Http\Controllers\API\WhatsAppController::class, 'sendMessage']);
    });

    // Campaign API Routes
    Route::prefix('campaigns')->group(function () {
        Route::get('/', [\App\Http\Controllers\API\CampaignController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\API\CampaignController::class, 'store']);
        Route::get('{campaign}', [\App\Http\Controllers\API\CampaignController::class, 'show']);
        Route::put('{campaign}', [\App\Http\Controllers\API\CampaignController::class, 'update']);
        Route::delete('{campaign}', [\App\Http\Controllers\API\CampaignController::class, 'destroy']);
        Route::post('{campaign}/execute', [\App\Http\Controllers\API\CampaignController::class, 'execute']);
        Route::get('{campaign}/analytics', [\App\Http\Controllers\API\CampaignController::class, 'analytics']);
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