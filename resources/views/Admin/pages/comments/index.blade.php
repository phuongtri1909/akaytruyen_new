@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý bình luận')

@section('main-content')
    <div class="comment-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Bình luận</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-comments icon-title"></i>
                    <h5>Danh sách bình luận</h5>
                </div>
                @can('xoa_binh_luan')
                    <div class="bulk-actions">
                        <button type="button" class="action-button bulk-delete-btn" disabled>
                            <i class="fas fa-trash"></i> Xóa đã chọn
                        </button>
                    </div>
                @endcan
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.comments.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="story_filter">Truyện</label>
                            <select id="story_filter" name="story_id" class="filter-input">
                                <option value="">Tất cả truyện</option>
                                @foreach ($stories as $story)
                                    <option value="{{ $story->id }}"
                                        {{ request('story_id') == $story->id ? 'selected' : '' }}>
                                        {{ $story->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="chapter_filter">Chương</label>
                            <select id="chapter_filter" name="chapter_id" class="filter-input">
                                <option value="">Tất cả chương</option>
                                @foreach ($chapters as $chapter)
                                    <option value="{{ $chapter->id }}"
                                        {{ request('chapter_id') == $chapter->id ? 'selected' : '' }}>
                                        Chương {{ $chapter->chapter }}: {{ $chapter->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4">
                            <label for="search_filter">Tìm kiếm</label>
                            <input type="text" id="search_filter" name="search" class="filter-input"
                                placeholder="Tìm theo nội dung hoặc tên người dùng" value="{{ request('search') }}">
                        </div>
                        <div class="col-2">
                            <label>&nbsp;</label>
                            <div class="filter-actions">
                                <button type="submit" class="filter-btn">
                                    <i class="fas fa-filter"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <a href="{{ route('admin.comments.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('story_id') || request('chapter_id') || request('search'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('story_id'))
                            <span class="filter-tag">
                                <span>Truyện:
                                    {{ $stories->where('id', request('story_id'))->first()->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('story_id', 'chapter_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('chapter_id'))
                            <span class="filter-tag">
                                <span>Chương:
                                    {{ $chapters->where('id', request('chapter_id'))->first()->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('chapter_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('search'))
                            <span class="filter-tag">
                                <span>Tìm kiếm: {{ request('search') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('search')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($comments->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        @if (request('story_id') || request('chapter_id') || request('search'))
                            <h4>Không tìm thấy bình luận nào</h4>
                            <p>Không có bình luận nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.comments.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có bình luận nào</h4>
                            <p>Chưa có bình luận nào trong hệ thống.</p>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @can('xoa_binh_luan')
                                        <th class="column-small text-center">
                                            <input type="checkbox" id="select-all" class="select-all-checkbox">
                                        </th>
                                    @endcan
                                    <th class="column-small">STT</th>
                                    <th class="column-medium">Người bình luận</th>
                                    <th class="column-large">Nội dung</th>
                                    <th class="column-medium">Truyện - Chương</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comments as $index => $comment)
                                    <tr data-comment-id="{{ $comment->id }}">
                                        @can('xoa_binh_luan')
                                            <td class="text-center">
                                                <input type="checkbox" class="comment-checkbox" value="{{ $comment->id }}">
                                            </td>
                                        @endcan
                                        <td class="text-center">
                                            {{ ($comments->currentPage() - 1) * $comments->perPage() + $index + 1 }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $comment->user->id) }}"
                                                class="user-info text-decoration-none">
                                                <div class="user-avatar">
                                                    @if ($comment->user->avatar)
                                                        <img src="{{ Storage::url($comment->user->avatar) }}"
                                                            alt="{{ $comment->user->name }}" class="avatar-img">
                                                    @else
                                                        <div class="avatar-placeholder">
                                                            {{ substr($comment->user->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="user-details">
                                                    <div class="user-name">{{ $comment->user->name }}</div>
                                                    <div class="user-email">{{ $comment->user->email }}</div>
                                                </div>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="comment-content">
                                                <div class="comment-text">
                                                    {{ Str::limit($comment->comment, 100) }}
                                                </div>
                                                @if ($comment->replies->count() > 0)
                                                    <div class="replies-info">
                                                        <button type="button" class="toggle-replies-btn" data-comment-id="{{ $comment->id }}">
                                                            <i class="fas fa-reply"></i>
                                                            {{ $comment->replies->count() }} phản hồi
                                                            <i class="fas fa-chevron-down toggle-icon"></i>
                                                        </button>
                                                    </div>
                                                @endif
                                                @if ($comment->is_pinned)
                                                    <span class="pinned-badge">
                                                        <i class="fas fa-thumbtack"></i> Đã ghim
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="story-chapter-info">
                                                @if ($comment->chapter->story)
                                                    <a href="{{ route('story', $comment->chapter->story->id) }}"
                                                        class="story-name text-decoration-none">{{ $comment->chapter->story->name ?? 'N/A' }}</a>
                                                @else
                                                    <span class="story-name text-decoration-none">N/A</span>
                                                @endif
                                                
                                                <br>

                                                @if ($comment->chapter->story)
                                                <a href="{{ route('chapter', ['slugStory' => $comment->chapter->story->slug, 'slugChapter' => $comment->chapter->slug]) }}"
                                                    class="chapter-name text-decoration-none">
                                                        Chương {{ $comment->chapter->chapter }}: {{ $comment->chapter->name }}
                                                    </a>
                                                @else
                                                    <span class="chapter-name text-decoration-none">N/A</span>
                                                @endif

                                            </div>
                                        </td>
                                        <td class="comment-date">
                                            {{ $comment->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @can('ghim_binh_luan')
                                                    <button class="action-icon pin-icon toggle-pin"
                                                        data-comment-id="{{ $comment->id }}"
                                                        title="{{ $comment->is_pinned ? 'Bỏ ghim' : 'Ghim' }}">
                                                        <i class="fas fa-thumbtack"></i>
                                                    </button>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                                @can('xoa_binh_luan')
                                                    @include('components.delete-form', [
                                                        'id' => $comment->id,
                                                        'route' => route('admin.comments.destroy', $comment),
                                                        'message' => 'Bạn có chắc chắn muốn xóa bình luận này?',
                                                    ])
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Replies Section -->
                                    @if ($comment->replies->count() > 0)
                                        <tr class="replies-row" id="replies-{{ $comment->id }}" style="display: none;">
                                            <td colspan="{{ auth()->user()->can('xoa_binh_luan') ? '7' : '6' }}">
                                                <div class="replies-container">
                                                    <div class="replies-header">
                                                        <h6>Phản hồi ({{ $comment->replies->count() }})</h6>
                                                    </div>
                                                    <div class="replies-list">
                                                        @foreach ($comment->replies as $reply)
                                                            <div class="reply-item" data-reply-id="{{ $reply->id }}">
                                                                <div class="reply-content">
                                                                    <div class="reply-user">
                                                                        <div class="reply-avatar">
                                                                            @if ($reply->user->avatar)
                                                                                <img src="{{ Storage::url($reply->user->avatar) }}" alt="{{ $reply->user->name }}" class="avatar-img">
                                                                            @else
                                                                                <div class="avatar-placeholder">
                                                                                    {{ substr($reply->user->name, 0, 1) }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="reply-user-info">
                                                                            <div class="reply-user-name">{{ $reply->user->name }}</div>
                                                                            <div class="reply-date">{{ $reply->created_at->format('d/m/Y H:i') }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="reply-text">
                                                                        {{ $reply->comment }}
                                                                    </div>
                                                                    @can('xoa_binh_luan')
                                                                        @include('components.delete-form', [
                                                                            'id' => $reply->id,
                                                                            'route' => route('admin.comments.destroy', $reply),
                                                                            'message' => 'Bạn có chắc chắn muốn xóa phản hồi này?',
                                                                        ])
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $comments->firstItem() ?? 0 }} đến {{ $comments->lastItem() ?? 0 }} của
                            {{ $comments->total() }} bình luận
                        </div>
                        <div class="pagination-controls">
                            {{ $comments->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: #e3f2fd;
            color: #1976d2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
        }

        .user-details {
            flex: 1;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .user-email {
            font-size: 12px;
            color: #6c757d;
        }

        .comment-content {
            position: relative;
        }

        .comment-text {
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            margin-bottom: 5px;
        }

        .replies-info {
            font-size: 12px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .pinned-badge {
            background: #ffc107;
            color: #000;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .story-chapter-info {
            font-size: 14px;
        }

        .story-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }

        .chapter-name {
            font-size: 12px;
            color: #6c757d;
        }

        .comment-date {
            font-size: 14px;
            color: #6c757d;
        }

        .pin-icon {
            color: #ffc107 !important;
        }

        .pin-icon:hover {
            color: #e0a800 !important;
        }

        .disabled-icon {
            color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.5;
        }

        .disabled-icon:hover {
            color: #6c757d !important;
        }

        /* Bulk Actions */
        .bulk-actions {
            display: flex;
            gap: 10px;
        }

        .bulk-delete-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Checkbox Styles */
        .select-all-checkbox,
        .comment-checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        /* Toggle Replies Button */
        .toggle-replies-btn {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 2px 0;
            transition: color 0.2s;
        }

        .toggle-replies-btn:hover {
            color: #007bff;
        }

        .toggle-icon {
            font-size: 10px;
            transition: transform 0.2s;
        }

        .toggle-replies-btn.active .toggle-icon {
            transform: rotate(180deg);
        }

        /* Replies Container */
        .replies-container {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }

        .replies-header {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e9ecef;
        }

        .replies-header h6 {
            margin: 0;
            color: #495057;
            font-weight: 600;
        }

        .replies-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .reply-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px;
            position: relative;
        }

        .reply-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .reply-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reply-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .reply-avatar .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .reply-avatar .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: #e3f2fd;
            color: #1976d2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .reply-user-info {
            flex: 1;
        }

        .reply-user-name {
            font-weight: 600;
            color: #333;
            font-size: 13px;
        }

        .reply-date {
            font-size: 11px;
            color: #6c757d;
        }

        .reply-text {
            font-size: 13px;
            line-height: 1.4;
            color: #333;
            margin-left: 42px;
        }

        .reply-actions {
            position: absolute;
            top: 12px;
            right: 12px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Select All Checkbox
            $('#select-all').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.comment-checkbox').prop('checked', isChecked);
                updateBulkDeleteButton();
            });

            // Individual Checkbox Change
            $(document).on('change', '.comment-checkbox', function() {
                updateBulkDeleteButton();
                updateSelectAllCheckbox();
            });

            // Update Bulk Delete Button State
            function updateBulkDeleteButton() {
                const checkedCount = $('.comment-checkbox:checked').length;
                const bulkDeleteBtn = $('.bulk-delete-btn');
                
                if (checkedCount > 0) {
                    bulkDeleteBtn.prop('disabled', false);
                    bulkDeleteBtn.html(`<i class="fas fa-trash"></i> Xóa đã chọn (${checkedCount})`);
                } else {
                    bulkDeleteBtn.prop('disabled', true);
                    bulkDeleteBtn.html('<i class="fas fa-trash"></i> Xóa đã chọn');
                }
            }

            // Update Select All Checkbox State
            function updateSelectAllCheckbox() {
                const totalCheckboxes = $('.comment-checkbox').length;
                const checkedCheckboxes = $('.comment-checkbox:checked').length;
                const selectAllCheckbox = $('#select-all');
                
                if (checkedCheckboxes === 0) {
                    selectAllCheckbox.prop('indeterminate', false);
                    selectAllCheckbox.prop('checked', false);
                } else if (checkedCheckboxes === totalCheckboxes) {
                    selectAllCheckbox.prop('indeterminate', false);
                    selectAllCheckbox.prop('checked', true);
                } else {
                    selectAllCheckbox.prop('indeterminate', true);
                }
            }

            // Bulk Delete
            $('.bulk-delete-btn').on('click', function() {
                const checkedIds = $('.comment-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (checkedIds.length === 0) {
                    Swal.fire('Thông báo', 'Vui lòng chọn ít nhất một bình luận để xóa.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: `Bạn có chắc chắn muốn xóa ${checkedIds.length} bình luận đã chọn?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Có, xóa ngay',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-outline-secondary ms-1'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send bulk delete request
                        fetch('{{ route("admin.comments.bulk-delete") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                comment_ids: checkedIds
                            })
                        })
                        .then(response => {
                            if (response.ok) {
                                location.reload();
                            } else {
                                throw new Error('Network response was not ok');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Lỗi!', 'Có lỗi xảy ra khi xóa bình luận.', 'error');
                        });
                    }
                });
            });

            // Toggle Replies
            $(document).on('click', '.toggle-replies-btn', function() {
                const commentId = $(this).data('comment-id');
                const repliesRow = $(`#replies-${commentId}`);
                const button = $(this);
                const icon = button.find('.toggle-icon');

                if (repliesRow.is(':visible')) {
                    repliesRow.slideUp();
                    button.removeClass('active');
                } else {
                    repliesRow.slideDown();
                    button.addClass('active');
                }
            });

            // Toggle pin comment
            $(document).on('click', '.toggle-pin', function() {
                const commentId = $(this).data('comment-id');
                const button = $(this);

                Swal.fire({
                    title: 'Xác nhận',
                    text: 'Bạn có muốn thay đổi trạng thái ghim bình luận này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Có',
                    cancelButtonText: 'Không',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-secondary ms-1'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ route('admin.comments.toggle-pin', ':id') }}`.replace(':id',
                                commentId), {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    location.reload();
                                } else {
                                    throw new Error('Network response was not ok');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire('Lỗi!', 'Có lỗi xảy ra khi thay đổi trạng thái ghim.',
                                    'error');
                            });
                    }
                });
            });

            // Auto load chapters when story changes
            $('#story_filter').on('change', function() {
                const storyId = $(this).val();
                const chapterSelect = $('#chapter_filter');

                chapterSelect.empty().append('<option value="">Tất cả chương</option>');

                if (storyId) {
                    fetch(`/admin/chapters-by-story/${storyId}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(chapter => {
                                chapterSelect.append(
                                    `<option value="${chapter.id}">Chương ${chapter.chapter}: ${chapter.name}</option>`
                                );
                            });
                        })
                        .catch(error => {
                            console.error('Error loading chapters:', error);
                        });
                }
            });
        });
    </script>
@endpush
