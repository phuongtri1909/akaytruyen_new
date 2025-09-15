<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use App\Models\Chapter;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('canAny:xem_danh_sach_binh_luan,xoa_binh_luan,ghim_binh_luan')->only('index');
        $this->middleware('can:xoa_binh_luan')->only(['destroy', 'bulkDelete']);
        $this->middleware('can:ghim_binh_luan')->only('togglePin');
    }

    public function index(Request $request)
    {
        $query = Comment::with(['user', 'chapter.story', 'replies.user'])
            ->whereNull('reply_id') 
            ->orderBy('created_at', 'desc');

        if ($request->filled('story_id')) {
            $query->whereHas('chapter', function($q) use ($request) {
                $q->where('story_id', $request->story_id);
            });
        }

        if ($request->filled('chapter_id')) {
            $query->where('chapter_id', $request->chapter_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $comments = $query->paginate(20);
        
        $stories = Story::select('id', 'name')->orderBy('name')->get();
        $chapters = collect();
        
        if ($request->filled('story_id')) {
            $chapters = Chapter::where('story_id', $request->story_id)
                ->select('id', 'name', 'chapter', 'story_id')
                ->orderBy('chapter')
                ->get();
        }

        return view('Admin.pages.comments.index', compact('comments', 'stories', 'chapters'));
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            
            $comment->replies()->delete();
            
            $comment->delete();

            return redirect()->route('admin.comments.index')->with('success', 'Xóa bình luận thành công!');

        } catch (\Exception $e) {
            \Log::error('Error deleting comment', [
                'error' => $e->getMessage(),
                'comment_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.comments.index')->with('error', 'Có lỗi xảy ra khi xóa bình luận.');
        }
    }

    public function togglePin($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            
            $comment->update([
                'is_pinned' => !$comment->is_pinned,
                'pinned_at' => $comment->is_pinned ? null : now()
            ]);

            $message = $comment->is_pinned ? 'Ghim bình luận thành công!' : 'Bỏ ghim bình luận thành công!';
            
            return redirect()->route('admin.comments.index')->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error toggling comment pin', [
                'error' => $e->getMessage(),
                'comment_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('admin.comments.index')->with('error', 'Có lỗi xảy ra khi thay đổi trạng thái ghim.');
        }
    }

    public function getChaptersByStory($storyId)
    {
        $chapters = Chapter::where('story_id', $storyId)
            ->select('id', 'name', 'chapter')
            ->orderBy('chapter')
            ->get();

        return response()->json($chapters);
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate([
                'comment_ids' => 'required|array',
                'comment_ids.*' => 'integer|exists:comments,id'
            ]);

            $commentIds = $request->comment_ids;
            $deletedCount = 0;

            foreach ($commentIds as $commentId) {
                $comment = Comment::find($commentId);
                if ($comment) {
                    // Xóa tất cả replies của comment này
                    $comment->replies()->delete();
                    
                    // Xóa comment chính
                    $comment->delete();
                    $deletedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Đã xóa thành công {$deletedCount} bình luận.",
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Error bulk deleting comments', [
                'error' => $e->getMessage(),
                'comment_ids' => $request->comment_ids ?? [],
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa bình luận.'
            ], 500);
        }
    }
}
