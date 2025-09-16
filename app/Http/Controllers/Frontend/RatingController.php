<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    public function storeClient(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng đăng nhập để đánh giá'
            ], 401);
        }

        $user = auth()->user();

        if ($user->userBan && $user->userBan->rate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản của bạn đã bị cấm đánh giá'
            ], 403);
        }

        try {
            $request->validate([
                'rating' => 'required|integer|between:1,5'
            ], [
                'rating.required' => 'Vui lòng chọn đánh giá',
                'rating.integer' => 'Đánh giá phải là số nguyên',
                'rating.between' => 'Đánh giá phải từ 1 đến 5 sao'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()['rating'][0] ?? 'Dữ liệu không hợp lệ'
            ], 422);
        }

        $user->rating = $request->rating;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đánh giá thành công!',
            'rating' => $request->rating
        ]);
    }
}
