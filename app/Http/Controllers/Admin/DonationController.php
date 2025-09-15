<?php

namespace App\Http\Controllers\Admin;

use App\Models\Donation;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{

    public function __construct()
    {
        $this->middleware('canAny:xem_danh_sach_thanh_vien_donate,them_thanh_vien_donate,sua_thanh_vien_donate,xoa_thanh_vien_donate')->only('index');
        $this->middleware('can:them_thanh_vien_donate')->only('store');
        $this->middleware('can:sua_thanh_vien_donate')->only('update');
        $this->middleware('can:xoa_thanh_vien_donate')->only('destroy');
    }

    public function index(Request $request, $storyId)
    {
        $story = Story::findOrFail($storyId);
        $authUser = Auth::user();   

        $donations = Donation::where('story_id', $storyId)
            ->orderBy('donated_at', 'desc')
            ->paginate(20);

        return view('Admin.pages.donations.index', compact('story', 'donations'));
    }

    public function store(Request $request, $storyId)
    {
        try {
          
            $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|integer|min:1',
                'donated_at' => 'nullable|date',
            ]);

            $donation = Donation::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'story_id' => $storyId,
                'donated_at' => $request->donated_at ?? now(),
            ]);


            return redirect()->route('admin.donations.index', $storyId)->with('success', 'Thêm donation thành công!');

        } catch (\Exception $e) {
            Log::error('Error creating donation', [
                'error' => $e->getMessage(),
                'story_id' => $storyId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm donation.'
            ], 500);
        }
    }

    public function update(Request $request, $donationId)
    {
        try {
            $donation = Donation::with('story')->findOrFail($donationId);
         
            $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|integer|min:1',
                'donated_at' => 'nullable|date',
            ]);

            $donation->update([
                'name' => $request->name,
                'amount' => $request->amount,
                'donated_at' => $request->donated_at ?? $donation->donated_at,
            ]);

            return redirect()->route('admin.donations.index', $donation->story_id)->with('success', 'Cập nhật donation thành công!');

        } catch (\Exception $e) {
            Log::error('Error updating donation', [
                'error' => $e->getMessage(),
                'donation_id' => $donationId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật donation.'
            ], 500);
        }
    }

    public function destroy($donationId)
    {
        try {
            $donation = Donation::with('story')->findOrFail($donationId);
        
            $donation->delete();

            return redirect()->route('admin.donations.index', $donation->story_id)->with('success', 'Xóa donation thành công');

        } catch (\Exception $e) {
            Log::error('Error deleting donation', [
                'error' => $e->getMessage(),
                'donation_id' => $donationId,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.donations.index', $donationId)->with('error', 'Có lỗi xảy ra khi xóa donation.');
        }
    }


}
