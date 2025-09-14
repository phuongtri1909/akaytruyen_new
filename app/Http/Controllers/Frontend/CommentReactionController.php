<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\CommentReaction;

class CommentReactionController extends Controller
{
    public function react(Request $request, $commentId)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'redirect' => route('login')
            ], 401);
        }
    
        $request->validate([
            'type' => 'required|in:like,dislike,haha,tym,angry,sad'
        ]);
    
        $comment = Comment::findOrFail($commentId);
        $userId = auth()->id();
    
        $existing = CommentReaction::where('comment_id', $commentId)
            ->where('user_id', $userId)
            ->first();
    
        if ($existing) {
            if ($existing->type === $request->type) {
                $existing->delete(); // Xóa phản ứng nếu bấm lại cùng loại
                $message = 'Đã hủy phản ứng';
            } else {
                $existing->update(['type' => $request->type]); // Cập nhật phản ứng
                $message = 'Đã thay đổi phản ứng';
            }
        } else {
            CommentReaction::create([
                'comment_id' => $commentId,
                'user_id' => $userId,
                'type' => $request->type
            ]);
            $message = 'Đã thêm phản ứng';
        }
    
        // Cập nhật số lượng reactions cho các loại khác nhau
        $reactionCounts = [
            'likes' => $comment->likes()->count(),
            'dislikes' => $comment->dislikes()->count(),
            'hahas' => $comment->hahas()->count(),
            'tyms' => $comment->tyms()->count(),
            'angrys' => $comment->angrys()->count(),
            'sads' => $comment->sads()->count(),
        ];
    
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'reactionCounts' => $reactionCounts // Trả về tất cả số lượng reactions
        ]);
    }
    
    
    
}
