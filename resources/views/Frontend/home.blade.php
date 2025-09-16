@extends('Frontend.layouts.default')


@push('custom_schema')
    {!! SEO::generate() !!}
@endpush


@section('content')
    <div class="mb-3 container py-2 d-flex flex-column justify-content-center text-start wuxia-banner"
        style="min-height: 50px;">
        <p class="mb-0 text-start text-md-center" style="font-size: 14px;">
            Akaytruyen.com là web đọc truyện chính chủ duy nhất của tác giả AkayHau. <br>
            Tham gia kênh Youtube và Facebook Page chính của truyện để ủng hộ tác giả.
        </p>
    </div>
    @include('Frontend.sections.main.stories_hot', ['categoryIdSelected' => 0])
    <hr>
    <div>
        @include('Frontend.components.info_book')
    </div>


    <div class="container">
        <div class="row align-items-start">
            <div class="col-12 col-md-8 col-lg-9">
                @include('Frontend.sections.main.stories_new')
            </div>

            <div class="col-12 col-md-4 col-lg-3 sticky-md-top">
                <div class="row">
                    <div class="col-12">
                        <br>
                        @include('Frontend.sections.main.list_category')
                    </div>
                </div>
            </div>
            <br>
            @include('Frontend.sections.main.stories_full', ['stories' => $storiesFull])
        </div>
    </div>
    <div id="id_feedback_button">
        <a href="https://m.me/596014326928548" target="_blank" rel="noreferrer" class="btn">
            <svg viewBox="0 0 512 512" data-icon="messender" width="1em" height="1em" fill="currentColor"
                aria-hidden="true">
                <path
                    d="M256,0C114.624,0,0,106.112,0,237.024c0,74.592,37.216,141.12,95.392,184.576V512l87.168-47.84c23.264,6.432,47.904,9.92,73.44,9.92c141.376,0,256-106.112,256-237.024C512,106.112,397.376,0,256,0z"
                    style="fill: rgb(30, 136, 229);"></path>
                <polygon points="281.44,319.2 216.256,249.664 89.056,319.2 228.96,170.656 295.744,240.192 421.376,170.656"
                    style="fill: rgb(250, 250, 250);"></polygon>
            </svg>
            <span class="ml-1">Liên Hệ QTV</span>
        </a>
    </div>
    <div class="container mt-4">
        <div class="section-list-category card-custom w-100" style="max-width: 1300px;">
            <div id="chat-box">
                @include('Frontend.snippets.title_component', [
                    'title' => 'Luận Thiên Hạ',
                ])
                <livewire:live-chat-section />
            </div>

        </div>
    </div>
    <hr>
@endsection

@push('styles')
    @vite(['resources/assets/frontend/css/home.css'])
@endpush
