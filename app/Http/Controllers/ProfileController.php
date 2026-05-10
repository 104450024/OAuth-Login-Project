<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Repositories\FBLoginDataRepository;


use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {

        $auth_provider = \Auth::user()->auth_provider;

        $FB_users_like_data = [];

        if( $auth_provider == 'facebook' ){

            $FB_users_like_data = ( new FBLoginDataRepository )->PersonalProfile() ?? [];
            
        }



        return view('profile.edit', [

            'user'                 => $request->user(),
            'FB_users_like_data'   => $FB_users_like_data ?? [],           // 使用者有按過讚的粉絲專業有哪些   
            'provider'             => \Auth::user()->auth_provider ?? '',  // user login by fb or google
        ]);
    }


    // 上傳多張圖片至 google drive API
    public function GoogleUploadImage( Request $request ){

         $request->validate([
            'photo' => 'required|image'
        ]);

        $client = new Client();

        $client->setAuthConfig(
            storage_path('app/google/service-account.json')
        );

        $client->addScope(Drive::DRIVE);

        $drive = new Drive($client);


        $folderId = env('GOOGLE_UPLOAD_FOLDER_ID');

        $photo = $request->file('photo');


        $metadata = new DriveFile([
            'name'    => time().'_'.$photo->getClientOriginalName(),
            'parents' => [$folderId]
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload
        |--------------------------------------------------------------------------
        */

        $file = $drive->files->create(
            $metadata,
            [
                'data'              => file_get_contents( $photo->getRealPath() ),
                'mimeType'          => $photo->getMimeType(),
                'uploadType'        => 'multipart',
                'fields'            => 'id',
                'supportsAllDrives' => true
            ]
        );

        return response()->json([
            'success' => true,
            'file_id' => $file->id
        ]);

    }
}
