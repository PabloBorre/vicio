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

Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('parties', [\App\Http\Controllers\Admin\AdminPartyController::class, 'index'])
            ->name('parties.index');

        Route::get('parties/create', [\App\Http\Controllers\Admin\AdminPartyController::class, 'create'])
            ->name('parties.create');

        Route::post('parties', [\App\Http\Controllers\Admin\AdminPartyController::class, 'store'])
            ->name('parties.store');

        Route::get('parties/{party}/edit', [\App\Http\Controllers\Admin\AdminPartyController::class, 'edit'])
            ->name('parties.edit');

        Route::put('parties/{party}', [\App\Http\Controllers\Admin\AdminPartyController::class, 'update'])
            ->name('parties.update');

        Route::patch('parties/{party}/status', [\App\Http\Controllers\Admin\AdminPartyController::class, 'updateStatus'])
            ->name('parties.status');

        Route::delete('parties/{party}', [\App\Http\Controllers\Admin\AdminPartyController::class, 'destroy'])
            ->name('parties.destroy');
    });
