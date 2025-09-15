<div class="modern-comment-section">
    @auth
        <div class="comment-input-card">

            <div class="comment-input-body">
                @include('Frontend.components.user-avatar', ['user' => auth()->user()])

                @if (!auth()->user()->userBan->comment)
                    <div class="input-wrapper">
                        <textarea wire:model.lazy="content" class="modern-textarea" placeholder="Viết bình luận của bạn..." rows="3"
                        maxlength="1000"></textarea>
                        <div class="textarea-footer">
                            <span class="char-counter" id="char-counter">0/1000</span>
                            <button wire:click="postComment" class="modern-submit-btn" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="postComment">
                                    <i class="fas fa-paper-plane"></i>
                                    Gửi
                                </span>
                        <span wire:loading wire:target="postComment">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Đang gửi...
                        </span>
                    </button>
                        </div>
                    </div>
                @else
                    <div class="ban-message">
                        <i class="fas fa-ban"></i>
                        <span>Bạn đã bị cấm bình luận.</span>
                    </div>
                @endif
            </div>
        </div>
    @endauth

    {{-- Comment List Container với Scroll Detection --}}
    <div class="modern-comment-list" id="comment-container">
        @forelse($comments as $comment)
            @if ($comment->user)
                <div class="comment-item" data-comment-id="{{ $comment->id }}">
                @include('Frontend.components.single-comment', ['comment' => $comment])
                </div>
            @endif
        @empty
            <div class="empty-comments">
                <div class="empty-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h4>Chưa có bình luận nào</h4>
                <p>Hãy là người đầu tiên chia sẻ suy nghĩ của bạn!</p>
            </div>
        @endforelse

        {{-- Loading indicator khi scroll --}}
        <div wire:loading wire:target="loadMoreComments" class="loading-indicator">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <span>Đang tải thêm bình luận...</span>
            </div>
        </div>

        {{-- Scroll sentinel --}}
        @if ($hasMoreComments)
            <div id="scroll-sentinel" class="scroll-sentinel">
                <div class="scroll-indicator">
                    <i class="fas fa-chevron-down"></i>
                    <span>Cuộn xuống để xem thêm</span>
                </div>
            </div>
        @else
            <div class="end-indicator">
                <div class="end-line"></div>
                <span>Đã hiển thị hết bình luận</span>
                <div class="end-line"></div>
            </div>
        @endif
    </div>
</div>

