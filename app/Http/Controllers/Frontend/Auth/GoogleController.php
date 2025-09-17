<?php

namespace App\Http\Controllers\Frontend\Auth;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\GoogleSetting;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Validator,Redirect,Response,File;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Frontend\Controller;

class GoogleController extends Controller
{
    
    public function redirectToGoogle()
    {
        $googleSettings = GoogleSetting::first();

        if (!$googleSettings) {
            return redirect()->route('login')
                ->with('error', 'Google login is not configured. Please contact support.');
        }

        config([
            'services.google.client_id' => $googleSettings->google_client_id,
            'services.google.client_secret' => $googleSettings->google_client_secret,
            'services.google.redirect' => route($googleSettings->google_redirect)
        ]);
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {

            $googleSettings = GoogleSetting::first();

            if (!$googleSettings) {
                return redirect()->route('login')
                    ->with('error', 'Google login is not configured. Please contact support.');
            }

            config([
                'services.google.client_id' => $googleSettings->google_client_id,
                'services.google.client_secret' => $googleSettings->google_client_secret,
                'services.google.redirect' => route($googleSettings->google_redirect)
            ]);

            $googleUser = Socialite::driver('google')->user();
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                if ($existingUser->roles()->count() === 0) {
                    $roleUser = Role::where('name', 'User')->first();
                    if ($roleUser) {
                        $existingUser->assignRole($roleUser);
                    }
                }
                
                $existingUser->active = 'active';
                $existingUser->ip_address = request()->ip();
                $existingUser->last_login_time = Carbon::now();
                $existingUser->save();
                Auth::login($existingUser);
                
                return redirect()->route('home');
            } else {
                $user = new User();
                $user->name = $googleUser->getName();
                $user->email = $googleUser->getEmail();
                $user->password = bcrypt(Str::random(16)); 
                $user->active = 'active';
                $user->ip_address = request()->ip();
                $user->last_login_time = Carbon::now();

                $roleUser = Role::where('name', 'User')->first();
                $user->assignRole($roleUser);

                if ($googleUser->getAvatar()) {
                    try {
                        $avatar = file_get_contents($googleUser->getAvatar());
                        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
                        file_put_contents($tempFile, $avatar);

                       
                        $pathInfo = $this->generateAvatarPath($tempFile);
                        
                       
                        $avatarPath = Storage::disk('public')->putFileAs($pathInfo['path'], new \Illuminate\Http\File($tempFile), $pathInfo['fileName']);
                        $user->avatar = $avatarPath;
                        
                        unlink($tempFile);
                    } catch (\Exception $e) {
                        Log::error('Error processing Google avatar:', ['error' => $e->getMessage()]);
                    }
                }

                $user->save();
                Auth::login($user);

                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            Log::error('Google login error:', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại sau.');
        }
    }

    private function generateAvatarPath($file)
    {
        $now = now();
        $yearMonth = $now->format('Y/m');
        
        $timestamp = $now->timestamp;
        $randomString = substr(md5(uniqid()), 0, 8);
        $extension = 'jpg';
        $fileName = "{$timestamp}_{$randomString}.{$extension}";
        
        return [
            'path' => "avatars/{$yearMonth}",
            'fileName' => $fileName,
            'fullPath' => "avatars/{$yearMonth}/{$fileName}"
        ];
    }
}
