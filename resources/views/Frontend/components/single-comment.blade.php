@php
    /** @var \App\Models\Livechat $comment */
    $authUser = auth()->user();
    $isOwnerOrStaff = $authUser && (
        $comment->user_id === $authUser->id ||
        $authUser->hasRole(['Admin', 'Mod'])
    );
    $isVipSieuViet = $comment->user?->hasRole('VIP SIÊU VIỆT');
    $replyCount = $comment->replies->count();
@endphp

<div class="modern-single-comment">
    {{-- Header --}}
    <div class="comment-header">
        @include('Frontend.components.user-avatar', ['user' => $comment->user])

        <div class="comment-user-info">
            @include('Frontend.components.user-badge', ['user' => $comment->user])

            <span class="comment-time">
                {{ $comment->created_at->locale('vi')->diffForHumans() }}
            </span>

            {{-- Pin comment --}}
            @if ($authUser && $authUser->hasRole(['Admin', 'Mod']) && !$comment->parent_id)
                <button wire:click="pinComment({{ $comment->id }})"
                        class="pin-btn {{ $comment->pinned ? 'pinned' : '' }}"
                        title="{{ $comment->pinned ? 'Bỏ ghim' : 'Ghim' }}">
                    <i class="fas fa-thumbtack"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- Nội dung --}}
    <div class="comment-content {{ $isVipSieuViet ? 'vip-sieu-viet-content' : '' }}">
        {!! \App\Helpers\Helper::parseLinks($comment->content) !!}
    </div>

    {{-- Actions --}}
    <div class="comment-actions">
        @if ($authUser)
            <button wire:click="$set('parent_id', {{ $comment->id }})"
                    class="action-btn reply-btn">
                <i class="fas fa-reply"></i>
                Trả lời
            </button>

            @if ($isOwnerOrStaff)
                <button onclick="confirmDelete({{ $comment->id }})"
                        class="action-btn delete-btn">
                    <i class="fas fa-trash"></i>
                    Xóa
                </button>
            @endif
        @endif
    </div>

    {{-- Reply form --}}
    @if ($parent_id === $comment->id)
        @if (!$authUser?->ban->comment)
            <div class="reply-form">
                <textarea wire:model.lazy="content" class="reply-textarea"
                          placeholder="Viết phản hồi..." rows="2" maxlength="1000"></textarea>
                <div class="reply-form-actions">
                    <button wire:click="postComment"
                            class="action-btn submit-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="postComment">
                            <i class="fas fa-paper-plane"></i>
                            Gửi
                        </span>
                        <span wire:loading wire:target="postComment">
                            <i class="fas fa-spinner fa-spin"></i>
                            Đang gửi...
                        </span>
                    </button>
                    <button wire:click="$set('parent_id', null)"
                            class="action-btn cancel-btn">
                        <i class="fas fa-times"></i>
                        Hủy
                    </button>
                </div>
            </div>
        @else
            <div class="ban-message-reply">
                <i class="fas fa-ban"></i>
                <span>Bạn đã bị cấm bình luận.</span>
            </div>
        @endif
    @endif

    {{-- Replies --}}
    @if ($replyCount > 0)
        <div class="replies-container">
            @foreach ($comment->replies->take(5) as $reply)
                @if ($reply->user)
                    <div class="reply-item">
                        <div class="reply-header">
                            @include('Frontend.components.user-avatar', ['user' => $reply->user])
                            <div class="reply-user-info">
                                @include('Frontend.components.user-badge', ['user' => $reply->user])
                                <span class="reply-time">
                                    {{ $reply->created_at->locale('vi')->diffForHumans() }}
                                </span>
                            </div>
                        </div>

                        @php $isReplyVip = $reply->user->hasRole('VIP SIÊU VIỆT'); @endphp

                        <div class="reply-content {{ $isReplyVip ? 'vip-sieu-viet-content' : '' }}">
                            {!! \App\Helpers\Helper::parseLinks($reply->content) !!}
                        </div>

                        @if ($authUser && ($reply->user_id === $authUser->id || $authUser->hasRole(['Admin','Mod'])))
                            <div class="reply-actions">
                                <button onclick="confirmDelete({{ $reply->id }})"
                                        class="action-btn delete-btn">
                                    <i class="fas fa-trash"></i>
                                    Xóa
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach

            {{-- Show more replies --}}
            @if ($replyCount > 5)
                <div class="more-replies">
                    <span>Còn {{ $replyCount - 5 }} phản hồi khác...</span>
                </div>
            @endif
        </div>
    @endif
</div>
