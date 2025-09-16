<?php

namespace App\Http\Controllers\Admin;

use App\Models\Chapter;
use App\Models\Story;
use App\Models\Rating;
use App\Models\User;
use App\Models\Donation;
use App\Repositories\Rating\RatingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected RatingRepositoryInterface $ratingRepository
    ) {
        $this->middleware('can:dashboard')->only('index');
    }

    public function index(Request $request)
    {
        $totalStory = Story::query()->count();
        $totalChapter = Chapter::query()->count();
        $totalViews = Chapter::query()->sum('views');
        $totalRating = User::query()->sum('rating');

        // Donation statistics
        $selectedMonth = $request->query('donation_month', Carbon::now()->month);
        $selectedYear = $request->query('donation_year', Carbon::now()->year);
        
        $donationStats = $this->getDonationStats($selectedMonth, $selectedYear);

        $ratingsDay = $this->ratingRepository->getRatingByType(Rating::TYPE_DAY);
        $arrStoryIdsRatingsDay = $this->getStoryIds(json_decode($ratingsDay->value ?? '', true)) ?? [];
        $storiesDay = $this->ratingRepository->getStories($arrStoryIdsRatingsDay);

        $ratingsMonth = $this->ratingRepository->getRatingByType(Rating::TYPE_MONTH);
        $arrStoryIdsRatingsMonth = $this->getStoryIds(json_decode($ratingsMonth->value ?? '', true)) ?? [];
        $storiesMonth = $this->ratingRepository->getStories($arrStoryIdsRatingsMonth);

        $ratingsAllTime = $this->ratingRepository->getRatingByType(Rating::TYPE_ALL_TIME);
        $arrStoryIdsRatingsAllTime = $this->getStoryIds(json_decode($ratingsAllTime->value ?? '', true)) ?? [];
        $storiesAllTime = $this->ratingRepository->getStories($arrStoryIdsRatingsAllTime);

        $data = [
            'totalStory' => $totalStory,
            'totalChapter' => $totalChapter,
            'ratingsDay' => $ratingsDay,
            'storiesDay' => $storiesDay,
            'ratingsMonth' => $ratingsMonth,
            'storiesMonth' => $storiesMonth,
            'ratingsAllTime' => $ratingsAllTime,
            'storiesAllTime' => $storiesAllTime,
            'totalViews' => $totalViews,
            'totalRating' => $totalRating,
            'donationStats' => $donationStats,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear
        ];

        return view('Admin.pages.dashboard', $data);
    }

    protected function getDonationStats($month, $year)
    {
        // Tổng số donation trong tháng
        $totalDonations = Donation::whereMonth('donated_at', $month)
            ->whereYear('donated_at', $year)
            ->count();

        // Tổng số tiền donation trong tháng
        $totalAmount = Donation::whereMonth('donated_at', $month)
            ->whereYear('donated_at', $year)
            ->sum('amount');

        // Top 10 người donate nhiều nhất trong tháng
        $topDonors = Donation::whereMonth('donated_at', $month)
            ->whereYear('donated_at', $year)
            ->with('story')
            ->get()
            ->groupBy('name')
            ->map(function ($donations) {
                $totalAmount = $donations->sum('amount');
                $firstDonation = $donations->first();
                
                return [
                    'name' => $firstDonation->name,
                    'total_amount' => $totalAmount,
                    'donation_count' => $donations->count()
                ];
            })
            ->sortByDesc('total_amount')
            ->take(10)
            ->values();

        // Thống kê theo story
        $topStories = Donation::whereMonth('donated_at', $month)
            ->whereYear('donated_at', $year)
            ->with('story')
            ->get()
            ->groupBy('story_id')
            ->map(function ($donations) {
                $story = $donations->first()->story;
                return [
                    'story_name' => $story ? $story->name : 'Truyện đã bị xóa',
                    'total_amount' => $donations->sum('amount'),
                    'donation_count' => $donations->count()
                ];
            })
            ->sortByDesc('total_amount')
            ->take(5)
            ->values();

        return [
            'total_donations' => $totalDonations,
            'total_amount' => $totalAmount,
            'top_donors' => $topDonors,
            'top_stories' => $topStories
        ];
    }

    protected function getStoryIds($ratings)
    {
        $result = [];

        if ($ratings) {
            foreach ($ratings as $rating) {
                $result[] = intval($rating['id']);
            }
        }

        return $result;
    }
}
