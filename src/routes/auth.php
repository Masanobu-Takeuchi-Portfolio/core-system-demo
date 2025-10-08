<?php

use App\Http\Controllers\User\Auth\AuthenticatedSessionController;
use App\Http\Controllers\User\Auth\ConfirmablePasswordController;
use App\Http\Controllers\User\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\User\Auth\EmailVerificationPromptController;
use App\Http\Controllers\User\Auth\NewPasswordController;
use App\Http\Controllers\User\Auth\PasswordController;
use App\Http\Controllers\User\Auth\PasswordResetLinkController;
use App\Http\Controllers\User\Auth\RegisteredUserController;
use App\Http\Controllers\User\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\JobController;
use App\Http\Controllers\User\FareController;
use App\Http\Controllers\User\ItemController;

Route::get('/dashboard', function () {
    return view('user.dashboard');
})->middleware(['auth:users', 'verified'])->name('dashboard');

Route::middleware('guest')->group(function () {
    // ドメイン直下のアクセスは一般ユーザーログイン画面に遷移させる
    Route::get('/', [AuthenticatedSessionController::class, 'create']);

    // 一般ユーザー登録のルートはセキュリティのため閉じておく
    // Route::get('register', [RegisteredUserController::class, 'create'])
    //     ->name('register');

    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth:users')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // プロフィール編集ここから
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 出勤簿ここから
    Route::prefix('job') // 頭に job をつける。URLの親パスを指定。/job/
        ->controller(JobController::class) // コントローラ指定(laravel9から)
        ->name('job.') // ルート名
        ->group(function () { // グループ化
            //Route::get('/', 'index')->name('index'); // 名前つきルート
            //Route::get('/{year?}/{month?}', 'index')->name('job');
            Route::get('/', 'index')->name('index');
            Route::get('/{year?}/{month?}', 'index')->name('index.search');
            Route::post('/store/{year?}/{month?}', 'store')->name('store');
            Route::post('/update/{id}', 'update')->name('update');
        });

    // 交通費申請ここから
    Route::prefix('fare') // 頭に job をつける。URLの親パスを指定。/job/
        ->controller(FareController::class) // コントローラ指定(laravel9から)
        ->name('fare.') // ルート名
        ->group(function () { // グループ化
            Route::get('/{year?}/{month?}', 'index')->name('index.search');
            Route::post('/tokyo/store/{year?}/{month?}', 'store')->name('store');
            Route::post('/store/{year?}/{month?}', 'storeOsaka')->name('store.osaka');
            Route::post('/tokyo/update/{id}', 'update')->name('update');
            Route::post('/update/{id}', 'updateOsaka')->name('update.osaka');
        });
    // 物品発注ここから
    Route::prefix('item')
        ->controller(ItemController::class) // コントローラ指定(laravel9から)
        ->name('item.') // ルート名
        ->group(function () { // グループ化
            Route::get('/', 'index')->name('index');
            Route::get('/{year?}/{month?}', 'index')->name('index.search');
            Route::post('/store/{year?}/{month?}', 'store')->name('store');
            Route::post('/update/{id}', 'update')->name('update');
            // Route::get('/{year?}/{month?}', 'index')->name('index.search');
            // Route::post('/tokyo/store/{year?}/{month?}', 'store')->name('store');
            // Route::post('/store/{year?}/{month?}', 'storeOsaka')->name('store.osaka');
            // Route::post('/tokyo/update/{id}', 'update')->name('update');
            // Route::post('/update/{id}', 'updateOsaka')->name('update.osaka');
        });
});
