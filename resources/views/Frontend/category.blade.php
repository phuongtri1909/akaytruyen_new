@extends('Frontend.layouts.default')

@push('custom_schema')
{{-- {!! SEOMeta::generate() !!} --}}
{{-- {!! JsonLd::generate() !!} --}}
{!! SEO::generate() !!}
@endpush

@section('content')
    <div class="container">
        <div class="row align-items-start">
            <div class="col-12 col-md-8 col-lg-9 mb-3">
                <div class="head-title-global d-flex justify-content-between mb-2">
                    <div class="col-12 col-md-12 col-lg-12 head-title-global__left d-flex">
                        <h2 class="me-2 mb-0 border-bottom border-secondary pb-1">
                            <span href="#" class="d-block text-decoration-none text-dark fs-4 category-name" title="{{ $category->name }}">{{ $category->name }}</span>
                        </h2>
                    </div>
                </div>

                <div class="list-story-in-category section-stories-hot__list">
                    @foreach ($stories as $story)
                        @include('Frontend.snippets.story_item', ['story' => $story])
                    @endforeach
                    {{-- @foreach ($stories as $story)
                        @include('Frontend.snippets.story_item_list', ['story' => $story])
                    @endforeach --}}
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 sticky-md-top">
                <div class="category-description bg-light p-2 rounded mb-3 card-custom">
                    <p class="mb-0 text-secondary">{!! \App\Helpers\Helper::sanitizeCKEditorContent($category->desc) !!}</p>
                </div>
                @include('Frontend.sections.main.list_category')
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Wuxia/Xianxia themed list (scoped to category list) */
        .head-title-global .category-name,
        .head-title-global .title-head-name {
            font-family: 'Noto Serif', serif;
            letter-spacing: 0.5px;
            color: #1c1c1c;
            position: relative;
        }

        .head-title-global .category-name::after,
        .head-title-global .title-head-name::after {
            content: "";
            display: block;
            width: 90px;
            height: 3px;
            margin-top: 6px;
            background: linear-gradient(90deg, #d4af37, rgba(212, 175, 55, 0));
            border-radius: 3px;
        }

        .head-title-global .fa-fire-flame-curved {
            color: #d4af37;
            filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.2));
        }

        /* Grid layout overrides for the category list */
        .list-story-in-category.section-stories-hot__list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        @media (min-width: 576px) {
            .list-story-in-category.section-stories-hot__list {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (min-width: 768px) {
            .list-story-in-category.section-stories-hot__list {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        @media (min-width: 992px) {
            .list-story-in-category.section-stories-hot__list {
                grid-template-columns: repeat(5, 1fr);
            }
        }
        @media (min-width: 1200px) {
            .list-story-in-category.section-stories-hot__list {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        /* Card styling with jade & gold accents */
        .list-story-in-category .story-item {
            background: linear-gradient(180deg, #faf6e9 0%, #f3efdf 100%);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            position: relative;
            overflow: hidden;
            max-width: 100%;
        }

        .list-story-in-category .story-item::before,
        .list-story-in-category .story-item::after {
            content: "";
            position: absolute;
            width: 12px;
            height: 12px;
            border: 2px solid rgba(47, 143, 131, 0.6); /* jade corners */
            border-radius: 2px;
        }
        .list-story-in-category .story-item::before {
            top: 6px;
            left: 6px;
            border-right: 0;
            border-bottom: 0;
        }
        .list-story-in-category .story-item::after {
            bottom: 6px;
            right: 6px;
            border-left: 0;
            border-top: 0;
        }

        .list-story-in-category .story-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }

        .list-story-in-category .story-item__image {
            border-bottom: 1px dashed rgba(47, 143, 131, 0.35);
            background: linear-gradient(180deg, #ffffff 0%, #f6fbfa 100%);
        }
        .list-story-in-category .story-item__image img {
            filter: saturate(1.05) contrast(1.02);
        }

        .list-story-in-category .story-item__name {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.55) 100%);
            color: #f9f5e6;
            font-family: 'Noto Serif', serif;
            letter-spacing: 0.2px;
        }

        .list-story-in-category .list-badge .badge {
            border-radius: 20px;
            padding: 4px 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }
        .list-story-in-category .story-item__badge-hot {
            background-color: #c94f4f !important; /* cinnabar */
            color: #fff !important;
        }
        .list-story-in-category .story-item__badge-new {
            background-color: #2f8f83 !important; /* jade */
            color: #fff !important;
        }
        .list-story-in-category .story-item__badge-count-chapter {
            background-color: #d4af37 !important; /* gold */
            color: #1c1c1c !important;
        }

        /* Sidebar description as parchment card */
        .category-description.card-custom {
            background: linear-gradient(180deg, #faf6e9 0%, #f3efdf 100%) !important;
            border: 1px solid rgba(212, 175, 55, 0.35);
        }

        /* Dark theme adjustments */
        .dark-theme .list-story-in-category .story-item {
            background: linear-gradient(180deg, #2b2b2b 0%, #242424 100%) !important;
            border-color: rgba(212, 175, 55, 0.25);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
        }
        .dark-theme .list-story-in-category .story-item__image {
            border-bottom-color: rgba(212, 175, 55, 0.25);
            background: linear-gradient(180deg, #303030 0%, #2a2a2a 100%);
        }
        .dark-theme .list-story-in-category .story-item__name {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.65) 100%);
            color: #fff;
        }
        .dark-theme .category-description.card-custom {
            background: #393333 !important;
            border-color: rgba(212, 175, 55, 0.25);
        }
    </style>
@endpush