@once
    @push('styles')
        <style>
            /* Modern Comment Section Styles */
            .modern-comment-section {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
                max-width: 100%;
                margin: 0 auto;
            }

            /* Comment Input Card */
            .comment-input-card {
                background: linear-gradient(135deg, #8fc4e3 0%, #14425d 100%);
                border-radius: 20px;
                padding: 0;
                margin-bottom: 30px;
                box-shadow: 0 20px 40px #8fc4e3;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .comment-input-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 30px 60px #8fc4e3;
            }

            .comment-input-header {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                padding: 15px 25px;
                color: white;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .comment-input-header i {
                font-size: 18px;
                opacity: 0.9;
            }

            .comment-input-body {
                padding: 25px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                display: flex;
                gap: 15px;
                align-items: flex-start;
            }

            .input-wrapper {
                flex: 1;
                position: relative;
            }

            .modern-textarea {
                width: 100%;
                border: 2px solid #e1e8ed;
                border-radius: 15px;
                padding: 15px 20px;
                font-size: 14px;
                line-height: 1.5;
                resize: vertical;
                transition: all 0.3s ease;
                background: white;
                color: #333;
                font-family: inherit;
            }

            .modern-textarea:focus {
                outline: none;
                border-color: #8fc4e3;
                box-shadow: 0 0 0 3px #8fc4e3;
                transform: scale(1.02);
            }

            .modern-textarea::placeholder {
                color: #8899a6;
                opacity: 1;
            }

            .textarea-footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .char-counter {
                font-size: 12px;
                color: #8899a6;
                font-weight: 500;
            }

            .modern-submit-btn {
                background: linear-gradient(135deg, #8fc4e3 0%, #14425d 100%);
                color: white;
                border: none;
                border-radius: 25px;
                padding: 12px 25px;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .modern-submit-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }

            .modern-submit-btn:hover::before {
                left: 100%;
            }

            .modern-submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px #8fc4e3;
            }

            .modern-submit-btn:disabled {
                opacity: 0.7;
                cursor: not-allowed;
                transform: none;
            }

            .ban-message {
                display: flex;
                align-items: center;
                gap: 10px;
                color: #e74c3c;
                font-weight: 500;
                background: rgba(231, 76, 60, 0.1);
                padding: 15px 20px;
                border-radius: 10px;
                border-left: 4px solid #e74c3c;
            }

            /* Comment List */
            .modern-comment-list {
                max-height: 700px;
                overflow-y: auto;
                padding-right: 10px;
                scrollbar-width: thin;
                scrollbar-color: #8fc4e3 #f1f3f4;
            }

            .modern-comment-list::-webkit-scrollbar {
                width: 8px;
            }

            .modern-comment-list::-webkit-scrollbar-track {
                background: #f1f3f4;
                border-radius: 4px;
            }

            .modern-comment-list::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #8fc4e3, #14425d);
                border-radius: 4px;
                transition: background 0.3s ease;
            }

            .modern-comment-list::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #14425d, #8fc4e3);
            }

            .comment-item {
                margin-bottom: 20px;
                opacity: 0;
                transform: translateY(20px);
                animation: fadeInUp 0.6s ease forwards;
            }

            .comment-item:nth-child(odd) {
                animation-delay: 0.1s;
            }

            .comment-item:nth-child(even) {
                animation-delay: 0.2s;
            }

            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Empty State */
            .empty-comments {
                text-align: center;
                padding: 60px 20px;
                color: #8899a6;
            }

            .empty-icon {
                font-size: 48px;
                color: #d1d9e0;
                margin-bottom: 20px;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0%, 100% { opacity: 0.5; }
                50% { opacity: 1; }
            }

            .empty-comments h4 {
                color: #333;
                margin-bottom: 10px;
                font-weight: 600;
            }

            /* Loading Indicator */
            .loading-indicator {
                display: flex;
                justify-content: center;
                padding: 30px;
            }

            .loading-spinner {
                display: flex;
                align-items: center;
                gap: 15px;
                color: #667eea;
                font-weight: 500;
            }

            .spinner {
                width: 20px;
                height: 20px;
                border: 2px solid #f3f3f3;
                border-top: 2px solid #667eea;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Scroll Sentinel */
            .scroll-sentinel {
                text-align: center;
                padding: 20px;
                color: #8899a6;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .scroll-sentinel:hover {
                color: #667eea;
                transform: translateY(-2px);
            }

            .scroll-indicator {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                font-weight: 500;
            }

            .scroll-indicator i {
                animation: bounce 2s infinite;
            }

            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
                40% { transform: translateY(-5px); }
                60% { transform: translateY(-3px); }
            }

            /* End Indicator */
            .end-indicator {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                padding: 30px 20px;
                color: #8899a6;
                font-weight: 500;
                font-size: 14px;
            }

            .end-line {
                flex: 1;
                height: 1px;
                background: linear-gradient(90deg, transparent, #ddd, transparent);
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .comment-input-card {
                    border-radius: 15px;
                    margin-bottom: 20px;
                }

                .comment-input-body {
                    padding: 20px;
                    flex-direction: column;
                    gap: 15px;
                }

                .modern-textarea {
                    padding: 12px 15px;
                    font-size: 16px; /* Prevent zoom on iOS */
                }

                .textarea-footer {
                    flex-direction: column;
                    gap: 10px;
                    align-items: stretch;
                }

                .modern-submit-btn {
                    width: 100%;
                    justify-content: center;
                    padding: 15px 25px;
                }

                .modern-comment-list {
                    max-height: 400px;
                    padding-right: 5px;
                }

                .empty-comments {
                    padding: 40px 20px;
                }

                .empty-icon {
                    font-size: 36px;
                }
            }

            /* Dark mode support */
            @media (prefers-color-scheme: dark) {
                .comment-input-body {
                    background: rgba(30, 30, 30, 0.95);
                }

                .modern-textarea {
                    background: #2a2a2a;
                    color: #e1e8ed;
                    border-color: #444;
                }

                .modern-textarea::placeholder {
                    color: #8899a6;
                }

                .char-counter {
                    color: #8899a6;
                }

                .empty-comments h4 {
                    color: #e1e8ed;
                }
            }

            /* Animation for new comments */
            .comment-item.new-comment {
                animation: slideInRight 0.5s ease;
            }

            @keyframes slideInRight {
                from {
                    opacity: 0;
                    transform: translateX(30px);
                }
                to {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            /* Single Comment Styles */
            .modern-single-comment {
                background: linear-gradient(180deg, #fbf6e6 0%, #8fc4e3 100%),
                           repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.03) 0 1px, rgba(0, 0, 0, 0) 1px 3px);
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 12px;
                box-shadow: 0 1px 6px #8fc4e3;
                border: 1px solid #8fc4e3;
                transition: all 0.3s ease;
                position: relative;
            }

            .modern-single-comment:hover {
                box-shadow: 0 4px 12px #8fc4e3;
                border-color: #8fc4e3;
            }

            /* Comment Header */
            .comment-header {
                display: flex;
                align-items: center;
                gap: 12px;
                margin-bottom: 10px;
            }

            .comment-user-info {
                flex: 1;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
            }

            .comment-time {
                font-size: 11px;
                color: #8899a6;
                font-weight: 400;
                margin-left: 8px;
            }

            .pin-btn {
                background: none;
                border: none;
                color: #8899a6;
                font-size: 12px;
                cursor: pointer;
                padding: 4px 6px;
                border-radius: 4px;
                transition: all 0.3s ease;
                margin-left: auto;
            }

            .pin-btn:hover {
                background: rgba(102, 126, 234, 0.1);
                color: #667eea;
            }

            .pin-btn.pinned {
                color: #f39c12;
                background: rgba(243, 156, 18, 0.1);
            }

            /* Comment Content */
            .comment-content {
                background: #f8f9fa;
                padding: 12px;
                border-radius: 8px;
                margin: 8px 0;
                line-height: 1.5;
                color: #333;
                font-size: 14px;
                border-left: 2px solid #e6ecf0;
                transition: all 0.3s ease;
            }

            .comment-content:hover {
                border-left-color: #8fc4e3;
            }

            .vip-sieu-viet-content {
                background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
                border-left-color: #ff8a00;
                color: #8b4513;
                font-weight: 500;
            }

            /* Comment Actions */
            .comment-actions {
                display: flex;
                gap: 6px;
                margin-top: 8px;
                flex-wrap: wrap;
            }

            .action-btn {
                background: #f8f9fa;
                color: #6c757d;
                border: 1px solid #e1e8ed;
                border-radius: 16px;
                padding: 4px 12px;
                font-size: 12px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 4px;
                text-decoration: none;
            }

            .action-btn:hover {
                background: #8fc4e3;
                color: white;
                border-color: #8fc4e3;
            }

            .action-btn.delete-btn:hover {
                background: #e74c3c;
                border-color: #e74c3c;
            }

            .action-btn.submit-btn {
                background: linear-gradient(135deg, #8fc4e3, #14425d);
                color: white;
                border: none;
            }

            .action-btn.submit-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px #8fc4e3;
            }

            .action-btn.cancel-btn:hover {
                background: #6c757d;
                border-color: #6c757d;
                color: white;
            }

            /* Reply Form */
            .reply-form {
                background: #f8fafb;
                border-radius: 12px;
                padding: 16px;
                margin-top: 16px;
                border: 1px dashed #d1d9e0;
            }

            .reply-textarea {
                width: 100%;
                border: 2px solid #e1e8ed;
                border-radius: 10px;
                padding: 12px 16px;
                font-size: 14px;
                line-height: 1.5;
                resize: vertical;
                transition: all 0.3s ease;
                background: white;
                color: #333;
                margin-bottom: 12px;
            }

            .reply-textarea:focus {
                outline: none;
                border-color: #8fc4e3;
                box-shadow: 0 0 0 3px #8fc4e3;
            }

            .reply-form-actions {
                display: flex;
                gap: 8px;
            }

            .ban-message-reply {
                display: flex;
                align-items: center;
                gap: 8px;
                color: #e74c3c;
                font-weight: 500;
                background: rgba(231, 76, 60, 0.1);
                padding: 12px 16px;
                border-radius: 8px;
                margin-top: 16px;
                border-left: 3px solid #e74c3c;
            }

            /* Replies Container */
            .replies-container {
                margin-top: 20px;
                padding-left: 20px;
                border-left: 2px solid #e6ecf0;
                position: relative;
            }

            .replies-container::before {
                content: '';
                position: absolute;
                left: -6px;
                top: 0;
                width: 10px;
                height: 10px;
                background: #e6ecf0;
                border-radius: 50%;
            }

            .reply-item {
                background: #fafbfc;
                border-radius: 12px;
                padding: 16px;
                margin-bottom: 12px;
                border: 1px solid #e6ecf0;
                transition: all 0.3s ease;
            }

            .reply-item:hover {
                background: #f5f8fa;
                border-color: #d1d9e0;
                transform: translateX(4px);
            }

            .reply-header {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                margin-bottom: 8px;
            }

            .reply-user-info {
                flex: 1;
                flex-direction: column;
                gap: 2px;
            }

            .reply-time {
                font-size: 11px;
                color: #8899a6;
                font-weight: 500;
            }

            .reply-content {
                background: white;
                padding: 12px;
                border-radius: 8px;
                margin: 8px 0;
                line-height: 1.5;
                color: #333;
                font-size: 13px;
                border-left: 2px solid #e6ecf0;
            }

            .reply-actions {
                margin-top: 8px;
            }

            .more-replies {
                text-align: center;
                padding: 12px;
                color: #8899a6;
                font-size: 13px;
                font-weight: 500;
                background: rgba(212, 175, 55, 0.05);
                border-radius: 8px;
                margin-top: 12px;
            }

            /* Mobile Responsive */
            @media (max-width: 768px) {
                .modern-single-comment {
                    padding: 12px;
                    border-radius: 8px;
                }

                .comment-header {
                    gap: 8px;
                    flex-wrap: wrap;
                }

                .comment-user-info {
                    flex: 1;
                    min-width: 0;
                }

                .comment-time {
                    font-size: 10px;
                    margin-left: 0;
                    margin-top: 2px;
                }

                .comment-content {
                    padding: 10px;
                    font-size: 13px;
                    margin: 6px 0;
                }

                .action-btn {
                    padding: 3px 8px;
                    font-size: 11px;
                    gap: 3px;
                }

                .pin-btn {
                    font-size: 11px;
                    padding: 3px 5px;
                }

                .replies-container {
                    padding-left: 12px;
                    margin-top: 12px;
                }

                .reply-item {
                    padding: 10px;
                }

                .reply-content {
                    padding: 8px;
                    font-size: 12px;
                }
            }

            /* User Badge Styles */
            .vip-sieu-viet-badge {
                background: linear-gradient(135deg, #ff8a00, #ff2070);
                color: white;
                padding: 2px 8px;
                border-radius: 12px;
                font-weight: bold;
                font-size: 11px;
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
                box-shadow: 0 1px 4px rgba(255, 138, 0, 0.3);
                display: inline-block;
                vertical-align: middle;
                animation: shimmer 2s infinite;
            }

            @keyframes shimmer {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.8; }
            }

            /* Tooltip Styles */
            .tooltip-icon {
                position: relative;
                display: inline-block;
                cursor: pointer;
                vertical-align: middle;
            }

            .tooltip-text {
                visibility: hidden;
                background: rgba(0, 0, 0, 0.9);
                color: white;
                text-align: center;
                border-radius: 4px;
                padding: 4px 6px;
                position: absolute;
                z-index: 1000;
                bottom: 120%;
                left: 50%;
                margin-left: -30px;
                opacity: 0;
                transition: all 0.3s ease;
                font-size: 10px;
                font-weight: 500;
                white-space: nowrap;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                max-width: 80px;
            }

            .tooltip-text::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -5px;
                border-width: 5px;
                border-style: solid;
                border-color: rgba(0, 0, 0, 0.9) transparent transparent transparent;
            }

            .tooltip-icon:hover .tooltip-text {
                visibility: visible;
                opacity: 1;
                transform: translateY(-5px);
            }

            /* Custom Badge Tooltip */
            .custom-badge-tooltip {
                position: relative;
                display: inline-block;
                cursor: pointer;
            }

            .custom-badge-tooltiptext {
                visibility: hidden;
                background: linear-gradient(135deg, #8fc4e3, #14425d);
                color: white;
                text-align: center;
                border-radius: 12px;
                padding: 15px;
                position: absolute;
                z-index: 1000;
                bottom: 125%;
                left: 50%;
                margin-left: -60px;
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.2);
                min-width: 120px;
            }

            .custom-badge-tooltiptext::after {
                content: "";
                position: absolute;
                top: 100%;
                left: 50%;
                margin-left: -8px;
                border-width: 8px;
                border-style: solid;
                border-color: #8fc4e3 transparent transparent transparent;
            }

            .custom-badge-tooltip:hover .custom-badge-tooltiptext {
                visibility: visible;
                opacity: 1;
                transform: translateY(-10px) scale(1.05);
            }

            .custom-badge-name {
                font-weight: bold;
                font-size: 13px;
                margin-top: 8px;
                text-shadow: 0 1px 2px rgba(0,0,0,0.3);
            }

            /* Crossed Swords Animation */
            .crossed-swords {
                position: relative;
                display: inline-block;
                width: 40px;
                height: 40px;
            }

            .sword-left, .sword-right {
                position: absolute;
                width: 25px;
                height: 25px;
                transition: all 0.3s ease;
            }

            .sword-left {
                top: 5px;
                left: 0;
                transform: rotate(-45deg);
            }

            .sword-right {
                top: 5px;
                right: 0;
                transform: rotate(45deg);
            }

            .crossed-swords:hover .sword-left {
                transform: rotate(-45deg) scale(1.1);
                filter: brightness(1.2);
            }

            .crossed-swords:hover .sword-right {
                transform: rotate(45deg) scale(1.1);
                filter: brightness(1.2);
            }

            /* Role-based text colors and effects */
            .admin-badge {
                color: #dc3545 !important;
                font-weight: bold;
                text-shadow: 0 1px 2px rgba(220,53,69,0.3);
            }

            .mod-badge {
                color: #198754 !important;
                font-weight: bold;
                text-shadow: 0 1px 2px rgba(25,135,84,0.3);
            }

            .vip-badge {
                color: #0d6efd !important;
                font-weight: bold;
                text-shadow: 0 1px 2px rgba(13,110,253,0.3);
            }

            .content-badge {
                color: #8fc4e3 !important;
                font-weight: bold;
                text-shadow: 0 1px 2px #8fc4e3;
            }

            .vip-pro-badge {
                color: purple !important;
                font-weight: bold;
                text-shadow: 0 1px 2px rgba(128,0,128,0.3);
            }

            .vip-pro-max-badge {
                color: #f37200 !important;
                font-weight: bold;
                text-shadow: 0 1px 2px rgba(243,114,0,0.3);
            }

            /* Badge hover effects */
            .tooltip-icon img, .custom-badge-tooltip img {
                transition: all 0.3s ease;
                border-radius: 4px;
            }

            .tooltip-icon:hover img, .custom-badge-tooltip:hover img {
                transform: scale(1.1);
                filter: brightness(1.1) saturate(1.2);
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }

            /* Responsive adjustments for badges */
            @media (max-width: 768px) {
                .tooltip-text {
                    font-size: 11px;
                    padding: 4px 6px;
                    margin-left: -30px;
                }

                .custom-badge-tooltiptext {
                    padding: 12px;
                    margin-left: -50px;
                    min-width: 100px;
                }

                .custom-badge-name {
                    font-size: 12px;
                    margin-top: 6px;
                }

                .tooltip-icon img, .custom-badge-tooltip img {
                    max-width: 24px;
                    max-height: 24px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Character counter functionality
                const textarea = document.querySelector('.modern-textarea');
                const charCounter = document.getElementById('char-counter');

                if (textarea && charCounter) {
                    textarea.addEventListener('input', function() {
                        const currentLength = this.value.length;
                        const maxLength = 1000;
                        charCounter.textContent = `${currentLength}/${maxLength}`;

                        // Color coding for character count
                        if (currentLength > maxLength * 0.9) {
                            charCounter.style.color = '#e74c3c';
                        } else if (currentLength > maxLength * 0.7) {
                            charCounter.style.color = '#f39c12';
                        } else {
                            charCounter.style.color = '#8899a6';
                        }
                    });
                }

                // Add animation class to new comments
                document.addEventListener('livewire:updated', function() {
                    const newComments = document.querySelectorAll('.comment-item:not(.animated)');
                    newComments.forEach(comment => {
                        comment.classList.add('animated', 'new-comment');
                    });
                });
                const commentContainer = document.getElementById('comment-container');
                const scrollSentinel = document.getElementById('scroll-sentinel');
                let isLoading = false;

                // Intersection Observer để detect khi scroll sentinel xuất hiện
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !isLoading) {
                            isLoading = true;
                            // Gọi Livewire method để load thêm comments
                            @this.call('loadMoreOnScroll').then(() => {
                                isLoading = false;
                            });
                        }
                    });
                }, {
                    root: commentContainer,
                    rootMargin: '100px',
                    threshold: 0.1
                });

                // Bắt đầu observe scroll sentinel
                if (scrollSentinel) {
                    observer.observe(scrollSentinel);
                }

                // Update observer khi Livewire re-render
                document.addEventListener('livewire:updated', function() {
                    const newScrollSentinel = document.getElementById('scroll-sentinel');
                    if (newScrollSentinel) {
                        observer.observe(newScrollSentinel);
                    }
                });
            });

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Bạn có chắc muốn xóa?',
                    text: "Hành động này không thể hoàn tác!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('deleteComment', id);
                    }
                });
            }

            // Event listeners cho Livewire v3
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('deleteSuccess', () => {
                    Swal.fire({
                        title: 'Đã xóa!',
                        text: 'Bình luận đã được xóa.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false,
                    });
                });
            });
        </script>
    @endpush
@endonce
