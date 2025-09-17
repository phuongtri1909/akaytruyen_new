<?php

namespace App\Http\Controllers\Admin;

use App\Models\Rating;
use App\Repositories\Rating\RatingRepositoryInterface;
use App\Repositories\Story\StoryRepositoryInterface;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function __construct(
        protected RatingRepositoryInterface $repository,
        protected StoryRepositoryInterface $storyRepository
    )
    {
        $this->middleware('can:xem_danh_sach_danh_gia')->only('index');
        $this->middleware('can:sua_danh_gia')->only('update');
    }

    public function index(Request $request)
    {
        $stories = $this->storyRepository->getStoriesActive();
        $ratingsDay = $this->repository->getRatingByType(Rating::TYPE_DAY);
        $ratingsMonth = $this->repository->getRatingByType(Rating::TYPE_MONTH);
        $ratingsAllTime = $this->repository->getRatingByType(Rating::TYPE_ALL_TIME);
        
        return view('Admin.pages.rating.index', compact('stories', 'ratingsDay', 'ratingsMonth', 'ratingsAllTime'));
    }

    public function store(Request $request)
    {
        $attributes = $request->all();
        $item       = $this->repository->create($attributes);
    }

    public function show($id)
    {
        $item = $this->repository->find($id);
    }

    public function edit($id)
    {
        $item = $this->repository->find($id);
    }

    public function update(Request $request)
    {
        try {
            $dataReq = $request->input();

            $typeMapping = [
                'day' => Rating::TYPE_DAY,      // 1
                'month' => Rating::TYPE_MONTH,  // 2
                'all_time' => Rating::TYPE_ALL_TIME // 3
            ];

            foreach ($dataReq as $typeKey => $value) {
                if (!isset($typeMapping[$typeKey])) {
                    continue; // Skip invalid types
                }

                $typeId = $typeMapping[$typeKey];
                
                // Find existing rating or create new one
                $rating = Rating::where('type', $typeId)->first();
                
                if (!$rating) {
                    $rating = new Rating();
                    $rating->type = $typeId;
                    $rating->status = Rating::STATUS_ACTIVE;
                }
                
                $rating->value = json_encode($value);
                $rating->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật xếp hạng thành công!'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating ratings', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật xếp hạng.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
    }
}
