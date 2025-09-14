<!-- Edit History Modal -->
<div class="modal fade" id="editHistoryModal" tabindex="-1" aria-labelledby="editHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4">
            <div class="modal-header">
                <h5 class="modal-title" id="editHistoryModalLabel">
                    📝 Lịch sử chỉnh sửa bình luận
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="editHistoryContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<style>
.edit-history-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #f8f9fa;
}

.edit-history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #dee2e6;
}

.edit-history-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.edit-history-editor {
    font-weight: 600;
    color: #495057;
}

.edit-history-time {
    font-style: italic;
}

.edit-history-reason {
    background: #e3f2fd;
    border-left: 3px solid #2196f3;
    padding: 0.5rem;
    margin: 0.5rem 0;
    border-radius: 0 4px 4px 0;
    font-size: 0.85rem;
    color: #1976d2;
}

.edit-history-content {
    margin-top: 0.75rem;
}

.content-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.25rem;
    font-size: 0.85rem;
}

.content-text {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 0.75rem;
    font-size: 0.9rem;
    line-height: 1.5;
    max-height: 200px;
    overflow-y: auto;
    word-wrap: break-word;
}

.content-old {
    border-left: 3px solid #dc3545;
}

.content-new {
    border-left: 3px solid #28a745;
}

.edit-history-empty {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.edit-history-empty i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .edit-history-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .edit-history-meta {
        flex-wrap: wrap;
    }
}
</style>
