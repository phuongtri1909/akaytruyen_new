<?php

namespace App\Http\Controllers\Admin;

use App\Models\Donation;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DonationController extends Controller
{
    public function index(Request $request, $storyId)
    {
        $story = Story::findOrFail($storyId);
        $authUser = Auth::user();

        // Kiểm tra quyền: Admin hoặc tác giả của truyện
        if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $story->author_id !== $authUser->id) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $donations = Donation::where('story_id', $storyId)
            ->orderBy('donated_at', 'desc')
            ->paginate(20);

        return view('Admin.pages.donations.index', compact('story', 'donations'));
    }

    public function store(Request $request, $storyId)
    {
        try {
            $story = Story::findOrFail($storyId);
            $authUser = Auth::user();

            // Kiểm tra quyền: Admin hoặc tác giả của truyện
            if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $story->author_id !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện hành động này.'
                ], 403);
            }

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


            return response()->json([
                'success' => true,
                'message' => 'Thêm donation thành công!',
                'donation' => $donation
            ]);

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
            $story = $donation->story;
            $authUser = Auth::user();

            // Kiểm tra quyền: Admin hoặc tác giả của truyện
            if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $story->author_id !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện hành động này.'
                ], 403);
            }

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

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật donation thành công!',
                'donation' => $donation
            ]);

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
            $story = $donation->story;
            $authUser = Auth::user();

            // Kiểm tra quyền: Admin hoặc tác giả của truyện
            if (!$authUser->hasRole('Admin') && !$authUser->hasRole('Content') && $story->author_id !== $authUser->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện hành động này.'
                ], 403);
            }

            $donation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa donation thành công!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting donation', [
                'error' => $e->getMessage(),
                'donation_id' => $donationId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa donation.'
            ], 500);
        }
    }


}
