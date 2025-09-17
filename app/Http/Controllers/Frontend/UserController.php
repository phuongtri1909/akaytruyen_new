<?php

namespace App\Http\Controllers\Frontend;

use App\Mail\OTPForgotPWMail;
use Exception;
use App\Models\User;
use App\Mail\OTPMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Mail\OTPUpdateUserMail;

class UserController extends Controller
{
    public function userProfile()
    {
        $user = Auth::user();

        if ($user->active == 'inactive') {
            return redirect()->route('login')->with('error', 'Tài khoản chưa được xác thực');
        }

        return view('Frontend.auth.profile', compact('user'));
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            ], [
                'avatar.required' => 'Hãy chọn ảnh avatar',
                'avatar.image' => 'Avatar phải là ảnh',
                'avatar.mimes' => 'Chỉ chấp nhận ảnh định dạng jpeg, png, jpg, gif, webp',
                'avatar.max' => 'Dung lượng avatar không được vượt quá 4MB'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        }
        $user = Auth::user();

        DB::beginTransaction();
        $imagePath = null;
        try {
            $imageBackup = $user->avatar;

            try {
                $this->validateImageFile($request->avatar);
            } catch (\Exception $validationError) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => $validationError->getMessage()
                ], 422);
            }

            $imageName = $user->id . '_' . time() . '.' . $request->avatar->extension();
            $imagePath = $request->avatar->storeAs('avatars', $imageName, 'public');

            $user->avatar = $imagePath;
            $user->ip_address = $request->ip();
            $user->last_login_time = Carbon::now();
            $user->save();

            DB::commit();

            if ($imageBackup && !str_starts_with($imageBackup, 'uploads/images/avatar/')) {
                if (Storage::disk('public')->exists($imageBackup)) {
                    Storage::disk('public')->delete($imageBackup);
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật avatar thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau'
            ], 500);
        }
    }
    public function updateNameOrPhone(Request $request)
    {

        if ($request->has('name')) {
            try {
                $request->validate([
                    'name' => 'required|string|min:3|max:255',
                ], [
                    'name.required' => 'Hãy nhập tên',
                    'name.string' => 'Tên phải là chuỗi',
                    'name.min' => 'Tên phải có ít nhất 3 ký tự',
                    'name.max' => 'Tên không được vượt quá 255 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->name = $request->name;
                $user->ip_address = $request->ip();
                $user->last_login_time = Carbon::now();
                $user->save();
                return redirect()->route('profile')->with('success', 'Cập nhật tên thành công');
            } catch (\Exception $e) {
                return redirect()->route('profile')->with('error', 'Cập nhật tên thất bại');
            }
        } elseif ($request->has('phone')) {

            try {
                $request->validate([
                    'phone' => 'required|string|min:10|max:10',
                ], [
                    'phone.required' => 'Hãy nhập số điện thoại',
                    'phone.string' => 'Số điện thoại phải là chuỗi',
                    'phone.min' => 'Số điện thoại phải có ít nhất 10 ký tự',
                    'phone.max' => 'Số điện thoại không được vượt quá 10 ký tự'
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->route('profile')->with('error', $e->errors());
            }

            try {
                $user = Auth::user();
                $user->phone = $request->phone;
                $user->ip_address = $request->ip();
                $user->last_login_time = Carbon::now();
                $user->save();
                return redirect()->route('profile')->with('success', 'Cập nhật số điện thoại thành công');
            } catch (\Exception $e) {
                return redirect()->route('profile')->with('error', 'Cập nhật số điện thoại thất bại');
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Dữ liệu không hợp lệ'
            ], 422);
        }
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if ($user->active == 'inactive') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản chưa được xác thực'
            ], 422);
        }

        if ($request->has('otp')) {
            $otp = $request->otp;

            if (!password_verify($otp, $user->key_reset_password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => ['otp' => ['Mã OTP không chính xác để thay đổi mật khẩu']],
                ], 422);
            }

            if ($request->has('password') && $request->has('password_confirmation')) {
                try {
                    $request->validate([
                        'password' => 'required|min:6|confirmed',
                    ], [
                        'password.required' => 'Hãy nhập mật khẩu mới',
                        'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                        'password.confirmed' => 'Mật khẩu xác nhận không khớp',
                    ]);
                } catch (\Illuminate\Validation\ValidationException $e) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $e->errors()
                    ], 422);
                }

                $user->password = bcrypt($request->password);
                $user->key_reset_password = null;
                $user->ip_address = $request->ip();
                $user->last_login_time = Carbon::now();
                $user->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật mật khẩu thành công',
                ], 200);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Xác thực OTP thành công',
            ], 200);
        }

        $otp = $this->generateRandomOTP();
        if ($user->reset_password_at != null) {
            $resetPasswordAt = Carbon::parse($user->reset_password_at);
            if (!$resetPasswordAt->lt(Carbon::now()->subMinutes(3))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'OTP đã được gửi đến Email của bạn, hoặc thử lại sau 3 phút',
                ], 200);
            }
        }

        $user->key_reset_password = bcrypt($otp);
        $user->reset_password_at = now();
        $user->ip_address = $request->ip();
        $user->last_login_time = Carbon::now();
        $user->save();

        Mail::to($user->email)->send(new OTPUpdateUserMail($otp, 'password'));
        return response()->json([
            'status' => 'success',
            'message' => 'Gửi mã OTP thành công, vui lòng kiểm tra Email của bạn',
        ], 200);
    }

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
            throw new \Exception("File extension '$extension' không được phép upload. Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP).");
        }

        $allowedMimes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'
        ];

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimes)) {
            throw new \Exception("File type '$mimeType' không được phép upload. Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP).");
        }

        try {
            $filePath = $file->getRealPath();
            if ($filePath && file_exists($filePath)) {
                $fileSignature = bin2hex(file_get_contents($filePath, false, null, 0, 4));
                
                $validSignatures = [
                    'ffd8ffe0', 'ffd8ffe1', 'ffd8ffe2', 'ffd8ffe3', 'ffd8ffe8', 'ffd8ffdb', 'ffd8ffc0', 'ffd8ffc2',
                    '89504e47', '47494638', '47494639', '52494646'
                ];
                
                if (!in_array($fileSignature, $validSignatures)) {
                    throw new \Exception('File không phải là ảnh hợp lệ. Vui lòng chọn file ảnh thực sự.');
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Cannot validate file signature: ' . $e->getMessage());
        }

        if ($file->getSize() > 4 * 1024 * 1024) {
            throw new \Exception('File quá lớn. Kích thước tối đa là 4MB.');
        }

        try {
            $content = file_get_contents($file->getRealPath());
            if (mb_check_encoding($content, 'UTF-8') && $this->containsPhpCode($content)) {
                throw new \Exception('File chứa mã PHP không được phép upload. Chỉ chấp nhận file ảnh hợp lệ.');
            }
        } catch (\Exception $e) {
            \Log::warning('Cannot read file content for PHP validation: ' . $e->getMessage());
        }
    }

    private function containsPhpCode($content): bool
    {
        $phpPatterns = [
            '/<\?php\s+/i', '/<\?=\s*/i', '/phpinfo\s*\(/i', '/eval\s*\(/i',
            '/exec\s*\(/i', '/system\s*\(/i', '/shell_exec\s*\(/i', '/passthru\s*\(/i',
            '/base64_decode\s*\(/i', '/gzinflate\s*\(/i', '/str_rot13\s*\(/i',
            '/file_get_contents\s*\(/i', '/file_put_contents\s*\(/i', '/fopen\s*\(/i',
            '/fwrite\s*\(/i', '/include\s*\(/i', '/require\s*\(/i',
            '/include_once\s*\(/i', '/require_once\s*\(/i'
        ];

        foreach ($phpPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }
}
