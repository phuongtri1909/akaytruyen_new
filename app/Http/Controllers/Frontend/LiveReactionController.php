<?php

namespace App\Http\Controllers\Frontend;
use App\Models\LiveComment;
use Illuminate\Http\Request;
use App\Models\LiveReaction;

class LiveReactionController extends Controller
{
    public function live(Request $request, $commentId)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'redirect' => route('login')
            ], 401);
        }

        $request->validate([
            'type' => 'required|in:like,dislike'
        ]);

        $comment = LiveComment::findOrFail($commentId);
        $userId = auth()->id();

        $existing = LiveReaction::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing->type === $request->type) {
                $existing->delete();
                $message = 'Đã hủy phản ứng';
            } else {
                $existing->update(['type' => $request->type]);
                $message = 'Đã cập nhật phản ứng';
            }
        } else {
            LiveReaction::create([
                'comment_id' => $commentId,
                'user_id' => $userId,
                'type' => $request->type
            ]);
            $message = 'Đã thêm phản ứng';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'likes' => $comment->likes()->count(),
            'dislikes' => $comment->dislikes()->count()
        ]);
    }
}
