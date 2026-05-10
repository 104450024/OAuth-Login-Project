<?php 

namespace App\Repositories;
use Illuminate\Support\Facades\Http;


class FBLoginDataRepository{


  
    // 根據 fb 的 api 文件，查詢fb粉絲團按讚資料
    public function PersonalProfile(){

        return Http::get('https://graph.facebook.com/v25.0/me', [
           'fields'         =>  'likes',
           'access_token'   =>  env('FACEBOOK_INFO_TOKEN'),
         ])->json()['likes']['data'] ?? [];

    }
}