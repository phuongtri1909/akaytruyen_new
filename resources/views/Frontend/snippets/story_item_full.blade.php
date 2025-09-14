<div class="story-item-full text-center">
    <a href="{{ route('story', ['slug' => $story->slug]) }}" class="d-block story-item-full__image rounded-3">
        <img src="{{ \App\Helpers\Helper::getStoryImageUrl($story->image) }}" alt="{{ $story->name }}" class="img-fluid w-100" width="150" height="230"
            loading='lazy'>
    </a>
    <h3 class="fs-6 story-item-full-name-custom fw-bold text-center mt-2">
        <a href="{{ route('story', ['slug' => $story->slug]) }}" class="text-decoration-none text-one-row story-name">
            {{ $story->name }}
        </a>
    </h3>
    <span class="story-item-full-badge-custom badge mt-2 text-white">Full -
        {{ $story->chapter_last ? $story->chapter_last->chapter : 0 }} chương</span>
</div>


@once
    @push('styles')
        <style>
            .story-item-full {
                max-width: 100%;
            }

            .story-item-full__image {
                min-height: 250px;
            }

            .story-item-full__image img {
                min-height: 250px;
            }

            .story-item-full-name-custom a {
                color: #2c1810 !important;
                text-shadow: 0px 0px 2px #000 !important;
            }

            .story-item-full-badge-custom {
                background: linear-gradient(135deg, #8fc4e3 0%, #14425d 100%) !important;
                color: #2c1810 !important;
                font-weight: 600;
                font-size: 0.75rem;
                padding: 6px 12px;
                border-radius: 15px;
                border: 1px solid rgba(139, 69, 19, 0.3);
                box-shadow: 0 2px 4px rgba(139, 69, 19, 0.2);
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
        </style>
    @endpush
@endonce
