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

        if ($user->ban_rate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản của bạn đã bị cấm đánh giá'
            ], 403);
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5'
        ]);

        $user->rating = $request->rating;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Đã cập nhật đánh giá',
            'rating' => $request->rating
        ]);
    }
}
