<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Donate;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DonateController extends Controller
{
    /**
     * Hiển thị form quản lý donate cho truyện
     */
    public function index($storyId)
    {
        $story = Story::with('donates')->findOrFail($storyId);
        $authUser = Auth::user();

        // Kiểm tra quyền: chỉ Admin hoặc tác giả của truyện mới được xem
        if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $story->author_id !== $authUser->id) {
            abort(403, 'Bạn không có quyền truy cập trang này');
        }

        return view('Admin.pages.donates.index', compact('story', 'authUser'));
    }

    /**
     * Lưu donate mới
     */
    public function store(Request $request, $storyId)
    {
        try {

            $story = Story::findOrFail($storyId);
            $authUser = Auth::user();

            // Kiểm tra quyền
            if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $story->author_id !== $authUser->id) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này']);
            }

            $request->validate([
                'bank_name' => 'required|string|max:255',
                'donate_info' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            $data = [
                'story_id' => $storyId,
                'bank_name' => $request->bank_name,
                'donate_info' => $request->donate_info
            ];

            // Xử lý upload ảnh
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Validate file security
                $this->validateImageFile($image);
                
                $imageName = time() . '_' . $this->sanitizeFileName($image->getClientOriginalName());
                $path = $image->storeAs('donates', $imageName, 'public');
                $data['image'] = $path;
            }

            $donate = Donate::create($data);

            return response()->json(['success' => true, 'message' => 'Thêm thông tin donate thành công']);
        } catch (\Exception $e) {
            \Log::error('Error in donate store', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    /**
     * Cập nhật donate
     */
    public function update(Request $request, $donateId)
    {
        $donate = Donate::with('story')->findOrFail($donateId);
        $authUser = Auth::user();

        // Kiểm tra quyền
        if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $donate->story->author_id !== $authUser->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này']);
        }

        $request->validate([
            'bank_name' => 'required|string|max:255',
            'donate_info' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'bank_name' => $request->bank_name,
            'donate_info' => $request->donate_info
        ];

                    // Xử lý upload ảnh mới
            if ($request->hasFile('image')) {
                // Xóa ảnh cũ nếu có
                if ($donate->image && Storage::disk('public')->exists($donate->image)) {
                    Storage::disk('public')->delete($donate->image);
                }

                $image = $request->file('image');
                
                // Validate file security
                $this->validateImageFile($image);
                
                $imageName = time() . '_' . $this->sanitizeFileName($image->getClientOriginalName());
                $path = $image->storeAs('donates', $imageName, 'public');
                $data['image'] = $path;
            }

        $donate->update($data);

        return response()->json(['success' => true, 'message' => 'Cập nhật thông tin donate thành công']);
    }

    /**
     * Xóa donate
     */
    public function destroy($donateId)
    {
        $donate = Donate::with('story')->findOrFail($donateId);
        $authUser = Auth::user();

        // Kiểm tra quyền
        if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $donate->story->author_id !== $authUser->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thực hiện hành động này']);
        }

        // Xóa ảnh nếu có
        if ($donate->image && Storage::disk('public')->exists($donate->image)) {
            Storage::disk('public')->delete($donate->image);
        }

        $donate->delete();

        return response()->json(['success' => true, 'message' => 'Xóa thông tin donate thành công']);
    }

    /**
     * Validate image file security
     */
    private function validateImageFile($file)
    {
        // Kiểm tra extension nguy hiểm
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
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'
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

    private function sanitizeFileName($filename)
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        $filename = substr($filename, 0, 100);
        return $filename;
    }
}
