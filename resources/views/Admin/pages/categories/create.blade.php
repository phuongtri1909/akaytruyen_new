@extends('admin.layouts.sidebar')

@section('title', 'Thêm danh mục')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-folder-plus icon-title"></i>
                    <h5>Thêm danh mục mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.categories.store') }}" method="POST" class="category-form" id="category-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="name" class="form-label-custom">
                                Tên danh mục <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                   value="{{ old('name') }}" required>
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
                                      placeholder="Nhập mô tả cho danh mục">{{ old('desc') }}</textarea>
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
                            <i class="fas fa-save"></i> Tạo danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

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
                            text: response.message || 'Danh mục đã được tạo thành công!',
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
                                text: 'Có lỗi xảy ra khi tạo danh mục!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
