{{-- Comments List Container --}}
<div class="comments-list-container">
    {{-- Show pinned comments first with special styling --}}
    @if ($pinnedComments->count() > 0)
        <div class="pinned-comments-section">
            <div class="pinned-header">
                <h6 class="pinned-title">
                    📌 Bình luận được ghim
                </h6>
            </div>
            <div class="pinned-comments">
                @foreach ($pinnedComments as $comment)
                    @include('Frontend.components.comments-item', ['comment' => $comment])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Show regular comments --}}
    @if ($regularComments->count() > 0)
        <div class="regular-comments-section">
            @if ($pinnedComments->count() > 0)
                <div class="section-divider">
                    <span class="divider-text">💬 Tất cả bình luận</span>
                </div>
            @endif
            <div class="regular-comments">
                @foreach ($regularComments as $comment)
                    @include('Frontend.components.comments-item', ['comment' => $comment])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Load More Button --}}
    @if (method_exists($regularComments, 'hasMorePages') &&
            $regularComments->hasMorePages() &&
            $regularComments->count() > 0)
        <div class="load-more-container">
            <button class="btn btn-link" id="load-more-comments">
                📄 Xem thêm bình luận...
            </button>
        </div>
    @endif

    {{-- Empty state --}}
    @if ($pinnedComments->count() == 0 && $regularComments->count() == 0)
        <div class="empty-comments">
            <div class="empty-icon">💭</div>
            <p class="empty-text">Chưa có bình luận nào. Hãy là người đầu tiên chia sẻ suy nghĩ!</p>
        </div>
    @endif
</div>

