@extends('admin.layouts.sidebar')

@section('title', 'Chỉnh sửa SEO')

@section('main-content')
<div class="category-form-container">
    <!-- Breadcrumb -->
    <div class="content-breadcrumb">
        <ol class="breadcrumb-list">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.seo.index') }}">Quản lý SEO</a></li>
            <li class="breadcrumb-item current">Chỉnh sửa</li>
        </ol>
    </div>

    <div class="form-card">
        <div class="form-header">
            <div class="form-title">
                <i class="fas fa-edit icon-title"></i>
                <h5>Chỉnh sửa SEO</h5>
                <small class="text-muted">Cập nhật SEO settings</small>
            </div>
            <div class="category-meta">
                <div class="category-created">
                    <i class="fas fa-globe"></i>
                    <span>Trang: {{ $pageKeys[$seo->page_key] ?? $seo->page_key }}</span>
                </div>
                <div class="category-created">
                    <i class="fas fa-clock"></i>
                    <span>Ngày tạo: {{ $seo->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="form-body">
            
            <form action="{{ route('admin.seo.update', $seo) }}" method="POST" class="category-form" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Vietnamese Title -->
                <div class="form-group">
                    <label for="title_vi" class="form-label-custom">
                        Title (Tiếng Việt) <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('title.vi') ? 'input-error' : '' }}" 
                        id="title_vi" name="title[vi]" value="{{ old('title.vi', $seo->getTranslation('title', 'vi')) }}" required maxlength="255">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Tiêu đề trang bằng tiếng Việt (tối đa 255 ký tự).</span>
                    </div>
                    @error('title.vi')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- English Title -->
                <div class="form-group">
                    <label for="title_en" class="form-label-custom">
                        Title (English) <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('title.en') ? 'input-error' : '' }}" 
                        id="title_en" name="title[en]" value="{{ old('title.en', $seo->getTranslation('title', 'en')) }}" required maxlength="255">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Page title in English (max 255 characters).</span>
                    </div>
                    @error('title.en')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Vietnamese Description -->
                <div class="form-group">
                    <label for="description_vi" class="form-label-custom">
                        Description (Tiếng Việt) <span class="required-mark">*</span>
                    </label>
                    <textarea class="custom-input {{ $errors->has('description.vi') ? 'input-error' : '' }}" 
                        id="description_vi" name="description[vi]" rows="3" required maxlength="500">{{ old('description.vi', $seo->getTranslation('description', 'vi')) }}</textarea>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Mô tả trang bằng tiếng Việt (tối đa 500 ký tự).</span>
                    </div>
                    @error('description.vi')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- English Description -->
                <div class="form-group">
                    <label for="description_en" class="form-label-custom">
                        Description (English) <span class="required-mark">*</span>
                    </label>
                    <textarea class="custom-input {{ $errors->has('description.en') ? 'input-error' : '' }}" 
                        id="description_en" name="description[en]" rows="3" required maxlength="500">{{ old('description.en', $seo->getTranslation('description', 'en')) }}</textarea>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Page description in English (max 500 characters).</span>
                    </div>
                    @error('description.en')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Vietnamese Keywords -->
                <div class="form-group">
                    <label for="keywords_vi" class="form-label-custom">
                        Keywords (Tiếng Việt) <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('keywords.vi') ? 'input-error' : '' }}" 
                        id="keywords_vi" name="keywords[vi]" value="{{ old('keywords.vi', $seo->getTranslation('keywords', 'vi')) }}" required maxlength="500">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Từ khóa bằng tiếng Việt, phân cách bằng dấu phẩy (tối đa 500 ký tự).</span>
                    </div>
                    @error('keywords.vi')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- English Keywords -->
                <div class="form-group">
                    <label for="keywords_en" class="form-label-custom">
                        Keywords (English) <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="custom-input {{ $errors->has('keywords.en') ? 'input-error' : '' }}" 
                        id="keywords_en" name="keywords[en]" value="{{ old('keywords.en', $seo->getTranslation('keywords', 'en')) }}" required maxlength="500">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Keywords in English, separated by commas (max 500 characters).</span>
                    </div>
                    @error('keywords.en')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="thumbnail" class="form-label-custom">
                        Thumbnail
                    </label>
                    @if($seo->thumbnail)
                        <div class="current-thumbnail mb-3">
                            <img src="{{ $seo->thumbnail_url }}" alt="Thumbnail hiện tại" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
                        </div>
                    @endif
                    <input type="file" class="custom-input {{ $errors->has('thumbnail') ? 'input-error' : '' }}" 
                        id="thumbnail" name="thumbnail" accept="image/*">
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        <span>Để trống nếu không muốn thay đổi thumbnail. Chấp nhận định dạng: JPG, PNG, GIF. Tối đa 2MB.</span>
                    </div>
                    @error('thumbnail')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                            {{ old('is_active', $seo->is_active) ? 'checked' : '' }}>
                        <label for="is_active">Kích hoạt SEO</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.seo.index') }}" class="back-button">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                   
                    <div class="action-group">
                       
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.current-thumbnail {
    text-align: center;
}
</style>
@endsection
