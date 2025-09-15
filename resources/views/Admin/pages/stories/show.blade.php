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
                        @if($story->image)
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
                                    @foreach($story->categories as $category)
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
                                    @if($story->status)
                                        <span class="status-badge status-active">Hoạt động</span>
                                    @else
                                        <span class="status-badge status-inactive">Tạm dừng</span>
                                    @endif

                                    @if($story->is_full)
                                        <span class="status-badge status-full">Hoàn thành</span>
                                    @endif

                                    @if($story->is_new)
                                        <span class="status-badge status-new">Mới</span>
                                    @endif

                                    @if($story->is_hot)
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
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </h4>
                    <div class="story-description" style="display: none;">
                        {!! $story->desc !!}
                    </div>
                </div>

                <!-- Chapters Section (Placeholder for future) -->
                <div class="chapters-section">
                    <div class="section-header">
                        <h4 class="section-title">
                            <i class="fas fa-list"></i> Danh sách chương
                        </h4>
                        <span class="chapters-count-badge">{{ $story->chapters_count }} chương</span>
                    </div>
                    <div class="chapters-placeholder">
                        <div class="placeholder-content">
                            <i class="fas fa-book-open"></i>
                            <h5>Quản lý chương</h5>
                            <p>Chức năng quản lý chương sẽ được thêm vào sau</p>
                            <button class="action-button" disabled>
                                <i class="fas fa-plus"></i> Thêm chương (Sắp có)
                            </button>
                        </div>
                    </div>
                </div>
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
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .chapters-count {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 6px 12px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-full {
            background: #d1ecf1;
            color: #0c5460;
        }

        .status-new {
            background: #fff3cd;
            color: #856404;
        }

        .status-hot {
            background: #f8d7da;
            color: #721c24;
        }

        .story-description-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-description {
            transition: color 0.3s ease;
        }

        .toggle-description:hover {
            color: #007bff;
        }

        .toggle-icon {
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        .toggle-description.active .toggle-icon {
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

        .chapters-placeholder {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
        }

        .placeholder-content i {
            font-size: 48px;
            color: #6c757d;
        }

        .placeholder-content h5 {
            color: #495057;
            margin-bottom: 10px;
        }

        .placeholder-content p {
            color: #6c757d;
            margin-bottom: 20px;
        }

        .card-actions {
            display: flex;
            gap: 10px;
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
        }
    </style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('.toggle-description').click(function() {
        const description = $(this).next('.story-description');
        const icon = $(this).find('.toggle-icon');
        
        if (description.is(':visible')) {
            description.slideUp(300);
            $(this).removeClass('active');
        } else {
            description.slideDown(300);
            $(this).addClass('active');
        }
    });
});
</script>
@endpush




