@extends('Admin.layouts.sidebar')

@section('title', 'Thêm vai trò mới')

@section('main-content')
    <div class="category-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}">Vai trò</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-plus icon-title"></i>
                    <h5>Thêm vai trò mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.roles.store') }}" method="POST" class="role-form" id="role-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="form-group">
                            <label for="name" class="form-label-custom">
                                Tên vai trò <span class="required-mark">*</span>
                            </label>
                            <input type="text" id="name" name="name"
                                class="custom-input {{ $errors->has('name') ? 'input-error' : '' }}"
                                value="{{ old('name') }}" placeholder="Nhập tên vai trò">
                            <div class="error-message" id="error-name">
                                @error('name')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        @can('gan_quyen_cho_vai_tro')
                            <div class="form-group">
                                <label class="form-label-custom">
                                    Quyền <span class="required-mark">*</span>
                                </label>
                                <div class="permissions-container">
                                    <div class="permissions-header">
                                        <div class="permissions-actions">
                                            <button type="button" class="select-all-btn" id="selectAll">
                                                <i class="fas fa-check-square"></i> Chọn tất cả
                                            </button>
                                            <button type="button" class="deselect-all-btn" id="deselectAll">
                                                <i class="fas fa-square"></i> Bỏ chọn tất cả
                                            </button>
                                        </div>
                                        <div class="permissions-search">
                                            <input type="text" id="permissionSearch" placeholder="Tìm kiếm quyền..." class="search-input">
                                        </div>
                                    </div>
                                    
                                    <div class="permissions-grid" id="permissionsGrid">
                                        @foreach ($permissions as $permission)
                                            <div class="permission-item" data-permission="{{ strtolower($permission->name) }}">
                                                <div class="custom-checkbox">
                                                    <input type="checkbox" id="permission_{{ $permission->id }}" 
                                                        name="permissions[]" value="{{ $permission->id }}"
                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label for="permission_{{ $permission->id }}" class="permission-label">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="error-message" id="error-permissions">
                                    @error('permissions')
                                        {{ $message }}
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Thông báo:</strong> Bạn chỉ có quyền tạo vai trò mà không thể gán quyền. Vai trò sẽ được tạo mà không có quyền nào.
                                </div>
                            </div>
                        @endcan
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.roles.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Lưu vai trò
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .permissions-container {
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            background: #f8f9fa;
        }

        .permissions-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .permissions-actions {
            display: flex;
            gap: 10px;
        }

        .select-all-btn, .deselect-all-btn {
            padding: 8px 16px;
            border: 1px solid #007bff;
            background: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .deselect-all-btn {
            background: #6c757d;
            border-color: #6c757d;
        }

        .select-all-btn:hover {
            background: #0056b3;
            border-color: #0056b3;
        }

        .deselect-all-btn:hover {
            background: #545b62;
            border-color: #545b62;
        }

        .permissions-search {
            flex: 1;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 14px;
        }

        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
        }

        .permission-item {
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .permission-item:hover {
            background: #e9ecef;
            border-color: #007bff;
        }

        .permission-item.hidden {
            display: none;
        }

        .permission-label {
            font-size: 14px;
            color: #333;
            cursor: pointer;
            margin: 0;
            padding-left: 25px;
            position: relative;
        }

        .permission-label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #ced4da;
            border-radius: 3px;
            background: white;
            transition: all 0.3s ease;
        }

        .permission-item input[type="checkbox"]:checked + .permission-label::before {
            background: #007bff;
            border-color: #007bff;
        }

        .permission-item input[type="checkbox"]:checked + .permission-label::after {
            content: '✓';
            position: absolute;
            left: 2px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .permission-item input[type="checkbox"] {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .permissions-header {
                flex-direction: column;
                align-items: stretch;
            }

            .permissions-actions {
                justify-content: center;
            }

            .permissions-search {
                max-width: none;
            }

            .permissions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Select all permissions
            $('#selectAll').click(function() {
                $('#permissionsGrid input[type="checkbox"]').prop('checked', true);
            });

            // Deselect all permissions
            $('#deselectAll').click(function() {
                $('#permissionsGrid input[type="checkbox"]').prop('checked', false);
            });

            // Search permissions
            $('#permissionSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.permission-item').each(function() {
                    const permissionName = $(this).data('permission');
                    if (permissionName.includes(searchTerm)) {
                        $(this).removeClass('hidden');
                    } else {
                        $(this).addClass('hidden');
                    }
                });
            });

            // Form validation and submission
            $('#role-form').submit(function(e) {
                e.preventDefault();

                // Clear previous errors
                $('.error-message').empty();
                $('.input-error').removeClass('input-error');

                const formData = $(this).serialize();
                const submitBtn = $('.save-button');
                const originalBtnText = submitBtn.html();

                // Disable button and show loading state
                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang xử lý...');
                submitBtn.prop('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        // Reset button
                        submitBtn.html(originalBtnText);
                        submitBtn.prop('disabled', false);

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: response.message || 'Vai trò đã được tạo thành công!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || "{{ route('admin.roles.index') }}";
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

                                if (field === 'permissions') {
                                    $('.permissions-container').addClass('input-error');
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
                                text: 'Có lỗi xảy ra, vui lòng thử lại sau.'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
