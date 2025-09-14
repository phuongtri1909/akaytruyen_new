<div class="story-item">
    <a href="{{ route('story', ['slug' => $story->slug]) }}" class="d-block text-decoration-none">
        <div class="story-item__image story-item__image--framed">
            <img src="{{ \App\Helpers\Helper::getStoryImageUrl($story->image) }}" alt="{{ $story->name }}" class="img-fluid story-cover" width="150" height="230"
                loading='lazy'>
        </div>
        <h3 class="story-item__name text-one-row story-name">{{ $story->name }}</h3>

        <div class="list-badge">
            @if ($story->is_full)
                <span class="story-item__badge badge text-bg-success">Full</span>
            @else
                <span class="story-item__badge badge text-bg-secondary">Đang viết</span>
            @endif

            @if ($story->is_hot)
                <span class="story-item__badge story-item__badge-hot badge text-bg-danger">Hot</span>
            @endif

            @if ($story->is_new)
                <span class="story-item__badge story-item__badge-new badge text-bg-info text-light">New</span>
            @endif

            @if (isset($showChaptersCount) && $showChaptersCount)
                <span
                    class="story-item__badge story-item__badge-count-chapter  badge text-bg-warning">{{ $story->chapters_count }}
                    chương</span>
            @endif
        </div>
    </a>
</div>

@once
    @push('styles')
        <style>
            .story-item__image--framed {
                position: relative;
                width: 150px;
                height: 300px;
                margin: 0 auto;
            }

            .story-item__image--framed .story-cover {

                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 8px;
                display: block;
            }

            .story-item__image--framed .story-frame {
                position: absolute;
                top: -8px;
                left: -8px;
                width: calc(100% + 16px);
                height: calc(100% + 16px);
                object-fit: contain;
                pointer-events: none;
                user-select: none;
                filter: drop-shadow(0 2px 6px rgba(0,0,0,0.2));
            }

            @media (min-width: 576px) {
                .story-item__image--framed {
                    width: 160px;
                    height: 350px;
                }
            }
        </style>
    @endpush
@endonce
