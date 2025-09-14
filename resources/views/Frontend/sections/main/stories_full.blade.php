<div class="section-stories-full mb-3 mt-3 col-12 col-md-8 col-lg-9">
    @include('Frontend.snippets.title_component', [
        'title' => 'Truyện hoàn thành',
    ])

    <div class="row">
        @foreach ($stories as $story)
            <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                @include('Frontend.snippets.story_item_full', ['story' => $story])
            </div>
        @endforeach
    </div>
</div>

