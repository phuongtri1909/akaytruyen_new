<?php

namespace App\Http\Controllers\Frontend;

use App\Models\LiveComment;
use Illuminate\Http\Request;

class LiveChatController extends Controller
{


    public function togglePin($commentId)
    {
        $comment = LiveComment::findOrFail($commentId);

        if (!auth()->user()->isAdmin() || $comment->level !== 0) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if (!$comment->is_pinned) {
            $pinnedCount = LiveComment::where('is_pinned', true)->count();
            if ($pinnedCount >= 10000) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã đạt giới hạn số bình luận được ghim'
                ], 400);
            }
        }

        $comment->is_pinned = !$comment->is_pinned;
        $comment->pinned_at = $comment->is_pinned ? now() : null;
        $comment->save();

        // Get updated comments
        $pinnedComments = LiveComment::with(['user', 'replies.user', 'reactions'])
            ->whereNull('reply_id')
            ->where('is_pinned', true)
            ->latest()
            ->get();

            

        $regularComments = LiveComment::with(['user', 'replies.user', 'reactions'])
            ->whereNull('reply_id')
            ->where('is_pinned', false)
            ->latest()
            ->paginate(10);

        $html = view('Frontend.sections.components.comments-list', compact('pinnedComments', 'regularComments'))->render();

        return response()->json([
            'status' => 'success',
            'message' => $comment->is_pinned ? 'Đã ghim bình luận' : 'Đã bỏ ghim bình luận',
            'is_pinned' => $comment->is_pinned,
            'html' => $html
        ]);
    }

    public function deleteComment($comment)
    {
        $authUser = auth()->user();
        $comment = LiveComment::with('user')->find($comment);

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy bình luận này' . $comment
            ], 404);
        }

        // Admin can delete all comments
        if ($authUser->role === 'admin') {
            $comment->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa bình luận thành công'
            ]);
        }

        // Mod can delete except admin comments
        if ($authUser->role === 'mod') {
            if ($comment->user && $comment->user->role === 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể xóa bình luận của Admin'
                ], 403);
            }
            $comment->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa bình luận thành công'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Không có quyền thực hiện'
        ], 403);
    }

    public function storeClient(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để bình luận'
            ], 401);
        }

        $user = auth()->user();
        if ($user->ban->comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản của bạn đã bị cấm bình luận'
            ], 403);
        }

        $request->validate([
            'comment' => 'required|max:700',
            'reply_id' => 'nullable|exists:comments,id'
        ]);

        $parentComment = null;
        if ($request->reply_id) {
            $parentComment = LiveComment::find($request->reply_id);
            if ($parentComment && $parentComment->level >= 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể trả lời bình luận này'
                ], 403);
            }
        }

        $comment = LiveComment::create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'reply_id' => $request->reply_id,
            'level' => $request->reply_id ? ($parentComment->level + 1) : 0
        ]);

        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm bình luận',
            'html' => view('Frontend.sections.components.comments-item', compact('comment'))->render()
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $userId = $request->user;
        $authUser = auth()->user();

        $query = LiveComment::with(['user', 'replies']);

        // If mod, only show user and vip comments
        if ($authUser->role === 'mod') {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', ['user', 'vip']);
            });
        }

        if ($search) {
            $query->where('comment', 'like', '%' . $search . '%');
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $comments = $query->orderBy('id', 'desc')->paginate(15);

        $usersQuery = \App\Models\User::whereHas('comments')
            ->where('active', 'active');

        if ($authUser->role === 'mod') {
            $usersQuery->whereIn('role', ['user', 'vip']);
        }

        $users = $usersQuery->orderBy('name')->get();

        $totalComments = LiveComment::count();

        return view('admin.pages.comments.index', compact('comments', 'users', 'totalComments'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($comment)
    {
        $authUser = auth()->user();
        $comment = LiveComment::find($comment);
        if (!$comment) {
            return redirect()->route('admin.comment.index')->with('error', 'Không tìm thấy bình luận này');
        }

        if (
            $authUser->role === 'admin' ||
            ($authUser->role === 'mod' && (!$comment->user || $comment->user->role !== 'admin'))
        ) {
            $comment->delete();
            return redirect()->route('admin.comment.index')->with('success', 'Xóa bình luận thành công');
        }

        return redirect()->route('admin.comment.index')->with('error', 'Không thể xóa bình luận của Admin');
    }
}
