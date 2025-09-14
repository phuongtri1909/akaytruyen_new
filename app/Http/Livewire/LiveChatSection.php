<?php

namespace App\Http\Livewire;

use App\Models\LiveChatReaction;
use Livewire\Component;
use App\Models\LiveChat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class LiveChatSection extends Component
{
    public $content;
    public $parent_id = null;
    public $loadedComments = 10;
    public $hasMoreComments = true;

    protected $listeners = ['deleteComment', 'loadMoreOnScroll'];

    protected $rules = [
        'content' => 'required|min:1',
    ];

    public function mount()
    {
        $this->checkHasMoreComments();
    }

    public function postComment()
    {
        $this->validate();

        if (Auth::user()->ban->comment) {
            return session()->flash('error', 'Bạn đã bị cấm bình luận.');
        }

        $comment = LiveChat::create([
            'user_id' => Auth::id(),
            'content' => trim($this->content),
            'parent_id' => $this->parent_id,
        ]);

        $this->reset(['content', 'parent_id']);

        $this->clearCommentCache();

        $this->dispatch('commentAdded');
        session()->flash('success', 'Bình luận đã được thêm.');
    }

    public function deleteComment($id)
    {
        $comment = LiveChat::with('replies')->find($id);

        if (!$comment) {
            return session()->flash('error', 'Bình luận không tồn tại.');
        }

        $user = Auth::user();
        if ($user->id === $comment->user_id || $user->hasAnyRole(['Admin','Mod'])) {
            $comment->replies()->delete();
            $comment->delete();

            $this->clearCommentCache();

            $this->dispatch('deleteSuccess');
            session()->flash('delete_success', 'Xóa thành công');
        } else {
            session()->flash('error', 'Bạn không có quyền xóa bình luận này.');
        }
    }

    public function react($commentId, $type)
    {
        if (!Auth::check()) return;

        LiveChatReaction::toggleReaction(Auth::id(), $commentId, $type);
    }

    public function pinComment($commentId)
    {
        $user = Auth::user();
        if (!$user || !$user->hasAnyRole(['Admin','Mod'])) return;

        $comment = LiveChat::find($commentId);
        if (!$comment || $comment->parent_id) return;

        if ($comment->pinned) {
            $comment->update(['pinned' => false]);
        } else {
            $pinned = LiveChat::where('pinned', true)->orderBy('updated_at')->get();
            if ($pinned->count() >= 2) {
                $pinned->first()->update(['pinned' => false]);
            }
            $comment->update(['pinned' => true]);
        }

        $this->clearCommentCache();
    }

    public function loadMoreComments()
    {
        $this->loadedComments += 10;
        $this->checkHasMoreComments();
    }

    public function loadMoreOnScroll()
    {
        if ($this->hasMoreComments) {
            $this->loadMoreComments();
        }
    }

    private function checkHasMoreComments()
    {
        $totalComments = LiveChat::getCachedMainCommentCount();

        $this->hasMoreComments = $this->loadedComments < $totalComments;
    }

    public function render()
    {
        $comments = LiveChat::getCachedComments($this->loadedComments);

        return view('Frontend.livewire.live-chat-section', [
            'comments' => $comments,
            'hasMoreComments' => $this->hasMoreComments
        ]);
    }

    /**
     * Clear tất cả cache liên quan đến comments
     */
    private function clearCommentCache()
    {
        Cache::forget('total_main_comments');

        for ($i = 10; $i <= 100; $i += 10) {
            Cache::forget("comments_page_{$i}");
        }

        Cache::forget('users_with_roles');
    }

    public function parseLinks($text)
    {
        if (empty($text)) return '';

        $text = e($text);
        $text = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 underline hover:text-blue-700">$1</a>',
            $text
        );

        $emojiPattern = '/[\x{1F000}-\x{1FFFF}|\x{2600}-\x{27BF}|\x{1F900}-\x{1F9FF}|\x{2B50}|\x{2705}]/u';
        $text = preg_replace_callback($emojiPattern, fn($m) => '<span class="emoji">'.$m[0].'</span>', $text);

        return nl2br('<span class="text">'.$text.'</span>');
    }
}
