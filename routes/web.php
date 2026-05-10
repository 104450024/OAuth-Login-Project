<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\SocialiteController;
use Illuminate\Support\Facades\Route;



// 起始畫面 
// resources\views\facebook_google_login_view.blade.php
Route::get('/', function () {
    return view('facebook_google_login_view');
});


// 登入 FaceBook OR Google 的 API
Route::controller(SocialiteController::class)->group(function() {


    // 登入重定向畫面
    Route::get('auth/redirection/{provider}', 'authProviderRedirect')->name('auth.redirection');

    // 登入驗證
    Route::get('auth/{provider}/callback', 'socialAuthentication')->name('auth.callback');

});

Route::middleware('auth')->group(function () {


    // google OR facebook 的 user profile
    // fb 的按讚粉絲團數量 ( Q1 )
    // 上傳多張照片至 google drive 內指定資料夾 ( Q2 )

    // resources\views\profile\edit.blade.php
    // resources\views\profile\partials\fb_users_likes.blade.php  ( Q1 )
    // resources\views\profile\partials\google_upload_images.blade.php  ( Q2 )

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');


    // 上傳多張照片至 google drive 內指定資料夾
    Route::post('/profile', [ProfileController::class, 'GoogleUploadImage'])->name('profile.GoogleUploadImage');


});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
