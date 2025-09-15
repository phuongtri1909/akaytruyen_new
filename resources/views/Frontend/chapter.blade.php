@extends('Frontend.layouts.default')

@push('custom_schema')
    {{-- {!! SEOMeta::generate() !!} --}}
    {{-- {!! JsonLd::generate() !!} --}}
    {!! SEO::generate() !!}
@endpush

@section('content')
    <div class="chapter-wrapper container my-5">
        <div class="chapter-nav text-center main">
            <div class="chapter-nav d-flex justify-content-between align-items-center mb-4 top-0">

                @if ($chapterBefore)
                    <a href="{{ route('chapter', ['slugStory' => $story->slug, 'slugChapter' => $chapterBefore->slug]) }}"
                        class="btn bg-primary-custom rounded-circle text-white px-3">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @else
                    <button disabled class="btn btn-outline-secondary rounded-circle px-3">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @endif

                <strong class="chapter-nav text-center">
                    <a href="{{ route('story', ['slug' => $chapter->story->slug]) }}"
                        style="color: red; position: relative; left: -4px; top: 1px; text-decoration: none;">
                        <i>{{ $chapter->story->name }}</i>
                    </a>
                </strong>



                @if ($chapterAfter)
                    <a href="{{ route('chapter', ['slugStory' => $story->slug, 'slugChapter' => $chapterAfter->slug]) }}"
                        class="btn bg-primary-custom rounded-circle text-white px-3">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button disabled class="btn btn-outline-secondary rounded-circle px-3">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>
            <h1 class="text-center custom-text"><b>Chương {{ $chapter->chapter }}: {{ $chapter->name }}</b>
            </h1>
        </div>


        <div class="container mt-2">
            <div class="card-search py-2 text-center">
                <div>

                    <span class="fs-8 custom-element">
                        <i class="fa-regular fa-file-word"></i> Tiểu thuyết gốc: {{ $chapter->word_count }} Chữ
                        <span class="ms-2">
                            <i class="fa-regular fa-clock"></i> {{ $chapter->created_at }}
                        </span>
                    </span>
                </div>


                <div class="search-wrapper position-relative timkiem">
                    <div class="search-wrapper">
                        <div class="wuxia-search">
                            <div class="wuxia-search__container">
                                <input class="form-control wuxia-search__input" type="text" id="search" placeholder="Tìm Nội dung ...">
                                <button class="btn wuxia-search__submit" type="button" id="btn-search" aria-label="Tìm kiếm">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                        <path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <hr class="chapter-end container-fluid">

        <div class="">

            <?php
            $chapter->content = html_entity_decode(htmlspecialchars_decode($chapter->content), ENT_QUOTES, 'UTF-8');

            $word_special_chars = [
                '&ldquo;' => '“',
                '&rdquo;' => '”',
                '&lsquo;' => '‘',
                '&rsquo;' => '’',
                '&nbsp;' => ' ',
                '&hellip;' => '...',
                '&ndash;' => '-',
                '&mdash;' => '—',
                '‐' => '-',
                '‑' => '-',
                '‒' => '-',
                '–' => '-',
                '—' => '-',
            ];
            $chapter->content = str_replace(array_keys($word_special_chars), array_values($word_special_chars), $chapter->content);
            $chapter->content = str_replace(['&nbsp;', "\xc2\xa0"], ' ', $chapter->content);

            $chapter->content = preg_replace('/\r\n|\r|\n/', '</p><p>', $chapter->content);

            $chapter->content = preg_replace('/\s*([.,!?]?\s*[."\'”’])\s*/u', '$1', $chapter->content);

            $chapter->content = preg_replace('/([.,!?])([^\s”’])/u', '$1 $2', $chapter->content);

            $chapter->content = '<p>' . $chapter->content . '</p>';

            $chapter->content = preg_replace('/<p>\s*<\/p>/', '', $chapter->content);
            ?>


            @php
                $restrictedSlug = 'con-duong-ba-chu-ngoai-truyen';
                $allowedRoles = ['Admin', 'vip', 'Mod', 'SEO', 'Content', 'VIP PRO', 'VIP PRO MAX', 'VIP SIÊU VIỆT'];
            @endphp

            <div id="chapter-content" class="chapter-content mb-4 p-3 border-0 rounded"
                style="font-size: 1.5rem; min-height: 500px; line-height: 2; position: relative; top: -54px;">
                @if ($slugStory === $restrictedSlug)
                    @if (auth()->check() && auth()->user()->hasAnyRole($allowedRoles))
                        {!! \App\Helpers\Helper::sanitizeChapterContent($chapter->content) !!}
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {

                                document.addEventListener('contextmenu', function(event) {
                                    event.preventDefault();
                                });


                                document.addEventListener('keydown', function(event) {
                                    if ((event.ctrlKey && ['c', 'x', 'u', 'a'].includes(event.key.toLowerCase())) || event
                                        .key === 'F12') {
                                        event.preventDefault();
                                    }
                                });


                                document.getElementById('chapter-content').style.userSelect = 'none';
                            });
                        </script>
                    @else
                        <div class="alert alert-success text-center" style="border: 2px dashed #28a745;">
                            <strong>Bạn vui lòng nâng cấp lên VIP ở đây hoặc trên kênh Youtube để đọc toàn bộ truyện
                                này.</strong>
                        </div>
                    @endif
                @else
                    {!! \App\Helpers\Helper::sanitizeChapterContent($chapter->content) !!}
                @endif
            </div>


            <div class="chapter-nav d-flex justify-content-between align-items-center mb-4">

                @if ($chapterBefore)
                    <a href="{{ route('chapter', ['slugStory' => $story->slug, 'slugChapter' => $chapterBefore->slug]) }}"
                        class="btn bg-primary-custom rounded-circle text-white px-3">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @else
                    <button disabled class="btn btn-outline-secondary rounded-circle px-3">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                @endif


                @auth
                    @if ($story->slug !== 'con-duong-ba-chu-ngoai-truyen')
                        @if (auth()->user()->hasRole('Admin'))
                            <a href="{{ route('download.epub', ['slugStory' => $story->slug, 'slugChapter' => $chapter->slug]) }}"
                                class="btn bg-primary-custom rounded-circle text-white px-4">
                                <i class="fas fa-download"></i> Download EPUB
                            </a>
                        @endif
                    @else
                        <div class="alert alert-success text-center">
                            <strong>Truyện này không cho phép tải về.</strong>
                        </div>
                    @endif
                @endauth



                @if ($chapterAfter)
                    <a href="{{ route('chapter', ['slugStory' => $story->slug, 'slugChapter' => $chapterAfter->slug]) }}"
                        class="btn bg-primary-custom rounded-circle text-white px-3">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                @else
                    <button disabled class="btn btn-outline-secondary rounded-circle px-3">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                @endif
            </div>

            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    let chapterContent = document.getElementById("chapter-content");
                    if (chapterContent) {
                        chapterContent.innerHTML = chapterContent.innerHTML.replace(/([^\s])\./g, "$1. ");
                    }
                });
            </script>

        </div>

        <div class="section-list-category w-100">
            @if (!Auth()->check() || (Auth()->check() && Auth()->user()->userBan->comment == false))
                @include('Frontend.components.comment', [
                    'pinnedComments' => $pinnedComments,
                    'regularComments' => $regularComments,
                ])
            @else
                <div class="text-center py-5">
                    <i class="fas fa-sad-tear fa-4x text-muted mb-3 animate__animated animate__shakeX"></i>
                    <h5 class="text-danger">Bạn đã bị cấm bình luận!</h5>
                </div>
            @endif
        </div>
    </div>
