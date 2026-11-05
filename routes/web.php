<?php

declare(strict_types=1);

use App\Http\Controllers\Catalog\BuildingController as CatalogBuildingController;
use App\Http\Controllers\Catalog\HomeController;
use App\Http\Controllers\Catalog\RoomController as CatalogRoomController;
use App\Http\Controllers\Chat\ConversationController as ChatConversationController;
use App\Http\Controllers\Chat\MessageController as ChatMessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\BookingController as TenantBookingController;
use App\Http\Controllers\Tenant\FavoriteController as TenantFavoriteController;
use App\Http\Controllers\Tenant\ReviewController as TenantReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/rooms', [CatalogRoomController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room}', [CatalogRoomController::class, 'show'])->name('rooms.show');
Route::get('/buildings/{building}', [CatalogBuildingController::class, 'show'])->name('buildings.show');

Route::get('/dashboard', function () {
    $user = auth()->user();

    return $user?->isOwner()
        ? redirect()->route('owner.dashboard')
        : redirect()->route('tenant.dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat', [ChatConversationController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatConversationController::class, 'show'])->name('chat.show');
    Route::get('/chat/{conversation}/messages', [ChatConversationController::class, 'messages'])->name('chat.messages.index');
    Route::post('/chat/{conversation}/messages', [ChatMessageController::class, 'store'])
        ->middleware('throttle:30,1')
        ->name('chat.messages.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
});

Route::middleware(['auth', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(base_path('routes/owner.php'));

Route::middleware(['auth', 'role:tenant'])->group(function () {
    Route::post('/rooms/{room}/bookings', [TenantBookingController::class, 'store'])->name('tenant.bookings.store');
    Route::post('/rooms/{room}/reviews', [TenantReviewController::class, 'store'])->name('tenant.reviews.store');
    Route::post('/rooms/{room}/favorite', [TenantFavoriteController::class, 'store'])->name('tenant.favorites.store');
    Route::delete('/rooms/{room}/favorite', [TenantFavoriteController::class, 'destroy'])->name('tenant.favorites.destroy');
    Route::post('/rooms/{room}/chat', [ChatConversationController::class, 'start'])->name('tenant.chat.start');
});

Route::middleware(['auth', 'role:tenant'])
    ->prefix('tenant')
    ->name('tenant.')
    ->group(base_path('routes/tenant.php'));

require __DIR__.'/auth.php';
