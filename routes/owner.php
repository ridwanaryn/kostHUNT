<?php

declare(strict_types=1);

use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\BuildingController;
use App\Http\Controllers\Owner\BuildingTenantController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\IncomeReportController;
use App\Http\Controllers\Owner\InvoiceController;
use App\Http\Controllers\Owner\InvoicePaymentController;
use App\Http\Controllers\Owner\MaintenanceRequestController;
use App\Http\Controllers\Owner\PhotoController;
use App\Http\Controllers\Owner\RoomController;
use App\Http\Controllers\Owner\TerminationDecisionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('owner.buildings.index'))->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('buildings', BuildingController::class);
Route::get('buildings/{building}/tenants', [BuildingTenantController::class, 'show'])->name('buildings.tenants');

Route::get('reports/income', [IncomeReportController::class, 'index'])->name('reports.income');

Route::get('buildings/{building}/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
Route::post('buildings/{building}/rooms', [RoomController::class, 'store'])->name('rooms.store');

Route::get('rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
Route::get('rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
Route::patch('rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
Route::patch('rooms/{room}/toggle-active', [RoomController::class, 'toggleActive'])->name('rooms.toggle-active');

Route::post('buildings/{building}/photos', [PhotoController::class, 'storeBuilding'])->name('buildings.photos.store');
Route::post('rooms/{room}/photos', [PhotoController::class, 'storeRoom'])->name('rooms.photos.store');
Route::patch('photos/{photo}/cover', [PhotoController::class, 'setCover'])->name('photos.cover');
Route::delete('photos/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');

Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
Route::post('bookings/{booking}/termination/approve', [TerminationDecisionController::class, 'approve'])->name('bookings.termination.approve');
Route::post('bookings/{booking}/termination/reject', [TerminationDecisionController::class, 'reject'])->name('bookings.termination.reject');

Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::post('invoices/{invoice}/mark-paid', InvoicePaymentController::class)->name('invoices.mark-paid');

Route::get('maintenance-requests', [MaintenanceRequestController::class, 'index'])->name('maintenance-requests.index');
Route::patch('maintenance-requests/{maintenanceRequest}', [MaintenanceRequestController::class, 'update'])->name('maintenance-requests.update');
