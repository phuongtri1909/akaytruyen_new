<div class="comment-edit-form" data-comment-id="{{ $comment->id }}">
    <div class="edit-form-header">
        <h6 class="edit-title">✏️ Chỉnh sửa bình luận</h6>
        <button type="button" class="btn-close-edit" onclick="closeEditForm({{ $comment->id }})">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <form class="edit-comment-form" onsubmit="submitEditForm(event, {{ $comment->id }})">
        <div class="form-group mb-3">
            <textarea
                class="form-control edit-comment-textarea"
                name="comment"
                rows="4"
                maxlength="700"
                placeholder="Nhập nội dung bình luận..."
                required>{{ $comment->comment }}</textarea>
            <div class="char-count">
                <span class="current-count">{{ strlen($comment->comment) }}</span>/700
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="edit-reason-{{ $comment->id }}" class="form-label">Lý do chỉnh sửa (tùy chọn):</label>
            <input
                type="text"
                class="form-control"
                id="edit-reason-{{ $comment->id }}"
                name="edit_reason"
                placeholder="Ví dụ: Sửa lỗi chính tả, bổ sung thông tin..."
                maxlength="100">
        </div>

        <div class="edit-form-actions">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="closeEditForm({{ $comment->id }})">
                <i class="fas fa-times"></i> Hủy
            </button>
            <button type="submit" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-save"></i> Lưu thay đổi
            </button>
        </div>
    </form>
</div>

<style>
.comment-edit-form {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 1rem;
    margin: 0.75rem 0;
    animation: slideInDown 0.3s ease-out;
}

.edit-form-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.edit-title {
    margin: 0;
    color: #495057;
    font-weight: 600;
    font-size: 0.9rem;
}

.btn-close-edit {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.btn-close-edit:hover {
    background: #e9ecef;
    color: #495057;
}

.edit-comment-textarea {
    border: 1px solid #ced4da;
    border-radius: 8px;
    resize: vertical;
    font-size: 0.9rem;
    transition: border-color 0.3s ease;
}

.edit-comment-textarea:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.1);
}

.char-count {
    text-align: right;
    font-size: 0.75rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.current-count {
    font-weight: 600;
}

.edit-form-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.edit-form-actions .btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
    border-radius: 6px;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .comment-edit-form {
        padding: 0.75rem;
    }

    .edit-form-actions {
        flex-direction: column;
    }

    .edit-form-actions .btn {
        width: 100%;
    }
}
</style>
