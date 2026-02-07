<?php

namespace App\Http\Controllers\Admin;

use Purifier;
use App\Models\User;
use App\Models\Story;
use App\Helpers\Helper;
use App\Models\Chapter;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:them_chuong')->only('create', 'store', 'bulkCreate', 'bulkStore', 'checkExisting');
        $this->middleware('can:sua_chuong')->only('edit', 'update', 'toggleStatus');
        $this->middleware('canAny:them_chuong,sua_chuong')->only('uploadImage');
    }

    public function create(Request $request)
    {
        $storyId = $request->get('story_id');
        if (!$storyId) {
            return redirect()->route('admin.stories.index')->with('error', 'Vui lòng chọn truyện để thêm chương.');
        }

        $story = Story::findOrFail($storyId);

        $nextChapterNumber = $story->chapters()->max('chapter') + 1;

        return view('Admin.pages.chapters.create', compact('story', 'nextChapterNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
            'name' => 'required|string|max:255',
            'chapter' => 'required|integer|min:1|unique:chapters,chapter,NULL,id,story_id,' . $request->story_id,
            'content' => 'required|string',
            'content_type' => 'required|in:plain,rich',
            'status' => 'required|in:draft,published',
            'scheduled_publish_at' => 'nullable|date|after:now',
        ], [
            'story_id.required' => 'Truyện là bắt buộc',
            'story_id.exists' => 'Truyện không tồn tại',
            'name.required' => 'Tên chương là bắt buộc',
            'name.string' => 'Tên chương phải là chuỗi',
            'name.max' => 'Tên chương không được vượt quá 255 ký tự',
            'chapter.required' => 'Số chương là bắt buộc',
            'chapter.integer' => 'Số chương phải là số nguyên',
            'chapter.min' => 'Số chương phải lớn hơn 0',
            'chapter.unique' => 'Số chương này đã tồn tại trong truyện.',
            'content.required' => 'Nội dung là bắt buộc',
            'content_type.required' => 'Vui lòng chọn kiểu nội dung',
            'status.required' => 'Vui lòng chọn trạng thái',
            'scheduled_publish_at.after' => 'Thời gian hẹn đăng phải sau thời điểm hiện tại',
        ]);

        $story = Story::findOrFail($request->story_id);

        $contentType = $request->content_type ?? Chapter::CONTENT_TYPE_PLAIN;
        $status = $request->status ?? Chapter::STATUS_PUBLISHED;

        $sanitizedContent = $contentType === Chapter::CONTENT_TYPE_RICH
            ? Helper::sanitizeChapterContent($request->content)
            : Helper::processTextareaContent($request->content);

        $scheduledPublishAt = null;
        $publishedAt = null;

        if ($status === Chapter::STATUS_DRAFT && $request->scheduled_publish_at) {
            $scheduledTime = \Carbon\Carbon::parse($request->scheduled_publish_at);
            if ($scheduledTime->isFuture()) {
                $scheduledPublishAt = $scheduledTime;
            }
        } elseif ($status === Chapter::STATUS_PUBLISHED) {
            $publishedAt = now();
        }

        $chapter = Chapter::create([
            'story_id' => $story->id,
            'name' => $request->name,
            'chapter' => $request->chapter,
            'content' => $sanitizedContent,
            'content_type' => $contentType,
            'status' => $status,
            'scheduled_publish_at' => $scheduledPublishAt,
            'published_at' => $publishedAt,
            'slug' => Helper::generateChapterSlug($request->chapter, $request->name, $request->story_id, null, null, null),
            'is_new' => 1,
            'views' => 0
        ]);

        if ($status === Chapter::STATUS_PUBLISHED) {
            $users = User::pluck('id');
            $now   = now();

            $data = $users->map(function ($userId) use ($chapter, $now) {
                return [
                    'user_id'    => $userId,
                    'story_id'   => $chapter->story_id,
                    'chapter_id' => $chapter->id,
                    'message'    => 'Một chapter mới đã được thêm vào truyện: '
                        . $chapter->story->name . ' - Chương '
                        . $chapter->chapter,
                    'created_at' => $now,
                ];
            })->toArray();

            Notification::insert($data);
        }

        return redirect()->route('admin.stories.show', $request->story_id)->with('success', 'Chương đã được tạo thành công!');
    }


    public function edit(Chapter $chapter)
    {
        $chapter->load('story');
        return view('Admin.pages.chapters.edit', compact('chapter'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $request->validate([
            'story_id' => 'required|exists:stories,id',
            'name' => 'required|string|max:255',
            'chapter' => 'required|integer|min:1|unique:chapters,chapter,' . $chapter->id . ',id,story_id,' . $request->story_id,
            'content' => 'required|string',
            'content_type' => 'required|in:plain,rich',
            'status' => 'required|in:draft,published',
            'scheduled_publish_at' => 'nullable|date|after:now',
        ], [
            'story_id.required' => 'Truyện là bắt buộc',
            'story_id.exists' => 'Truyện không tồn tại',
            'name.required' => 'Tên chương là bắt buộc',
            'name.string' => 'Tên chương phải là chuỗi',
            'name.max' => 'Tên chương không được vượt quá 255 ký tự',
            'chapter.required' => 'Số chương là bắt buộc',
            'chapter.integer' => 'Số chương phải là số nguyên',
            'chapter.min' => 'Số chương phải lớn hơn 0',
            'chapter.unique' => 'Số chương này đã tồn tại trong truyện.',
            'content.required' => 'Nội dung là bắt buộc',
            'content_type.required' => 'Vui lòng chọn kiểu nội dung',
            'status.required' => 'Vui lòng chọn trạng thái',
            'scheduled_publish_at.after' => 'Thời gian hẹn đăng phải sau thời điểm hiện tại',
        ]);

        $contentType = $request->content_type ?? $chapter->content_type ?? Chapter::CONTENT_TYPE_PLAIN;
        $status = $request->status ?? $chapter->status ?? Chapter::STATUS_PUBLISHED;

        $sanitizedContent = $contentType === Chapter::CONTENT_TYPE_RICH
            ? Helper::sanitizeChapterContent($request->content)
            : Helper::processTextareaContent($request->content);

        $scheduledPublishAt = null;
        $publishedAt = $chapter->published_at;

        if ($status === Chapter::STATUS_DRAFT) {
            if ($request->scheduled_publish_at) {
                $scheduledTime = \Carbon\Carbon::parse($request->scheduled_publish_at);
                if ($scheduledTime->isFuture()) {
                    $scheduledPublishAt = $scheduledTime;
                }
            }
            $publishedAt = null;
        } else {
            $publishedAt = $chapter->published_at ?? now();
            $scheduledPublishAt = null;
        }

        // Lưu slug và name cũ để so sánh
        $oldSlug = $chapter->slug;
        $oldName = $chapter->name;

        $chapter->update([
            'story_id' => $request->story_id,
            'name' => $request->name,
            'chapter' => $request->chapter,
            'content' => $sanitizedContent,
            'content_type' => $contentType,
            'status' => $status,
            'scheduled_publish_at' => $scheduledPublishAt,
            'published_at' => $publishedAt,
            'slug' => Helper::generateChapterSlug($request->chapter, $request->name, $request->story_id, $chapter->id, $oldSlug, $oldName),
        ]);

        return redirect()->route('admin.stories.show', $chapter->story_id)->with('success', 'Chương đã được cập nhật thành công!');
    }

    public function destroy(Chapter $chapter)
    {
        $storyId = $chapter->story_id;
        $chapter->delete();
        return redirect()->route('admin.stories.show', $storyId)->with('success', 'Chương đã được xóa thành công!');
    }

    public function toggleStatus(Chapter $chapter)
    {
        $chapter->is_new = $chapter->is_new ? 0 : 1;
        $chapter->save();

        $status = $chapter->is_new ? 'mới' : 'cũ';
        return response()->json([
            'success' => true,
            'message' => "Chương đã được đánh dấu là {$status}",
            'is_new' => $chapter->is_new
        ]);
    }

    /**
     * Upload ảnh cho CKEditor (drag-drop vào content)
     */
    public function uploadImage(Request $request)
    {
        if ($request->isMethod('options')) {
            return response('', 200);
        }

        $file = $request->file('upload') ?? $request->file('file');

        if (!$file || !$file->isValid()) {
            return response()->json([
                'error' => ['message' => 'Không có file ảnh được gửi hoặc file không hợp lệ']
            ], 400);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return response()->json([
                'error' => ['message' => 'Hình ảnh không được vượt quá 10MB']
            ], 422);
        }
        $validation = Helper::validateImageFile($file);
        if (!$validation['valid']) {
            return response()->json([
                'error' => ['message' => $validation['message']]
            ], 422);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $imageName = 'chapter_' . time() . '_' . uniqid() . '.' . $extension;
        $imagePath = $file->storeAs('chapters', $imageName, 'public');

        $imageUrl = asset('storage/' . $imagePath);

        return response()->json([
            'url' => $imageUrl,
            'uploaded' => true,
            'fileName' => $imageName
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Form tạo nhiều chương hàng loạt
     */
    public function bulkCreate(Story $story)
    {
        return view('Admin.pages.chapters.bulk-create', compact('story'));
    }

    /**
     * Kiểm tra chương đã tồn tại
     */
    public function checkExisting(Request $request, Story $story)
    {
        $request->validate([
            'chapter_numbers' => 'required|array',
            'chapter_numbers.*' => 'integer|min:1'
        ]);

        $existingNumbers = $story->chapters()
            ->whereIn('chapter', $request->chapter_numbers)
            ->pluck('chapter')
            ->toArray();

        return response()->json([
            'existing' => $existingNumbers,
            'available' => array_values(array_diff($request->chapter_numbers, $existingNumbers))
        ]);
    }

    /**
     * Lưu nhiều chương hàng loạt
     */
    public function bulkStore(Request $request, Story $story)
    {
        $validated = $request->validate([
            'chapters' => 'required|string',
        ]);

        $chaptersData = json_decode($validated['chapters'], true);

        if (!is_array($chaptersData)) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu chương không hợp lệ'
            ], 400);
        }

        foreach ($chaptersData as $index => $chapterData) {
            if (!isset($chapterData['chapter']) || !is_numeric($chapterData['chapter']) || $chapterData['chapter'] < 1) {
                return response()->json([
                    'success' => false,
                    'message' => "Chương thứ " . ($index + 1) . " có số chương không hợp lệ"
                ], 400);
            }
            if (!isset($chapterData['name']) || empty(trim($chapterData['name']))) {
                return response()->json([
                    'success' => false,
                    'message' => "Chương thứ " . ($index + 1) . " thiếu tên chương"
                ], 400);
            }
            if (!isset($chapterData['content']) || empty(trim($chapterData['content']))) {
                return response()->json([
                    'success' => false,
                    'message' => "Chương thứ " . ($index + 1) . " thiếu nội dung"
                ], 400);
            }
            if (!isset($chapterData['publish_now']) || !$chapterData['publish_now']) {
                if (!isset($chapterData['published_at']) || empty($chapterData['published_at'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Chương thứ " . ($index + 1) . " thiếu ngày công bố"
                    ], 400);
                }
            }
        }

        $existingNumbers = $story->chapters()
            ->whereIn('chapter', collect($chaptersData)->pluck('chapter'))
            ->pluck('chapter')
            ->toArray();

        $createdCount = 0;

        DB::beginTransaction();
        try {
            foreach ($chaptersData as $chapterData) {
                if (in_array($chapterData['chapter'], $existingNumbers)) {
                    continue;
                }

                $contentType = $chapterData['content_type'] ?? Chapter::CONTENT_TYPE_PLAIN;
                $publishNow = $chapterData['publish_now'] ?? false;

                if ($publishNow) {
                    $status = Chapter::STATUS_PUBLISHED;
                    $publishedAt = now();
                    $scheduledPublishAt = null;
                } else {
                    $scheduledTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $chapterData['published_at'], 'Asia/Ho_Chi_Minh');
                    $now = now();

                    if ($scheduledTime->gt($now)) {
                        $status = Chapter::STATUS_DRAFT;
                        $publishedAt = null;
                        $scheduledPublishAt = $scheduledTime;
                    } else {
                        $status = Chapter::STATUS_PUBLISHED;
                        $publishedAt = $scheduledTime;
                        $scheduledPublishAt = null;
                    }
                }

                $sanitizedContent = $contentType === Chapter::CONTENT_TYPE_RICH
                    ? Helper::sanitizeChapterContent($chapterData['content'])
                    : Helper::processTextareaContent($chapterData['content']);

                $chapter = Chapter::create([
                    'story_id' => $story->id,
                    'name' => $chapterData['name'],
                    'chapter' => $chapterData['chapter'],
                    'content' => $sanitizedContent,
                    'content_type' => $contentType,
                    'status' => $status,
                    'scheduled_publish_at' => $scheduledPublishAt ?? null,
                    'published_at' => $publishedAt ?? null,
                    'slug' => Helper::generateChapterSlug($chapterData['chapter'], $chapterData['name'], $story->id, null, null, null),
                    'is_new' => 1,
                    'views' => 0,
                ]);

                if ($status === Chapter::STATUS_PUBLISHED) {
                    $users = User::pluck('id');
                    $now = now();
                    $data = $users->map(function ($userId) use ($chapter, $now) {
                        return [
                            'user_id' => $userId,
                            'story_id' => $chapter->story_id,
                            'chapter_id' => $chapter->id,
                            'message' => 'Một chapter mới đã được thêm vào truyện: ' . $chapter->story->name . ' - Chương ' . $chapter->chapter,
                            'created_at' => $now,
                        ];
                    })->toArray();
                    Notification::insert($data);
                }

                $existingNumbers[] = $chapterData['chapter'];
                $createdCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã tạo thành công {$createdCount} chương",
                'created_count' => $createdCount
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo chương: ' . $e->getMessage()
            ], 500);
        }
    }
}
