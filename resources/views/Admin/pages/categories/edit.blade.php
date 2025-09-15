@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa danh mục')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-folder-edit icon-title"></i>
                    <h5>Chỉnh sửa danh mục</h5>
                </div>
                <div class="category-meta">
                    <div class="category-badge name">
                        <i class="fas fa-folder"></i>
                        <span>{{ $category->name }}</span>
                    </div>
                    <div class="category-badge slug">
                        <i class="fas fa-link"></i>
                        <span>{{ $category->slug }}</span>
                    </div>
                    <div class="category-badge stories-count">
                        <i class="fas fa-book"></i>
                        <span>{{ $category->stories_count }} truyện</span>
                    </div>
                    <div class="category-badge created">
                        <i class="fas fa-calendar"></i>
                        <span>Ngày tạo: {{ $category->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="category-form" id="category-form">
                    @csrf
                    @method('PUT')

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="name" class="form-label-custom">
                                Tên danh mục <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                   value="{{ old('name', $category->name) }}" required>
                            <div class="error-message" id="error-name">
                                @error('name')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="desc" class="form-label-custom">
                                Mô tả
                            </label>
                            <textarea id="desc" name="desc" rows="4" 
                                      class="custom-input {{ $errors->has('desc') ? 'input-error' : '' }}"
                                      placeholder="Nhập mô tả cho danh mục">{{ old('desc', $category->desc) }}</textarea>
                            <div class="form-hint">
                                <i class="fas fa-info-circle"></i>
                                <span>Tối đa 1000 ký tự</span>
                            </div>
                            <div class="error-message" id="error-desc">
                                @error('desc')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.categories.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .category-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .category-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 14px;
            color: #495057;
        }

        .category-badge i {
            color: #007bff;
        }

        .category-badge.name {
            background: #e3f2fd;
            color: #1976d2;
        }

        .category-badge.slug {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .category-badge.stories-count {
            background: #e8f5e9;
            color: #2e7d32;
        }

        @media (max-width: 768px) {
            .category-meta {
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Form validation and submission
            $('#category-form').submit(function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.error-message').empty();
                $('.input-error').removeClass('input-error');

                const formData = new FormData($(this)[0]);
                const submitBtn = $('.save-button');
                const originalBtnText = submitBtn.html();

                // Disable button and show loading state
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
                        // Reset button
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Danh mục đã được cập nhật thành công!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || "{{ route('admin.categories.index') }}";
                        });
                    },
                    error: function(xhr) {
                        // Reset button
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);

                        // Handle validation errors
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;

                            $.each(errors, function(field, messages) {
                                const fieldElement = $(`[name="${field}"]`);
                                const errorElement = $(`#error-${field}`);

                                fieldElement.addClass('input-error');
                                errorElement.text(messages[0]);
                            });

                            // Scroll to first error
                            $('html, body').animate({
                                scrollTop: $('.input-error').first().offset().top - 100
                            }, 500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Có lỗi xảy ra khi cập nhật danh mục!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
