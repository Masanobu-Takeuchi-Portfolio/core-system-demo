<?php
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\User\ProfileController;
// use App\Http\Controllers\User\JobController;
// use App\Http\Controllers\TestController;
// use App\Http\Controllers\ContactFormController;
// use App\Models\ContactForm;
// use App\Http\Controllers\ShopController;
// use App\Http\Controllers\ComponentTestController;
// use App\Http\Controllers\LifeCycleTestController;

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

// Route::get('/', function () {
//     return view('user.welcome');
// });

// Route::get('/dashboard', function () {
//     return view('user.dashboard');
// })->middleware(['auth:users', 'verified'])->name('dashboard');



// Route::get('tests/test', [TestController::class, 'index']);

// Route::get('shops', [ShopController::class, 'index']);

// Route::resource('contacts', ContactFormController::class);
// Route::get('contacts', [ContactFormController::class, 'index'])->name('contacts.index');
// // グループ化してまとめるとシンプルに書ける
// Route::prefix('contacts') // 頭に contacts をつける。URLの親パスを指定。/contacts/
//     ->middleware(['auth']) // 認証(ログインしていないとアクセスできない)
//     ->controller(ContactFormController::class) // コントローラ指定(laravel9から)
//     ->name('contacts.') // ルート名
//     ->group(function () { // グループ化
//         Route::get('/', 'index')->name('index'); // 名前つきルート
//         // Route::get('/create', 'create')->name('create');
//         Route::post('/', 'store')->name('store');
//         Route::get('/{id}', 'show')->name('show');
//         Route::get('/{id}/edit', 'edit')->name('edit');
//         Route::post('/{id}', 'update')->name('update');
//         Route::post('/{id}/destroy', 'destroy')->name('destroy');
//     });

// Route::get('/component-test1', [ComponentTestController::class, 'showComponent1']);
// Route::get('/component-test2', [ComponentTestController::class, 'showComponent2']);
// Route::get('/servicecontainertest', [LifeCycleTestController::class, 'showServiceContainerTest']);

// Route::middleware('auth:users')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__ . '/auth.php';
