<?php

namespace App\Http\Controllers\Admin;

use Purifier;
use App\Models\User;
use App\Models\Story;
use App\Helpers\Helper;
use App\Models\Chapter;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChapterController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:them_chuong')->only('create', 'store');
        $this->middleware('can:sua_chuong')->only('edit', 'update', 'toggleStatus');
        $this->middleware('can:xoa_chuong')->only('destroy');
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
        ]);

        $story = Story::findOrFail($request->story_id);

        $sanitizedContent = Helper::processTextareaContent($request->content);

        $chapter = Chapter::create([
            'story_id' => $story->id,
            'name' => $request->name,
            'chapter' => $request->chapter,
            'content' => $sanitizedContent,
            'slug' => Helper::generateChapterSlug($request->chapter, $request->name, $request->story_id, null, null, null),
            'is_new' => 1,
            'views' => 0
        ]);

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
        ]);


        $sanitizedContent = Helper::processTextareaContent($request->content);

        // Lưu slug và name cũ để so sánh
        $oldSlug = $chapter->slug;
        $oldName = $chapter->name;

        $chapter->update([
            'story_id' => $request->story_id,
            'name' => $request->name,
            'chapter' => $request->chapter,
            'content' => $sanitizedContent,
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
}
