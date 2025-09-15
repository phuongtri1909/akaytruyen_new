@extends('admin.layouts.sidebar')

@section('title', 'Chi tiết truyện')

@section('main-content')
    <div class="story-detail-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Truyện</a></li>
                <li class="breadcrumb-item current">Chi tiết</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-book icon-title"></i>
                    <h5>Chi tiết truyện</h5>
                </div>
                <div class="card-actions">
                    @can('sua_truyen')
                        <a href="{{ route('admin.stories.edit', $story) }}" class="action-button">
                            <i class="fas fa-edit"></i> Chỉnh sửa
                        </a>
                    @endcan
                    <a href="{{ route('admin.stories.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="card-content">
                <div class="story-detail-grid">
                    <!-- Story Image -->
                    <div class="story-image-section">
                        @if ($story->image)
                            <img src="{{ Storage::url($story->image) }}" alt="{{ $story->name }}" class="story-main-image">
                        @else
                            <div class="no-image-large">
                                <i class="fas fa-image"></i>
                                <span>Không có hình ảnh</span>
                            </div>
                        @endif
                    </div>

                    <!-- Story Info -->
                    <div class="story-info-section">
                        <div class="story-header">
                            <h2 class="story-title">{{ $story->name }}</h2>
                            <div class="story-meta">
                                <span class="story-slug">
                                    <i class="fas fa-link"></i> {{ $story->slug }}
                                </span>
                            </div>
                        </div>

                        <div class="story-details">
                            <div class="detail-item">
                                <span class="detail-label">Tác giả:</span>
                                <span class="detail-value">{{ $story->author->name ?? 'N/A' }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Danh mục:</span>
                                <div class="detail-value">
                                    @foreach ($story->categories as $category)
                                        <span class="category-badge">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Số chương:</span>
                                <span class="detail-value chapters-count">{{ $story->chapters_count }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Trạng thái:</span>
                                <div class="status-badges">
                                    @if ($story->status)
                                        <span class="status-badge status-active">Hoạt động</span>
                                    @else
                                        <span class="status-badge status-inactive">Tạm dừng</span>
                                    @endif

                                    @if ($story->is_full)
                                        <span class="status-badge status-full">Hoàn thành</span>
                                    @endif

                                    @if ($story->is_new)
                                        <span class="status-badge status-new">Mới</span>
                                    @endif

                                    @if ($story->is_hot)
                                        <span class="status-badge status-hot">Hot</span>
                                    @endif
                                </div>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Ngày tạo:</span>
                                <span class="detail-value">{{ $story->created_at->format('d/m/Y H:i') }}</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Cập nhật lần cuối:</span>
                                <span class="detail-value">{{ $story->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Story Description -->
                <div class="story-description-section">
                    <h4 class="section-title toggle-description" style="cursor: pointer;">
                        <i class="fas fa-align-left"></i> Mô tả
                        <i class="fas fa-chevron-down description-toggle-icon"></i>
                    </h4>
                    <div class="story-description" style="display: none;">
                        {!! $story->desc !!}
                    </div>
                </div>

                @canany(['xem_danh_sach_chuong', 'them_chuong', 'sua_chuong', 'xoa_chuong'])
                    <div class="chapters-section">
                        <div class="section-header">
                            <h4 class="section-title">
                                <i class="fas fa-list"></i> Danh sách chương
                            </h4>
                            <div class="chapters-header-actions">
                                <span class="chapters-count-badge">{{ $story->chapters_count }} chương</span>
                                @can('them_chuong')
                                    <a href="{{ route('admin.chapters.create', ['story_id' => $story->id]) }}"
                                        class="action-button">
                                        <i class="fas fa-plus"></i> Thêm chương
                                    </a>
                                @endcan
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="filter-section">
                            <form method="GET" action="{{ route('admin.stories.show', $story) }}" class="filter-form">
                                <div class="filter-row">
                                    <div class="filter-group">
                                        <label for="search" class="filter-label">Tìm kiếm:</label>
                                        <input type="text" id="search" name="search" class="filter-input"
                                            value="{{ request('search') }}" placeholder="ID chương hoặc tên chương...">
                                    </div>
                                    <div class="filter-actions">
                                        <button type="submit" class="filter-btn">
                                            <i class="fas fa-search"></i> Tìm
                                        </button>
                                        <a href="{{ route('admin.stories.show', $story) }}" class="clear-btn">
                                            <i class="fas fa-times"></i> Xóa
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        @if ($chapters->count() > 0)
                            <div class="data-table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th class="column-small">Chương</th>
                                            <th class="column-medium">Tên chương</th>
                                            <th class="column-small text-center">Lượt xem</th>
                                            <th class="column-small text-center">Trạng thái</th>
                                            <th class="column-small text-center">Ngày tạo</th>
                                            <th class="column-small text-center">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($chapters as $chapter)
                                            <tr>
                                                <td class="text-center">
                                                    <span class="chapter-badge">Chương {{ $chapter->chapter }}</span>
                                                </td>
                                                <td class="item-title">
                                                    <div class="title-text">{{ $chapter->name }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <i class="fas fa-eye"></i> {{ number_format($chapter->views) }}
                                                </td>
                                                <td class="text-center">
                                                    <span
                                                        class="status-badge {{ $chapter->is_new ? 'status-new' : 'status-old' }}">
                                                        {{ $chapter->is_new ? 'Mới' : 'Cũ' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">{{ $chapter->created_at->format('d/m/Y') }}</td>
                                                <td class="text-center">
                                                    <div class="action-buttons-wrapper">
                                                        @can('sua_chuong')
                                                            <a href="{{ route('admin.chapters.edit', $chapter) }}"
                                                                class="action-icon edit-icon text-decoration-none" title="Sửa">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @else
                                                            <span class="action-icon disabled-icon" title="Không có quyền">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        @endcan
                                                        @can('sua_chuong')
                                                            <button
                                                                class="action-icon toggle-icon {{ $chapter->is_new ? 'active' : '' }}"
                                                                data-id="{{ $chapter->id }}" title="Đánh dấu mới/cũ">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        @else
                                                            <span class="action-icon disabled-icon" title="Không có quyền">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        @endcan
                                                        @can('xoa_chuong')
                                                            @include('components.delete-form', [
                                                                'id' => $chapter->id,
                                                                'route' => route(
                                                                    'admin.chapters.destroy',
                                                                    $chapter),
                                                                'message' => "Bạn có chắc chắn muốn xóa chương '{$chapter->name}'?",
                                                            ])
                                                        @else
                                                            <span class="action-icon disabled-icon" title="Không có quyền">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="pagination-wrapper">
                                <div class="pagination-info">
                                    Hiển thị {{ $chapters->firstItem() ?? 0 }} đến {{ $chapters->lastItem() ?? 0 }} của
                                    {{ $chapters->total() }} chương
                                </div>
                                <div class="pagination-controls">
                                    {{ $chapters->appends(request()->query())->links('components.paginate') }}
                                </div>
                            </div>
                        @else
                            <div class="chapters-empty">
                                <div class="empty-content">
                                    <i class="fas fa-book-open"></i>
                                    <h5>Chưa có chương nào</h5>
                                    <p>Truyện này chưa có chương nào được tạo.</p>
                                    @can('them_chuong')
                                        <a href="{{ route('admin.chapters.create', ['story_id' => $story->id]) }}"
                                            class="action-button">
                                            <i class="fas fa-plus"></i> Thêm chương đầu tiên
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    </div>
                @endcanany
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .story-detail-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .story-image-section {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .story-main-image {
            width: 100%;
            max-width: 280px;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #dee2e6;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .no-image-large {
            width: 100%;
            max-width: 280px;
            height: 400px;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .no-image-large i {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .story-info-section {
            padding: 20px 0;
        }

        .story-header {
            margin-bottom: 25px;
        }

        .story-title {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
            line-height: 1.3;
        }

        .story-slug {
            color: #6c757d;
            font-size: 14px;
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .story-details {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
            min-width: 120px;
            flex-shrink: 0;
        }

        .detail-value {
            color: #6c757d;
            flex: 1;
        }

        .category-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
            display: inline-block;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-new {
            background: #d4edda;
            color: #155724;
        }

        .status-old {
            background: #f8d7da;
            color: #721c24;
        }

        .story-description-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }

        .toggle-description {
            cursor: pointer;
            color: #007bff;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }

        .toggle-description:hover {
            color: #0056b3;
        }

        .description-toggle-icon {
            transition: transform 0.3s ease;
        }

        .toggle-description.active .description-toggle-icon {
            transform: rotate(180deg);
        }

        .story-description {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            line-height: 1.6;
            color: #495057;
        }

        .chapters-section {
            border-top: 2px solid #dee2e6;
            padding-top: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chapters-count-badge {
            background: #007bff;
            color: white;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
        }

        .chapters-header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Filter Section */
        .filter-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .filter-form {
            margin: 0;
        }

        .filter-row {
            display: flex;
            align-items: end;
            gap: 20px;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 300px;
        }

        .filter-label {
            display: block;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .filter-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .filter-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
        }

        .filter-actions {
            display: flex;
            gap: 10px;
        }

        .filter-btn,
        .clear-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn {
            background: #007bff;
            color: white;
        }

        .filter-btn:hover {
            background: #0056b3;
            color: white;
        }

        .clear-btn {
            background: #6c757d;
            color: white;
        }

        .clear-btn:hover {
            background: #545b62;
            color: white;
        }

        /* Chapter Badge */
        .chapter-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Action Buttons */
        .action-buttons-wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
            max-width: 120px;
            margin: 0 auto;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 12px;
        }

        .edit-icon {
            background: #ffc107;
            color: #212529;
        }

        .edit-icon:hover {
            background: #e0a800;
            color: #212529;
        }

        .toggle-icon {
            background: #6c757d;
            color: white;
        }

        .toggle-icon:hover {
            background: #545b62;
        }

        .toggle-icon.active {
            background: #28a745;
        }

        .disabled-icon {
            background: #e9ecef;
            color: #6c757d;
            cursor: not-allowed;
        }

        .chapters-empty {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
        }

        .empty-content i {
            font-size: 48px;
            color: #6c757d;
        }

        .empty-content h5 {
            color: #495057;
            margin-bottom: 10px;
        }

        .empty-content p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .story-detail-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .story-image-section {
                order: 2;
            }

            .story-info-section {
                order: 1;
            }

            .story-main-image,
            .no-image-large {
                max-width: 200px;
                height: 300px;
            }

            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group {
                min-width: auto;
            }

            .filter-actions {
                justify-content: center;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Toggle description
            $('.toggle-description').click(function() {
                const description = $(this).next('.story-description');
                const icon = $(this).find('.description-toggle-icon');

                if (description.is(':visible')) {
                    description.slideUp(300);
                    $(this).removeClass('active');
                } else {
                    description.slideDown(300);
                    $(this).addClass('active');
                }
            });

            // Chapter status toggle
            $('.toggle-icon').click(function() {
                const chapterId = $(this).data('id');
                const button = $(this);

                $.ajax({
                    url: "{{ route('admin.chapters.toggle-status', ':id') }}".replace(':id',
                        chapterId),
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        button.toggleClass('active');
                        location.reload();
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra khi cập nhật trạng thái!'
                        });
                    }
                });
            });
        });
    </script>
@endpush
