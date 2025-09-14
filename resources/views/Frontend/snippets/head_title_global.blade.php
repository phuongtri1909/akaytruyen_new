<div class="head-title-global d-flex justify-content-between mb-2">
    <div class="col-6 col-md-4 col-lg-4 head-title-global__left d-flex align-items-center">
        @include('Frontend.snippets.title_component', [
            'title' => $title,
        ])
    </div>

    @if ($showSelect && count($selectOptions))
        <div class="col-4 col-md-3 col-lg-2">
            <select id="categorySelect" class="form-select {{ $classSelect }}" aria-label="Truyen hot">
                <option value="" {{ $categoryIdSelected == null ? 'selected' : '' }}>Tất cả</option>
                @foreach ($selectOptions as $item)
                    <option value="{{ $item->id }}" {{ $item->id == $categoryIdSelected ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif

</div>

@push('styles')
    <style>
        .head-title-global .story-name-full::after {
            content: "";
            display: block;
            width: 90px;
            height: 3px;
            margin-top: 6px;
            background: linear-gradient(90deg, #d4af37, rgba(212, 175, 55, 0));
            border-radius: 3px;
        }
    </style>
@endpush