@once
    @push('styles')
        <style>
            /* Compact Comment Item Styles */
            .comment-item-wrapper {
                margin-bottom: 0.75rem;
                animation: fadeInUp 0.4s ease-out;
            }

            .comment-item {
                display: flex;
                align-items: flex-start;
                gap: 0.75rem;
                padding: 0;
                margin: 0;
                list-style: none;
            }

            /* Avatar */
            .avatar-container {
                flex-shrink: 0;
            }

            .avatar-wrapper {
                position: relative;
                width: 45px;
                height: 45px;
                border-radius: 50%;

                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .avatar-wrapper:hover {
                transform: scale(1.02);
            }

            .user-avatar {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 50%;
                border: 2px solid #fff;
                transition: all 0.3s ease;
            }

            /* Comment Content */
            .post-comments {
                flex: 1;
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
                transition: all 0.3s ease;
                position: relative;
            }

            .post-comments:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            }

            .post-comments.pinned {
                border-left: 3px solid #ffc107;
                background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            }

            .post-comments.pinned::before {
                content: '📌';
                position: absolute;
                top: 8px;
                right: 8px;
                font-size: 1rem;
                animation: bounce 2s infinite;
            }

            .content-post-comments {
                border: none;
                border-radius: 12px;
                background: white;
            }

            /* User Meta */
            .meta {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-bottom: 0.75rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #e9ecef;
            }

            .user-info {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.4rem;
            }

            .user-name {
                text-decoration: none;
                transition: all 0.3s ease;
            }

            .user-name:hover {
                transform: translateY(-1px);
            }

            .admin-actions {
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            /* Role Badges - Keep original colors */
            .role-badge {
                padding: 0.2rem 0.5rem;
                border-radius: 12px;
                font-size: 0.7rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.3px;
            }

            .admin-badge {
                color: #dc3545 !important;
            }

            .mod-badge {
                color: #198754 !important;
            }

            .vip-badge {
                color: #0d6efd !important;
            }

            .content-badge {
                color: #cdb94f !important;
            }

            .vip-pro-badge {
                color: purple !important;
            }

            .vip-pro-max-badge {
                color: #f37200 !important;
            }

            .vip-pro-sv-badge {
                background: linear-gradient(to right, #ff8a00, #ff2070);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                display: inline-block;
            }

            /* Comment Content */
            .comment-content {
                font-size: 0.9rem;
                line-height: 1.5;
                color: #2c3e50;
                margin-bottom: 0.75rem;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .vip-super-role {
                font-size: 1rem;
                font-weight: 600;
                position: relative;
            }

            .vip-super-role::before {
                content: attr(data-text);
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, #005f99, #87cefa, #00cc66);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                pointer-events: none;
                z-index: 1;
            }

            .vip-super-role img,
            .vip-super-role .emoji,
            .vip-super-role a {
                position: relative;
                z-index: 2;
                background: none !important;
                -webkit-text-fill-color: unset !important;
                filter: none !important;
                display: inline-block;
            }

            /* Actions */
            .comment-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.75rem;
                margin-top: 0.75rem;
            }

            .left-actions {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                flex-wrap: wrap;
            }

            .comment-time {
                font-size: 0.75rem;
                color: #6c757d;
                font-style: italic;
                display: flex;
                align-items: center;
                gap: 0.2rem;
            }

            .reply-btn {
                color: #2c3e50;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                padding: 0.25rem 0.5rem;
                border-radius: 12px;
                text-decoration: none;
                border: 1px solid #2c3e50;
                background: transparent;
                display: flex;
                align-items: center;
                gap: 0.3rem;
            }

            .reply-btn:hover {
                background: rgba(0, 123, 255, 0.1);
                color: #0056b3;
            }

            .edit-btn {
                color: #28a745;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                padding: 0.25rem 0.5rem;
                border-radius: 12px;
                text-decoration: none;
                border: 1px solid #28a745;
                background: transparent;
                display: flex;
                align-items: center;
                gap: 0.3rem;
            }

            .edit-btn:hover {
                background: rgba(40, 167, 69, 0.1);
                color: #1e7e34;
            }

            .history-btn {
                color: #6f42c1;
                font-size: 0.8rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                padding: 0.25rem 0.5rem;
                border-radius: 12px;
                text-decoration: none;
                border: 1px solid #6f42c1;
                background: transparent;
                display: flex;
                align-items: center;
                gap: 0.3rem;
            }

            .history-btn:hover {
                background: rgba(111, 66, 193, 0.1);
                color: #5a32a3;
            }

            .edited-badge {
                margin-top: 0.5rem;
                padding-top: 0.5rem;
                border-top: 1px solid #e9ecef;
            }

            .edited-badge small {
                font-size: 0.75rem;
                opacity: 0.8;
            }

            /* Reaction System - Fixed */
            .reaction-wrapper {
                position: relative;
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            /* Reset any conflicting CSS */
            .reaction-wrapper .reaction-group {
                display: none !important;
                opacity: 0 !important;
                visibility: hidden !important;
            }

            .reaction-wrapper .reaction-group.show {
                display: flex !important;
                opacity: 1 !important;
                visibility: visible !important;
            }

            .smiley-btn {
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                color: #6c757d;
                font-size: 0.8rem;
            }

            .smiley-btn:hover {
                background: #e9ecef;
                transform: scale(1.05);
            }

            .reaction-group {
                position: absolute;
                bottom: 120%;
                left: -200px;
                background: white;
                border-radius: 15px;
                padding: 0.5rem;
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
                z-index: 1000;
                animation: slideInUp 0.2s ease-out;
            }

            .reaction-btn {
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                border: 1px solid transparent;
                margin: 0 0.2rem;
                background: #f8f9fa;
                color: #6c757d;
                font-size: 0.8rem;
            }

            .reaction-btn:hover {
                transform: scale(1.1);
                border-color: #007bff;
            }

            .reaction-display {
                display: flex;
                align-items: center;
                gap: 0.4rem;
            }

            .reaction-display-btn {
                background: rgba(0, 123, 255, 0.1);
                border: 1px solid rgba(0, 123, 255, 0.2);
                border-radius: 12px;
                padding: 0.2rem 0.4rem;
                font-size: 0.75rem;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.2rem;
            }

            .reaction-display-btn:hover {
                background: rgba(0, 123, 255, 0.15);
            }

            /* Reaction Colors */
            .reaction-like {
                background-color: #0d6efd !important;
                color: white !important;
            }

            .reaction-dislike {
                background-color: #6c757d !important;
                color: white !important;
            }

            .reaction-haha {
                background-color: #ffc107 !important;
                color: black !important;
            }

            .reaction-tym {
                background-color: #dc3545 !important;
                color: white !important;
            }

            .reaction-angry {
                background-color: #fd7e14 !important;
                color: white !important;
            }

            .reaction-sad {
                background-color: #ffca2a !important;
                color: black !important;
            }

            /* Admin Actions */
            .delete-comment {
                color: #dc3545;
                cursor: pointer;
                padding: 0.25rem;
                border-radius: 50%;
                transition: all 0.3s ease;
                background: transparent;
                border: none;
                font-size: 0.8rem;
            }

            .delete-comment:hover {
                background: rgba(220, 53, 69, 0.1);
                transform: scale(1.05);
            }

            .pin-comment {
                background: transparent;
                border: none;
                color: #6c757d;
                transition: all 0.3s ease;
                padding: 0.25rem;
                border-radius: 50%;
                font-size: 0.8rem;
            }

            .pin-comment:hover {
                background: rgba(255, 193, 7, 0.1);
                color: #ffc107;
                transform: scale(1.05);
            }

            .pin-comment .text-warning {
                animation: pulse 2s infinite;
            }

            /* Tooltip */
            .tooltip-icon {
                position: relative;
                display: inline-block;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .tooltip-icon:hover {
                transform: scale(1.05);
            }

            .tooltip-icon .tooltip-text {
                visibility: hidden;
                background: #2c3e50;
                color: white;
                font-size: 0.7rem;
                text-align: center;
                padding: 0.4rem 0.6rem;
                border-radius: 6px;
                position: absolute;
                z-index: 1000;
                bottom: 125%;
                left: 50%;
                transform: translateX(-50%);
                white-space: nowrap;
                opacity: 0;
                transition: all 0.3s ease;
                box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            }

            .tooltip-icon:hover .tooltip-text {
                visibility: visible;
                opacity: 1;
                transform: translateX(-50%) translateY(-3px);
            }

            /* Reply Section */
            .fb-reply-border {
                border-left: 2px solid #e4e6eb;
                padding-left: 0.5rem;
                border-radius: 0 0 0 6px;
                transition: background-color 0.3s ease;
                margin-top: 0.75rem;
            }

            /* Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(15px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes pulse {
                0% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.03);
                }

                100% {
                    transform: scale(1);
                }
            }

            @keyframes bounce {

                0%,
                20%,
                50%,
                80%,
                100% {
                    transform: translateY(0);
                }

                40% {
                    transform: translateY(-8px);
                }

                60% {
                    transform: translateY(-4px);
                }
            }

            /* Mobile */
            @media (max-width: 768px) {
                .comment-item {
                    gap: 0.5rem;
                }

                .avatar-wrapper {
                    width: 40px;
                    height: 40px;
                }

                .meta {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.3rem;
                }

                .user-info {
                    gap: 0.2rem;
                }

                .role-badge {
                    font-size: 0.65rem;
                    padding: 0.15rem 0.4rem;
                }

                .comment-actions {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.5rem;
                }

                .left-actions {
                    gap: 0.5rem;
                }

                .reaction-group {
                    left: -80px;
                    max-width: 250px;
                }

                .reaction-btn {
                    width: 28px;
                    height: 28px;
                    font-size: 0.75rem;
                }

                .reply-btn {
                    padding: 0.2rem 0.4rem;
                    font-size: 0.75rem;
                }

                .comment-time {
                    font-size: 0.7rem;
                }
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            $(document).on('click', '.pin-comment', function() {
                const btn = $(this);
                const commentId = btn.data('id');

                if (btn.prop('disabled')) return;
                btn.prop('disabled', true);

                $.ajax({
                    url: `/comments/${commentId}/pin`,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            showToast(res.message, 'success', true); // Hiển thị thông báo và reload
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            showToast('Không thể ghim bình luận', 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Có lỗi xảy ra';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast(errorMessage, 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false);
                    }
                });
            });
            $(document).ready(function() {
                let commentToDelete = null;
                let deleteModal = null;

                // Initialize modal only once
                function initDeleteModal() {
                    if (!deleteModal) {
                        const modalElement = document.getElementById('deleteModal');
                        if (modalElement) {
                            deleteModal = new bootstrap.Modal(modalElement, {
                                backdrop: 'static',
                                keyboard: false
                            });
                        }
                    }
                }

                // Initialize modal when document is ready
                initDeleteModal();

                // Khi bấm vào nút xóa
                $('body').on('click', '.delete-comment', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    commentToDelete = $(this).data('id');

                    // Ensure modal is initialized
                    initDeleteModal();

                    if (deleteModal) {
                        deleteModal.show();
                    }
                });

                // Khi xác nhận xóa
                $('#confirmDelete').click(function() {
                    if (!commentToDelete) return;

                    const btn = $(this);
                    const originalText = btn.html();

                    // Disable button and show loading
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Đang xóa...');

                    $.ajax({
                        url: `/comments/${commentToDelete}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                showToast(response.message, 'success', true);
                                if (deleteModal) {
                                    deleteModal.hide();
                                }
                                setTimeout(function() {
                                    location.reload();
                                }, 500);
                            } else {
                                showToast(response.message, 'error');
                                btn.prop('disabled', false).html(originalText);
                            }
                        },
                        error: function(xhr) {
                            showToast('Có lỗi xảy ra khi xóa bình luận', 'error');
                            btn.prop('disabled', false).html(originalText);
                            if (deleteModal) {
                                deleteModal.hide();
                            }
                        }
                    });
                });

                // Handle modal hidden event
                $('#deleteModal').on('hidden.bs.modal', function() {
                    commentToDelete = null;
                });
            });

            function showToast(message, type = 'info', reload = false) {
                const bgColor = type === 'success' ? 'green' : 'red';
                $('body').append(`
        <div class="toast-message" style="
            position: fixed; bottom: 10px; right: 10px;
            background: ${bgColor}; color: white; padding: 10px;
            border-radius: 5px; z-index: 9999;">
            ${message}
        </div>
    `);

                setTimeout(() => {
                    $('.toast-message').fadeOut(500, function() {
                        $(this).remove();
                        if (reload) location.reload(); // Reload trang sau khi thông báo biến mất
                    });
                }, 100);
            }
        </script>







        <!--Add existing delete modal scripts first -->
        <script>
            $(document).ready(function() {
                $(document).on('click', '.reaction-btn', function() {
                    const btn = $(this);
                    const commentId = btn.data('id');
                    const type = btn.data('type');

                    btn.prop('disabled', true); // disable trong lúc gửi

                    $.ajax({
                        url: `/comments/${commentId}/react`,
                        type: 'POST',
                        data: {
                            type: type,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Highlight nút vừa chọn
                                $(`.reaction-wrapper[data-id="${commentId}"] .reaction-btn`)
                                    .removeClass('active');
                                btn.addClass('active');

                                // Cập nhật toàn bộ icon và số lượng trong phần hiển thị
                                renderReactions(commentId, response.reactionCounts);

                                // Ẩn nhóm cảm xúc với animation
                                btn.closest('.reaction-group').fadeOut(200);

                                // Hiển thị thông báo
                                showToast(response.message, 'success');
                            } else {
                                showToast(response.message || 'Có lỗi xảy ra', 'error');
                            }
                        },
                        error: function(xhr) {
                            if (xhr.status === 401 && xhr.responseJSON?.redirect) {
                                window.location.href = xhr.responseJSON.redirect;
                            } else if (xhr.responseJSON?.message) {
                                showToast(xhr.responseJSON.message, 'error');
                            } else {
                                showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                            }
                        },
                        complete: function() {
                            btn.prop('disabled', false); // bật lại sau khi xong
                        }
                    });
                });

                function renderReactions(commentId, counts) {
                    const reactionIcons = {
                        like: 'fa-thumbs-up',
                        dislike: 'fa-thumbs-down',
                        haha: 'fa-face-laugh',
                        tym: 'fa-heart',
                        angry: 'fa-face-angry',
                        sad: 'fa-face-sad-tear'
                    };

                    let html = '<div class="d-flex gap-1 mt-1">';
                    Object.keys(reactionIcons).forEach(type => {
                        const count = counts[type + 's'];
                        if (count > 0) {
                            html += `
                <button class="btn btn-sm d-flex align-items-center gap-1 px-2 py-1
                    reaction-${type} border-0 rounded-pill reaction-display-btn">
                    <i class="fa-solid ${reactionIcons[type]}"></i>
                    <span class="${type}s-count">${count}</span>
                </button>`;
                        }
                    });
                    html += '</div>';

                    $(`#reaction-display-${commentId}`).html(html);
                }


                // Cập nhật số lượng cảm xúc
                // Cập nhật toàn bộ count các loại
                function updateReactionCounts(commentId, counts) {
                    const types = ['like', 'dislike', 'haha', 'tym', 'angry', 'sad'];
                    types.forEach(type => {
                        const selector = `.reaction-wrapper[data-id="${commentId}"] .${type}s-count`;
                        $(selector).text(counts[type + 's']);

                        // Nếu reaction count = 0 thì ẩn button đó
                        const btn = $(selector).closest('.reaction-display-btn');
                        if (counts[type + 's'] == 0) {
                            btn.hide();
                        } else {
                            btn.show();
                        }
                    });
                }

                // Hiển thị thông báo
                function showToast(message, type = 'info') {
                    const bgColor = type === 'success' ? 'green' : 'red';
                    const toast = $(`
            <div class="toast-message" style="
                position: fixed; bottom: 10px; right: 10px;
                background: ${bgColor}; color: white; padding: 10px;
                border-radius: 5px; z-index: 9999;">
                ${message}
            </div>
        `);
                    $('body').append(toast);
                    setTimeout(() => {
                        toast.fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, 1500);
                }
            });
        </script>
        <script>
            $(document).ready(function() {
                // Hiện/ẩn nhóm cảm xúc khi click icon mặt cười
                $(document).on('click', '.smiley-btn', function(e) {
                    e.stopPropagation(); // Ngăn bubbling
                    const wrapper = $(this).closest('.reaction-wrapper');
                    const group = wrapper.find('.reaction-group');

                    // Ẩn tất cả group khác
                    $('.reaction-group').removeClass('show');

                    // Toggle nhóm này
                    group.toggleClass('show');
                });

                // Click ra ngoài thì ẩn hết nhóm cảm xúc
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.reaction-wrapper').length) {
                        $('.reaction-group').removeClass('show');
                    }
                });

                // Ẩn reaction group khi click vào reaction button
                $(document).on('click', '.reaction-btn', function() {
                    $(this).closest('.reaction-group').removeClass('show');
                });
            });


            document.addEventListener("DOMContentLoaded", function() {
                const hash = window.location.hash;

                if (hash.startsWith("#comment-")) {
                    setTimeout(() => {
                        const commentElement = document.querySelector(hash);

                        if (commentElement) {
                            // Cuộn đến phần tử và đưa nó ra giữa màn hình
                            commentElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            // Highlight nhẹ để user dễ nhìn
                            commentElement.style.transition = 'background-color 0.5s ease';
                            commentElement.style.backgroundColor = '#ffffcc';

                            setTimeout(() => {
                                commentElement.style.backgroundColor = '';
                            }, 2000);
                        }
                    }, 300); // delay 300ms
                }
            });

            // Edit Comment Functions
            $(document).on('click', '.edit-btn', function() {
                const commentId = $(this).data('id');
                const commentItem = $(this).closest('.comment-item-wrapper');

                // Ẩn tất cả form edit khác
                $('.comment-edit-form').remove();

                // Hiển thị loading
                commentItem.append(
                    '<div class="edit-loading text-center p-3"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>'
                );

                $.ajax({
                    url: `/comments/${commentId}/edit-form`,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            commentItem.find('.edit-loading').remove();
                            commentItem.append(response.html);

                            // Focus vào textarea
                            setTimeout(() => {
                                commentItem.find('.edit-comment-textarea').focus();
                            }, 100);
                        } else {
                            commentItem.find('.edit-loading').remove();
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        commentItem.find('.edit-loading').remove();
                        let errorMessage = 'Có lỗi xảy ra';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 403) {
                            errorMessage = 'Bạn không có quyền chỉnh sửa bình luận này';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Không tìm thấy bình luận';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Lỗi server, vui lòng thử lại';
                        }
                        showToast(errorMessage, 'error');
                        console.error('Edit form error:', xhr);
                    }
                });
            });

            // Submit edit form
            window.submitEditForm = function(event, commentId) {
                event.preventDefault();
                const form = $(event.target);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Đang lưu...');

                const formData = {
                    comment: form.find('textarea[name="comment"]').val(),
                    edit_reason: form.find('input[name="edit_reason"]').val(),
                    _token: '{{ csrf_token() }}'
                };

                $.ajax({
                    url: `/comments/${commentId}/edit`,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            // Cập nhật nội dung comment
                            const commentItem = $(`.comment-item-wrapper[data-comment-id="${commentId}"]`);
                            const commentContent = commentItem.find('.comment-content');

                            // Cập nhật nội dung (giữ lại cấu trúc VIP nếu có)
                            const vipRole = commentContent.find('.vip-super-role');
                            if (vipRole.length > 0) {
                                vipRole.attr('data-text', response.comment.comment);
                                vipRole.html($('<div>').text(response.comment.comment).html());
                            } else {
                                commentContent.html($('<div>').text(response.comment.comment).html());
                            }

                            // Thêm badge edited nếu chưa có
                            if (!commentItem.find('.edited-badge').length) {
                                commentContent.append(`
                                    <div class="edited-badge">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-edit"></i> Đã chỉnh sửa vừa xong
                                        </small>
                                    </div>
                                `);
                            }

                            // Thêm nút history nếu chưa có
                            if (!commentItem.find('.history-btn').length) {
                                commentItem.find('.left-actions').append(`
                                    <button class="history-btn" style="cursor: pointer;"
                                        data-id="${commentId}" title="Xem lịch sử chỉnh sửa">
                                        <i class="fa-solid fa-history"></i> Lịch sử
                                    </button>
                                `);
                            }

                            // Xóa form edit
                            commentItem.find('.comment-edit-form').remove();

                            showToast(response.message, 'success');
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            showToast(xhr.responseJSON.message, 'error');
                        } else {
                            showToast('Có lỗi xảy ra', 'error');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            };

            // Close edit form
            window.closeEditForm = function(commentId) {
                $(`.comment-item-wrapper[data-comment-id="${commentId}"] .comment-edit-form`).remove();
            };

            // View edit history
            $(document).on('click', '.history-btn', function() {
                const commentId = $(this).data('id');

                $.ajax({
                    url: `/comments/${commentId}/edit-history`,
                    type: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            let html = '';

                            if (response.edit_histories.length > 0) {
                                response.edit_histories.forEach(history => {
                                    html += `
                                        <div class="edit-history-item">
                                            <div class="edit-history-header">
                                                <div class="edit-history-meta">
                                                    <span class="edit-history-editor">${history.editor.name}</span>
                                                    <span class="edit-history-time">${history.edited_at}</span>
                                                </div>
                                            </div>
                                            ${history.edit_reason ? `<div class="edit-history-reason">Lý do: ${$('<div>').text(history.edit_reason).html()}</div>` : ''}
                                            <div class="edit-history-content">
                                                <div class="content-label">Nội dung cũ:</div>
                                                <div class="content-text content-old">${$('<div>').text(history.old_content).html()}</div>
                                                <div class="content-label mt-2">Nội dung mới:</div>
                                                <div class="content-text content-new">${$('<div>').text(history.new_content).html()}</div>
                                            </div>
                                        </div>
                                    `;
                                });
                            } else {
                                html = `
                                    <div class="edit-history-empty">
                                        <i class="fas fa-history"></i>
                                        <p>Chưa có lịch sử chỉnh sửa</p>
                                    </div>
                                `;
                            }

                            $('#editHistoryContent').html(html);
                            new bootstrap.Modal(document.getElementById('editHistoryModal')).show();
                        } else {
                            showToast(response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        showToast('Có lỗi xảy ra', 'error');
                    }
                });
            });

            // Character count for edit textarea
            $(document).on('input', '.edit-comment-textarea', function() {
                const textarea = $(this);
                const charCount = textarea.val().length;
                const countDisplay = textarea.closest('.form-group').find('.current-count');
                countDisplay.text(charCount);

                if (charCount > 650) {
                    countDisplay.css('color', '#dc3545');
                } else if (charCount > 600) {
                    countDisplay.css('color', '#ffc107');
                } else {
                    countDisplay.css('color', '#6c757d');
                }
            });
        </script>
    @endpush
@endonce


@once
    @push('styles')
        <style>
            .comments-list-container {
                margin-top: 0.75rem;
            }

            .pinned-comments-section {
                margin-bottom: 1.5rem;
            }

            .pinned-header {
                margin-bottom: 0.75rem;
            }

            .pinned-title {
                color: #ffc107;
                font-weight: 600;
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.3px;
                margin: 0;
                padding: 0.4rem 0.8rem;
                background: linear-gradient(135deg, #fff3cd, #ffeaa7);
                border-radius: 15px;
                display: inline-block;
                box-shadow: 0 2px 6px rgba(255, 193, 7, 0.15);
            }

            .pinned-comments {
                border-left: 2px solid #ffc107;
                padding-left: 0.75rem;
                background: linear-gradient(135deg, rgba(255, 193, 7, 0.03), rgba(255, 234, 167, 0.08));
                border-radius: 0 8px 8px 0;
                padding: 0.75rem 0.75rem 0.75rem 1rem;
            }

            .section-divider {
                text-align: center;
                margin: 1.5rem 0;
                position: relative;
            }

            .section-divider::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                right: 0;
                height: 1px;
                background: linear-gradient(90deg, transparent, #e9ecef, transparent);
            }

            .divider-text {
                background: white;
                padding: 0 0.75rem;
                color: #6c757d;
                font-size: 0.8rem;
                font-weight: 500;
                position: relative;
                z-index: 1;
            }

            .regular-comments-section {
                margin-top: 0.75rem;
            }

            .regular-comments {
                animation: fadeInUp 0.4s ease-out;
            }

            .load-more-container {
                text-align: center;
                margin-top: 1.5rem;
                padding: 1rem 0;
            }

            .load-more-container .btn-link {
                color: #007bff;
                text-decoration: none;
                font-weight: 600;
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
                border-radius: 10px;
                border: 1px solid #007bff;
                background: transparent;
                transition: all 0.3s ease;
                display: inline-block;
            }

            .load-more-container .btn-link:hover {
                background: #e9ecef;
                color: #0056b3;
                border-color: #0056b3;
            }

            .empty-comments {
                text-align: center;
                padding: 2rem 1rem;
                color: #6c757d;
            }

            .empty-icon {
                font-size: 2.5rem;
                margin-bottom: 0.75rem;
                opacity: 0.5;
            }

            .empty-text {
                font-size: 0.9rem;
                margin: 0;
                opacity: 0.7;
            }

            @media (max-width: 768px) {
                .pinned-comments {
                    padding: 0.5rem 0.5rem 0.5rem 0.75rem;
                }

                .pinned-title {
                    font-size: 0.75rem;
                    padding: 0.3rem 0.6rem;
                }

                .divider-text {
                    font-size: 0.75rem;
                }

                .load-more-container .btn-link {
                    padding: 0.4rem 0.8rem;
                    font-size: 0.8rem;
                }

                .empty-comments {
                    padding: 1.5rem 1rem;
                }

                .empty-icon {
                    font-size: 2rem;
                }

                .empty-text {
                    font-size: 0.85rem;
                }
            }
        </style>
    @endpush
@endonce
