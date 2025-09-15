@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa người dùng')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                <li class="breadcrumb-item current">Chỉnh sửa</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-user-edit icon-title"></i>
                    <h5>Chỉnh sửa người dùng</h5>
                </div>
                <div class="user-meta">
                    <div class="user-badge name">
                        <i class="fas fa-user"></i>
                        <span>{{ $user->name }}</span>
                    </div>
                    <div class="user-badge email">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="user-badge roles-count">
                        <i class="fas fa-user-tag"></i>
                        <span>{{ $user->roles->count() }} vai trò</span>
                    </div>
                    <div class="user-badge created">
                        <i class="fas fa-calendar"></i>
                        <span>Ngày tạo: {{ $user->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                @php
                    $smtpSetting = \App\Models\SMTPSetting::first();
                    $isSuperAdmin = $smtpSetting && $smtpSetting->admin_email === auth()->user()->email;
                    $isUserSuperAdmin = $smtpSetting && $smtpSetting->admin_email === $user->email;
                @endphp

                @if($isUserSuperAdmin && !$isSuperAdmin)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Cảnh báo:</strong> Bạn không có quyền chỉnh sửa Super Admin này!
                    </div>
                    <div class="form-actions">
                        <a href="{{ route('admin.users.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                @else
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="user-form" id="user-form">
                        @csrf
                        @method('PUT')

                        <div class="form-tabs">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-label-custom">
                                            Tên người dùng <span class="required-mark">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" 
                                               class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                               value="{{ old('name', $user->name) }}" required>
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
                                               value="{{ old('email', $user->email) }}" required>
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
                                            Mật khẩu mới
                                        </label>
                                        <input type="password" id="password" name="password" 
                                               class="custom-input {{ $errors->has('password') ? 'input-error' : '' }}">
                                        <div class="form-hint">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Để trống nếu không muốn thay đổi mật khẩu</span>
                                        </div>
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
                                            Xác nhận mật khẩu mới
                                        </label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" 
                                               class="custom-input">
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
                                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
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
                                <i class="fas fa-save"></i> Cập nhật người dùng
                            </button>
                        </div>
                    </form>
                @endif
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

        .user-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 14px;
            color: #495057;
        }

        .user-badge i {
            color: #007bff;
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
            .user-meta {
                flex-direction: column;
            }
            
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
                            text: response.message || 'Người dùng đã được cập nhật thành công!',
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
                                text: 'Có lỗi xảy ra khi cập nhật người dùng!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
