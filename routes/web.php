<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// ── Rutas públicas de fiesta ──
Route::prefix('party')->name('party.')->group(function () {

    Route::get('{qr}', [\App\Http\Controllers\Party\PartyJoinController::class, 'show'])
        ->name('show');

    // GET: mostrar formulario de registro (si no autenticado) o unir directamente (si autenticado)
    Route::get('{qr}/register', [\App\Http\Controllers\Party\PartyJoinController::class, 'register'])
        ->name('register');

    // POST: procesar creación de cuenta + unirse a la fiesta
    Route::post('{qr}/register', [\App\Http\Controllers\Party\PartyJoinController::class, 'store'])
        ->name('store');

    Route::get('{qr}/waiting', [\App\Http\Controllers\Party\PartyJoinController::class, 'waiting'])
        ->middleware('auth')
        ->name('waiting');

    Route::get('{qr}/swipe', [\App\Http\Controllers\Party\PartyJoinController::class, 'swipe'])
        ->middleware('auth')
        ->name('swipe');
        
    Route::post('{qr}/login', [\App\Http\Controllers\Party\PartyJoinController::class, 'login'])
    ->name('login');
});

require __DIR__.'/settings.php';

// ── Rutas de chat ──
Route::middleware(['auth'])->prefix('chats')->name('chats.')->group(function () {
    Route::view('/', 'pages.chat.index')->name('index');
    Route::get('{match}', [\App\Http\Controllers\Chat\ChatController::class, 'show'])
        ->name('show');
});

// ── Push subscriptions ──
Route::middleware(['auth'])->group(function () {
    Route::post('push/subscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'store']);
    Route::post('push/unsubscribe', [\App\Http\Controllers\PushSubscriptionController::class, 'destroy']);
});

// ── Admin ──
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Parties ──
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

        Route::get('parties/{party}/qr', [\App\Http\Controllers\Admin\AdminPartyController::class, 'qr'])
            ->name('parties.qr');

        // ── Users ──
        Route::get('users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])
            ->name('users.index');

        Route::get('users/{user}/edit', [\App\Http\Controllers\Admin\AdminUserController::class, 'edit'])
            ->name('users.edit');

        Route::put('users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'update'])
            ->name('users.update');

        Route::delete('users/{user}', [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])
            ->name('users.destroy');

        Route::patch('users/{user}/toggle-ban', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleBan'])
            ->name('users.toggle-ban');

        Route::patch('users/{user}/toggle-admin', [\App\Http\Controllers\Admin\AdminUserController::class, 'toggleAdmin'])
            ->name('users.toggle-admin');
    });