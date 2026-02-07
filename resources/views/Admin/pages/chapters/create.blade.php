@extends('Admin.layouts.sidebar')

@section('title', 'Thêm chương mới')

@section('main-content')
    <div class="chapter-create-container">
        <!-- Breadcrumb -->
        <div class="content-breadcrumb">
            <ol class="breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.index') }}">Truyện</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.stories.show', $story) }}">{{ $story->name }}</a></li>
                <li class="breadcrumb-item current">Thêm chương</li>
            </ol>
        </div>

        <div class="form-card">
            <div class="form-header">
                <div class="form-title">
                    <i class="fas fa-list icon-title"></i>
                    <h5>Thêm chương mới</h5>
                </div>
            </div>

            <div class="form-body">
                @include('components.alert', ['alertType' => 'alert'])

                <form action="{{ route('admin.chapters.store') }}" method="POST" class="chapter-form" id="chapter-form">
                    @csrf

                    <div class="form-tabs">
                        <div class="form-group">
                            <label class="form-label-custom">Truyện</label>
                            <div class="story-info-display">
                                <div class="story-name">{{ $story->name }}</div>
                                <div class="story-meta">ID: {{ $story->id }} | Tác giả: {{ $story->author->name ?? 'N/A' }}</div>
                            </div>
                            <input type="hidden" name="story_id" value="{{ $story->id }}">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label-custom">
                                        Tên chương <span class="required-mark">*</span>
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
                                    <label for="chapter" class="form-label-custom">
                                        Số chương <span class="required-mark">*</span>
                                    </label>
                                    <input type="number" id="chapter" name="chapter" 
                                           class="custom-input {{ $errors->has('chapter') ? 'input-error' : '' }}"
                                           value="{{ old('chapter', $nextChapterNumber ?? 1) }}" min="1" required>
                                    <div class="error-message" id="error-chapter">
                                        @error('chapter')
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Kiểu nội dung <span class="required-mark">*</span></label>
                                    <div class="radio-group">
                                        <label class="radio-label">
                                            <input type="radio" name="content_type" value="plain" {{ old('content_type', 'plain') === 'plain' ? 'checked' : '' }}>
                                            <span>Text thông thường (textarea)</span>
                                        </label>
                                        <label class="radio-label">
                                            <input type="radio" name="content_type" value="rich" {{ old('content_type') === 'rich' ? 'checked' : '' }}>
                                            <span>Rich content (CKEditor - kéo thả ảnh)</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label-custom">Trạng thái <span class="required-mark">*</span></label>
                                    <div class="radio-group">
                                        <label class="radio-label">
                                            <input type="radio" name="status" value="published" {{ old('status', 'published') === 'published' ? 'checked' : '' }}>
                                            <span>Xuất bản ngay</span>
                                        </label>
                                        <label class="radio-label">
                                            <input type="radio" name="status" value="draft" {{ old('status') === 'draft' ? 'checked' : '' }}>
                                            <span>Nháp</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="scheduled-publish-group" style="display: none;">
                            <label for="scheduled_publish_at" class="form-label-custom">Hẹn giờ đăng (tùy chọn)</label>
                            <input type="datetime-local" id="scheduled_publish_at" name="scheduled_publish_at" 
                                   class="custom-input" value="{{ old('scheduled_publish_at') }}">
                            <small class="text-muted">Chỉ áp dụng khi trạng thái là Nháp. Tới giờ hẹn, chương sẽ tự động xuất bản.</small>
                            <div class="error-message" id="error-scheduled_publish_at">
                                @error('scheduled_publish_at')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="content" class="form-label-custom">
                                Nội dung <span class="required-mark">*</span>
                            </label>
                            <textarea id="content" name="content" class="custom-input {{ $errors->has('content') ? 'input-error' : '' }}" rows="15">{{ old('content') }}</textarea>
                            <div class="error-message" id="error-content">
                                @error('content')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.stories.show', $story) }}" class="back-button">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Tạo chương
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

    .radio-group { display: flex; flex-direction: column; gap: 8px; }
    .radio-label { display: flex; align-items: center; gap: 8px; cursor: pointer; }
    .radio-label input { margin: 0; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    $(document).ready(function() {
        var ckEditor = null;

        function initCKEditor() {
            if (ckEditor) return;
            var uploadUrl = "{{ url(route('admin.chapters.upload-image')) }}";
            var csrfToken = "{{ csrf_token() }}";
            CKEDITOR.replace('content', {
                height: 400,
                imageUploadUrl: uploadUrl,
                filebrowserImageUploadUrl: uploadUrl,
                filebrowserUploadMethod: 'form',
                extraPlugins: 'uploadimage,image',
                removePlugins: 'elementspath',
                toolbar: [
                    { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo'] },
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
                    { name: 'links', items: ['Link', 'Unlink'] },
                    { name: 'insert', items: ['Image', 'UploadImage'] }
                ]
            });
            ckEditor = CKEDITOR.instances.content;
            ckEditor.on('fileUploadRequest', function(evt) {
                evt.data.fileLoader.xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                evt.data.requestData._token = csrfToken;
            });
        }

        function destroyCKEditor() {
            if (ckEditor) {
                ckEditor.destroy();
                ckEditor = null;
            }
        }

        function toggleContentType() {
            var contentType = $('input[name="content_type"]:checked').val();
            if (contentType === 'rich') {
                var content = $('#content').val();
                destroyCKEditor();
                setTimeout(function() {
                    $('#content').val(content);
                    initCKEditor();
                }, 100);
            } else {
                if (ckEditor) {
                    var data = ckEditor.getData();
                    destroyCKEditor();
                    $('#content').val(data).show();
                }
            }
        }

        function toggleScheduledPublish() {
            var status = $('input[name="status"]:checked').val();
            if (status === 'draft') {
                $('#scheduled-publish-group').show();
            } else {
                $('#scheduled-publish-group').hide();
            }
        }

        $('input[name="content_type"]').on('change', toggleContentType);
        $('input[name="status"]').on('change', toggleScheduledPublish);

        if ($('input[name="content_type"]:checked').val() === 'rich') {
            initCKEditor();
        }
        toggleScheduledPublish();

        $('#chapter-form').submit(function(e) {
            e.preventDefault();

            if (ckEditor) {
                ckEditor.updateElement();
            }

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
                        text: response.message || 'Chương đã được tạo thành công!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = response.redirect || "{{ route('admin.stories.show', $story) }}";
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
                            text: 'Có lỗi xảy ra khi tạo chương!'
                        });
                    }
                }
            });
        });
    });
</script>
@endpush
