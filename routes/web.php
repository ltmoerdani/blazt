<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\CampaignController;
use App\Http\Controllers\Dashboard\ContactController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\WhatsAppAccountController;

// Define route path constant to avoid duplication
const CONTACTS_GROUP_ROUTE = 'contacts/groups/{group}';

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
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
