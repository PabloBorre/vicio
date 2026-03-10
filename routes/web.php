<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// ── Rutas públicas de fiesta (no requieren auth) ──
Route::prefix('party')->name('party.')->group(function () {

    // Escaneo del QR → muestra info de la fiesta y botón de registro
    Route::get('{qr}', [\App\Http\Controllers\Party\PartyJoinController::class, 'show'])
        ->name('show');

    // Registro en la fiesta (formulario multi-paso)
    Route::get('{qr}/register', [\App\Http\Controllers\Party\PartyJoinController::class, 'register'])
        ->name('register');

    // Sala de espera / countdown
    Route::get('{qr}/waiting', [\App\Http\Controllers\Party\PartyJoinController::class, 'waiting'])
        ->middleware('auth')
        ->name('waiting');

    // Swipe (se activará en paso 5)
    Route::get('{qr}/swipe', [\App\Http\Controllers\Party\PartyJoinController::class, 'swipe'])
        ->middleware('auth')
        ->name('swipe');
});

require __DIR__.'/settings.php';
// ── Rutas de chat ──
Route::middleware(['auth'])->prefix('chats')->name('chats.')->group(function () {
    Route::view('/', 'pages.chat.index')->name('index');
    Route::get('{match}', [\App\Http\Controllers\Chat\ChatController::class, 'show'])
        ->name('show');
});
