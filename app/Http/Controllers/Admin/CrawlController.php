<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Jobs\CrawlStoryJob;
use App\Models\Story;
use App\Repositories\Author\AuthorRepositoryInterface;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\Chapter\ChapterRepository;
use App\Repositories\Chapter\ChapterRepositoryInterface;
use App\Repositories\Story\StoryRepositoryInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use App\Models\Comment;
use App\Models\LiveComment;
use App\Models\User;

class CrawlController extends Controller
{
    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository,
        protected AuthorRepositoryInterface $authorRepository,
        protected ChapterRepositoryInterface $chapterRepository,
        protected StoryRepositoryInterface $storyRepository
        // protected Stor
        // protected UserRepositoryInterface $repository,
        // protected UserService             $service
    ) {
        $this->middleware('can:xem_comment_data')->only('index', 'store');
    }

    public function index(Request $request)
    {
        $search        = $request->get('search', []);

        $data = [];
        $users = User::all(); // Lấy danh sách tất cả người dùng
        $comments = Comment::latest()->paginate(30);
        $live_comments = LiveComment::orderBy('created_at', 'desc')->get();
        $totalComments = Comment::count();

        return view('Admin.pages.comment.index', compact('data', 'users', 'comments', 'live_comments'));
    }

    public function destroy($id)
    {
        $authUser = auth()->user();
        $comment = Comment::find($id);
        if (!$comment) {
            return redirect()->route('chapter')->with('error', 'Không tìm thấy bình luận này');
        }

        if (
            $authUser->role === 'Admin' ||
            ($authUser->role === 'Mod' && (!$comment->user || $comment->user->role !== 'Admin'))
        ) {
            $comment->delete();
            return redirect()->route('chapter')->with('success', 'Xóa bình luận thành công');
        }

        return redirect()->route('chapter')->with('error', 'Không thể xóa bình luận của Admin');
    }
    public function delete(Comment $comment)
    {
        if (!$comment) {
            return redirect()->route('chapter')->with('error', 'Không tìm thấy bình luận này');
        }

        $authUser = auth()->user();

        if ($authUser->role === 'Admin' || ($authUser->role === 'Mod' && (!$comment->user || $comment->user->role !== 'Admin'))) {
            $comment->delete();
            return redirect()->route('chapter')->with('success', 'Xóa bình luận thành công');
        }

        return redirect()->back()->with('success', 'Xóa bình luận thành công');
    }
}
