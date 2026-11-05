<?php

declare(strict_types=1);

use App\Http\Controllers\Tenant\BookingController;
use App\Http\Controllers\Tenant\BookingRenewalController;
use App\Http\Controllers\Tenant\BookingTerminationController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\FavoriteController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\MaintenanceRequestController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('bookings/{booking}/renew', BookingRenewalController::class)->name('bookings.renew');
Route::post('bookings/{booking}/termination', BookingTerminationController::class)->name('bookings.termination');

Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');

Route::get('maintenance-requests', [MaintenanceRequestController::class, 'index'])->name('maintenance-requests.index');
Route::get('maintenance-requests/create', [MaintenanceRequestController::class, 'create'])->name('maintenance-requests.create');
Route::post('maintenance-requests', [MaintenanceRequestController::class, 'store'])->name('maintenance-requests.store');
Route::get('maintenance-requests/{maintenanceRequest}', [MaintenanceRequestController::class, 'show'])->name('maintenance-requests.show');
