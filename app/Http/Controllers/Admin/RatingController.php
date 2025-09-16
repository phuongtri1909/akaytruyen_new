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

            $dataType = [
                1 => 'day',
                2 => 'month', 
                3 => 'all_time'
            ];

            // Empty all ratings
            Rating::truncate();

            foreach ($dataReq as $type => $value) {
                $data = [
                    'status' => Rating::STATUS_ACTIVE
                ];
                $intDataType = array_filter($dataType, function ($item) use ($type) {
                    return $item == $type;
                });
                $data['type'] = array_key_first($intDataType);
                $data['value'] = json_encode($value);
                
                // Save rating
                $rating = new Rating();
                $rating->status = $data['status'];
                $rating->type = $data['type'];
                $rating->value = $data['value'];
                $rating->save();
            }

            return redirect()->route('admin.ratings.index')->with('success', 'Cập nhật xếp hạng thành công!');

        } catch (\Exception $e) {
            \Log::error('Error updating ratings', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return redirect()->route('admin.ratings.index')->with('error', 'Có lỗi xảy ra khi cập nhật xếp hạng.');
        }
    }

    public function destroy($id)
    {
        $this->repository->delete($id);
    }
}
