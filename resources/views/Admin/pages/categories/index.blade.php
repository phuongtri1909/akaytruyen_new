@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý danh mục')

@section('main-content')
    <div class="category-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Danh mục</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-folder icon-title"></i>
                    <h5>Danh sách danh mục</h5>
                </div>
                @can('them_danh_muc')
                    <a href="{{ route('admin.categories.create') }}" class="action-button">
                        <i class="fas fa-plus"></i> Thêm danh mục
                    </a>
                @endcan
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form action="{{ route('admin.categories.index') }}" method="GET" class="filter-form">
                    <div class="row">
                        <div class="col-6">
                            <label for="name_filter">Tên danh mục</label>
                            <input type="text" id="name_filter" name="name" class="filter-input"
                                placeholder="Tìm theo tên danh mục" value="{{ request('name') }}">
                        </div>
                        <div class="col-6">
                            <label for="slug_filter">Slug</label>
                            <input type="text" id="slug_filter" name="slug" class="filter-input"
                                placeholder="Tìm theo slug" value="{{ request('slug') }}">
                        </div>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-filter"></i> Lọc
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="filter-clear-btn">
                            <i class="fas fa-times"></i> Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>

            <div class="card-content">
                @if (request('name') || request('slug'))
                    <div class="active-filters">
                        <span class="active-filters-title">Đang lọc: </span>
                        @if (request('name'))
                            <span class="filter-tag">
                                <span>Tên: {{ request('name') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('name')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                        @if (request('slug'))
                            <span class="filter-tag">
                                <span>Slug: {{ request('slug') }}</span>
                                <a href="{{ request()->url() }}?{{ http_build_query(request()->except('slug')) }}"
                                    class="remove-filter">×</a>
                            </span>
                        @endif
                    </div>
                @endif

                @if ($categories->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-folder"></i>
                        </div>
                        @if (request('name') || request('slug'))
                            <h4>Không tìm thấy danh mục nào</h4>
                            <p>Không có danh mục nào phù hợp với bộ lọc hiện tại.</p>
                            <a href="{{ route('admin.categories.index') }}" class="action-button">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        @else
                            <h4>Chưa có danh mục nào</h4>
                            <p>Bắt đầu bằng cách thêm danh mục đầu tiên.</p>
                            <a href="{{ route('admin.categories.create') }}" class="action-button">
                                <i class="fas fa-plus"></i> Thêm danh mục mới
                            </a>
                        @endif
                    </div>
                @else
                    <div class="data-table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th class="column-small">STT</th>
                                    <th class="column-large">Tên danh mục</th>
                                    <th class="column-large">Slug</th>
                                    <th class="column-medium">Mô tả</th>
                                    <th class="column-small text-center">Số truyện</th>
                                    <th class="column-medium">Ngày tạo</th>
                                    <th class="column-small text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $index => $category)
                                    <tr>
                                        <td class="text-center">
                                            {{ ($categories->currentPage() - 1) * $categories->perPage() + $index + 1 }}
                                        </td>
                                        <td class="item-title">
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="slug-text">{{ $category->slug }}</span>
                                        </td>
                                        <td>
                                            <div class="description-text">
                                                {{ $category->desc ? Str::limit($category->desc, 50) : 'Không có mô tả' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="stories-count">{{ $category->stories_count }}</span>
                                        </td>
                                        <td class="category-date">
                                            {{ $category->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            <div class="action-buttons-wrapper">
                                                <a href="{{ route('admin.categories.show', $category) }}"
                                                    class="action-icon view-icon text-decoration-none" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('sua_danh_muc')
                                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                                        class="action-icon edit-icon text-decoration-none" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @else
                                                    <span class="action-icon disabled-icon" title="Không có quyền">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endcan
                                                @can('xoa_danh_muc')
                                                    @include('components.delete-form', [
                                                        'id' => $category->id,
                                                        'route' => route('admin.categories.destroy', $category),
                                                        'message' => "Bạn có chắc chắn muốn xóa danh mục '{$category->name}'?",
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
                            Hiển thị {{ $categories->firstItem() ?? 0 }} đến {{ $categories->lastItem() ?? 0 }} của
                            {{ $categories->total() }} danh mục
                        </div>
                        <div class="pagination-controls">
                            {{ $categories->appends(request()->query())->links('components.paginate') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .slug-text {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #6c757d;
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
        }

        .description-text {
            font-size: 14px;
            color: #6c757d;
            line-height: 1.4;
        }

        .stories-count {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-date {
            font-size: 14px;
            color: #6c757d;
        }

        .disabled-icon {
            color: #6c757d !important;
            cursor: not-allowed !important;
            opacity: 0.5;
        }

        .disabled-icon:hover {
            color: #6c757d !important;
        }
    </style>
@endpush
