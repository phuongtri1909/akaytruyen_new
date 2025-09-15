@extends('admin.layouts.sidebar')

@section('title', 'Quản lý truyện')

@section('main-content')
    <div class="story-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Truyện</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-book icon-title"></i>
                    <h5>Danh sách truyện</h5>
                </div>
                @can('them_truyen')
                    <a href="{{ route('admin.stories.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm truyện
                    </a>
                @endcan
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.stories.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-3">
                            <label for="name_filter">Tên truyện</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên truyện" value="{{ request('name') }}">
                        </div>
                        <div class="col-3">
                            <label for="author_filter">Tác giả</label>
                            <select id="author_filter" name="author_id" class="filter-input">
                                <option value="">Tất cả tác giả</option>
                                @foreach ($authors as $author)
                                    <option value="{{ $author->id }}"
                                        {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                        {{ $author->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="category_filter">Danh mục</label>
                            <select id="category_filter" name="category_id" class="filter-input">
                                <option value="">Tất cả danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-3">
                            <label for="status_filter">Trạng thái</label>
                            <select id="status_filter" name="status" class="filter-input">
                                <option value="">Tất cả trạng thái</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tạm dừng</option>
                            </select>
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.stories.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>


            <div class="card-content">
                @if (request('name') || request('author_id') || request('category_id') || request('status'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('author_id'))
                            <span class="filter-tag">
                                <span>Tác giả: {{ $authors->find(request('author_id'))->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('author_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('category_id'))
                            <span class="filter-tag">
                                <span>Danh mục: {{ $categories->find(request('category_id'))->name ?? 'N/A' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('category_id')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('status') !== null)
                            <span class="filter-tag">
                                <span>Trạng thái: {{ request('status') == '1' ? 'Hoạt động' : 'Tạm dừng' }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('status')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($stories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        @if (request('name') || request('author_id') || request('category_id') || request('status'))
                            <h4>Không tìm thấy truyện nào</h4>
                            <p>Không có truyện nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.stories.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có truyện nào</h4>
                            <p>Bắt đầu bằng cách thêm truyện đầu tiên.</p>
                            <a href="{{ route('admin.stories.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm truyện mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-small">Hình ảnh</th>
                                    <th class="column-medium">Tên truyện</th>
                                    <th class="column-small">Tác giả</th>
                                    <th class="column-small text-center">Trạng thái</th>
                                    <th class="column-small text-center">Hoàn thành</th>
                                    <th class="column-small text-center">Mới</th>
                                    <th class="column-small text-center">Hot</th>
                                    <th class="column-small text-center">Số chương</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stories as $index => $story)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($stories->currentPage() - 1) * $stories->perPage() + $index + 1 }}
                                        </td>
                                        <td class="text-center">
                                            @if ($story->image)
                                                <img src="{{ Storage::url($story->image) }}" alt="{{ $story->name }}"
                                                    class="story-thumbnail">
                                            @else
                                                <div class="no-image">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="item-title">
                                            <div class="story-info">
                                                <strong>{{ $story->name }}</strong>
                                                <div class="story-categories">
                                                    @foreach ($story->categories as $category)
                                                        <span class="category-badge">{{ $category->name }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $story->author->name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <div class="switch-container">
                                                <label class="switch">
                                                    <input type="checkbox" 
                                                           {{ $story->status ? 'checked' : '' }}
                                                           onchange="toggleStatus({{ $story->id }}, 'status', this.checked)">
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="switch-container">
                                                <label class="switch">
                                                    <input type="checkbox" 
                                                           {{ $story->is_full ? 'checked' : '' }}
                                                           onchange="toggleStatus({{ $story->id }}, 'is_full', this.checked)">
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="switch-container">
                                                <label class="switch">
                                                    <input type="checkbox" 
                                                           {{ $story->is_new ? 'checked' : '' }}
                                                           onchange="toggleStatus({{ $story->id }}, 'is_new', this.checked)">
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="switch-container">
                                                <label class="switch">
                                                    <input type="checkbox" 
                                                           {{ $story->is_hot ? 'checked' : '' }}
                                                           onchange="toggleStatus({{ $story->id }}, 'is_hot', this.checked)">
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="chapters-count">{{ $story->chapters_count }}</span>
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                @canany(['xem_danh_sach_chuong', 'them_chuong', 'sua_chuong', 'xoa_chuong'])
                                                    <a href="{{ route('admin.stories.show', $story) }}"
                                                        class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcanany
                                                @can('sua_truyen')
                                                    <a href="{{ route('admin.stories.edit', $story) }}"
                                                        class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                                @can('them_chuong')
                                                    <a href="{{ route('admin.chapters.create', ['story_id' => $story->id]) }}"
                                                        class="action-icon chapter-icon text-decoration-none" title="Thêm chương">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                                @can('xoa_truyen')
                                                    @include('components.delete-form', [
                                                        'id' => $story->id,
                                                        'route' => route('admin.stories.destroy', $story),
                                                        'message' => "Bạn có chắc chắn muốn xóa truyện '{$story->name}'?",
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

                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Hiển thị {{ $stories->firstItem() ?? 0 }} đến {{ $stories->lastItem() ?? 0 }} của
                            {{ $stories->total() }} truyện
                        </div>
                        <div class="pagination-controls">
                            {{ $stories->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .story-thumbnail {
            width: 70px;
            height: 125px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .no-image {
            width: 50px;
            height: 50px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        .story-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .story-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 3px;
        }

        .category-badge {
            padding: 2px 6px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 500;
        }

        .chapters-count {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Switch Styles */
        .switch-container {
            display: flex;
            justify-content: center;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #28a745;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .disabled-icon {
            color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.5;
        }

        .action-buttons-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            max-width: 80px;
        }

        .chapter-icon {
            background: #17a2b8 !important;
            color: white !important;
        }

        .chapter-icon:hover {
            background: #138496 !important;
            color: white !important;
        }

        .disabled-icon:hover {
            color: #6c757d !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Toggle status function
        function toggleStatus(storyId, field, value) {
            $.ajax({
                url: `/admin/stories/${storyId}/toggle-status`,
                type: 'POST',
                data: {
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    // Revert the switch state
                    const checkbox = event.target;
                    checkbox.checked = !checkbox.checked;
                    
                    const message = xhr.responseJSON?.message || 'Có lỗi xảy ra';
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: message
                    });
                }
            });
        }

        // Auto submit filter form on change
        document.getElementById('status_filter').addEventListener('change', function() {
            document.querySelector('.filter-form').submit();
        });
    </script>
@endpush


