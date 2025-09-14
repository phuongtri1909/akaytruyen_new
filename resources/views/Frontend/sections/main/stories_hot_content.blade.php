@foreach ($stories as $story)
    @include('Frontend.snippets.story_item', ['story' => $story])
@endforeach 