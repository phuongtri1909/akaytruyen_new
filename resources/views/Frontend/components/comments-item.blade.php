@php
    $level = $comment->level ?? 0;
@endphp

<div class="comment-item-wrapper" data-comment-id="{{ $comment->id }}">
    <li class="comment-item clearfix d-flex" id="comment-{{ $comment->id }}">

        @php
            $avatar =
                $comment->user && $comment->user->avatar
                    ? $comment->user->avatar_url
                    : asset('images/defaults/avatar_default.jpg');

            $role =
                $comment->user && $comment->user->roles && $comment->user->roles->first()
                    ? $comment->user->roles->first()->name
                    : null;
            $email = $comment->user ? $comment->user->email : null;

            $borderMap = [
                'Admin' => 'admin-vip-8.png',
                'Mod' => 'vien_mod.png',
                'Content' => 'avt_content.png',
                'vip' => 'avt_admin.png',
                'VIP PRO' => 'ma-van-dang-tptk.gif',
                'VIP PRO MAX' => 'avt_vip_pro_max.gif',
                'VIP SIÊU VIỆT' => 'khung-sieu-viet.png',
            ];
            $border = null;
            $borderStyle = '';

            if ($role === 'Admin' && $email === 'nang2025@gmail.com') {
                $border = asset('images/roles/vien-thanh-nu.png');
            } elseif ($role === 'Admin' && $email === 'nguyenphuochau12t2@gmail.com') {
                $border = asset('images/roles/akay-vip-16.png');
                $borderStyle = 'width: 200%; height: 200%; top: 31%;';
            } else {
                $border = isset($borderMap[$role]) ? asset('images/roles/' . $borderMap[$role]) : null;
            }
        @endphp

        <!-- Avatar Container with Enhanced Styling -->
        <div class="avatar-container">
            <div class="avatar-wrapper"
                style="position: relative; width: 45px; height: 45px; display: inline-block; flex-shrink: 0;">
                <img src="{{ $avatar }}" class="user-avatar rounded-circle border border-3"
                    alt="{{ $comment->user ? $comment->user->name : 'Người dùng không tồn tại' }}"
                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">

                @if ($border)
                    <img src="{{ $border }}" class="avatar-border rounded-circle" alt="Border {{ $role }}"
                        style="
                            position: absolute;
                            top: 50%;
                            left: 50%;
                            {{ $borderStyle ?: 'width: 130%; height: 130%;' }}
                            transform: translate(-50%, -50%);
                            pointer-events: none;
                            z-index: 1;
                            border-radius: 50%;
                        ">
                @endif
            </div>
        </div>

        <div class="post-comments p-2 p-md-3 {{ $comment->is_pinned ? 'pinned' : '' }}">
            <div class="content-post-comments">
                @php
                    $userRole =
                        $comment->user && $comment->user->roles && $comment->user->roles->first()
                            ? $comment->user->roles->first()->name
                            : null;
                @endphp

                <div class="meta mb-3">
                    <div class="user-info">
                        <a class="user-name fw-bold ms-2 text-decoration-none" target="_blank">
                            @if ($comment->user)
                                @if ($userRole === 'Admin')
                                    <span class="role-badge admin-badge">
                                        @if (auth()->check() && auth()->user()->hasRole('Admin'))
                                            <a href="{{ route('admin.users.edit', $comment->user->id) }}"
                                                target="_blank" class="text-decoration-none admin-badge">
                                                👑 [ADM] <b>{{ $comment->user->name }}</b>
                                            </a>
                                        @else
                                            👑 [ADM] <b>{{ $comment->user->name }}</b>
                                        @endif
                                    </span>
                                @elseif ($userRole === 'Mod')
                                    <span class="role-badge mod-badge">
                                        @if (auth()->check() && auth()->user()->hasRole('Admin'))
                                            <a href="{{ route('admin.users.edit', $comment->user->id) }}"
                                                target="_blank" class="text-decoration-none mod-badge">
                                                🛡️ [MOD] <b>{{ $comment->user->name }}</b>
                                            </a>
                                        @else
                                            🛡️ [MOD] <b>{{ $comment->user->name }}</b>
                                        @endif
                                    </span>
                                @elseif ($userRole === 'vip')
                                    <span class="role-badge vip-badge">
                                        @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod')))
                                            <a href="{{ route('admin.users.edit', $comment->user->id) }}"
                                                target="_blank" class="text-decoration-none vip-badge">
                                                ⭐ [VIP] <b>{{ $comment->user->name }}</b>
                                            </a>
                                        @else
                                            ⭐ [VIP] <b>{{ $comment->user->name }}</b>
                                        @endif
                                    </span>
                                @elseif ($userRole === 'Content')
                                    <span class="role-badge content-badge">
                                        @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod')))
                                            <a href="{{ route('admin.users.edit', $comment->user->id) }}"
                                                target="_blank" class="text-decoration-none content-badge">
                                                ✍️ [CONTENT] <b>{{ $comment->user->name }}</b>
                                            </a>
                                        @else
                                            ✍️ [CONTENT] <b>{{ $comment->user->name }}</b>
                                        @endif
                                    </span>
                                @elseif ($userRole === 'VIP PRO')
                                    <span class="role-badge vip-pro-badge">
                                        @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod')))
                                            <a href="{{ route('admin.users.edit', $comment->user->id) }}"
                                                target="_blank" class="text-decoration-none vip-pro-badge">
                                                💎 [VIP PRO] <b>{{ $comment->user->name }}</b>
                                            </a>
                                        @else
                                            💎 [VIP PRO] <b>{{ $comment->user->name }}</b>
                                        @endif
                                    </span>
                                @elseif ($userRole === 'VIP PRO MAX')
                                    <span class="role-badge vip-pro-max-badge">
                                        @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod')))
                                            <a href="{{ route('admin.users.edit', $comment->user->id) }}"
                                                target="_blank" class="text-decoration-none vip-pro-max-badge">
                                                🔥 [VIP PRO MAX] <b>{{ $comment->user->name }}</b>
                                            </a>
                                        @else
                                            🔥 [VIP PRO MAX] <b>{{ $comment->user->name }}</b>
                                        @endif
                                    </span>
                                @elseif ($userRole === 'VIP SIÊU VIỆT')
                                    <span class="role-badge vip-pro-sv-badge">
                                        @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod')))
                                            <a href="{{ route('admin.users.edit', $comment->user->id) }}"
                                                target="_blank" class="text-decoration-none vip-pro-sv-badge">
                                                🌟 [VIP SIÊU VIỆT] <b>{{ $comment->user->name }}</b>
                                            </a>
                                        @else
                                            🌟 [VIP SIÊU VIỆT] <b>{{ $comment->user->name }}</b>
                                        @endif
                                    </span>
                                @else
                                    @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod')))
                                        <a href="{{ route('admin.users.edit', $comment->user->id) }}" target="_blank"
                                            class="text-decoration-none text-dark">
                                            👤 <b>{{ $comment->user->name }}</b>
                                        </a>
                                    @else
                                        <span class="text-dark">👤 <b>{{ $comment->user->name }}</b></span>
                                    @endif
                                @endif

                                @if ($comment->user && $comment->user->email === 'tri2003bt@gmail.com')
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/64012-management.png" width="30px"
                                            style="margin-left:5px; margin-top:-10px;" alt="Hỗ trợ">
                                        <span class="tooltip-text">Hỗ Trợ</span>
                                    </span>
                                @elseif($comment->user && $comment->user->hasRole('Content'))
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/65928-owner.png" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="tac gia">
                                        <span class="tooltip-text">Tác Giả</span>
                                    </span>
                                @elseif($comment->user && $comment->user->hasRole('Admin'))
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/39760-owner.png" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="Admin">
                                        <span class="tooltip-text">Quản Trị Viên</span>
                                    </span>
                                @endif

                                @if ($comment->user && $comment->user->hasRole('Mod'))
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/80156-developer.png" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="Mod">
                                        <span class="tooltip-text">Mod kiểm duyệt</span>
                                    </span>
                                @elseif($comment->user && $comment->user->hasRole('vip'))
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/45918-msp-super-vip.png" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="vip1">
                                        <span class="tooltip-text">Tinh Anh Bậc I</span>
                                    </span>
                                @elseif($comment->user && $comment->user->hasRole('VIP PRO'))
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/44014-msp-elite-vip.png" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="vip2">
                                        <span class="tooltip-text">Hộ Pháp Bậc II</span>
                                    </span>
                                @elseif($comment->user && $comment->user->hasRole('VIP PRO MAX'))
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/55280-msp-star-vip.png" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="vip3">
                                        <span class="tooltip-text">Trưởng Lão Bậc III</span>
                                    </span>
                                @elseif($comment->user && $comment->user->hasRole('VIP SIÊU VIỆT'))
                                    <span class="tooltip-icon">
                                        <img src="https://cdn3.emoji.gg/emojis/2336-vipgif.gif" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="vipmax">
                                        <img src="https://cdn3.emoji.gg/emojis/53879-bluevip.png" width="30px"
                                            style="margin-left:5px;margin-top:-10px;" alt="vipmax">
                                        <span class="tooltip-text">Thái Thượng</span>
                                    </span>
                                @endif
                            @else
                                <span>👤 Người dùng không tồn tại</span>
                            @endif
                        </a>
                    </div>

                    <!-- Admin Actions -->
                    <div class="admin-actions">
                        @if (
                            $comment->level == 0 &&
                                auth()->check() &&
                                (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod') || auth()->user()->hasRole('Content')))
                            <span class="delete-comment text-danger ms-2" style="cursor: pointer;"
                                data-id="{{ $comment->id }}" title="Xóa bình luận">
                                <i class="fas fa-trash-alt"></i>
                            </span>
                        @endif

                        @if ($comment->level == 0 && auth()->check() && auth()->user()->hasRole('Admin'))
                            <button class="btn btn-sm pin-comment ms-2" data-id="{{ $comment->id }}"
                                title="{{ $comment->is_pinned ? 'Bỏ ghim' : 'Ghim' }}">
                                @if ($comment->is_pinned)
                                    <i class="fas fa-thumbtack text-warning"></i>
                                @else
                                    <i class="fas fa-thumbtack"></i>
                                @endif
                            </button>
                        @endif
                    </div>
                </div>

                <div class="comment-content mb-3" id="comment-{{ $comment->id }}">
                    @if ($comment->user && $comment->user->hasRole('VIP SIÊU VIỆT'))
                        <div class="vip-super-role" data-text="{{ strip_tags($comment->comment) }}">
                            {!! \App\Helpers\Helper::parseLinks($comment->comment) !!}
                        </div>
                    @else
                        {!! \App\Helpers\Helper::parseLinks($comment->comment) !!}
                    @endif

                    @if ($comment->is_edited)
                        <div class="edited-badge">
                            <small class="text-muted">
                                <i class="fa-solid fa-edit"></i> Đã chỉnh sửa
                                @if ($comment->edited_at)
                                    {{ $comment->edited_at->locale('vi')->diffForHumans() }}
                                @endif
                            </small>
                        </div>
                    @endif
                </div>

                <div class="comment-actions">
                    <div class="left-actions">
                        <span class="comment-time">
                            <i class="far fa-clock"></i> {{ $comment->created_at->locale('vi')->diffForHumans() }}
                        </span>

                        @if ($comment->level < 1 && auth()->check())
                            <button class="reply-btn" style="cursor: pointer;" data-id="{{ $comment->id }}">
                                <i class="fa-solid fa-reply"></i> Trả lời
                            </button>
                        @endif

                        @if (auth()->check() && auth()->id() == $comment->user_id)
                            <button class="edit-btn" style="cursor: pointer;" data-id="{{ $comment->id }}"
                                title="Chỉnh sửa bình luận">
                                <i class="fa-solid fa-edit"></i> Sửa
                            </button>
                        @endif

                        @if ($comment->is_edited)
                            <button class="history-btn" style="cursor: pointer;" data-id="{{ $comment->id }}"
                                title="Xem lịch sử chỉnh sửa">
                                <i class="fa-solid fa-history"></i> Lịch sử
                            </button>
                        @endif
                    </div>

                    <div class="reaction-wrapper position-relative d-flex align-items-center"
                        data-id="{{ $comment->id }}">
                        <button class="btn btn-sm smiley-btn" title="Thêm cảm xúc">
                            <i class="fa-regular fa-face-smile"></i>
                        </button>

                        <div class="reaction-group d-flex gap-1 p-1 bg-white border rounded shadow-sm">
                            <button class="btn btn-sm reaction-btn" data-type="like" data-id="{{ $comment->id }}"
                                title="Thích">
                                <i class="fas fa-thumbs-up"></i>
                            </button>
                            <button class="btn btn-sm reaction-btn" data-type="dislike"
                                data-id="{{ $comment->id }}" title="Không thích">
                                <i class="fas fa-thumbs-down"></i>
                            </button>
                            <button class="btn btn-sm reaction-btn" data-type="haha" data-id="{{ $comment->id }}"
                                title="Haha">
                                <i class="fa-solid fa-face-laugh-squint"></i>
                            </button>
                            <button class="btn btn-sm reaction-btn" data-type="tym" data-id="{{ $comment->id }}"
                                title="Tim">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                            <button class="btn btn-sm reaction-btn" data-type="angry" data-id="{{ $comment->id }}"
                                title="Giận">
                                <i class="fa-solid fa-face-angry"></i>
                            </button>
                            <button class="btn btn-sm reaction-btn" data-type="sad" data-id="{{ $comment->id }}"
                                title="Buồn">
                                <i class="fa-solid fa-face-frown"></i>
                            </button>
                        </div>

                        @php
                            $reactionTypes = ['like', 'dislike', 'haha', 'tym', 'angry', 'sad'];
                            $reactionIcons = [
                                'like' => 'fa-thumbs-up',
                                'dislike' => 'fa-thumbs-down',
                                'haha' => 'fa-face-laugh',
                                'tym' => 'fa-heart',
                                'angry' => 'fa-face-angry',
                                'sad' => 'fa-face-sad-tear',
                            ];
                            $reactionColors = [
                                'like' => 'primary',
                                'dislike' => 'secondary',
                                'haha' => 'warning',
                                'tym' => 'danger',
                                'angry' => 'danger',
                                'sad' => 'warning',
                            ];

                            $reactionCounts = [];
                            if ($comment->reactions) {
                                foreach ($reactionTypes as $type) {
                                    $reactionCounts[$type] = $comment->reactions->where('type', $type)->count();
                                }
                            } else {
                                foreach ($reactionTypes as $type) {
                                    $reactionCounts[$type] = 0;
                                }
                            }

                            $userReactionType = null;
                            if (auth()->check() && $comment->reactions) {
                                $userReaction = $comment->reactions->where('user_id', auth()->id())->first();
                                $userReactionType = $userReaction ? $userReaction->type : null;
                            }
                        @endphp

                        <div id="reaction-display-{{ $comment->id }}" class="reaction-display">
                            <div class="d-flex gap-1 mt-1">
                                @foreach ($reactionTypes as $type)
                                    @php
                                        $count = $reactionCounts[$type] ?? 0;
                                    @endphp

                                    @if ($count > 0)
                                        <button
                                            class="btn btn-sm d-flex align-items-center gap-1 px-2 py-1
                                                reaction-{{ $type }} border-0 rounded-pill reaction-display-btn">
                                            <i class="fa-solid {{ $reactionIcons[$type] }}"></i>
                                            <span class="{{ $type }}s-count">{{ $count }}</span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($comment->replies && $comment->replies->count() > 0)
                <ul class="comments mt-3 fb-reply-border">
                    @foreach ($comment->replies as $reply)
                        @include('Frontend.components.comments-item', ['comment' => $reply])
                    @endforeach
                </ul>
            @endif
        </div>
    </li>
</div>



