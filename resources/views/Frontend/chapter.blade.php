@extends('Frontend.layouts.default')

@push('custom_schema')
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
            if ($chapter->content) {
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

            // Removed regex patterns that were adding unwanted spaces before quotes
            
            // Removed all regex processing to preserve original content formatting

            // Removed regex that was adding spaces after punctuation

            $chapter->content = '<p>' . $chapter->content . '</p>';

            $chapter->content = preg_replace('/<p>\s*<\/p>/', '', $chapter->content);
            }
            ?>

            <div id="chapter-content" class="chapter-content mb-4 p-3 border-0 rounded"
                style="font-size: 1.5rem; min-height: 500px; line-height: 2; position: relative; top: -54px;">
                
                    @if ($chapter->content)
                        {!! \App\Helpers\Helper::sanitizeChapterContent($chapter->content) !!}
                    @else
                        <div class="access-denied-container">
                            <div class="access-denied-card">
                                <div class="lock-icon-container">
                                    <div class="lock-icon-wrapper">
                                        <i class="fas fa-lock lock-icon"></i>
                                        <div class="lock-shine"></div>
                                    </div>
                                </div>
                                
                                <div class="access-denied-content">
                                    <h4 class="access-denied-title">
                                        <span class="title-text">Truy cập bị hạn chế</span>
                                        <div class="title-underline"></div>
                                    </h4>
                                    
                                    <p class="access-denied-message">
                                        Bạn không có quyền xem nội dung chương này. 
                                        <br>Vui lòng liên hệ Quản trị viên để được hỗ trợ.
                                    </p>
                                    
                                    <div class="contact-section">
                                        <div class="contact-icon">
                                            <i class="fab fa-facebook-messenger"></i>
                                        </div>
                                        <a href="https://m.me/596014326928548" target="_blank" rel="noreferrer" class="contact-btn">
                                            <span class="btn-text">Liên Hệ QTV</span>
                                            <span class="btn-arrow">→</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="floating-particles">
                                    <div class="particle"></div>
                                    <div class="particle"></div>
                                    <div class="particle"></div>
                                    <div class="particle"></div>
                                    <div class="particle"></div>
                                </div>
                            </div>
                        </div>
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
                    if (chapterContent && chapterContent.innerHTML.trim() !== '') {
                        const walker = document.createTreeWalker(
                            chapterContent,
                            NodeFilter.SHOW_TEXT,
                            null,
                            false
                        );
                        
                        let node;
                        while (node = walker.nextNode()) {
                            if (node.parentElement.tagName !== 'A') {
                                // Removed JavaScript regex patterns that were adding unwanted spaces before quotes
                                
                                // Removed JavaScript regex that was adding spaces after periods
                            }
                        }
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
            .alert-custom {
                background-color: #eef9ff;
            }

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

            .search-wrapper .wuxia-search__submit {
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

            /* Access Denied Styling */
            .access-denied-container {
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 400px;
                padding: 2rem;
                position: relative;
                overflow: hidden;
            }

            .access-denied-card {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 20px;
                padding: 3rem 2rem;
                text-align: center;
                position: relative;
                box-shadow: 
                    0 20px 40px rgba(0, 0, 0, 0.1),
                    0 0 0 1px rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                max-width: 500px;
                width: 100%;
                animation: cardSlideIn 0.8s ease-out;
            }

            @keyframes cardSlideIn {
                0% {
                    opacity: 0;
                    transform: translateY(30px) scale(0.95);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .lock-icon-container {
                margin-bottom: 2rem;
                position: relative;
            }

            .lock-icon-wrapper {
                position: relative;
                display: inline-block;
                animation: lockBounce 2s ease-in-out infinite;
            }

            @keyframes lockBounce {
                0%, 20%, 50%, 80%, 100% {
                    transform: translateY(0);
                }
                40% {
                    transform: translateY(-10px);
                }
                60% {
                    transform: translateY(-5px);
                }
            }

            .lock-icon {
                font-size: 4rem;
                color: #14425d;
                position: relative;
                z-index: 2;
                filter: drop-shadow(0 4px 8px rgba(20, 66, 93, 0.3));
            }

            .lock-shine {
                position: absolute;
                top: -5px;
                left: -5px;
                right: -5px;
                bottom: -5px;
                background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.4), transparent);
                border-radius: 50%;
                animation: shine 3s ease-in-out infinite;
                z-index: 1;
            }

            @keyframes shine {
                0% {
                    transform: rotate(0deg);
                    opacity: 0;
                }
                50% {
                    opacity: 1;
                }
                100% {
                    transform: rotate(360deg);
                    opacity: 0;
                }
            }

            .access-denied-content {
                position: relative;
                z-index: 3;
            }

            .access-denied-title {
                margin-bottom: 1.5rem;
                position: relative;
            }

            .title-text {
                font-size: 1.8rem;
                font-weight: 700;
                color: #14425d;
                display: inline-block;
                animation: titleGlow 2s ease-in-out infinite alternate;
            }

            @keyframes titleGlow {
                0% {
                    text-shadow: 0 0 5px rgba(20, 66, 93, 0.3);
                }
                100% {
                    text-shadow: 0 0 20px rgba(20, 66, 93, 0.6);
                }
            }

            .title-underline {
                width: 60px;
                height: 3px;
                background: linear-gradient(90deg, #14425d, #8fc4e3);
                margin: 0.5rem auto 0;
                border-radius: 2px;
                animation: underlineExpand 1.5s ease-out;
            }

            @keyframes underlineExpand {
                0% {
                    width: 0;
                }
                100% {
                    width: 60px;
                }
            }

            .access-denied-message {
                font-size: 1.1rem;
                color: #6c757d;
                line-height: 1.6;
                margin-bottom: 2rem;
                animation: messageFadeIn 1s ease-out 0.5s both;
            }

            @keyframes messageFadeIn {
                0% {
                    opacity: 0;
                    transform: translateY(10px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .contact-section {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 1rem;
                animation: contactSlideIn 1s ease-out 0.8s both;
            }

            @keyframes contactSlideIn {
                0% {
                    opacity: 0;
                    transform: translateX(-20px);
                }
                100% {
                    opacity: 1;
                    transform: translateX(0);
                }
            }

            .contact-icon {
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #0084ff, #0066cc);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                animation: iconPulse 2s ease-in-out infinite;
            }

            @keyframes iconPulse {
                0%, 100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(0, 132, 255, 0.4);
                }
                50% {
                    transform: scale(1.05);
                    box-shadow: 0 0 0 10px rgba(0, 132, 255, 0);
                }
            }

            .contact-icon i {
                font-size: 1.5rem;
                color: white;
            }

            .contact-btn {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0rem 1.5rem;
                background: linear-gradient(135deg, #14425d, #8fc4e3);
                color: white;
                text-decoration: none;
                border-radius: 25px;
                font-weight: 600;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .contact-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(20, 66, 93, 0.3);
                color: #fff;
            }

            .contact-btn::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: left 0.5s ease;
            }

            .contact-btn:hover::before {
                left: 100%;
            }

            .contact-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(20, 66, 93, 0.3);
            }

            .btn-text {
                position: relative;
                z-index: 2;
            }

            .btn-arrow {
                position: relative;
                z-index: 2;
                transition: transform 0.3s ease;
            }

            .contact-btn:hover .btn-arrow {
                transform: translateX(3px);
            }

            .floating-particles {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                pointer-events: none;
                overflow: hidden;
            }

            .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(20, 66, 93, 0.3);
                border-radius: 50%;
                animation: float 6s ease-in-out infinite;
            }

            .particle:nth-child(1) {
                top: 20%;
                left: 10%;
                animation-delay: 0s;
                animation-duration: 4s;
            }

            .particle:nth-child(2) {
                top: 60%;
                left: 80%;
                animation-delay: 1s;
                animation-duration: 5s;
            }

            .particle:nth-child(3) {
                top: 80%;
                left: 20%;
                animation-delay: 2s;
                animation-duration: 6s;
            }

            .particle:nth-child(4) {
                top: 30%;
                left: 70%;
                animation-delay: 3s;
                animation-duration: 4.5s;
            }

            .particle:nth-child(5) {
                top: 70%;
                left: 50%;
                animation-delay: 4s;
                animation-duration: 5.5s;
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px) rotate(0deg);
                    opacity: 0.3;
                }
                50% {
                    transform: translateY(-20px) rotate(180deg);
                    opacity: 0.8;
                }
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .access-denied-card {
                    padding: 2rem 1.5rem;
                    margin: 1rem;
                }

                .lock-icon {
                    font-size: 3rem;
                }

                .title-text {
                    font-size: 1.5rem;
                }

                .access-denied-message {
                    font-size: 1rem;
                }

                .contact-section {
                    flex-direction: column;
                    gap: 1rem;
                }

                .contact-icon {
                    width: 45px;
                    height: 45px;
                }

                .contact-icon i {
                    font-size: 1.3rem;
                }
            }

            @media (max-width: 480px) {
                .access-denied-container {
                    padding: 1rem;
                }

                .access-denied-card {
                    padding: 1.5rem 1rem;
                }

                .lock-icon {
                    font-size: 2.5rem;
                }

                .title-text {
                    font-size: 1.3rem;
                }

                .access-denied-message {
                    font-size: 0.95rem;
                }
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
                    
                    if (html && !html.includes('Bạn không có quyền xem nội dung chương này')) {
                        const newHtml = html.replace(regex, function(matched) {
                            matches.push(matched);
                            return `<span class="highlight">${matched}</span>`;
                        });
                        $this.html(newHtml);
                    }
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
                            // Tạm thời comment để test
                            // let content = chapterContent.innerHTML;
                            // content = content.replace(/\s*([.,!?])\s*(["'”’])/g, '$1$2');
                            // content = content.replace(/([.,!?])([^\s"”’])/g, '$1 $2');
                            // content = content.replace(/(["'”’])([^\s.,!?])/g, '$1 $2');
                            // chapterContent.innerHTML = content;
                        }
                    });
        </script>

       
        @vite(['resources/assets/frontend/js/chapter.js'])
        <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/jquery.mark.min.js"></script>

        <!-- Reading Progress Tracking -->
        <script>
            let scrollTimeout;
            let isSaving = false;

            // Khôi phục vị trí cuộn khi load trang
            document.addEventListener('DOMContentLoaded', function() {
                const scrollPosition = {{ $scrollPosition ?? 0 }};
                if (scrollPosition > 0) {
                    setTimeout(() => {
                        window.scrollTo(0, scrollPosition);
                    }, 500);
                }
            });

            // Lưu vị trí cuộn khi user cuộn
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    saveReadingProgress();
                }, 1000); // Lưu sau 1 giây không cuộn
            });

            // Lưu khi user rời khỏi trang
            window.addEventListener('beforeunload', function() {
                saveReadingProgress();
            });

            function saveReadingProgress() {
                if (isSaving || !{{ auth()->check() ? 'true' : 'false' }}) return;
                
                isSaving = true;
                const scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
                const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
                const readProgress = documentHeight > 0 ? Math.round((scrollPosition / documentHeight) * 100) : 0;

                fetch('{{ route('save.reading.progress') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        story_id: {{ $story->id }},
                        chapter_id: {{ $chapter->id }},
                        scroll_position: scrollPosition,
                        read_progress: readProgress
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Đã lưu tiến độ đọc:', readProgress + '%');
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi lưu tiến độ đọc:', error);
                })
                .finally(() => {
                    isSaving = false;
                });
            }
        </script>

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
