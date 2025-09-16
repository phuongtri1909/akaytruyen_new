@extends('Admin.layouts.sidebar')

@section('title', 'Quản lý xếp hạng')

@section('main-content')
    <div class="rating-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item current">Xếp hạng</li>
            </ol>
        </div>

        <div class="content-card">
            <div class="card-top">
                <div class="card-title">
                    <i class="fas fa-star icon-title"></i>
                    <h5>Quản lý xếp hạng truyện</h5>
                </div>
                @can('sua_danh_gia')
                    <button type="button" class="action-button" id="btn_save_ratings">
                        <i class="fas fa-save"></i> Cập nhật xếp hạng
                    </button>
                @endcan
            </div>

            <div class="card-content">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="ratings-section">
                            <div class="row">
                                <!-- Xếp hạng ngày -->
                                <div class="col-12 col-md-4 mb-4">
                                    <div class="rating-column">
                                        <div class="rating-header">
                                            <h6><i class="fas fa-calendar-day"></i> Xếp hạng ngày</h6>
                                        </div>
                                        <div class="rating-list" id="rating-day-list">
                                            @if ($ratingsDay)
                                                @php
                                                    $value = json_decode($ratingsDay->value, true);
                                                @endphp
                                                @foreach ($value as $item)
                                                    <div class="rating-item" data-story-id="{{ $item['id'] }}" data-story-name="{{ $item['name'] }}">
                                                        <div class="rating-item-content">
                                                            <div class="drag-handle">
                                                                <i class="fas fa-grip-vertical"></i>
                                                            </div>
                                                            <span class="story-name">{{ $item['name'] }}</span>
                                                            @can('xoa_danh_gia')
                                                                <button class="btn-remove-story" type="button" title="Xóa khỏi xếp hạng">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Xếp hạng tháng -->
                                <div class="col-12 col-md-4 mb-4">
                                    <div class="rating-column">
                                        <div class="rating-header">
                                            <h6><i class="fas fa-calendar-alt"></i> Xếp hạng tháng</h6>
                                        </div>
                                        <div class="rating-list" id="rating-month-list">
                                            @if ($ratingsMonth)
                                                @php
                                                    $value = json_decode($ratingsMonth->value, true);
                                                @endphp
                                                @foreach ($value as $item)
                                                    <div class="rating-item" data-story-id="{{ $item['id'] }}" data-story-name="{{ $item['name'] }}">
                                                        <div class="rating-item-content">
                                                            <div class="drag-handle">
                                                                <i class="fas fa-grip-vertical"></i>
                                                            </div>
                                                            <span class="story-name">{{ $item['name'] }}</span>
                                                            @can('xoa_danh_gia')
                                                                <button class="btn-remove-story" type="button" title="Xóa khỏi xếp hạng">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Xếp hạng all time -->
                                <div class="col-12 col-md-4 mb-4">
                                    <div class="rating-column">
                                        <div class="rating-header">
                                            <h6><i class="fas fa-trophy"></i> Xếp hạng all time</h6>
                                        </div>
                                        <div class="rating-list" id="rating-alltime-list">
                                            @if ($ratingsAllTime)
                                                @php
                                                    $value = json_decode($ratingsAllTime->value, true);
                                                @endphp
                                                @foreach ($value as $item)
                                                    <div class="rating-item" data-story-id="{{ $item['id'] }}" data-story-name="{{ $item['name'] }}">
                                                        <div class="rating-item-content">
                                                            <div class="drag-handle">
                                                                <i class="fas fa-grip-vertical"></i>
                                                            </div>
                                                            <span class="story-name">{{ $item['name'] }}</span>
                                                            @can('xoa_danh_gia')
                                                                <button class="btn-remove-story" type="button" title="Xóa khỏi xếp hạng">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="stories-section">
                            <div class="stories-header">
                                <h6><i class="fas fa-book"></i> Danh sách truyện</h6>
                                <p class="text-muted">Kéo thả hoặc click để thêm vào xếp hạng</p>
                            </div>
                            <div class="stories-list">
                                @if ($stories->count() > 0)
                                    @foreach ($stories as $story)
                                        <div class="story-item" data-story-id="{{ $story->id }}" data-story-name="{{ $story->name }}">
                                            <div class="story-info">
                                                <span class="story-name">{{ $story->name }}</span>
                                                <small class="story-author">{{ $story->author->name ?? 'N/A' }}</small>
                                            </div>
                                            <div class="story-actions">
                                                @can('sua_danh_gia')
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item add-to-rating" href="#" data-type="day">
                                                                <i class="fas fa-calendar-day"></i> Thêm vào xếp hạng ngày
                                                            </a></li>
                                                            <li><a class="dropdown-item add-to-rating" href="#" data-type="month">
                                                                <i class="fas fa-calendar-alt"></i> Thêm vào xếp hạng tháng
                                                            </a></li>
                                                            <li><a class="dropdown-item add-to-rating" href="#" data-type="all_time">
                                                                <i class="fas fa-trophy"></i> Thêm vào xếp hạng all time
                                                            </a></li>
                                                        </ul>
                                                    </div>
                                                @endcan
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-state">
                                        <i class="fas fa-book"></i>
                                        <p>Không có truyện nào</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
   
    <style>
        .ratings-section {
            margin-bottom: 20px;
        }

        .rating-column {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            height: 500px;
            display: flex;
            flex-direction: column;
        }

        .rating-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .rating-header h6 {
            margin: 0;
            color: #495057;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rating-list {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
        }

        .rating-item {
            margin-bottom: 8px;
        }

        .rating-item-content {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }

        .rating-item:hover .rating-item-content {
            background: #f8f9fa;
            border-color: #007bff;
        }

        .drag-handle {
            cursor: move;
            color: #6c757d;
            margin-right: 10px;
            padding: 4px;
        }

        .drag-handle:hover {
            color: #007bff;
        }

        .rating-item-content .story-name {
            font-weight: 500;
            color: #333;
            flex: 1;
        }

        .btn-remove-story {
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 4px;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.2s;
        }

        .btn-remove-story:hover {
            background: #c82333;
        }

        .stories-section {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            height: 500px;
            display: flex;
            flex-direction: column;
        }

        .stories-header {
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .stories-header h6 {
            margin: 0 0 5px 0;
            color: #495057;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stories-header p {
            margin: 0;
            font-size: 12px;
        }

        .stories-list {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
        }

        .story-item {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }

        .story-item:hover {
            background: #f8f9fa;
            border-color: #007bff;
        }

        .story-info {
            flex: 1;
        }

        .story-info .story-name {
            font-weight: 500;
            color: #333;
            display: block;
            margin-bottom: 2px;
        }

        .story-info .story-author {
            color: #6c757d;
            font-size: 11px;
        }

        .story-actions .dropdown-toggle {
            padding: 4px 8px;
            font-size: 12px;
        }

        .dropdown-item {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-state p {
            margin: 0;
            font-size: 14px;
        }

        /* Sortable drag and drop styles */
        .rating-placeholder {
            background: #e3f2fd;
            border: 2px dashed #2196f3;
            border-radius: 6px;
            height: 50px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2196f3;
            font-size: 14px;
        }

        .ui-sortable-helper {
            z-index: 9999;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transform: rotate(2deg);
        }

        .order-changed {
            border-color: #28a745 !important;
            box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
        }

        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }

        .btn-loading::after {
            content: "";
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Add story to rating
            $(document).on('click', '.add-to-rating', function(e) {
                e.preventDefault();
                
                const storyItem = $(this).closest('.story-item');
                const storyId = storyItem.data('story-id');
                const storyName = storyItem.data('story-name');
                const type = $(this).data('type');
                
                const typeMap = {
                    'day': { container: '#rating-day-list', name: 'ngày' },
                    'month': { container: '#rating-month-list', name: 'tháng' },
                    'all_time': { container: '#rating-alltime-list', name: 'all time' }
                };
                
                const config = typeMap[type];
                const container = $(config.container);
                
                // Check if story already exists
                const existingItem = container.find(`[data-story-id="${storyId}"]`);
                
                if (existingItem.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Thông báo',
                        text: `Truyện "${storyName}" đã có trong xếp hạng ${config.name}`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                
                // Add new item
                const newItemHtml = `
                    <div class="rating-item" data-story-id="${storyId}" data-story-name="${storyName}">
                        <div class="rating-item-content">
                            <div class="drag-handle">
                                <i class="fas fa-grip-vertical"></i>
                            </div>
                            <span class="story-name">${storyName}</span>
                            @can('xoa_danh_gia')
                                <button class="btn-remove-story" type="button" title="Xóa khỏi xếp hạng">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                `;
                
                container.append(newItemHtml);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: `Đã thêm "${storyName}" vào xếp hạng ${config.name}`,
                    timer: 1500,
                    showConfirmButton: false
                });
            });
            
            // Remove story from rating
            $(document).on('click', '.btn-remove-story', function() {
                const storyName = $(this).closest('.rating-item').data('story-name');
                const ratingItem = $(this).closest('.rating-item');
                
                Swal.fire({
                    title: 'Xác nhận xóa',
                    text: `Bạn có chắc chắn muốn xóa "${storyName}" khỏi xếp hạng?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Có, xóa',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-outline-secondary ms-1'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        ratingItem.fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Đã xóa',
                            text: `Đã xóa "${storyName}" khỏi xếp hạng`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });
            
            // Make rating lists sortable
            $('.rating-list').sortable({
                handle: '.drag-handle',
                placeholder: 'rating-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer',
                cursor: 'move',
                opacity: 0.8,
                update: function(event, ui) {
                    // Optional: Add visual feedback when order changes
                    $(this).addClass('order-changed');
                    setTimeout(() => {
                        $(this).removeClass('order-changed');
                    }, 1000);
                }
            });
            
            // Save ratings
            $('#btn_save_ratings').on('click', function() {
                const button = $(this);
                const originalText = button.html();
                
                button.addClass('btn-loading').prop('disabled', true);
                
                const body = {};
                
                // Serialize day ratings
                const dayItems = $('#rating-day-list .rating-item').map(function() {
                    return {
                        id: $(this).data('story-id'),
                        name: $(this).data('story-name')
                    };
                }).get();
                if (dayItems.length > 0) {
                    body.day = dayItems;
                }
                
                // Serialize month ratings
                const monthItems = $('#rating-month-list .rating-item').map(function() {
                    return {
                        id: $(this).data('story-id'),
                        name: $(this).data('story-name')
                    };
                }).get();
                if (monthItems.length > 0) {
                    body.month = monthItems;
                }
                
                // Serialize all time ratings
                const allTimeItems = $('#rating-alltime-list .rating-item').map(function() {
                    return {
                        id: $(this).data('story-id'),
                        name: $(this).data('story-name')
                    };
                }).get();
                if (allTimeItems.length > 0) {
                    body.all_time = allTimeItems;
                }
                
                fetch('{{ route("admin.ratings.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(body)
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi cập nhật xếp hạng.'
                    });
                })
                .finally(() => {
                    button.removeClass('btn-loading').prop('disabled', false).html(originalText);
                });
            });
        });
    </script>
@endpush