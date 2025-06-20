<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\WhatsApp\AccountIndex;

Route::view('/', 'welcome');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// WhatsApp Routes
Route::middleware(['auth', 'verified'])->prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('accounts', AccountIndex::class)->name('accounts.index');
});

// Temporary route names for navigation links (will be implemented later)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('campaigns', function () {
        return redirect()->route('dashboard')->with('message', 'Campaigns feature coming soon!');
    })->name('campaigns.index');
    
    Route::get('campaigns/create', function () {
        return redirect()->route('dashboard')->with('message', 'Campaign creation feature coming soon!');
    })->name('campaigns.create');
    
    Route::get('contacts', function () {
        return redirect()->route('dashboard')->with('message', 'Contacts feature coming soon!');
    })->name('contacts.index');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
