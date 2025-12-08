<div class="section-stories-new mb-3 mt-5">
    <!-- Top Header -->
    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-4405345005005059" data-ad-slot="4536454491"
        data-ad-format="auto" data-full-width-responsive="true"></ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
    <div class="row">
        @include('Frontend.snippets.head_title_global', [
            'title' => 'Truyện Mới',
            'showIcon' => false,
            'showSelect' => false,
            'selectOptions' => [],
        ])
    </div>

    <div class="row">
        <div class="col-12">
            <div id="list-index">
                <div class="list list-truyen list-new">
                    @foreach ($storiesNew as $story)
                        @include('Frontend.snippets.story_item_no_image', ['story' => $story])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    @vite(['resources/assets/frontend/css/stories_new_styles.css'])
@endpush
