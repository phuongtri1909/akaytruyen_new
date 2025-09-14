<div class="row top-ratings">
    <div class="col-12 top-ratings__tab mb-2">
        <div class="list-group d-flex flex-row justify-content-evenly" id="list-tab" role="tablist">
            @if ($ratingsDay && count($ratingsDay->toArray()) > 0)
                <a class="rounded-5 w-auto bg-custom-active btn border-1 active" id="top-day-list" data-bs-toggle="list"
                    href="#top-day" role="tab" aria-controls="top-day">Ngày</a>
            @endif
            @if ($ratingsMonth && count($ratingsMonth->toArray()) > 0)
                <a class="rounded-5 w-auto bg-custom-active btn border-1" id="top-month-list" data-bs-toggle="list"
                    href="#top-month" role="tab" aria-controls="top-month">Tháng</a>
            @endif
            @if ($ratingsAllTime && count($ratingsAllTime->toArray()) > 0)
                <a class="rounded-5 w-auto bg-custom-active btn border-1" id="top-all-time-list" data-bs-toggle="list"
                    href="#top-all-time" role="tab" aria-controls="top-all-time">All time</a>
            @endif
        </div>
    </div>
    <div class="col-12 top-ratings__content">
        <div class="tab-content" id="nav-tabContent">
            @if ($ratingsDay && $storiesDay->count() > 0)
                <div class="tab-pane fade show active" id="top-day" role="tabpanel" aria-labelledby="top-day-list">
                    <ul>
                        @foreach ($storiesDay as $k => $story)
                            @php
                                $number = $k + 1;
                                $classBgNumber = 'border border-dark';
                                $classColorNumber = 'text-dark';
                                switch ($number) {
                                    case 1:
                                        $classBgNumber = 'bg-danger-custom';
                                        $classColorNumber = 'text-light';
                                        break;
                                    case 2:
                                        $classBgNumber = 'bg-success-custom';
                                        $classColorNumber = 'text-light';
                                        break;
                                    case 3:
                                        $classBgNumber = 'bg-info-custom';
                                        $classColorNumber = 'text-light';
                                        break;

                                    default:
                                        # code...
                                        break;
                                }
                            @endphp
                            <li>
                                <div class="rating-item d-flex align-items-center story-stop-rating">
                                    <div class="rating-item__number {{ $classBgNumber }} rounded-circle">
                                        <span class="{{ $classColorNumber }}">{{ $number }}</span>
                                    </div>
                                    <div class="rating-item__story">
                                        <a href="{{ route('story', ['slug' => $story->slug]) }}"
                                            class="text-decoration-none hover-title rating-item__story--name text-one-row">{{ $story->name }}</a>
                                        <div class="d-flex flex-wrap rating-item__story--categories">
                                            @foreach ($story->categories as $category)
                                                <a href="{{ route('category', ['slug' => $category->slug]) }}"
                                                    class="text-decoration-none text-dark hover-title @if (!$loop->last) me-1 @endif">{{ $category->name }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($ratingsMonth && $storiesMonth->count() > 0)
                <div class="tab-pane fade" id="top-month" role="tabpanel" aria-labelledby="top-month-list">
                    <ul>
                        @foreach ($storiesMonth as $k => $story)
                            @php
                                $number = $k + 1;
                                $classBgNumber = 'bg-light border';
                                $classColorNumber = 'text-dark';
                                switch ($number) {
                                    case 1:
                                        $classBgNumber = 'bg-danger-custom';
                                        $classColorNumber = 'text-light';
                                        break;
                                    case 2:
                                        $classBgNumber = 'bg-success-custom';
                                        $classColorNumber = 'text-light';
                                        break;
                                    case 3:
                                        $classBgNumber = 'bg-info-custom';
                                        $classColorNumber = 'text-light';
                                        break;

                                    default:
                                        # code...
                                        break;
                                }
                            @endphp
                            <li>
                                <div class="rating-item d-flex align-items-center">
                                    <div class="rating-item__number {{ $classBgNumber }} rounded-circle">
                                        <span class="{{ $classColorNumber }}">{{ $number }}</span>
                                    </div>
                                    <div class="rating-item__story">
                                        <a href="{{ route('story', ['slug' => $story->slug]) }}"
                                            class="text-decoration-none hover-title rating-item__story--name text-one-row">{{ $story->name }}</a>
                                        <div class="d-flex flex-wrap rating-item__story--categories">
                                            @foreach ($story->categories as $category)
                                                <a href="{{ route('category', ['slug' => $category->slug]) }}"
                                                    class="text-decoration-none text-dark hover-title @if (!$loop->last) me-1 @endif">{{ $category->name }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($ratingsAllTime && $storiesAllTime->count() > 0)
                <div class="tab-pane fade" id="top-all-time" role="tabpanel" aria-labelledby="top-all-time-list">
                    <ul>
                        @foreach ($storiesAllTime as $k => $story)
                            @php
                                $number = $k + 1;
                                $classBgNumber = 'bg-light border';
                                $classColorNumber = 'text-dark';
                                switch ($number) {
                                    case 1:
                                        $classBgNumber = 'bg-danger-custom';
                                        $classColorNumber = 'text-light';
                                        break;
                                    case 2:
                                        $classBgNumber = 'bg-success-custom';
                                        $classColorNumber = 'text-light';
                                        break;
                                    case 3:
                                        $classBgNumber = 'bg-info-custom';
                                        $classColorNumber = 'text-light';
                                        break;

                                    default:
                                        # code...
                                        break;
                                }
                            @endphp
                            <li>
                                <div class="rating-item d-flex align-items-center">
                                    <div class="rating-item__number {{ $classBgNumber }} rounded-circle">
                                        <span class="{{ $classColorNumber }}">{{ $number }}</span>
                                    </div>
                                    <div class="rating-item__story">
                                        <a href="{{ route('story', ['slug' => $story->slug]) }}"
                                            class="text-decoration-none hover-title rating-item__story--name text-one-row">{{ $story->name }}</a>
                                        <div class="d-flex flex-wrap rating-item__story--categories">
                                            @foreach ($story->categories as $category)
                                                <a href="{{ route('category', ['slug' => $category->slug]) }}"
                                                    class="text-decoration-none text-dark hover-title @if (!$loop->last) me-1 @endif">{{ $category->name }}
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
    <style>
        .story-stop-rating {
            border-bottom: 1px dashed #ccc !important;
        }

        .rating-item__story--name {
            color: #083767 !important;
        }

        .rating-item__story--name:hover {
            color: #083767 !important;
            text-decoration: underline !important;
        }

        .rating-item__story--categories a:hover {
            color: #083767 !important;
            text-decoration: underline !important;
        }

        .bg-danger-custom {
            background-color: #ff0000 !important;
        }

        .bg-success-custom {
            background-color: #00ff00 !important;
        }

        .bg-info-custom {
            background-color: #0000ff !important;
        }

        /* Tab buttons equal width */
        .top-ratings__tab .list-group {
            display: flex !important;
            width: 100% !important;
        }

        .top-ratings__tab .list-group a {
            flex: 1 !important;
            min-width: 0 !important;
            text-align: center !important;
            white-space: nowrap !important;
            padding: 8px 12px !important;
            margin: 0 2px !important;
        }

        .top-ratings__tab .list-group a:first-child {
            margin-left: 0 !important;
        }

        .top-ratings__tab .list-group a:last-child {
            margin-right: 0 !important;
        }

        .bg-custom-active {
            border: 1px solid #083767 !important;
            color: #083767 !important;
        }

        .bg-custom-active:hover {
            background-color: #083767 !important;
            color: #fff !important;
        }

        .bg-custom-active.active {
            background-color: #083767 !important;
            color: #fff !important;
        }
    </style>
@endpush
