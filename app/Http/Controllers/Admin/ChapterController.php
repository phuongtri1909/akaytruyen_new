<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Chapter\ChapterRepositoryInterface;
use App\Repositories\Story\StoryRepositoryInterface;
use App\Services\ChapterService;
use Error;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChapterController extends Controller
{
    public function __construct(
        protected ChapterRepositoryInterface $repository,
        protected ChapterService $service,
        protected StoryRepositoryInterface $storyRepository
    ) {
        $this->middleware('can:xem_chapter')->only('index', 'show');
        $this->middleware('can:them_chapter')->only('create', 'store');
        $this->middleware('can:sua_chapter')->only('edit', 'update');
        $this->middleware('can:xoa_chapter')->only('destroy');
    }

    public function index(Request $request)
    {
        $page_title = 'Chapter';
        $search     = $request->get('search', []);
        $results    = $this->repository->paginate(20, [], $search);
    }

    public function create($story_id)

    {
        $story_id = html_entity_decode($story_id, ENT_QUOTES, 'UTF-8');
        $story = $this->storyRepository->find(intval($story_id));

        $chapter = null;

        return view('Admin.pages.chapters.edit', compact('chapter', 'story'));
    }

    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'chapter' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'story_id' => 'required|integer|exists:stories,id'
        ]);

        $attributes = $request->all();

        $chapterNumber = intval($request->input('chapter')); // Đảm bảo là integer
        $chapterName   = $request->input('name');    // Ví dụ: "Con đường bà chủ"

        $slug = Str::slug("{$chapterNumber} {$chapterName}");

        // Kiểm tra slug có trùng không, nếu trùng thì thêm số chương vào
        $originalSlug = $slug;
        $count = 1;
        while ($this->repository->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $attributes['slug'] = $slug;

        $chapter = $this->repository->create($attributes);
        // 🔥 Kiểm tra slug của truyện, nếu đúng thì không tạo thông báo
        if ($chapter->story->slug === 'goc-nha-cua-nang') {
            return redirect()->route('admin.story.show', ['story' => $chapter->story_id])
                ->with('successMessage', 'Thêm mới chapter thành công nhưng không tạo thông báo.');
        }
            // 🔥 Lưu thông báo vào notifications
            $users = DB::table('users')->pluck('id'); // Lấy danh sách user ID
                foreach ($users as $userId) {
                    DB::table('notifications')->insert([
                        'user_id' => $userId,
                        'story_id' => $chapter->story_id,
                        'chapter_id' => $chapter->id,
                        'message' => 'Một chapter mới đã được thêm vào truyện: ' . $chapter->story->name . ' - Chương ' . $chapter->chapter,
                        'created_at' => now(),
                    ]);
                }


        return redirect()->route('admin.story.show', ['story' => $chapter->story_id])->with('successMessage', 'Thêm mới chapter thành công.');
    }

    public function show($id)
    {
        $item = $this->repository->find($id);
    }

    public function edit($id)
    {
        $chapter = $this->repository->find($id);
        if (!$chapter) {
            return throw(new Error('Truyện không tồn tại'));
        }
        $story = $this->storyRepository->find(intval($chapter->story_id));

        return view('Admin.pages.chapters.edit', compact('chapter', 'story'));
    }

    public function update(Request $request, $id)
    {
        // Get the chapter first to ensure story_id exists
        $chapter = $this->repository->find($id);
        if (!$chapter) {
            return redirect()->back()->withErrors(['error' => 'Chapter không tồn tại']);
        }

        // Validate input data
        $request->validate([
            'chapter' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'story_id' => 'required|integer|exists:stories,id'
        ]);

        $attributes = $request->all();

        // Không làm sạch nội dung, chỉ lấy nguyên văn
        $attributes['content'] = $request->input('content');

        // Xử lý slug
        $chapterNumber = intval($request->input('chapter')); // Đảm bảo là integer
        $chapterName   = $request->input('name');    // Ví dụ: "Con đường bà chủ"

        $slug = Str::slug("{$chapterNumber} {$chapterName}");


        // Kiểm tra slug có trùng không (trừ chính nó)
        $originalSlug = $slug;
        $count = 1;
        while ($this->repository->findBySlugExcept($slug, $id)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        $attributes['slug'] = $slug;

        // Cập nhật chương
        $chapter = $this->repository->update($id, $attributes);

        return redirect()->route('admin.story.show', ['story' => $chapter->story_id])
                         ->with('successMessage', 'Thay đổi thành công.');
    }


    public function destroy($id)
    {
        $res = ['success' => false];

        $this->repository->delete($id);

        $res['success'] = true;

        return response()->json($res);
    }
// private function cleanContent($content)
// {
//     // 1. Xóa các thẻ HTML không mong muốn nhưng giữ lại định dạng cơ bản
//     $allowed_tags = '<p><strong><em><ul><ol><li><br>';
//     $content = strip_tags($content, $allowed_tags);

//     // 2. Chuyển đổi HTML entity
//     $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

//     // 3. Xóa ký tự đặc biệt từ Word
//     $content = preg_replace('/[\x00-\x1F\x7F]/u', '', $content);

//     // 4. **Loại bỏ khoảng trắng thừa nhưng giữ nguyên dấu `.`**
//     $content = preg_replace('/\s*([.,!?])\s*/', '$1 ', $content); // Giữ dấu chấm, dấu phẩy đúng khoảng cách
//     $content = preg_replace('/\s{2,}/', ' ', $content); // Xóa khoảng trắng thừa giữa các từ

//     return trim($content);
// }

// private function cleanContent($content)
// {
//     // 1. Tìm tất cả URL và thay thế bằng placeholder
//     preg_match_all('/https?:\/\/[^\s]+|www\.[^\s]+/i', $content, $urls);
//     $placeholders = [];
//     foreach ($urls[0] as $index => $url) {
//         $placeholder = "__URL{$index}__";
//         $placeholders[$placeholder] = $url;
//         $content = str_replace($url, $placeholder, $content);
//     }

//     // 2. Xóa các thẻ HTML không mong muốn nhưng giữ lại định dạng cơ bản
//     $allowed_tags = '<p><strong><em><ul><ol><li><br>';
//     $content = strip_tags($content, $allowed_tags);

//     // 3. Chuyển đổi HTML entity
//     $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');

//     // 4. Xóa ký tự đặc biệt từ Word
//     $content = preg_replace('/[\x00-\x1F\x7F]/u', '', $content);

//     // 5. Loại bỏ khoảng trắng thừa nhưng giữ nguyên dấu `.` và các dấu câu khác
//     $content = preg_replace('/\s*([.,!?])\s*/', '$1 ', $content); // Giữ dấu câu đúng khoảng cách
//     $content = preg_replace('/\s{2,}/', ' ', $content); // Xóa khoảng trắng thừa giữa các từ

//     // 6. Khôi phục các URL từ placeholder
//     $content = str_replace(array_keys($placeholders), array_values($placeholders), $content);

//     // 7. Trả về nội dung đã được xử lý và loại bỏ khoảng trắng đầu cuối
//     return trim($content);
// }




}
