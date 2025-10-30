@extends('Frontend.layouts.default')

@push('custom_schema')
    {!! SEO::generate() !!}
@endpush

@section('content')
    @php
        if (count($chapters->items()) > 1) {
            $arrChapters = array_chunk($chapters->items(), count($chapters->items()) / 2);
        } else {
            $arrChapters[] = $chapters->items();
        }
        $storyFinal = $story;

    @endphp
    <input type="hidden" id="story_slug" value="{{ $slug }}">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-12 col-md-7 col-lg-8">
                <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4405345005005059"
                    crossorigin="anonymous"></script>
                <!-- Top Header -->
                <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-4405345005005059"
                    data-ad-slot="4536454491" data-ad-format="auto" data-full-width-responsive="true"></ins>
                <script>
                    (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
                @include('Frontend.snippets.title_component', [
                    'title' => 'Thông tin truyện',
                ])

                <div class="story-detail">
                    <div class="story-detail__top d-flex align-items-start">
                        <div class="row align-items-start">
                            <div class="col-12 col-md-12 col-lg-3 story-detail__top--image">
                                <div class="book-3d">
                                    <img src="{{ \App\Helpers\Helper::getStoryImageUrl($story->image) }}"
                                        alt="{{ $story->name }}" class="img-fluid w-100" width="200" height="300"
                                        loading='lazy'>
                                </div>
                            </div>
                            <div class="col-12 col-md-12 col-lg-9">
                                <h3 class="text-center story-name fw-bold">{{ $story->name }}</h3>
                                <hr>

                                <div class="story-detail__top--desc px-3">
                                    {!! \App\Helpers\Helper::sanitizeCKEditorContent($story->desc) !!}
                                </div>

                                <div class="info-more">
                                    <div class="info-more--more active" id="info_more">
                                        <span class="me-1 text-dark">Xem thêm</span>
                                        <svg width="14" height="8" viewBox="0 0 14 8" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.70749 7.70718L13.7059 1.71002C14.336 1.08008 13.8899 0.00283241 12.9989 0.00283241L1.002 0.00283241C0.111048 0.00283241 -0.335095 1.08008 0.294974 1.71002L6.29343 7.70718C6.68394 8.09761 7.31699 8.09761 7.70749 7.70718Z"
                                                fill="#2C2C37"></path>
                                        </svg>
                                    </div>

                                    <a class="info-more--collapse text-decoration-none" href="#info_more">
                                        <span class="me-1 text-dark">Thu gọn</span>
                                        <svg width="14" height="8" viewBox="0 0 14 8" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M7.70749 0.292817L13.7059 6.28998C14.336 6.91992 13.8899 7.99717 12.9989 7.99717L1.002 7.99717C0.111048 7.99717 -0.335095 6.91992 0.294974 6.28998L6.29343 0.292817C6.68394 -0.097606 7.31699 -0.0976055 7.70749 0.292817Z"
                                                fill="#2C2C37"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="story-detail__bottom mb-3 mt-4">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-3 story-detail__bottom--info">
                                <p class="mb-1">
                                    <strong>Tác giả:</strong>
                                    <a href="#"
                                        class="text-decoration-none text-dark hover-title">{{ $story->author?->name ?? 'Chưa xác định' }}</a>
                                </p>
                                <div class="d-flex align-items-center mb-1 flex-wrap">
                                    <strong class="me-1">Thể loại:</strong>
                                    <div class="d-flex align-items-center flex-warp">
                                        @if ($story->categories)
                                            @foreach ($story->categories as $category)
                                                <a href="#"
                                                    class="text-decoration-none text-dark hover-title @if (!$loop->last) me-1 @endif"
                                                    style="width: max-content;">{{ $category->name }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                </a>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <p class="mb-1">
                                    <strong>Trạng thái:</strong>
                                    <span class="text-info">{{ $story->is_full ? 'Full' : 'Đang ra' }}</span>
                                </p>

                                {{-- @if (auth()->check() && (auth()->user()->id == $story->author_id || auth()->user()->can('sua_truyen')))
                                    <div class="vip-toggle-container mb-2">
                                        <div class="d-flex align-items-center">
                                            <strong class="me-2">Truyện VIP:</strong>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="vipToggle" 
                                                       {{ $story->is_vip ? 'checked' : '' }} 
                                                       data-story-id="{{ $story->id }}">
                                                <label class="form-check-label" for="vipToggle">
                                                    {{ $story->is_vip ? 'Có' : 'Không' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($story->is_vip)
                                    <p class="mb-1">
                                        <strong>Truyện VIP:</strong>
                                        <span class="text-warning">Có</span>
                                    </p>
                                @endif --}}
                            </div>



                            @if (count($chaptersNew) > 0)
                                <div class="col-12 col-md-12 col-lg-9">
                                    @include('Frontend.snippets.title_component', [
                                        'title' => 'Các chương mới nhất',
                                    ])

                                    <div class="story-detail__bottom--chapters-new">
                                        <ul>
                                            @foreach ($chaptersNew as $chapterNew)
                                                <li style="list-style: none" class="story-stop-rating py-3">
                                                    <a href="{{ route('chapter', ['slugStory' => $story->slug, 'slugChapter' => $chapterNew->slug]) }}"
                                                        class="text-decoration-none hover-title"><span
                                                            class="new-badge">{{ $chapterNew->name }}</span></a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Thông tin Donate -->
                            @if ($donates->count() > 0)
                                <div class="col-12">
                                    <div class="donate-section">
                                        <h6 class="donate-title">
                                            <i class="fas fa-heart text-danger me-2"></i>Thông tin Ủng hộ
                                        </h6>
                                        <div class="donate-grid">
                                            @foreach ($donates as $donate)
                                                <div class="donate-item">
                                                    <div class="donate-bank">
                                                        <i class="fas fa-university text-primary"></i>
                                                        <div>
                                                            <strong>{{ $donate->bank_name }}</strong>
                                                            @if ($donate->donate_info)
                                                                <div class="text-muted small">{{ $donate->donate_info }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @if ($donate->image)
                                                        <div class="donate-qr">
                                                            <img src="{{ Storage::url($donate->image) }}"
                                                                alt="QR {{ $donate->bank_name }}" class="qr-thumb"
                                                                onclick="viewQRCode('{{ Storage::url($donate->image) }}', '{{ $donate->bank_name }}')">
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    <div class="story-detail__list-chapter">
                        <div class="row">
                            <div class="col-12 col-sm-7 col-md-12 col-lg-7">
                                @include('Frontend.snippets.title_component', [
                                    'title' => 'Danh sách chương',
                                ])
                            </div>
                            <div class="col-12 col-sm-5 col-md-12 col-lg-5">

                                <div class="card-search py-2">
                                    <div class="search-wrapper">

                                        <div class="wuxia-search">
                                            <div class="wuxia-search__container">
                                                <input class="form-control wuxia-search__input" type="text"
                                                    id="search-chapter" data-story-slug="{{ $story->slug }}"
                                                    placeholder="Chương, Tên chương, Nội dung ...">
                                                <button class="btn search-chapter-story wuxia-search__submit" type="button"
                                                    id="btn-search" aria-label="Tìm kiếm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em"
                                                        viewBox="0 0 512 512">
                                                        <path
                                                            d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>



                        <div class="form-check ms-0 ms-md-3 mt-3 mt-md-0">
                            <input class="form-check-input" type="checkbox" id="orderToggle"
                                {{ request()->input('old_first') ? 'checked' : '' }}>
                            <label class="form-check-label" for="orderToggle">
                                Chương cũ lên trước
                            </label>
                        </div>

                        @php
                            $isOldFirst = request()->input('old_first', false);
                            $reversedChapters = $isOldFirst
                                ? $chapters->sortBy('chapter')
                                : $chapters->sortByDesc('chapter');
                        @endphp



                        <div class="story-detail__list-chapter--list mt-3">
                            <div id="chapters-container">
                                @include('Frontend.components.chapter-list', [
                                    'chapters' => $reversedChapters,
                                    'storySlug' => $story->slug,
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="pagination" style="justify-content: center;">
                        {{ $chapters->appends(request()->query())->onEachSide(2)->links('Frontend.snippets.custom_pagination', ['has_ajax' => true]) }}
                    </div>

                </div>
            </div>

            <div class="col-12 col-md-5 col-lg-4 sticky-md-top">
                @include('Frontend.components.story_donations', ['story' => $story])
                @include('Frontend.snippets.top_ratings', [
                    'ratingsDay' => $ratingsDay,
                    'ratingsMonth' => $ratingsMonth,
                    'ratingsAllTime' => $ratingsAllTime,
                    'storiesDay' => $storiesDay,
                    'storiesMonth' => $storiesMonth,
                    'storiesAllTime' => $storiesAllTime,
                ])

                @if ($story->author && $story->author->stories->count() - 1 > 0)
                    <div class="section-stories-reading p-2 rounded mb-3">
                        @include('Frontend.snippets.title_component', [
                            'title' => 'Truyện cùng tác giả',
                        ])
                        <div class="stories-reading">
                            @foreach ($story->author->stories as $storyNear)
                                @if ($storyNear->slug != $story->slug)
                                    <div class="story-item-no-image">
                                        <div class="story-item-no-image__name d-flex align-items-center border-0"
                                            style="width: 70%;">
                                            <h3 class="me-1 mb-0 d-flex align-items-center">
                                                <i class="fa-solid fa-chevron-right"></i>
                                                <a href="{{ route('story', ['slug' => $storyNear->slug]) }}"
                                                    class="text-decoration-none text-dark fs-6 hover-title text-one-row">{{ $storyNear->name }}</a>
                                            </h3>
                                            @if ($storyNear->is_new)
                                                <span class="section-stories-reading label-title label-new">New</span>
                                            @endif

                                            @if ($storyNear->is_full)
                                                <span class="section-stories-reading label-title label-full">Full</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif


                @include('Frontend.sections.main.list_category')


            </div>
        </div>

        {{-- Plugin FB Comment --}}
        {{-- <div class="fb-comments" data-href="https://developers.facebook.com/docs/plugins/comments#configurator"
            data-width="" data-numposts="5"></div> --}}
    </div>
@endsection

@push('styles')
    <style>
        .chapter-card {
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .chapter-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .stats-list-chapter {
            display: flex;
            flex-direction: row;
            gap: 0.8rem;
        }

        .counter-chapter {
            font-weight: bold;
            margin-right: 5px;
            transition: all 0.3s ease-out;
        }

        .stat-item-chapter {
            opacity: 0;
            animation: fadeIn 0.5s ease forwards;
        }

        .new-badge {
            color: #ff0000;
            font-weight: bold;
            margin-left: 5px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .new-badge {
            animation: pulse 1s ease-in-out infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.7;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .chapter-list li a {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .chapter-list .date {
            font-weight: bold;
            border: 1px solid;
            display: flex;
            border-radius: 4px;
            text-align: center;
            width: 45px;
            height: 51px;
            line-height: 1.2;
            flex-direction: column;
            justify-content: center;
        }

        .chapter-list {
            list-style: none;
            padding: 0;
        }

        .chapter-list li {
            border-bottom: none !important;
            margin-bottom: 8px;
        }

        .section-stories-reading.label-title.label-new {
            border-color: #8EB3FD;
            color: #8EB3FD;
        }

        .section-stories-reading.label-title.label-full {
            border-color: #86AD86;
            color: #86AD86;
        }

        .section-stories-reading.label-title {
            padding: 1px 3px;
            font-size: 13px;
            vertical-align: bottom;
            margin-left: 5px;
            border: 1px solid;
            border-radius: 2px;
            display: inline-block;
        }

        .stories-reading .fa-chevron-right {
            font-size: 14px;
            color: #999;
            margin-right: 8px;
        }

        /* Wuxia search box styling for story page */
        .wuxia-search {
            position: relative;
        }

        .wuxia-search__container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .wuxia-search__input {
            border-radius: 12px;
            border: 1px solid #8fc4e3;
            background:
                linear-gradient(180deg, #fbf6e6 0%, #efe4c9 100%),
                repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.03) 0 1px, rgba(0, 0, 0, 0) 1px 3px);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .4);
            padding-right: 50px;
            width: 100%;
        }

        .wuxia-search__input::placeholder {
            color: #7a5c2f;
        }

        .search-chapter-story.wuxia-search__submit {
            position: absolute;
            right: 0;
            top: 0;

        }

        .dark-theme .wuxia-search__input {
            background: linear-gradient(180deg, #2c2a26 0%, #24221f 100%);
            color: #fff;
            border-color: #8fc4e3;
        }

        .dark-theme .wuxia-search__submit {
            background: radial-gradient(circle at 30% 30%, #a58a36, #6b5a22 70%);
            color: #fff;
        }

        .dark-theme .wuxia-search__submit:hover {
            background: radial-gradient(circle at 30% 30%, #c4a94a, #8b7a2e 70%);
        }

        /* Donate Section Styles */
        .donate-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .donate-title {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 1rem;
        }

        .donate-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .donate-item {
            background: white;
            border-radius: 6px;
            padding: 12px;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .donate-bank {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .donate-bank i {
            font-size: 1.2rem;
        }

        .donate-qr {
            flex-shrink: 0;
        }

        .qr-thumb {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .qr-thumb:hover {
            transform: scale(1.1);
        }

        /* Dark theme */
        .dark-theme .donate-section {
            background: #2c2c2c;
            border-color: #404040;
        }

        .dark-theme .donate-item {
            background: #333;
            border-color: #404040;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .donate-grid {
                grid-template-columns: 1fr;
            }

            .donate-item {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
@endpush

@push('scripts')
    @vite(['resources/assets/frontend/js/story.js'])
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let searchTimeout;
            const searchInput = $('#search-chapter');
            const btnSearch = $('#btn-search');
            const chaptersContainer = $('#chapters-container');
            const loadMoreBtn = $('#load-more');
            const storySlug = $('#search-chapter').data('story-slug');

            function performSearch() {
                const searchTerm = searchInput.val();
                btnSearch.html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: `/truyen/${storySlug}/search-chapters`,
                    data: {
                        search: searchTerm,
                        page: 1
                    },
                    success: function(response) {
                        chaptersContainer.html(response.html);

                        if (response.hasMore) {
                            loadMoreBtn.data('page', 1).show()
                                .html(`Xem thêm 1/${response.lastPage}`);
                        } else {
                            loadMoreBtn.hide();
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            showToast(response.message, 'error');
                        } else if (xhr.status === 403) {
                            showToast('Bạn không có quyền tìm kiếm chương', 'error');
                        } else {
                            showToast('Có lỗi xảy ra khi tìm kiếm', 'error');
                        }
                    },
                    complete: function() {
                        btnSearch.html('<i class="fas fa-search"></i>');
                    }
                });
            }

            searchInput.on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 500);
            });

            btnSearch.on('click', performSearch);
        });
        $('#close-search').click(function() {
            $('#search-results').addClass('d-none');
            $('#search-chapter').val('');
            removeHighlights();
        });

        // Click ra ngoài thì ẩn kết quả
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-wrapper').length) {
                $('#search-results').addClass('d-none');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            document.getElementById('orderToggle').addEventListener('change', function() {
                const isChecked = this.checked ? 1 : 0;
                const url = new URL(window.location.href);
                url.searchParams.set('old_first', isChecked);
                window.location.href = url.href;
            });

            $('#orderToggle').on('change', function() {
                loadChapters();
            });
        });
    </script>

    <!-- QR Code Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrCodeImage" src="" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                    <p id="qrCodeTitle" class="mt-3 mb-0 fw-bold"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-outline-primary" onclick="downloadQRCode()">
                        <i class="fas fa-download"></i> Tải xuống
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewQRCode(imageSrc, bankName) {
            document.getElementById('qrCodeImage').src = imageSrc;
            document.getElementById('qrCodeTitle').textContent = bankName;
            new bootstrap.Modal(document.getElementById('qrCodeModal')).show();
        }

        function downloadQRCode() {
            const link = document.createElement('a');
            link.href = document.getElementById('qrCodeImage').src;
            link.download = 'qr-code-' + document.getElementById('qrCodeTitle').textContent.replace(/\s+/g, '-')
                .toLowerCase() + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // VIP Toggle functionality
        $('#vipToggle').change(function() {
            const isVip = $(this).is(':checked');
            const storyId = $(this).data('story-id');
            const label = $(this).siblings('label');

            $.ajax({
                url: `/truyen/${storyId}/toggle-vip`,
                type: 'POST',
                data: {
                    is_vip: isVip ? 1 : 0,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    label.text(isVip ? 'Có' : 'Không');
                    showToast(response.message, 'success');
                },
                error: function(xhr) {
                    // Revert toggle state
                    $('#vipToggle').prop('checked', !isVip);
                    label.text(!isVip ? 'Có' : 'Không');

                    const response = xhr.responseJSON;
                    showToast(response?.message || 'Có lỗi xảy ra', 'error');
                }
            });
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = $(`
                <div class="toast-notification ${type}" style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? '#48bb78' : '#e53e3e'};
                    color: white;
                    padding: 16px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 9999;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                    max-width: 300px;
                    font-weight: 500;
                ">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `);

            $('body').append(toast);

            // Animate in
            setTimeout(() => {
                toast.css('transform', 'translateX(0)');
            }, 100);

            // Remove after 4 seconds
            setTimeout(() => {
                toast.css('transform', 'translateX(100%)');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
@endpush
