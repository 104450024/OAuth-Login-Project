<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;


use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class SocialiteController extends Controller
{

    public function authProviderRedirect($provider) {
        if ($provider) {
            return Socialite::driver($provider)->redirect();
        }
        abort(404);
    }


    public function socialAuthentication($provider) {


        try {

            // facebook 第三方登入
            if ($provider) {
                $socialUser = Socialite::driver($provider)->user();
                


                // $user = User::where('auth_provider_id', $socialUser->id )->first();

                $user = User::where( 'email' , $socialUser->email )->first();


                if ($user) {

                    Auth::login($user);

                } else {

                    // 如果使用者 facebook 帳號 第一次從 facebook 第三方登入
                    // 新增帳號

                    $userData = User::create([
                        'name'             =>  $socialUser->name,
                        'email'            =>  $socialUser->email,
                        'password'         =>  Hash::make('#login_1234'),
                        'auth_provider_id' =>  $socialUser->id,        // fb login id
                        'auth_provider'    =>  $provider,
                    ]);

                    if ($userData) {
                        Auth::login($userData);
                    }
                }

                return redirect()->route('profile.edit');
            }






            abort(404);

        } catch (Exception $e) {
            dd($e);
        }
    }

}