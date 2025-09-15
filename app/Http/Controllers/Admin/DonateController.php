<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Donate;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;

class DonateController extends Controller
{
    public function __construct()
    {
        $this->middleware('canAny:xem_danh_sach_thong_tin_donate,them_thong_tin_donate,sua_thong_tin_donate,xoa_thong_tin_donate')->only('index');
        $this->middleware('can:them_thong_tin_donate')->only('store');
        $this->middleware('can:sua_thong_tin_donate')->only('update');
        $this->middleware('can:xoa_thong_tin_donate')->only('destroy');
    }
    /**
     * Hiển thị form quản lý donate cho truyện
     */
    public function index($storyId)
    {
        $story = Story::with('donates')->findOrFail($storyId);
        $authUser = Auth::user();

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


            $request->validate([
                'bank_name' => 'required|string|max:255',
                'donate_info' => 'nullable|string',
                'image' => 'nullable|file|max:2048'
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
                $validation = Helper::validateImageFile($image);
                if (!$validation['valid']) {
                    return response()->json(['success' => false, 'message' => $validation['message']], 422);
                }
                
                $imageName = time() . '_' . $this->sanitizeFileName($image->getClientOriginalName());
                $path = $image->storeAs('donates', $imageName, 'public');
                $data['image'] = $path;
            }

            $donate = Donate::create($data);

            return redirect()->route('admin.donate.index', $storyId)->with('success', 'Thêm thông tin donate thành công');
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


        $request->validate([
            'bank_name' => 'required|string|max:255',
            'donate_info' => 'nullable|string',
            'image' => 'nullable|file|max:2048'
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
                $validation = Helper::validateImageFile($image);
                if (!$validation['valid']) {
                    return response()->json(['success' => false, 'message' => $validation['message']], 422);
                }
                
                $imageName = time() . '_' . $this->sanitizeFileName($image->getClientOriginalName());
                $path = $image->storeAs('donates', $imageName, 'public');
                $data['image'] = $path;
            }

        $donate->update($data);

        return redirect()->route('admin.donate.index', $donate->story_id)->with('success', 'Cập nhật thông tin donate thành công');
    }

    /**
     * Xóa donate
     */
    public function destroy($donateId)
    {
        $donate = Donate::with('story')->findOrFail($donateId);
        $authUser = Auth::user();

        // Xóa ảnh nếu có
        if ($donate->image && Storage::disk('public')->exists($donate->image)) {
            Storage::disk('public')->delete($donate->image);
        }

        $donate->delete();

       return redirect()->route('admin.donate.index', $donate->story_id)->with('success', 'Xóa thông tin donate thành công');
    }


    private function sanitizeFileName($filename)
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        $filename = substr($filename, 0, 100);
        return $filename;
    }
}
