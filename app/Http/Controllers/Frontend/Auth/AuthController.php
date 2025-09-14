<?php

namespace App\Http\Controllers\Frontend\Auth;
use App\Http\Controllers\Frontend\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\OTPForgotPWMail;
use Illuminate\Support\Facades\Auth;
use App\Mail\OTPMail;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller

{

    public function register(Request $request)
    {
        if ($request->has('email') && $request->has('password')) {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
                'name' => 'required|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp',
            ], [
                'email.required' => 'Hãy nhập email của bạn vào đi',
                'email.email' => 'Email bạn nhập không hợp lệ rồi',
                'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'name.required' => 'Hãy nhập tên của bạn vào đi',
                'name.max' => 'Tên của bạn quá dài rồi',
                'avatar.image' => 'Ảnh bạn chọn không hợp lệ',
                'avatar.mimes' => 'Ảnh bạn chọn phải có định dạng jpeg, png, jpg, gif, svg, webp',
            ]);

            $user = User::where('email', $request->email)->first();
            if ($user) {
                return redirect()->back()
                    ->withErrors(['email' => 'Email này đã tồn tại, hãy dùng email khác'])
                    ->withInput();
            }

            try {
                $user = new User();
                $user->email = $request->email;
                $user->name = $request->name;
                $user->password = bcrypt($request->password);
                $user->active = 'active';

                // Xử lý ảnh đại diện nếu có
                if ($request->hasFile('avatar')) {
                    $avatar = $request->file('avatar');
                    
                    // Validate file security
                    $this->validateImageFile($avatar);
                    
                    $imageName = time() . '.' . $avatar->extension();
                    $avatarPath = $avatar->storeAs('avatars', $imageName, 'public');
                    $user->avatar = $avatarPath;
                }

                $user->save();
                $user->assignRole('User');

                Auth::login($user);

                return redirect()->route('home')->with('success', 'Đăng ký thành công! Chào mừng bạn.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Đã xảy ra lỗi. Vui lòng thử lại sau.' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Dữ liệu không hợp lệ');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Hãy nhập email của bạn vào đi',
            'email.email' => 'Email bạn nhập không hợp lệ rồi',
            'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember'); // Kiểm tra xem người dùng có chọn "Remember Me" không

        if (Auth::attempt($credentials, $remember)) {
            return redirect()->route('home'); // Điều hướng sau khi đăng nhập thành công
        }

        try {

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            if ($user->active == 'inactive') {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            if (!password_verify($request->password, $user->password)) {
                return redirect()->back()->withInput()->withErrors([
                    'email' => 'Thông tin xác thực không chính xác',
                ]);
            }

            Auth::login($user);

            $user->ip_address = $request->ip();
            $user->save();

            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Đã xảy ra lỗi trong quá trình đăng nhập. Vui lòng thử lại sau.');
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route(('home'));
    }

    public function forgotPassword(Request $request)
    {
        if ($request->has('email')) {
            try {
                $request->validate([
                    'email' => 'required|email',
                ], [
                    'email.required' => 'Hãy nhập email của bạn vào đi',
                    'email.email' => 'Email bạn nhập không hợp lệ rồi',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->errors()
                ], 422);
            }

            try {
                $user = User::where('email', $request->email)->first();
                if (!$user || $user->active == 'inactive') {
                    return response()->json([
                        'status' => 'error',
                        'message' => ['email' => ['Thông tin xác thực không chính xác']],
                    ], 422);
                }



                if ($request->has('email') && $request->has('otp')) {

                    try {
                        $request->validate([
                            'otp' => 'required',
                        ], [
                            'otp.required' => 'Hãy nhập mã OTP của bạn vào đi',
                        ]);
                    } catch (\Illuminate\Validation\ValidationException $e) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $e->errors()
                        ], 422);
                    }

                    if (!password_verify($request->otp, $user->key_reset_password)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => ['otp' => ['Mã OTP không chính xác']],
                        ], 422);
                    }

                    if ($request->has('email') && $request->has('otp') && $request->has('password')) {
                        try {
                            $request->validate([
                                'password' => 'required|min:6',
                            ], [
                                'password.required' => 'Hãy nhập mật khẩu của bạn vào đi',
                                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                            ]);
                        } catch (\Illuminate\Validation\ValidationException $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => $e->errors()
                            ], 422);
                        }

                        try {

                            $user->key_reset_password = null;
                            $user->password = bcrypt($request->password);
                            $user->save();

                            Auth::login($user);

                            return response()->json([
                                'status' => 'success',
                                'message' => 'Đặt lại mật khẩu thành công',
                                'url' => route('home'),
                            ]);
                        } catch (Exception $e) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                                'error' => $e->getMessage(),
                            ], 500);
                        }
                    }

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Hãy nhập mật khẩu mới của bạn',
                    ], 200);
                }

                if ($user->reset_password_at != null) {
                    $resetPasswordAt = Carbon::parse($user->reset_password_at);
                    if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Dùng lại OTP đã gửi trước đó, nhận OTP mới sau 3 phút',
                        ], 200);
                    }
                }

                $randomOTPForgotPW = $this->generateRandomOTP();
                $user->key_reset_password = bcrypt($randomOTPForgotPW);
                $user->reset_password_at = Carbon::now();
                $user->save();

                Mail::to($user->email)->send(new OTPForgotPWMail($randomOTPForgotPW));
                return response()->json([
                    'status' => 'success',
                    'message' => 'Hãy kiểm tra email của bạn để lấy mã OTP',
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã xảy ra lỗi trong quá trình đặt lại mật khẩu. Vui lòng thử lại sau.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }

    public function changePassword() {}

    private function validateImageFile($file)
    {
        $dangerousExtensions = [
            'php', 'php3', 'php4', 'php5', 'php7', 'phtml', 'phar',
            'asp', 'aspx', 'ashx', 'asmx', 'jsp', 'jspx',
            'pl', 'py', 'rb', 'sh', 'bash', 'exe', 'bat', 'cmd', 'com',
            'js', 'vbs', 'wsf', 'htaccess', 'htpasswd', 'ini', 'log', 'sql',
            'dll', 'so', 'dylib'
        ];

        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $dangerousExtensions)) {
            return response()->json([
                'success' => false,
                'error' => 'File không được phép upload.',
                'message' => "File extension '$extension' không được phép upload. Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)."
            ], 200);
        }

        $allowedMimes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'
        ];

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimes)) {
            return response()->json([
                'success' => false,
                'error' => 'File không được phép upload.',
                'message' => "File type '$mimeType' không được phép upload. Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)."
            ], 200);
        }

        if ($extension === 'png' || $extension === 'jpg' || $extension === 'jpeg' || $extension === 'gif' || $extension === 'webp') {
            return;
        }

        $content = file_get_contents($file->getRealPath());
        if ($this->containsPhpCode($content)) {
            return response()->json([
                'success' => false,
                'error' => 'File không được phép upload.',
                'message' => 'File chứa mã PHP không được phép upload. Chỉ chấp nhận file ảnh hợp lệ.'
            ], 200);
        }
    }

    private function containsPhpCode($content): bool
    {
        $phpPatterns = [
            '/<\?php/i', '/<\?=/i', '/<\?/i',
            '/phpinfo\s*\(/i', '/eval\s*\(/i', '/exec\s*\(/i',
            '/system\s*\(/i', '/shell_exec\s*\(/i', '/passthru\s*\(/i',
            '/base64_decode\s*\(/i', '/gzinflate\s*\(/i', '/str_rot13\s*\(/i',
            '/file_get_contents\s*\(/i', '/file_put_contents\s*\(/i',
            '/fopen\s*\(/i', '/fwrite\s*\(/i', '/include\s*\(/i',
            '/require\s*\(/i', '/include_once\s*\(/i', '/require_once\s*\(/i'
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
}
