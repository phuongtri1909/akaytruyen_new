@php
    // Get story slug from first chapter or passed parameter
    $storySlug = $storySlug ?? ($chapters->first() ? $chapters->first()->story->slug : '');
@endphp

<div class="row" id="chapters-container">
    {{-- Mobile View: Single Column --}}
    <div class="d-block d-md-none">
        <div class="chapter-grid-mobile">
            @foreach ($chapters as $chapter)
                <div class="chapter-card-mobile" data-chapter="{{ $chapter->chapter }}">
                    <a href="{{ route('chapter', ['slugStory' => $storySlug, 'slugChapter' => $chapter->slug]) }}"
                        class="chapter-link-mobile">
                        <div class="chapter-date-mobile">
                            <div class="date-day">{{ $chapter->created_at->format('d') }}</div>
                            <div class="date-month">{{ \App\Helpers\Helper::getVietnameseMonth($chapter->created_at->format('m')) }}</div>
                        </div>
                        <div class="chapter-content-mobile">
                            <div class="chapter-number">Chương {{ $chapter->chapter }}</div>
                            <div class="chapter-title">{{ $chapter->name }}</div>
                            @if ($chapter->created_at->isToday())
                                <div class="new-indicator">
                                    <span class="new-dot"></span>
                                    <span class="new-text">Mới</span>
                                </div>
                            @endif
                        </div>
                        <div class="chapter-arrow">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Desktop View: Two Columns --}}
    <div class="col-6 d-none d-md-block">
        <div class="chapter-grid-desktop">
                                            @foreach ($chapters->take(ceil($chapters->count() / 2)) as $chapter)
                                    <div class="chapter-card-desktop" data-chapter="{{ $chapter->chapter }}">
                                        <a href="{{ route('chapter', ['slugStory' => $storySlug, 'slugChapter' => $chapter->slug]) }}"
                                            class="chapter-link-desktop">
                        <div class="chapter-date-desktop">
                            <div class="date-day">{{ $chapter->created_at->format('d') }}</div>
                            <div class="date-month">{{ \App\Helpers\Helper::getVietnameseMonth($chapter->created_at->format('m')) }}</div>
                        </div>
                        <div class="chapter-content-desktop">
                            <div class="chapter-number">Chương {{ $chapter->chapter }}</div>
                            <div class="chapter-title">{{ $chapter->name }}</div>
                            @if ($chapter->created_at->isToday())
                                <div class="new-indicator">
                                    <span class="new-dot"></span>
                                    <span class="new-text">Mới</span>
                                </div>
                            @endif
                        </div>
                        <div class="chapter-arrow">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col-6 d-none d-md-block">
        <div class="chapter-grid-desktop">
                                            @foreach ($chapters->skip(ceil($chapters->count() / 2)) as $chapter)
                                    <div class="chapter-card-desktop" data-chapter="{{ $chapter->chapter }}">
                                        <a href="{{ route('chapter', ['slugStory' => $storySlug, 'slugChapter' => $chapter->slug]) }}"
                                            class="chapter-link-desktop">
                        <div class="chapter-date-desktop">
                            <div class="date-day">{{ $chapter->created_at->format('d') }}</div>
                            <div class="date-month">{{ \App\Helpers\Helper::getVietnameseMonth($chapter->created_at->format('m')) }}</div>
                        </div>
                        <div class="chapter-content-desktop">
                            <div class="chapter-number">Chương {{ $chapter->chapter }}</div>
                            <div class="chapter-title">{{ $chapter->name }}</div>
                            @if ($chapter->created_at->isToday())
                                <div class="new-indicator">
                                    <span class="new-dot"></span>
                                    <span class="new-text">Mới</span>
                                </div>
                            @endif
                        </div>
                        <div class="chapter-arrow">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    /* Chapter List Styling - Compact */
    .chapter-grid-mobile, .chapter-grid-desktop {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .chapter-card-mobile, .chapter-card-desktop {
        border: 1px solid #aeaeae;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
        animation: slideInUp 0.4s ease-out;
    }

    .chapter-card-mobile:nth-child(even), .chapter-card-desktop:nth-child(even) {
        animation-delay: 0.05s;
    }

    .chapter-card-mobile:nth-child(3n), .chapter-card-desktop:nth-child(3n) {
        animation-delay: 0.1s;
    }

    .chapter-card-mobile:hover, .chapter-card-desktop:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #8fc4e3;
    }

    .chapter-card-mobile::before, .chapter-card-desktop::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, #8fc4e3, #14425d);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .chapter-card-mobile:hover::before, .chapter-card-desktop:hover::before {
        transform: scaleX(1);
    }

    .chapter-link-mobile, .chapter-link-desktop {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        text-decoration: none;
        color: #495057;
        transition: all 0.3s ease;
    }

    .chapter-link-mobile:hover, .chapter-link-desktop:hover {
        color: #8fc4e3;
        text-decoration: none;
    }

    /* Date Styling - Compact */
    .chapter-date-mobile, .chapter-date-desktop {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #8fc4e3 0%, #14425d 100%);
        border-radius: 6px;
        color: white;
        font-weight: 600;
        margin-right: 12px;
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.2);
        transition: all 0.3s ease;
    }

    .chapter-link-mobile:hover .chapter-date-mobile,
    .chapter-link-desktop:hover .chapter-date-desktop {
        transform: scale(1.05);
        box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
    }

    .date-day {
        font-size: 14px;
        line-height: 1;
    }

    .date-month {
        font-size: 10px;
        opacity: 0.9;
        text-transform: uppercase;
    }

    /* Content Styling - Compact */
    .chapter-content-mobile, .chapter-content-desktop {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .chapter-number {
        font-weight: 600;
        font-size: 12px;
        color: #14425d;
        margin-bottom: 1px;
    }

    .chapter-title {
        font-size: 12px;
        font-weight: 500;
        color: #495057;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* New Indicator - Compact */
    .new-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    .new-dot {
        width: 6px;
        height: 6px;
        background: #dc3545;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .new-text {
        font-size: 9px;
        font-weight: 600;
        color: #dc3545;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Arrow Styling - Compact */
    .chapter-arrow {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        background: rgba(0, 123, 255, 0.1);
        border-radius: 4px;
        color: #8fc4e3;
        transition: all 0.3s ease;
        opacity: 0.6;
    }

    .chapter-link-mobile:hover .chapter-arrow,
    .chapter-link-desktop:hover .chapter-arrow {
        background: rgba(0, 123, 255, 0.2);
        transform: translateX(2px);
        opacity: 1;
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.7;
        }
    }

    /* Dark Theme */
    .dark-theme .chapter-card-mobile,
    .dark-theme .chapter-card-desktop {
        background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
        border-color: #404040;
    }

    .dark-theme .chapter-link-mobile,
    .dark-theme .chapter-link-desktop {
        color: #e9ecef;
    }

    .dark-theme .chapter-title {
        color: #e9ecef;
    }

    .dark-theme .chapter-arrow {
        background: rgba(0, 123, 255, 0.2);
        color: #8fc4e3;
    }

    /* Responsive - Compact */
    @media (max-width: 768px) {
        .chapter-grid-mobile {
            gap: 4px;
        }

        .chapter-card-mobile {
            margin-bottom: 4px;
        }

        .chapter-link-mobile {
            padding: 6px 10px;
        }

        .chapter-date-mobile {
            min-width: 36px;
            height: 36px;
            margin-right: 10px;
        }

        .date-day {
            font-size: 12px;
        }

        .date-month {
            font-size: 9px;
        }

        .chapter-number {
            font-size: 11px;
        }

        .chapter-title {
            font-size: 11px;
        }

        .chapter-arrow {
            width: 20px;
            height: 20px;
        }

        .new-dot {
            width: 5px;
            height: 5px;
        }

        .new-text {
            font-size: 8px;
        }
    }
</style>
