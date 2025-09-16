@extends('Admin.layouts.sidebar')

@section('title', 'Thêm người dùng')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-user-plus icon-title"></i>
                    <h5>Thêm người dùng mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.users.store') }}" method="POST" class="user-form" id="user-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên người dùng <span class="required-mark">*</span>
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
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label-custom">
                                        Email <span class="required-mark">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" 
                                           class="custom-input {{ $errors->has('email') ? 'input-error' : '' }}"
                                           value="{{ old('email') }}" required>
                                    <div class="error-message" id="error-email">
                                        @error('email')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password" class="form-label-custom">
                                        Mật khẩu <span class="required-mark">*</span>
                                    </label>
                                    <input type="password" id="password" name="password" 
                                           class="custom-input {{ $errors->has('password') ? 'input-error' : '' }}"
                                           required>
                                    <div class="error-message" id="error-password">
                                        @error('password')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation" class="form-label-custom">
                                        Xác nhận mật khẩu <span class="required-mark">*</span>
                                    </label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                           class="custom-input" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="role" class="form-label-custom">
                                Vai trò <span class="required-mark">*</span>
                            </label>
                            <select id="role" name="role" class="custom-input" required>
                                <option value="">Chọn vai trò</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="error-message" id="error-role">
                                @error('role')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.users.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo người dùng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
            background-color: #fff !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #007bff !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            min-height: 38px;
            border-radius: 4px;
        }

        .roles-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .roles-header {
            margin-bottom: 15px;
        }

        .roles-actions {
            display: flex;
            gap: 10px;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .role-item {
            background: white;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .role-item:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.1);
        }

        .role-label {
            display: flex;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .role-name {
            font-weight: 600;
            color: #333;
        }

        .role-permissions-count {
            font-size: 12px;
            color: #6c757d;
        }

        .custom-checkbox input[type="checkbox"] {
            margin-right: 10px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
        }

        .section-title i {
            color: #007bff;
        }

        @media (max-width: 768px) {
            .roles-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#role').select2({
                placeholder: 'Chọn vai trò',
                allowClear: true,
                width: '100%'
            });

            // Form validation and submission
            $('#user-form').submit(function(e) {
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
                            text: response.message || 'Người dùng đã được tạo thành công!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || "{{ route('admin.users.index') }}";
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

                                if (field === 'roles') {
                                    $('.roles-container').addClass('input-error');
                                } else {
                                    fieldElement.addClass('input-error');
                                }
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
                                text: 'Có lỗi xảy ra khi tạo người dùng!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
