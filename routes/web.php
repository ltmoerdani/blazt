<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CampaignController;
use App\Http\Controllers\Dashboard\ContactController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\WhatsAppAccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;

// Define route path constant to avoid duplication
if (!defined('CONTACTS_GROUP_ROUTE')) {
    define('CONTACTS_GROUP_ROUTE', 'contacts/groups/{group}');
}

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard.index');
    }
    return view('welcome');
});

// Guest routes (authentication)
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [ProfileController::class, 'updatePassword'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Profile routes
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Campaign Routes
    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/execute', [CampaignController::class, 'execute'])->name('campaigns.execute');

    // Contact Routes
    Route::resource('contacts', ContactController::class);
    Route::get('contacts/groups', [ContactController::class, 'indexGroups'])->name('contacts.groups.index');
    Route::get('contacts/groups/create', [ContactController::class, 'createGroup'])->name('contacts.groups.create');
    Route::post('contacts/groups', [ContactController::class, 'storeGroup'])->name('contacts.groups.store');
    Route::get(CONTACTS_GROUP_ROUTE, [ContactController::class, 'showGroup'])->name('contacts.groups.show');
    Route::get(CONTACTS_GROUP_ROUTE . '/edit', [ContactController::class, 'editGroup'])->name('contacts.groups.edit');
    Route::put(CONTACTS_GROUP_ROUTE, [ContactController::class, 'updateGroup'])->name('contacts.groups.update');
    Route::delete(CONTACTS_GROUP_ROUTE, [ContactController::class, 'destroyGroup'])->name('contacts.groups.destroy');
    Route::post('contacts/import', [ContactController::class, 'importContacts'])->name('contacts.import');

    // Settings Routes
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::put('settings/password', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    Route::put('settings/ai-configuration', [SettingsController::class, 'updateAIConfiguration'])->name('settings.updateAIConfiguration');

    // WhatsApp Account Routes
    Route::resource('whatsapp-accounts', WhatsAppAccountController::class);
    Route::post('whatsapp-accounts/{whatsAppAccount}/connect', [WhatsAppAccountController::class, 'connect'])->name('whatsapp-accounts.connect');
    Route::post('whatsapp-accounts/{whatsAppAccount}/disconnect', [WhatsAppAccountController::class, 'disconnect'])->name('whatsapp-accounts.disconnect');
});
