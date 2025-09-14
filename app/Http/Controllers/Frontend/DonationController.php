<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Donation;
use App\Models\Story;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DonationController extends Controller
{
    public function getStoryDonations(Request $request, $storySlug)
    {
        $story = Story::where('slug', $storySlug)->firstOrFail();

        $selectedMonth = $request->query('month', Carbon::now()->month);
        $selectedYear = $request->query('year', Carbon::now()->year);

        $cacheTtl = now()->addMinutes(5);

        // Lấy danh sách các tháng có donate cho truyện này (tối ưu query)
        $months = Cache::remember("story:{$story->id}:donations:months", $cacheTtl, function () use ($story) {
            return Donation::where('story_id', $story->id)
                ->selectRaw('MONTH(donated_at) as month, YEAR(donated_at) as year')
                ->groupBy('month', 'year')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        });

        // Lấy danh sách donate theo tháng được chọn cho truyện này (tối ưu query)
        $usersDonate = User::where('donate_amount', '>', 0)
            ->whereMonth('updated_at', $selectedMonth)
            ->whereYear('updated_at', $selectedYear)
            ->selectRaw("CAST(name AS CHAR CHARACTER SET utf8mb4) as name, donate_amount, updated_at")
            ->orderBy('donate_amount', 'desc');

        $guestDonate = Donation::where('story_id', $story->id)
            ->whereMonth('donated_at', $selectedMonth)
            ->whereYear('donated_at', $selectedYear)
            ->selectRaw("CAST(name AS CHAR CHARACTER SET utf8mb4) as name, amount as donate_amount, donated_at as updated_at");

        // Gộp hai danh sách lại và lấy toàn bộ dữ liệu (tối ưu query)
        $topDonors = Cache::remember("story:{$story->id}:donors:top:{$selectedYear}-{$selectedMonth}", $cacheTtl, function () use ($usersDonate, $guestDonate) {
            return $usersDonate->union($guestDonate)
                ->orderByDesc('donate_amount')
                ->limit(20) // Giới hạn kết quả để tối ưu performance
                ->get();
        });

        return response()->json([
            'topDonors' => $topDonors,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear
        ]);
    }

    public function store(Request $request, $storySlug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:1',
        ]);

        $story = Story::where('slug', $storySlug)->firstOrFail();

        Donation::create([
            'name' => $request->name,
            'amount' => $request->amount,
            'story_id' => $story->id,
            'donated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Donate thành công!');
    }

    public function destroy($id)
    {
        $donation = Donation::find($id);
        if ($donation) {
            $donation->delete();
            return redirect()->back()->with('success', 'Xóa donate thành công!');
        }
        return redirect()->back()->with('error', 'Không tìm thấy donate!');
    }


}
