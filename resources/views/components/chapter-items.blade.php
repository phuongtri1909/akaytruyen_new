@if ($chapters->count() > 0)
    @foreach ($chapters as $chapter)
        <div class="col-12 col-sm-6 col-lg-6 story-detail__list-chapter--list__item">
            <ul>
                <li>
                    <a href="{{ route('chapter', ['slugStory' => $chapter->story->slug, 'slugChapter' => $chapter->slug]) }}"
                       class="text-decoration-none text-dark hover-title">
                        {{ $chapter->name }}
                    </a>
                </li>
            </ul>
        </div>
    @endforeach
@else
    <p class="text-center text-muted">Không tìm thấy chương phù hợp.</p>
@endif
