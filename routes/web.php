<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;

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


// 登入 FaceBook OR Google 的 API
Route::controller(SocialiteController::class)->group(function() {

    Route::get('auth/redirection/{provider}', 'authProviderRedirect')->name('auth.redirection');
    Route::get('auth/{provider}/callback', 'socialAuthentication')->name('auth.callback');

});

// 起始畫面
Route::get('/', function () {
    return view('facebook_google_login_view');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {


    // google OR facebook 的 user profile
    // fb 的按讚粉絲團數量 ( Q1 )
    // 上傳多張照片至 google drive 內指定資料夾 ( Q2 )
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');


    // 上傳多張照片至 google drive 內指定資料夾
    Route::post('/profile', [ProfileController::class, 'GoogleUploadImage'])->name('profile.GoogleUploadImage');


});

require __DIR__.'/auth.php';
