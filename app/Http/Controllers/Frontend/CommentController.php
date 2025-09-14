<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Chapter;
use App\Models\UserTagged;
use Spatie\Permission\Models\Role;

class CommentController extends Controller
{


    public function togglePin($commentId)
    {
        $comment = Comment::findOrFail($commentId);


        if (!auth()->user()->hasRole('Admin')) {
            return response()->json(['status' => 'error', 'message' => 'Bạn không có quyền ghim bình luận'], 403);
        }

        if (!$comment->is_pinned) {
            $pinnedCount = Comment::where('is_pinned', true)->count();
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


        $pinnedComments = Comment::with(['user', 'replies.user', 'reactions'])
            ->whereNull('reply_id')
            ->where('is_pinned', true)
            ->latest()
            ->get();

        $regularComments = Comment::with(['user', 'replies.user', 'reactions'])
            ->whereNull('reply_id')
            ->where('is_pinned', false)
            ->latest()
            ->paginate(10);

        $html = view('Frontend.components.comments-list', compact('pinnedComments', 'regularComments'))->render();

        return response()->json([
            'status' => 'success',
            'message' => $comment->is_pinned ? 'Đã ghim bình luận' : 'Đã bỏ ghim bình luận',
            'is_pinned' => $comment->is_pinned,
            'html' => $html
        ]);
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
            'chapter_id' => 'required|string|exists:chapters,id',
            'reply_id' => 'nullable|integer|exists:comments,id',
        ]);

        $level = 0;
        $parentComment = null;
        if ($request->reply_id) {
            $parentComment = Comment::find($request->reply_id);
            if ($parentComment && $parentComment->level >= 2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể trả lời bình luận này',
                ], 403);
            }
        }

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'chapter_id' => $request->chapter_id,
            'comment' => $request->comment,
            'reply_id' => $request->reply_id,
            'level' => $request->reply_id ? ($parentComment->level + 1) : 0
        ]);
        $comment->chapter_id = $request->chapter_id;
        $comment->user_id = auth()->id();
        $comment->comment = $request->comment;
        $comment->save();
        $taggedUsernames = [];

        preg_match_all('/@([\p{L}\p{N}_\-\x{00C0}-\x{017F}]+(?:\s[\p{L}\p{N}_\-\x{00C0}-\x{017F}]+)*)/u', $comment->comment, $matches);

        if (!empty($matches[1])) {
            $taggedUsernames = array_unique($matches[1]);

            foreach ($taggedUsernames as $tag) {
                $chapterId = $request->chapter_id;
                if (!is_numeric($chapterId)) {
                    \Log::error("Invalid chapter_id: $chapterId for comment ID: {$comment->id}");
                    continue;
                }

                $role = Role::where('name', $tag)->first();

                if ($role) {
                    $usersWithRole = \App\Models\User::role($role->name)->get();

                    foreach ($usersWithRole as $user) {
                        if ($user->id !== auth()->id()) {
                            UserTagged::create([
                                'user_id'    => $user->id,
                                'tagged_by'  => auth()->id(),
                                'comment_id' => $comment->id,
                                'chapter_id' => $chapterId,
                            ]);
                        }
                    }
                } else {
                    $taggedUser = \App\Models\User::where('name', 'like', "$tag%")->first();

                    if ($taggedUser && $taggedUser->id !== auth()->id()) {
                        UserTagged::create([
                            'user_id'    => $taggedUser->id,
                            'tagged_by'  => auth()->id(),
                            'comment_id' => $comment->id,
                            'chapter_id' => $chapterId,
                        ]);
                    }
                }
            }
        }

        $comment->load('user');

        return response()->json([
            'status' => 'success',
            'message' => 'Đã thêm bình luận',
            'comment' => $comment->load('user'),
            'html' => view('Frontend.components.comments-item', compact('comment'))->render()
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

        $query = Comment::with(['user', 'replies']);


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

        $totalComments = Comment::count();

        return view('admin.pages.comments.index', compact('comments', 'users', 'totalComments'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $authUser = auth()->user();
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy bình luận này'], 404);
        }

        if (
            $authUser->hasRole('Admin') || $authUser->hasRole('Content') ||
            ($authUser->hasRole('Mod') && (!$comment->user || !$comment->user->hasRole('Admin')))
        ) {
            $comment->delete();
            return response()->json(['status' => 'success', 'message' => 'Xóa bình luận thành công']);
        }

        return response()->json(['status' => 'error', 'message' => 'Không thể xóa bình luận của Admin'], 403);
    }

    /**
     * Load more comments for pagination
     */
    public function loadMoreComments(Request $request)
    {
        $request->validate([
            'chapter_id' => 'required|string|exists:chapters,id',
            'page' => 'required|integer|min:1',
            'is_pinned' => 'boolean'
        ]);

        $chapterId = $request->chapter_id;
        $page = $request->page;
        $isPinned = $request->boolean('is_pinned', false);
        $perPage = 10;

        $query = Comment::with([
            'user:id,name,email,avatar',
            'user.roles:id,name,guard_name',
            'editor:id,name,email,avatar',
            'editHistories.editor:id,name,email,avatar',
            'replies' => function ($query) {
                $query->with([
                    'user:id,name,email,avatar',
                    'user.roles:id,name,guard_name',
                    'editor:id,name,email,avatar',
                    'editHistories.editor:id,name,email,avatar',
                    'reactions:id,comment_id,type,user_id',
                    'replies' => function ($subQuery) {
                        $subQuery->with([
                            'user:id,name,email,avatar',
                            'user.roles:id,name,guard_name',
                            'editor:id,name,email,avatar',
                            'editHistories.editor:id,name,email,avatar',
                            'reactions:id,comment_id,type,user_id'
                        ])->oldest();
                    }
                ])->oldest();
            },
            'reactions:id,comment_id,type,user_id'
        ])
            ->where('chapter_id', $chapterId)
            ->whereNull('reply_id')
            ->where('is_pinned', $isPinned);

        $comments = $query->latest()->paginate($perPage, ['*'], 'page', $page);


        $html = '';
        if ($comments->count() > 0) {
            foreach ($comments as $comment) {
                $html .= view('Frontend.components.comments-item', compact('comment'))->render();
            }
        }

        return response()->json([
            'status' => 'success',
            'html' => $html,
            'hasMore' => $comments->hasMorePages(),
            'currentPage' => $comments->currentPage(),
            'lastPage' => $comments->lastPage(),
            'count' => $comments->count()
        ]);
    }


    public function xoa($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['error' => 'Bình luận không tồn tại!'], 404);
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Bình luận đã được xóa!');
    }


    public function delete(Comment $comment)
    {
        if (!$comment) {
            return redirect()->route('chapter')->with('error', 'Không tìm thấy bình luận này');
        }

        $authUser = auth()->user();

        if ($authUser->role === 'Admin' || ($authUser->role === 'Mod' && (!$comment->user || $comment->user->role !== 'Admin'))) {
            $comment->delete();
            return redirect()->route('chapter')->with('success', 'Xóa bình luận thành công');
        }

        if (!$comment->chapter) {
            return redirect()->back()->with('error', 'Bình luận này không thuộc chương nào.');
        }

        return redirect()->route('chapter', [
            'slugStory' => $comment->chapter->story->slug ?? '',
            'slugChapter' => $comment->chapter->slug ?? ''
        ])->with('success', 'Xóa bình luận thành công');
    }
}
