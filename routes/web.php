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
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\IngredientTransactionController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanKamarController;
use App\Http\Controllers\RoomInfoController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


// ==========================================================
// == MANUAL CAPTCHA ==
// ==========================================================
Route::get('/captcha/generate', function (Request $request) {
    $captcha_code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 5);
    Session::put('captcha_code', strtoupper($captcha_code));

    $image = imagecreate(150, 40);
    $background = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);

    for ($i = 0; $i < 5; $i++) {
        imageline($image, 0, rand() % 40, 150, rand() % 40, $text_color);
    }

    imagestring($image, 5, 40, 12, strtoupper($captcha_code), $text_color);

    ob_start();
    imagepng($image);
    $contents = ob_get_clean();
    imagedestroy($image);

    return response($contents)->header('Content-type', 'image/png');
})->name('captcha.generate');


// ==========================================================
// == ROLE SUPER ==
// ==========================================================
Route::group(['middleware' => ['auth', 'checkRole:Super']], function () {
    Route::resource('user', UserController::class);
});


// ==========================================================
// == ROLE SUPER & ADMIN ==
// ==========================================================
Route::group(['middleware' => ['auth', 'checkRole:Super,Admin']], function () {

    // Upload & Delete Gambar
    Route::post('/room/{room}/image/upload', [ImageController::class, 'store'])->name('image.store');
    Route::delete('/image/{image}', [ImageController::class, 'destroy'])->name('image.destroy');

    // Reservation Flow
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
    Route::resource('facility', FacilityController::class);
    Route::resource('amenity', AmenityController::class);
    Route::resource('ingredient', IngredientController::class);


    // ==========================================================
    // == PENGATURAN RUANG RAPAT ==
    // ==========================================================
    Route::resource('ruangrapat', RuangRapatController::class);

    Route::delete('/rapat/reservasi/{rapatTransaction}/cancel', 
        [RuangRapatController::class, 'cancelReservation'])->name('rapat.transaction.cancel');

    Route::get('/rapat/payments', 
        [RuangRapatController::class, 'paymentHistory'])->name('rapat.payment.index');

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
    // == LAPORAN ==
    // ==========================================================
    Route::name('laporan.')->group(function () {
        Route::get('/laporan/rapat', [LaporanController::class, 'laporanRuangRapat'])->name('rapat.index');
        Route::get('/laporan/rapat/export', [LaporanController::class, 'exportExcel'])->name('rapat.export');

        Route::get('/laporan/kamar', [LaporanKamarController::class, 'index'])->name('kamar.index');
        Route::get('/laporan/kamar/export', [LaporanKamarController::class, 'exportExcel'])->name('kamar.export');
    });

    // Payment
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payment.invoice');
    Route::get('/transaction/{transaction}/payment/create', [PaymentController::class, 'create'])->name('transaction.payment.create');
    Route::post('/transaction/{transaction}/payment/store', [PaymentController::class, 'store'])->name('transaction.payment.store');

    // Charts
    Route::get('/get-dialy-guest-chart-data', [ChartController::class, 'dailyGuestPerMonth']);
    Route::get('/get-dialy-guest/{year}/{month}/{day}', [ChartController::class, 'dailyGuest'])->name('chart.dailyGuest');
});


// ==========================================================
// == ROLE SUPER, ADMIN, CUSTOMER ==
// ==========================================================
Route::group(['middleware' => ['auth', 'checkRole:Super,Admin,Customer']], function () {
    Route::get('/activity-log', [ActivityController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/all', [ActivityController::class, 'all'])->name('activity-log.all');
    Route::resource('user', UserController::class)->only(['show']);

    Route::view('/notification', 'notification.index')->name('notification.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])->name('notification.markAllAsRead');
    Route::get('/notification-to/{id}', [NotificationsController::class, 'routeTo'])->name('notification.routeTo');
});


// ==========================================================
// == AUTH (LOGIN, FORGOT PASSWORD) ==
// ==========================================================
Route::view('/login', 'auth.login')->name('login.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/forgot-password', fn () => view('auth.passwords.email'))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    Route::get('/reset-password/{token}', fn ($token) => view('auth.reset-password', ['token' => $token]))->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});


// ==========================================================
// == ROOM INFO (Monitoring)
// ==========================================================
Route::prefix('room-info')->as('room-info.')->middleware('auth')->group(function () {
    Route::get('/available', [RoomInfoController::class, 'availableRooms'])->name('available');
    Route::get('/reservation', [RoomInfoController::class, 'pendingReservations'])->name('reservation');
    Route::get('/cleaning', [RoomInfoController::class, 'cleaningRooms'])->name('cleaning');
});


// ==========================================================
// == OPERASIONAL (CHECK-IN & CHECK-OUT)  <-- DIPINDAHKAN KE ATAS
// ==========================================================
Route::prefix('transaction')->as('transaction.')->middleware('auth')->group(function () {
    Route::get('/check-in', [CheckinController::class, 'index'])->name('checkin.index');
    Route::get('/check-in/{transaction}/edit', [CheckinController::class, 'edit'])->name('checkin.edit');
    Route::put('/check-in/{transaction}', [CheckinController::class, 'update'])->name('checkin.update');
    Route::delete('/check-in/{transaction}', [CheckinController::class, 'destroy'])->name('checkin.destroy');

    Route::get('/check-out', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/check-out/{transaction}', [CheckoutController::class, 'process'])->name('checkout.process');
});


// ==========================================================
// == RESOURCE TRANSACTION (HARUS DIBAWAH CHECK-IN)
// ==========================================================
Route::resource('transaction', TransactionController::class)->middleware('auth');


// ==========================================================
// == ROOT REDIRECT ==
// ==========================================================
Route::redirect('/', '/dashboard');
