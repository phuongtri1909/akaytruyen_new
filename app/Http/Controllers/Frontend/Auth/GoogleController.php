<?php

namespace App\Http\Controllers\Frontend\Auth;
use App\Http\Controllers\Frontend\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Validator,Redirect,Response,File;
use Socialite;
class GoogleController extends Controller
{
    
    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    // Chuyển hướng đến Google
    public function redirectToGoogle($provider)
    {
        return \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->redirect();
    }

    // Xử lý phản hồi từ Google
    public function handleGoogleCallback($provider)

    {

        $getInfo = \Laravel\Socialite\Facades\Socialite::driver($provider)->stateless()->user();
     
    $user = $this->createUser($getInfo,$provider);
 
    auth()->login($user);
 
    return redirect()->to('/home');
    }

    function createUser($getInfo,$provider){
 
        $user = User::where('provider_id', $getInfo->id)->first();
        
        if (!$user) {
            $user = User::create([
               'name'     => $getInfo->name,
               'email'    => $getInfo->email,
               'provider' => $provider,
               'provider_id' => $getInfo->id
           ]);
         }
         return $user;
       }
}
