@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa chương')

@section('main-content')
    <div class="chapter-edit-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Truyện</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.show', $chapter->story) }}">{{ $chapter->story->name }}</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa chương</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-edit icon-title"></i>
                    <h5>Chỉnh sửa chương</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.chapters.update', $chapter) }}" method="POST" class="chapter-form" id="chapter-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="form-group">
                            <label class="form-label-custom">Truyện</label>
                            <div class="story-info-display">
                                <div class="story-name">{{ $chapter->story->name }}</div>
                                <div class="story-meta">ID: {{ $chapter->story->id }} | Tác giả: {{ $chapter->story->author->name ?? 'N/A' }}</div>
                            </div>
                            <input type="hidden" name="story_id" value="{{ $chapter->story_id }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên chương <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" 
                                           class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                           value="{{ old('name', $chapter->name) }}" required>
                                    <div class="error-message" id="error-name">
                                        @error('name')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="chapter" class="form-label-custom">
                                        Số chương <span class="required-mark">*</span>
                                    </label>
                                    <input type="number" id="chapter" name="chapter" 
                                           class="custom-input {{ $errors->has('chapter') ? 'input-error' : '' }}"
                                           value="{{ old('chapter', $chapter->chapter) }}" min="1" required>
                                    <div class="error-message" id="error-chapter">
                                        @error('chapter')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Thông tin bổ sung</label>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Lượt xem:</span>
                                    <span class="info-value">{{ number_format($chapter->views) }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Trạng thái:</span>
                                    <span class="status-badge {{ $chapter->is_new ? 'status-new' : 'status-old' }}">
                                        {{ $chapter->is_new ? 'Mới' : 'Cũ' }}
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Ngày tạo:</span>
                                    <span class="info-value">{{ $chapter->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Cập nhật:</span>
                                    <span class="info-value">{{ $chapter->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content" class="custom-input {{ $errors->has('content') ? 'input-error' : '' }}" rows="15" required>{{ old('content', $chapter->content) }}</textarea>
                            <div class="error-message" id="error-content">
                                @error('content')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.stories.show', $chapter->story_id) }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật chương
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .story-info-display {
        background: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 15px;
    }

    .story-name {
        font-size: 16px;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .story-meta {
        font-size: 14px;
        color: #6c757d;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        background: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 15px;
    }

    .info-label {
        font-size: 12px;
        color: #6c757d;
        font-weight: 500;
    }

    .info-value {
        font-size: 14px;
        color: #495057;
        font-weight: 600;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-new {
        background: #d4edda;
        color: #155724;
    }

    .status-old {
        background: #f8d7da;
        color: #721c24;
    }


    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('#chapter-form').submit(function(e) {
            e.preventDefault();

            $('.error-message').empty();
            $('.input-error').removeClass('input-error');

            const formData = new FormData($(this)[0]);
            const submitBtn = $('.save-button');
            const originalBtnText = submitBtn.html();

            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
            submitBtn.prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);

                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message || 'Chương đã được cập nhật thành công!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect || "{{ route('admin.stories.show', $chapter->story_id) }}";
                    });
                },
                error: function(xhr) {
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);

                    if (xhr.status === 422) {
                        const response = xhr.responseJSON;
                        
                        // Xử lý lỗi từ middleware (không có errors object)
                        if (response.message && !response.errors) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: response.message
                            });
                            return;
                        }
                        
                        // Xử lý lỗi validation thông thường
                        if (response.errors) {
                            $.each(response.errors, function(field, messages) {
                                const fieldElement = $(`[name="${field}"]`);
                                const errorElement = $(`#error-${field}`);

                                fieldElement.addClass('input-error');
                                errorElement.text(messages[0]);
                            });

                            $('html, body').animate({
                                scrollTop: $('.input-error').first().offset().top - 100
                            }, 500);
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Có lỗi xảy ra khi cập nhật chương!'
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
