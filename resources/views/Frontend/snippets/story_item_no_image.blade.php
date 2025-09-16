<div class="row" itemscope="" itemtype="https://schema.org/Book">
    <div class="col-9 col-md-7 col-lg-5 col-title">
        <i class="fa-solid fa-chevron-right"></i>
        <h3 itemprop="name" class="title-text-story">
            <a href="{{ route('story', ['slug' => $story->slug]) }}" itemprop="url"
                title="{{ $story->name }}">{{ $story->name }}</a>
        </h3>
        <span>
            @if ($story->is_new)
                <span class="label-title label-new d-inline"></span>
            @endif
            @if ($story->is_full)
                <span class="label-title label-full d-inline"></span>
            @endif
            @if ($story->is_hot)
                <span class="label-title label-hot d-inline"></span>
            @endif
        </span>
    </div>
    <div class="hidden-xs col-sm-3 col-md-3 col-cat">
        @foreach ($story->categories as $category)
            <a href="{{ route('category', ['slug' => $category->slug]) }}" itemprop="genre"
                title="{{ $category->name }}">{{ $category->name }}</a>
            @if (!$loop->last)
                ,
            @endif
        @endforeach
    </div>
    <div class="col-3 col-md-2 col-chap text-info">
        @if ($story->chapter_last)
            <a href="{{ url($story->slug . '/' . $story->chapter_last->slug) }}"
                title="{{ $story->name }} - Chương {{ $story->chapter_last->chapter }}">
                <span class="chapter-text">
                    <span class="chapter-label">Chương </span>
                    <span class="chapter-number">{{ $story->chapter_last->chapter }}</span>
                </span>
                @if ($story->chapter_last->created_at && $story->chapter_last->created_at->diffInHours(now()) <= 24)
                    <span class="chapter-new-badge">new</span>
                @endif
            </a>
        @endif
    </div>
    <div class="hidden-xs hidden-sm col-md-2 col-time">
        @if ($story->chapter_last && $story->chapter_last->created_at)
            @if ($story->chapter_last->created_at->diffInMinutes(now()) > 60)
                {{ $story->chapter_last->created_at->diffInHours(now()) }} giờ trước
            @else
                {{ $story->chapter_last->created_at->diffInMinutes(now()) }} phút trước
            @endif
        @elseif ($story->updated_at)
            @if ($story->updated_at->diffInMinutes(now()) > 60)
                {{ $story->updated_at->diffInHours(now()) }} giờ trước
            @else
                {{ $story->updated_at->diffInMinutes(now()) }} phút trước
            @endif
        @endif
    </div>
</div>
@once
    @push('styles')
        <style>
            /* Dark theme */
            .dark-theme .title-text-story a {
                color: #ffffff !important;
            }

            .dark-theme .col-cat {
                color: #ffffff !important;
            }

            .dark-theme .col-cat a {
                color: #ffffff !important;
            }

            .dark-theme .col-chap a {
                color: #17a2b8 !important;
            }

            .dark-theme .col-time {
                color: #ffffff !important;
            }
        </style>
    @endpush
@endonce