@endsection

@once
    @push('styles')
        <style>
            .fs-8 {
                font-size: 0.8em;
            }

            .custom-element {
                position: relative;
                top: -59px;
            }

            .input-group {
                position: relative;
                top: -21px;

            }

            .main {
                position: relative;
                top: -71px;
            }

            .timkiem {
                position: relative;
                top: -28px;
            }

            @media (max-width: 768px) {
                .main {
                    top: -50px;
                }

                .timkiem {
                    top: -15px;
                }

                .custom-element {
                    position: relative;
                    top: -20px;
                    left: 1px;
                }
            }

            @media (max-width: 480px) {
                .main {
                    top: -30px;
                }

                .timkiem {
                    top: -10px;
                }

                .custom-element {
                    position: relative;
                    top: -50px;
                    left: 0;
                }
            }

            .chapter-content {
                @if($chapterFont)
                    font-family: {!! config('fonts.' . $chapterFont) !!};
                @endif
                @if ($chapterFontSize)
                    font-size: {{ $chapterFontSize }}px;
                @endif
                @if ($chapterLineHeight)
                    line-height: {{ $chapterLineHeight }}%;
                @endif
            }

            .custom-alert {
                background-color: #28a745;
                /* Xanh lá cây đậm */
                color: white;
                padding: 15px;
                border-radius: 10px;
                text-align: center;
                font-weight: bold;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            }

            .custom-alert a {
                color: black;
                text-decoration: underline;
                font-weight: bold;
            }

            .custom-alert a:hover {
                color: rgb(0, 0, 0);
            }

            @media (max-width: 768px) {
                #chapter-content {
                    padding: 0px !important;
                    margin: 0px !important;
                }
            }

            .highlight-search {
                background-color: yellow;
                color: black;
                font-weight: bold;
            }

            @media (max-width: 768px) {
                .my-5 {
                    margin-top: 1rem !important;
                    margin-bottom: 2rem !important;
                }
            }

            @media (max-width: 576px) {
                .my-5 {
                    margin-top: 1.5rem !important;
                    margin-bottom: 1.5rem !important;
                }
            }

            .chapter-end {
                background: url(//static.8cache.com/img/spriteimg_new_white_op.png) 0 -51px;
                width: 277px !important;
                height: 35px;
                border-top: none;
                position: relative;
                top: -35px;

            }

            #chapter {
                min-height: 100vh;
                transition: all 0.3s ease;
            }

            .chapter-content {
                position: relative;
                padding: 20px;
                font-size: 18px;
                line-height: 1.8;
                text-align: justify;
                border-radius: 8px;
                scroll-behavior: smooth;
                transition: font-size 0.3s ease, font-family 0.3s ease, line-height 0.3s ease;
            }

            @media (max-width: 768px) {
                .chapter-content {
                    padding: 10px;
                    margin: 5px;
                    font-size: 16px;
                }
            }

            .chapter-content::-webkit-scrollbar {
                width: 8px;
            }

            .chapter-content::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 4px;
            }

            .chapter-content::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 4px;
            }

            .chapter-content::-webkit-scrollbar-thumb:hover {
                background: #555;
            }

            .theme-light {
                background-color: #fff;
                color: #333;
            }

            .theme-sepia {
                background-color: #f4ecd8;
                color: #5b4636;
            }

            .theme-dark {
                background-color: #2d2d2d;
                color: #ccc;
            }

            #search-results {
                z-index: 1000;
            }

            #search-results .card {
                border: 1px solid rgba(0, 0, 0, .125);
                box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            }

            #search-results .list-group-item {
                padding: 0.5rem 1rem;
                border-left: 0;
                border-right: 0;
            }

            #search-results .list-group-item:first-child {
                border-top: 0;
            }

            #search-results .list-group-item:hover {
                background-color: #f8f9fa;
            }

            @media (max-width: 768px) {
                #search-results {
                    position: fixed !important;
                    top: 60px;
                    left: 0;
                    right: 0;
                    margin: 0 15px;
                }
            }

            .theme-light {
                background-color: #fff;
                color: #333;
            }

            .theme-sepia {
                background-color: #f4ecd8;
                color: #5b4636;
            }

            .theme-dark {
                background-color: #2d2d2d;
                color: #ccc;


            }

            .chapter-content {

                font-family: var(--font-choice, 'Noto Sans', sans-serif);
            }

            .highlight {
                background-color: yellow;
                color: black;
                font-weight: bold;
                padding: 2px 4px;
                border-radius: 4px;
            }

            #chapter {
                min-height: 100vh;
                transition: all 0.3s ease;
            }

            .chapter-content {

                padding: 20px;
                font-size: 18px;
                line-height: 1.8;
                text-align: justify;
                border-radius: 8px;
                scroll-behavior: smooth;
            }

            .bg-primary-custom {
                background-color: #14425d !important;
            }

            .custom-text {
                color: #14425d;
                font-size: 24px;
                position: relative;
                left: -1px;
                top: -21px;
            }

            @media (max-width: 768px) {
                .custom-text {
                    font-size: 18px;
                    top: -15px;
                }
            }

            @media (max-width: 480px) {
                .custom-text {
                    font-size: 24px;
                    top: -10px;
                }
            }

            /* Wuxia search box styling for chapter page */
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

            .wuxia-search__submit {
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-50%);
                border-radius: 8px;
                border: none;
                background: radial-gradient(circle at 30% 30%, #8fc4e3, #14425d 70%);
                color: #4c380b;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .wuxia-search__submit:hover {
                background: radial-gradient(circle at 30% 30%, #a58a36, #6b5a22 70%);
                transform: translateY(-50%) scale(1.05);
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
        </style>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&family=Noto+Serif&family=Charter&display=swap"
            rel="stylesheet">
    @endpush
    @push('scripts')
        <script>
            let searchTimeout;
            let currentIndex = -1;
            let matches = [];

            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val().trim().toLowerCase();

                if (searchTerm.length < 2) {
                    removeHighlights();
                    return;
                }

                searchTimeout = setTimeout(() => {
                    highlightText(searchTerm);
                }, 300);
            });

            $('#close-search').click(function() {
                $('#search').val('');
                removeHighlights();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-wrapper').length) {
                    removeHighlights();
                }
            });

            function highlightText(searchTerm) {
                removeHighlights();

                const regex = new RegExp(searchTerm, 'gi');
                matches = [];

                $('.chapter-content').each(function() {
                    const $this = $(this);
                    const html = $this.html();
                    const newHtml = html.replace(regex, function(matched) {
                        matches.push(matched);
                        return `<span class="highlight">${matched}</span>`;
                    });
                    $this.html(newHtml);
                });

                if (matches.length > 0) {
                    currentIndex = 0;
                    scrollToMatch(currentIndex);
                }
            }


            function removeHighlights() {
                $('.highlight').each(function() {
                    $(this).replaceWith($(this).text());
                });
                matches = [];
                currentIndex = -1;
            }


            function scrollToMatch(index) {
                if (matches.length === 0) return;

                const element = $('.highlight').eq(index);
                if (element.length) {
                    $('html, body').animate({
                        scrollTop: element.offset().top - 100
                    }, 300);
                }
            }


            $(document).keydown(function(e) {
                if (matches.length === 0) return;

                if (e.key === 'Enter') {
                    if (e.shiftKey) {
                        currentIndex = (currentIndex - 1 + matches.length) % matches.length;
                    } else {
                        currentIndex = (currentIndex + 1) % matches.length;
                    }
                    scrollToMatch(currentIndex);
                }
            });
        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                        let chapterContent = document.getElementById("chapter-content");
                        if (chapterContent) {
                            let content = chapterContent.innerHTML;
                            content = content.replace(/\s*([.,!?])\s*(["'”’])/g, '$1$2');
                            content = content.replace(/([.,!?])([^\s"”’])/g, '$1 $2');
                            content = content.replace(/(["'”’])([^\s.,!?])/g, '$1 $2');
                            chapterContent.innerHTML = content;
                        }
                    });
        </script>

       
        @vite(['resources/assets/frontend/js/chapter.js'])
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/jquery.mark.min.js"></script>

                <!-- Fix floating tools settings persistence -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Re-initialize floating tools if needed
                if (typeof window.objConfigFont === 'undefined') {
                    window.objConfigFont = [{
                            name: 'roboto',
                            value: "'Roboto Condensed', sans-serif"
                        },
                        {
                            name: 'mooli',
                            value: "'Mooli', sans-serif"
                        },
                        {
                            name: 'patrick_hand',
                            value: "'Patrick Hand', cursive"
                        },
                        {
                            name: 'noto_sans',
                            value: "'Noto Sans', sans-serif"
                        },
                        {
                            name: 'noto_serif',
                            value: "'Noto Serif', serif"
                        },
                        {
                            name: 'charter',
                            value: "'Charter', serif"
                        }
                    ];
                }

                // Helper functions
                function getCookieValue(name) {
                    const value = `; ${document.cookie}`;
                    const parts = value.split(`; ${name}=`);
                    if (parts.length === 2) return parts.pop().split(';').shift();
                    return null;
                }

                function applySavedSettings() {
                    const chapterContent = document.querySelector('.chapter-content');
                    if (!chapterContent) return;

                    // Apply saved font size
                    let savedFontSize = localStorage.getItem('chapter_fs_px') || getCookieValue('font_size_chapter');
                    if (savedFontSize) {
                        chapterContent.style.fontSize = savedFontSize + 'px';
                        const fontSizeSelect = document.querySelector('.setting-font-size');
                        if (fontSizeSelect) {
                            fontSizeSelect.value = savedFontSize;
                        }
                    }

                    // Apply saved font family
                    let savedFont = localStorage.getItem('chapterFont') || getCookieValue('font_chapter');
                    if (savedFont) {
                        const fontObj = window.objConfigFont.find(f => f.name === savedFont);
                        if (fontObj) {
                            chapterContent.style.fontFamily = fontObj.value;
                            const fontSelect = document.querySelector('.setting-font');
                            if (fontSelect) {
                                fontSelect.value = savedFont;
                            }
                        }
                    }

                    // Apply saved line height
                    let savedLineHeight = localStorage.getItem('chapter_line_height') || getCookieValue('line_height_chapter');
                    if (savedLineHeight) {
                        chapterContent.style.lineHeight = savedLineHeight + '%';
                        const lineHeightSelect = document.querySelector('.setting-line-height');
                        if (lineHeightSelect) {
                            lineHeightSelect.value = savedLineHeight;
                        }
                    }
                }

                // Apply saved settings immediately
                applySavedSettings();

                // A+ A- buttons with proper saving
                const fontInc = document.getElementById('fontInc');
                const fontDec = document.getElementById('fontDec');

                if (fontInc) {
                    fontInc.addEventListener('click', function() {
                        const chapterContent = document.querySelector('.chapter-content');
                        if (chapterContent) {
                            const currentSize = parseInt(window.getComputedStyle(chapterContent).fontSize) || 18;
                            const newSize = Math.min(currentSize + 2, 48);
                            chapterContent.style.fontSize = newSize + 'px';
                            localStorage.setItem('chapter_fs_px', newSize);
                            if (window.setCookie) {
                                window.setCookie('font_size_chapter', newSize, 1);
                            }
                            // Update select dropdown
                            const fontSizeSelect = document.querySelector('.setting-font-size');
                            if (fontSizeSelect) {
                                fontSizeSelect.value = newSize;
                            }
                        }
                    });
                }

                if (fontDec) {
                    fontDec.addEventListener('click', function() {
                        const chapterContent = document.querySelector('.chapter-content');
                        if (chapterContent) {
                            const currentSize = parseInt(window.getComputedStyle(chapterContent).fontSize) || 18;
                            const newSize = Math.max(currentSize - 2, 12);
                            chapterContent.style.fontSize = newSize + 'px';
                            localStorage.setItem('chapter_fs_px', newSize);
                            if (window.setCookie) {
                                window.setCookie('font_size_chapter', newSize, 1);
                            }
                            // Update select dropdown
                            const fontSizeSelect = document.querySelector('.setting-font-size');
                            if (fontSizeSelect) {
                                fontSizeSelect.value = newSize;
                            }
                        }
                    });
                }

                // Ensure dropdown changes are saved properly
                const fontSelect = document.querySelector('.setting-font');
                if (fontSelect) {
                    fontSelect.addEventListener('change', function() {
                        const selectedFont = this.value;
                        const fontObj = window.objConfigFont.find(f => f.name === selectedFont);
                        if (fontObj) {
                            const chapterContent = document.querySelector('.chapter-content');
                            if (chapterContent) {
                                chapterContent.style.fontFamily = fontObj.value;
                            }
                            localStorage.setItem('chapterFont', selectedFont);
                            if (window.setCookie) {
                                window.setCookie('font_chapter', selectedFont, 1);
                            }
                        }
                    });
                }

                const fontSizeSelect = document.querySelector('.setting-font-size');
                if (fontSizeSelect) {
                    fontSizeSelect.addEventListener('change', function() {
                        const size = this.value;
                        const chapterContent = document.querySelector('.chapter-content');
                        if (chapterContent) {
                            chapterContent.style.fontSize = size + 'px';
                        }
                        localStorage.setItem('chapter_fs_px', size);
                        if (window.setCookie) {
                            window.setCookie('font_size_chapter', size, 1);
                        }
                    });
                }

                const lineHeightSelect = document.querySelector('.setting-line-height');
                if (lineHeightSelect) {
                    lineHeightSelect.addEventListener('change', function() {
                        const height = this.value;
                        const chapterContent = document.querySelector('.chapter-content');
                        if (chapterContent) {
                            chapterContent.style.lineHeight = height + '%';
                        }
                        localStorage.setItem('chapter_line_height', height);
                        if (window.setCookie) {
                            window.setCookie('line_height_chapter', height, 1);
                        }
                    });
                }
            });
        </script>
    @endpush
@endonce
