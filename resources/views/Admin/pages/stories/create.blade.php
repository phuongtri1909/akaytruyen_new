@extends('Admin.layouts.sidebar')

@section('title', 'Thêm truyện')

@section('main-content')
    <div class="story-form-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Truyện</a></li>
                <li class="breadcrumb-item current">Thêm mới</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-book-plus icon-title"></i>
                    <h5>Thêm truyện mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.stories.store') }}" method="POST" class="story-form" id="story-form" enctype="multipart/form-data">
                    @csrf

                    <div class="form-tabs">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên truyện <span class="required-mark">*</span>
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

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="author_id" class="form-label-custom">
                                        Tác giả <span class="required-mark">*</span>
                                    </label>
                                    <select id="author_id" name="author_id" class="custom-input" required>
                                        <option value="">Chọn tác giả</option>
                                        @foreach($authors as $author)
                                            <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                                {{ $author->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-message" id="error-author_id">
                                        @error('author_id')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="desc" class="form-label-custom">
                                Mô tả <span class="required-mark">*</span>
                            </label>
                            <textarea id="desc" name="desc" class="custom-input ckeditor {{ $errors->has('desc') ? 'input-error' : '' }}" required>{{ old('desc') }}</textarea>
                            <div class="error-message" id="error-desc">
                                @error('desc')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image" class="form-label-custom">
                                        Hình ảnh
                                        <span class="required-mark">*</span>
                                    </label>
                                    <input type="file" id="image" name="image" 
                                           class="custom-input {{ $errors->has('image') ? 'input-error' : '' }}"
                                           accept="image/*">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle"></i>
                                        <span>Định dạng: JPEG, PNG, JPG, GIF, WEBP. Tối đa 10MB</span>
                                    </div>
                                    <div class="error-message" id="error-image">
                                        @error('image')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Danh mục <span class="required-mark">*</span></label>
                                    <div class="categories-container">
                                        @foreach($categories as $category)
                                            <label class="category-checkbox">
                                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                                       {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                                                <span class="checkmark"></span>
                                                {{ $category->name }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="error-message" id="error-categories">
                                        @error('categories')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label-custom">Trạng thái</label>
                            <div class="status-options">
                                <label class="status-checkbox">
                                    <input type="checkbox" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Hoạt động
                                </label>
                                <label class="status-checkbox">
                                    <input type="checkbox" name="is_full" value="1" {{ old('is_full') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Hoàn thành
                                </label>
                                <label class="status-checkbox">
                                    <input type="checkbox" name="is_new" value="1" {{ old('is_new') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Truyện mới
                                </label>
                                <label class="status-checkbox">
                                    <input type="checkbox" name="is_hot" value="1" {{ old('is_hot') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Truyện hot
                                </label>
                                <label class="status-checkbox">
                                    <input type="checkbox" name="is_vip" value="1" {{ old('is_vip') ? 'checked' : '' }}>
                                    <span class="checkmark"></span>
                                    Truyện VIP
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.stories.index') }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo truyện
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .categories-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 15px;
            background: #f8f9fa;
        }

        .category-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #495057;
        }

        .category-checkbox input[type="checkbox"] {
            margin-right: 8px;
            display: none;
        }

        .status-options {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .status-checkbox {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
            color: #495057;
        }

        .status-checkbox input[type="checkbox"] {
            margin-right: 8px;
            display: none;
        }

        .checkmark {
            position: relative;
            display: inline-block;
            width: 18px;
            height: 18px;
            background-color: #fff;
            border: 2px solid #ced4da;
            border-radius: 3px;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .category-checkbox input[type="checkbox"]:checked + .checkmark,
        .status-checkbox input[type="checkbox"]:checked + .checkmark {
            background-color: #007bff;
            border-color: #007bff;
        }

        .category-checkbox input[type="checkbox"]:checked + .checkmark:after,
        .status-checkbox input[type="checkbox"]:checked + .checkmark:after {
            content: "";
            position: absolute;
            left: 5px;
            top: 2px;
            width: 4px;
            height: 8px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .ckeditor {
            min-height: 200px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script>
        $(document).ready(function() {
            CKEDITOR.replace('desc', {});

            $('#story-form').submit(function(e) {
                e.preventDefault();

                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }

                $('.error-message').empty();
                $('.input-error').removeClass('input-error');

                const formData = new FormData($(this)[0]);
                
                // Remove all category checkboxes first
                formData.delete('categories[]');
                
                // Only add selected categories
                $('input[name="categories[]"]:checked').each(function() {
                    formData.append('categories[]', $(this).val());
                });
                
                // Ensure other checkboxes are included in FormData
                $('input[type="checkbox"]:not([name="categories[]"])').each(function() {
                    if (!$(this).is(':checked')) {
                        formData.append($(this).attr('name'), '0');
                    }
                });
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
                            text: response.message || 'Truyện đã được tạo thành công!',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect || "{{ route('admin.stories.index') }}";
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

                                    if (field === 'categories') {
                                        $('.categories-container').addClass('input-error');
                                    } else {
                                        fieldElement.addClass('input-error');
                                    }
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
                                text: 'Có lỗi xảy ra khi tạo truyện!'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
