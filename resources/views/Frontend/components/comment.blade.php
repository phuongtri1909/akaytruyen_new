@push('styles')
    <style>
        /* Compact Comment Section Styles */
        .comment-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin: 1.5rem 0;
        }

        .comment-section h5 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
            text-align: center;
        }

        /* Comment Input */
        .comment-input-container {
            margin-bottom: 1.5rem;
        }

        .form-floating.submit-comment {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .form-floating.submit-comment .form-control {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 0.75rem 2.5rem 0.75rem 0.75rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            resize: none;
        }

        .form-floating.submit-comment .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.15rem rgba(0, 123, 255, 0.1);
        }

        .btn-send-comment {
            position: absolute;
            right: 6px;
            bottom: 6px;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #007bff;
            border: none;
            color: white;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .btn-send-comment:hover {
            transform: scale(1.05);
            background: #0056b3;
        }

        .btn-send-comment:disabled {
            background: #6c757d;
            transform: none;
        }

        /* Comments List */
        .blog-comment {
            margin-top: 1.5rem;
        }

        .blog-comment ul.comments ul:before {
            left: -12px;
            border-left: 2px solid #e9ecef;
        }

        .blog-comment ul.comments ul li:before {
            left: -12px;
            top: 20px;
            width: 12px;
            border-top: 2px solid #e9ecef;
        }

        /* Meta Info */
        .meta {
            font-size: 0.85rem;
            color: #6c757d;
            padding-bottom: 0.5rem;
            margin-bottom: 0.75rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 0.5rem;
        }


        /* Reply Form */
        .reply-form {
            margin: 0.75rem 0;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid #007bff;
            animation: slideInDown 0.3s ease-out;
        }

        .reply-form .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            font-size: 0.85rem;
        }

        .submit-reply {
            background: #007bff;
            border: none;
            border-radius: 15px;
            padding: 0.4rem 1rem;
            color: white;
            font-weight: 500;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .submit-reply:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        /* Mention System */
        .mention-list {
            position: absolute;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            width: 200px;
            max-height: 150px;
            overflow-y: auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 0.25rem 0;
            display: none;
            z-index: 1000;
            animation: slideInUp 0.2s ease-out;
        }

        .mention-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 4px;
            margin: 0 0.25rem;
            font-size: 0.8rem;
        }

        .mention-item:hover {
            background: #f8f9fa;
            transform: translateX(3px);
        }

        /* Load More */
        .load-more-container {
            text-align: center;
            margin-top: 1.5rem;
        }

        .btn-link {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: white;
            border: 1px solid #007bff;
            transition: all 0.3s ease;
            display: inline-block;
            font-size: 0.85rem;
        }

        .btn-link:hover {
            background: #007bff;
            color: white;
            transform: translateY(-1px);
        }

        /* Animations */
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        /* Mobile */
        @media (max-width: 768px) {

            .comment-section h5 {
                font-size: 1.1rem;
            }

            .meta {
                font-size: 0.8rem;
                gap: 0.25rem;
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

            .reaction-group {
                left: -80px;
                max-width: 250px;
            }

            .reaction-btn {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }

            .reply-form {
                padding: 0.5rem;
            }

            .blog-comment ul.comments ul:before {
                left: -8px;
            }

            .blog-comment ul.comments ul li:before {
                left: -8px;
                width: 8px;
            }
        }

        /* Modal Styles - Fixed */
        .modal {
            overflow: hidden !important;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.15s ease-out !important;
            transform: translate(0, -50px) !important;
        }

        .modal.show .modal-dialog {
            transform: translate(0, 0) !important;
        }

        .modal-backdrop {
            opacity: 0 !important;
            transition: opacity 0.15s ease-out !important;
        }

        .modal-backdrop.show {
            opacity: 0.5 !important;
        }

        .modal-dialog {
            margin: 1.75rem auto !important;
            pointer-events: auto !important;
            max-width: 500px !important;
        }

        .modal-content {
            border: none !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef !important;
            border-radius: 12px 12px 0 0 !important;
            background: #f8f9fa !important;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef !important;
            border-radius: 0 0 12px 12px !important;
            background: #f8f9fa !important;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.css">
@endpush
<section id="comments" class="comment-section">
    <div class="container px-2 px-md-3">
        <h5 class="mb-3">💬 BÌNH LUẬN TRUYỆN</h5>
        <div class="row">
            <div class="col-12">
                <div class="comment-input-container">
                    <div class="form-floating submit-comment">
                        <textarea class="form-control" id="comment-input" placeholder="Chia sẻ suy nghĩ của bạn..." rows="2"
                            maxlength="700"></textarea>
                        <label for="comment-input">✍️ Viết bình luận...</label>
                        <button class="btn btn-sm btn-send-comment" id="btn-comment" value="{{ $chapter->id ?? '' }}">
                            <i class="fa-regular fa-paper-plane"></i>
                        </button>
                    </div>
                </div>

                <div class="blog-comment">
                    <ul class="comments mb-0 px-0" id="comments-list">
                        @include('Frontend.components.comments-list', [
                            'pinnedComments' => $pinnedComments,
                            'regularComments' => $regularComments,
                        ])
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@include('Frontend.components.comment-edit-history-modal')

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Xác nhận xóa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Bạn có chắc muốn xóa bình luận này?</p>
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Hủy
                </button>
                <button type="button" class="btn btn-outline-danger" id="confirmDelete">
                    <i class="fas fa-trash me-1"></i>
                    Xóa
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            let page = 1;
            let isSubmitting = false;

            $('#btn-comment').click(function() {

                const btn = $(this);
                const comment = $('#comment-input').val().trim();

                if (!comment || isSubmitting) return;

                isSubmitting = true;
                btn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '{{ route('comment.store.client') }}',
                    type: 'POST',
                    data: {
                        comment: comment,
                        chapter_id: {{ $chapter->id }},
                        reply_id: null,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            removeEmptyCommentsState();

                            $('#comments-list').prepend(res.html);
                            $('#comment-input').val('');

                            showToast(res.message || 'Bình luận đã được đăng thành công!',
                                'success');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            window.location.href = '{{ route('login') }}';
                        }
                    },
                    complete: function() {
                        isSubmitting = false;
                        btn.prop('disabled', false)
                            .html('<i class="fa-regular fa-paper-plane"></i>');
                    }
                });
            });

            $(document).on('click', '.reply-btn', function(e) {
                e.preventDefault();
                const commentId = $(this).data('id');
                const userName = $(this).closest('.post-comments').find('.meta b').text().trim();

                if ($(this).closest('.post-comments').find('.reply-form').length === 0) {
                    const replyForm = `
                        <div class="reply-form mt-2">
                            <div class="form-floating">
                                <textarea class="form-control reply-text" placeholder="Nhập trả lời..." maxlength="700"></textarea>
                                <label>Trả lời</label>
                                <ul class="mention-list"></ul>
                            </div>
                            <button class="btn btn-sm btn-info mt-2 submit-reply" data-id="${commentId}">Gửi</button>
                        </div>
                    `;
                    $(this).closest('.post-comments').append(replyForm);
                    $(this).hide();
                }
            });

            $(document).on('input', '.reply-text', function() {
                const input = $(this);
                const text = input.val();
                const mentionList = input.closest('.reply-form').find('.mention-list');

                if (text.includes('@')) {
                    const atIndex = text.lastIndexOf('@');
                    const colonIndex = text.indexOf(':');

                    if (colonIndex === -1 || atIndex > colonIndex) {
                        const query = text.substring(atIndex + 1).trim();
                        if (query.length > 0) {
                            $.ajax({
                                url: "/search-users",
                                type: "GET",
                                data: {
                                    query: query
                                },
                                success: function(res) {
                                    mentionList.empty().show();
                                    if (res.users.length === 0) {
                                        mentionList.append(
                                            `<li class="mention-item">Không tìm thấy</li>`);
                                    } else {
                                        res.users.forEach(user => {
                                            mentionList.append(
                                                `<li class="mention-item" data-name="${user.name}"><b>@${user.name}</b></li>`
                                            );
                                        });
                                    }
                                },
                                error: function(xhr) {
                                }
                            });
                        }
                    } else {
                        mentionList.hide();
                    }
                } else {
                    mentionList.hide();
                }

            });



            $(document).on('click', '.mention-item', function() {
                const mention = $(this).data('name'); // username
                const textarea = $(this).closest('.reply-form').find('.reply-text');
                const currentText = textarea.val();

                const lastAtIndex = currentText.lastIndexOf('@');
                if (lastAtIndex !== -1) {
                    const beforeAt = currentText.substring(0, lastAtIndex);
                    const formattedMention = `@${mention}: `;
                    textarea.val(beforeAt + formattedMention);
                }

                $(this).parent().hide();
                textarea.focus();
            });


            $(document).on('click', '.submit-reply', function() {
                const chapter_id = {{ $chapter->id }};
              
                const btn = $(this);
                const commentId = btn.data('id');
                const reply = btn.closest('.reply-form').find('textarea').val().trim();

                if (!reply || btn.prop('disabled')) return;

                btn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '{{ route('comment.store.client') }}',
                    type: 'POST',
                    data: {
                        comment: reply,
                        reply_id: commentId,
                        chapter_id: chapter_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            let replyContainer = btn.closest('.post-comments').find(
                                'ul.comments');
                            let replyBtn = btn.closest('.post-comments').find('.reply-btn');

                            if (replyContainer.length === 0) {
                                btn.closest('.post-comments').append(
                                    '<ul class="comments"></ul>');
                                replyContainer = btn.closest('.post-comments').find(
                                    'ul.comments');
                            }

                            replyContainer.append(res.html);
                            btn.closest('.reply-form').remove();

                            setTimeout(() => {
                                replyBtn.css('display', 'inline-block');
                            }, 100);

                            removeEmptyCommentsState();

                            showToast(res.message || 'Trả lời đã được đăng thành công!',
                                'success');
                        }
                    },
                    error: function(xhr) {
                        showToast(xhr.responseJSON.message || 'Có lỗi xảy ra', 'error');
                        btn.prop('disabled', false).text('Gửi');
                    }
                });
            });
        });
    </script>

    <!-- Helper functions -->
    <script>
        function removeEmptyCommentsState() {
            $('.empty-comments').fadeOut(300, function() {
                $(this).remove();
            });
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let loadMoreBtn = document.getElementById("load-more-comments");
            let currentPage = 1;
            let isLoading = false;

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener("click", function() {
                    if (isLoading) return;

                    isLoading = true;
                    const btn = this;
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
                    btn.disabled = true;

                    currentPage++;

                    fetch('{{ route('comments.load.more.ajax') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                chapter_id: '{{ $chapter->id ?? '' }}',
                                page: currentPage,
                                is_pinned: false
                            })
                        })
                        .then(response => response.json())
                        .then(data => {

                            if (data.status === 'success') {
                                const emptyComments = document.querySelector('.empty-comments');
                                if (emptyComments) {
                                    emptyComments.style.opacity = '0';
                                    emptyComments.style.transition = 'opacity 0.3s ease';
                                    setTimeout(() => emptyComments.remove(), 300);
                                }

                                if (data.html && data.html.trim() !== '') {
                                    const regularComments = document.querySelector('.regular-comments');
                                    if (regularComments) {
                                        regularComments.insertAdjacentHTML('beforeend', data.html);
                                    }
                                }

                                if (!data.hasMore) {
                                    btn.remove();
                                }

                                showToast(`Đã tải ${data.count} bình luận`, 'success');
                            } else {
                                showToast(data.message || 'Có lỗi xảy ra', 'error');
                            }
                        })
                        .catch(error => {
                            showToast('Có lỗi xảy ra khi tải bình luận', 'error');
                        })
                        .finally(() => {
                            isLoading = false;
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        });
                });
            }
        });
    </script>
@endpush
