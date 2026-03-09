<?php

//use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\ShopController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\UserInfoController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\FareController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\SpreadsheetController;
use App\Http\Controllers\Admin\ScrapingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('admin.welcome');
});

Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth:admin', 'verified'])->name('dashboard');

// Route::get('shops', [ShopController::class, 'index']);

// Route::resource('contacts', ContactFormController::class);
//Route::get('contacts', [ContactFormController::class, 'index'])->name('contacts.index');
// グループ化してまとめるとシンプルに書ける
// Route::prefix('contacts') // 頭に contacts をつける。URLの親パスを指定。/contacts/
//     ->middleware(['auth']) // 認証(ログインしていないとアクセスできない)
//     ->controller(ContactFormController::class) // コントローラ指定(laravel9から)
//     ->name('contacts.') // ルート名
//     ->group(function () { // グループ化
//         Route::get('/', 'index')->name('index'); // 名前つきルート
//         Route::get('/create', 'create')->name('create');
//         Route::post('/', 'store')->name('store');
//         Route::get('/{id}', 'show')->name('show');
//         Route::get('/{id}/edit', 'edit')->name('edit');
//         Route::post('/{id}', 'update')->name('update');
//         Route::post('/{id}/destroy', 'destroy')->name('destroy');
//     });

Route::middleware('auth:admin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('/', [AuthenticatedSessionController::class, 'create']);
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

Route::middleware('auth:admin')->group(function () {
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

    //ユーザー一覧
    //Route::get('/user', [UserInfoController::class, 'index'])->name('userinfo.list');
    Route::prefix('userinfo') // 頭に userinfo をつける。URLの親パスを指定。/userinfo/
        ->controller(UserInfoController::class) // コントローラ指定(laravel9から)
        ->name('userinfo.') // ルート名
        ->group(function () { // グループ化
            Route::get('/', 'index')->name('index'); // 名前つきルート
            Route::get('/{id}', 'show')->name('show')->where('id', '[0-9]+'); //whereがないと{id}処理で画面真っ白になる
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit')->where('id', '[0-9]+');
            Route::post('/{id}', 'update')->name('update')->where('id', '[0-9]+');
            Route::post('/{id}/destroy', 'destroy')->name('destroy')->where('id', '[0-9]+');
        });

    // 出勤簿ここから
    Route::prefix('job') // 頭に job をつける。URLの親パスを指定。/job/
        ->controller(JobController::class) // コントローラ指定(laravel9から)
        ->name('job.') // ルート名
        ->group(function () { // グループ化
            Route::get('/', 'index')->name('index'); // 名前つきルート
            Route::get('/search', 'search')->name('search');
            Route::get('/{id}/{year}/{month}/edit', 'edit')->name('edit');
            Route::post('/{id}/{year}/{month}/edit', 'update')->name('update');
        });
    // 交通費明細ここから
    Route::prefix('fare')
        ->controller(FareController::class) // コントローラ指定(laravel9から)
        ->name('fare.') // ルート名
        ->group(function () { // グループ化
            Route::get('/', 'index')->name('index'); // 名前つきルート
            Route::get('/search', 'search')->name('search');
            Route::get('/{id}/{year}/{month}/edit', 'edit')->name('edit');
            Route::post('/{id}/{year}/{month}/edit', 'update')->name('update');
            Route::post('/{id}/download', [SpreadsheetController::class, 'downloadFare'])->name('download');
        });
    // 物品発注ここから
    Route::prefix('item')
        ->controller(ItemController::class) // コントローラ指定(laravel9から)
        ->name('item.') // ルート名
        ->group(function () { // グループ化
            Route::get('/', 'index')->name('index'); // 名前つきルート
            Route::get('/search', 'search')->name('search');
            Route::get('/{id}/{year}/{month}/edit', 'edit')->name('edit');
            // Route::post('/{id}/{year}/{month}/edit', 'update')->name('update');
        });
    // 交通費ダウンロード
    Route::post('/fare/{id}/download', [SpreadsheetController::class, 'downloadFare'])->name('fare.download');
    // 出勤簿ダウンロード
    Route::post('/{id}/download', [SpreadsheetController::class, 'download'])->name('job.download');
    //Route::get('/job/{year?}/{month?}', [JobController::class, 'index'])->name('job');

    // スクレイピング
    Route::prefix('scraping')
        ->controller(ScrapingController::class)
        ->name('scraping.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'execute')->name('execute');
        });
});
