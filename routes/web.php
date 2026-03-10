<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// ── Rutas públicas de fiesta ──
Route::prefix('party')->name('party.')->group(function () {
    Route::get('{qr}', [\App\Http\Controllers\Party\PartyJoinController::class, 'show'])
        ->name('show');
    Route::get('{qr}/register', [\App\Http\Controllers\Party\PartyJoinController::class, 'register'])
        ->name('register');
    Route::get('{qr}/waiting', [\App\Http\Controllers\Party\PartyJoinController::class, 'waiting'])
        ->middleware('auth')
        ->name('waiting');
    Route::get('{qr}/swipe', [\App\Http\Controllers\Party\PartyJoinController::class, 'swipe'])
        ->middleware('auth')
        ->name('swipe');
});

// ── Rutas de chat ──
Route::middleware(['auth'])->prefix('chats')->name('chats.')->group(function () {
    Route::view('/', 'pages.chat.index')->name('index');
    Route::get('{match}', [\App\Http\Controllers\Chat\ChatController::class, 'show'])
        ->name('show');
});

require __DIR__.'/settings.php';