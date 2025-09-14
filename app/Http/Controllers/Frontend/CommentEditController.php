<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Comment;
use App\Models\CommentEditHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommentEditController extends Controller
{
    public function edit(Request $request, $commentId)
    {
        try {
            $comment = Comment::with('user')->findOrFail($commentId);

            // Kiểm tra quyền chỉnh sửa
            if (!Auth::check() || Auth::id() != $comment->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền chỉnh sửa bình luận này'
                ], 403);
            }

            $newContent = $request->input('comment');
            $editReason = $request->input('edit_reason');

            if (empty($newContent) || trim($newContent) === '') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nội dung bình luận không được để trống'
                ], 400);
            }

            // Nếu nội dung không thay đổi
            if (trim($comment->comment) === trim($newContent)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nội dung không có thay đổi'
                ], 400);
            }

            DB::beginTransaction();

            // Lưu lịch sử chỉnh sửa
            CommentEditHistory::create([
                'comment_id' => $comment->id,
                'old_content' => $comment->comment,
                'new_content' => $newContent,
                'edited_by' => Auth::id(),
                'edited_at' => now(),
                'edit_reason' => $editReason
            ]);

            // Cập nhật comment
            $comment->update([
                'comment' => $newContent,
                'is_edited' => true,
                'edited_at' => now(),
                'edited_by' => Auth::id(),
                'edit_count' => $comment->edit_count + 1
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Bình luận đã được cập nhật thành công',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'is_edited' => $comment->is_edited,
                    'edited_at' => $comment->edited_at,
                    'edit_count' => $comment->edit_count
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi cập nhật bình luận'
            ], 500);
        }
    }

    public function getEditHistory($commentId)
    {
        try {
            $comment = Comment::with(['editHistories.editor'])->findOrFail($commentId);

            // Kiểm tra comment có được edit chưa
            if (!$comment->is_edited) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bình luận này chưa được chỉnh sửa'
                ], 404);
            }

            $editHistories = $comment->editHistories()->with('editor')->get();

            return response()->json([
                'status' => 'success',
                'edit_histories' => $editHistories,
                'comment' => $comment
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi lấy lịch sử chỉnh sửa'
            ], 500);
        }
    }

    public function getEditForm($commentId)
    {
        try {
            $comment = Comment::with('user')->findOrFail($commentId);

            // Kiểm tra quyền chỉnh sửa
            if (!Auth::check() || Auth::id() != $comment->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền chỉnh sửa bình luận này'
                ], 403);
            }

            $html = view('Frontend.components.comment-edit-form', compact('comment'))->render();

            return response()->json([
                'status' => 'success',
                'html' => $html
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
