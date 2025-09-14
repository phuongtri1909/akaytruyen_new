<div class="section-stories-hot mb-3">
    <div class="container">
        <div class="row">
            @include('Frontend.snippets.head_title_global', [
                'title' => 'Truyện Hot',
                'showIcon' => true,
                'showSelect' => true,
                'selectOptions' => $categories,
                'classSelect' => 'select-stories-hot',
                'categoryIdSelected' => $categoryIdSelected,
            ])
        </div>

        <div class="row">
            <div class="col-12">
                <div class="section-stories-hot__list">
                    @foreach ($storiesHot as $story)
                        @include('Frontend.snippets.story_item', ['story' => $story])
                    @endforeach
                </div>

                <div class="section-stories-hot__list wrapper-skeleton d-none">
                    @for ($i = 0; $i < $storiesHot->count(); $i++)
                        <div class="skeleton" style="max-width: 150px; width: 100%; height: 230px;"></div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Wuxia/Xianxia themed styles for Hot list */
        .section-stories-hot .head-title-global .story-name {
            font-family: 'Noto Serif', serif;
            letter-spacing: 0.5px;
            color: #1c1c1c;
            position: relative;
        }
        .section-stories-hot .head-title-global .story-name::after {
            content: "";
            display: block;
            width: 90px;
            height: 3px;
            margin-top: 6px;
            background: linear-gradient(90deg, #d4af37, rgba(212, 175, 55, 0));
            border-radius: 3px;
        }
        .section-stories-hot .head-title-global .fa-fire-flame-curved {
            color: #d4af37;
            filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.2));
        }

        /* Themed select */
        .section-stories-hot select.select-stories-hot.form-select {
            border: 1px solid rgba(212, 175, 55, 0.5);
            background: #fffdf4;
            color: #1c1c1c;
        }
        .dark-theme .section-stories-hot select.select-stories-hot.form-select {
            background: #2b2b2b;
            color: #fff;
            border-color: rgba(212, 175, 55, 0.35);
        }

        /* Grid overrides */
        .section-stories-hot__list {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        @media (min-width: 576px) {
            .section-stories-hot__list { grid-template-columns: repeat(3, 1fr); }
        }
        @media (min-width: 768px) {
            .section-stories-hot__list { grid-template-columns: repeat(4, 1fr); }
        }
        @media (min-width: 992px) {
            .section-stories-hot__list { grid-template-columns: repeat(6, 1fr); }
        }
        @media (min-width: 1200px) {
            .section-stories-hot__list { grid-template-columns: repeat(8, 1fr); }
        }

        /* Card styling */
        .section-stories-hot__list .story-item {
            background: linear-gradient(180deg, #faf6e9 0%, #f3efdf 100%);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            overflow: hidden;
            max-width: 100%;
            position: relative;
        }
        .section-stories-hot__list .story-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.15);
        }
        .section-stories-hot__list .story-item__image {
            border-bottom: 1px dashed rgba(47, 143, 131, 0.35);
            background: linear-gradient(180deg, #ffffff 0%, #f6fbfa 100%);
        }
        .section-stories-hot__list .story-item__name {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.55) 100%);
            color: #f9f5e6;
            font-family: 'Noto Serif', serif;
            letter-spacing: 0.2px;
        }
        .section-stories-hot__list .list-badge .badge {
            border-radius: 20px;
            padding: 4px 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }
        .section-stories-hot__list .story-item__badge-hot { background-color: #c94f4f !important; color: #fff !important; }
        .section-stories-hot__list .story-item__badge-new { background-color: #2f8f83 !important; color: #fff !important; }
        .section-stories-hot__list .story-item__badge-count-chapter { background-color: #d4af37 !important; color: #1c1c1c !important; }

        /* Dark theme */
        .dark-theme .section-stories-hot__list .story-item {
            background: linear-gradient(180deg, #2b2b2b 0%, #242424 100%) !important;
            border-color: rgba(212, 175, 55, 0.25);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
        }
        .dark-theme .section-stories-hot__list .story-item__image {
            border-bottom-color: rgba(212, 175, 55, 0.25);
            background: linear-gradient(180deg, #303030 0%, #2a2a2a 100%);
        }
        .dark-theme .section-stories-hot__list .story-item__name {
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.65) 100%);
            color: #fff;
        }
    </style>
@endpush
