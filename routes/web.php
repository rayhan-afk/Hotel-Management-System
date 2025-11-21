<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomStatusController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionRoomReservationController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RuangRapatController;
use App\Http\Controllers\RuangRapatReservationController; 
use App\Http\Controllers\LaporanController;
use \App\Http\Controllers\AmenityController;
use App\Http\Controllers\IngredientController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth', 'checkRole:Super']], function () {
    Route::resource('user', UserController::class);
});

Route::group(['middleware' => ['auth', 'checkRole:Super,Admin']], function () {
    Route::post('/room/{room}/image/upload', [ImageController::class, 'store'])->name('image.store');
    Route::delete('/image/{image}', [ImageController::class, 'destroy'])->name('image.destroy');

    Route::name('transaction.reservation.')->group(function () {
        Route::get('/createIdentity', [TransactionRoomReservationController::class, 'createIdentity'])->name('createIdentity');
        Route::get('/pickFromCustomer', [TransactionRoomReservationController::class, 'pickFromCustomer'])->name('pickFromCustomer');
        Route::post('/storeCustomer', [TransactionRoomReservationController::class, 'storeCustomer'])->name('storeCustomer');
        Route::get('/{customer}/viewCountPerson', [TransactionRoomReservationController::class, 'viewCountPerson'])->name('viewCountPerson');
        Route::get('/{customer}/chooseRoom', [TransactionRoomReservationController::class, 'chooseRoom'])->name('chooseRoom');
        Route::get('/{customer}/{room}/{from}/{to}/confirmation', [TransactionRoomReservationController::class, 'confirmation'])->name('confirmation');
        Route::post('/{customer}/{room}/payDownPayment', [TransactionRoomReservationController::class, 'payDownPayment'])->name('payDownPayment');
    });

    Route::resource('customer', CustomerController::class);
    Route::resource('type', TypeController::class);
    Route::resource('room', RoomController::class);
    Route::resource('roomstatus', RoomStatusController::class);
    Route::resource('transaction', TransactionController::class);
    Route::resource('facility', FacilityController::class);
    Route::resource('amenity', AmenityController::class);
    Route::resource('ingredient', IngredientController::class);

    // ==========================================================
    // == PENGATURAN RUANG RAPAT ==
    // ==========================================================
    
    // 1. Rute utama untuk Manajemen Paket (AJAX DataTable) & List Reservasi
    Route::resource('ruangrapat', RuangRapatController::class);

    // 2. Rute untuk CANCEL reservasi
    Route::delete('/rapat/reservasi/{rapatTransaction}/cancel', [RuangRapatController::class, 'cancelReservation'])
          ->name('rapat.transaction.cancel');

    // 3. Rute untuk RIWAYAT PEMBAYARAN Rapat
    Route::get('/rapat/payments', [RuangRapatController::class, 'paymentHistory'])->name('rapat.payment.index');

    // 4. GRUP RUTE UNTUK ALUR 4-LANGKAH
    Route::group(['prefix' => 'rapat/reservasi', 'as' => 'rapat.reservation.'], function () {
        Route::get('/step-1', [RuangRapatReservationController::class, 'showStep1_CustomerInfo'])->name('showStep1');
        Route::post('/step-1', [RuangRapatReservationController::class, 'storeStep1_CustomerInfo'])->name('storeStep1');
        Route::get('/step-2', [RuangRapatReservationController::class, 'showStep2_TimeInfo'])->name('showStep2');
        Route::post('/step-2', [RuangRapatReservationController::class, 'storeStep2_TimeInfo'])->name('storeStep2');
        Route::get('/step-3', [RuangRapatReservationController::class, 'showStep3_PaketInfo'])->name('showStep3');
        Route::post('/step-3', [RuangRapatReservationController::class, 'storeStep3_PaketInfo'])->name('storeStep3');
        Route::get('/step-4', [RuangRapatReservationController::class, 'showStep4_Confirmation'])->name('showStep4');
        Route::post('/bayar', [RuangRapatReservationController::class, 'processPayment'])->name('processPayment');
        Route::get('/cancel', [RuangRapatReservationController::class, 'cancelReservation'])->name('cancel');
    });
    
    // ==========================================================
    // == AKHIR PENGATURAN RUANG RAPAT ==
    // ==========================================================

    // ==========================================================
// == RUTE LAPORAN BARU (SIMPAN DI SINI) ==
// ==========================================================
Route::name('laporan.')->group(function () {
    // Laporan Ruang Rapat
    // Rute ini sudah benar. Controller (Langkah 4) akan menangani AJAX di rute ini.
    Route::get('/laporan/rapat', [LaporanController::class, 'laporanRuangRapat'])->name('rapat.index');
    Route::get('/laporan/rapat/export', [LaporanController::class, 'exportExcel'])->name('rapat.export');
        
    // Laporan Kamar Hotel (Stub)
    Route::get('/laporan/kamar', [LaporanController::class, 'laporanKamarHotel'])->name('kamar.index');
});

    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payment.invoice');
    
    Route::get('/transaction/{transaction}/payment/create', [PaymentController::class, 'create'])->name('transaction.payment.create');
    
    Route::post('/transaction/{transaction}/payment/store', [PaymentController::class, 'store'])->name('transaction.payment.store');
    
    Route::get('/get-dialy-guest-chart-data', [ChartController::class, 'dailyGuestPerMonth']);
    Route::get('/get-dialy-guest/{year}/{month}/{day}', [ChartController::class, 'dailyGuest'])->name('chart.dailyGuest');

});

Route::group(['middleware' => ['auth', 'checkRole:Super,Admin,Customer']], function () {
    Route::get('/activity-log', [ActivityController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/all', [ActivityController::class, 'all'])->name('activity-log.all');
    Route::resource('user', UserController::class)->only([
        'show',
    ]);

    Route::view('/notification', 'notification.index')->name('notification.index');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])->name('notification.markAllAsRead');

    Route::get('/notification-to/{id}', [NotificationsController::class, 'routeTo'])->name('notification.routeTo');
});

// Login routes
Route::view('/login', 'auth.login')->name('login.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Forgot Password routes
Route::group(['middleware' => 'guest'], function () {
    Route::get('/forgot-password', fn () => view('auth.passwords.email'))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    // Reset Password routes
    Route::get('/reset-password/{token}', fn (string $token) => view('auth.reset-password', ['token' => $token]))
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::redirect('/', '/dashboard');