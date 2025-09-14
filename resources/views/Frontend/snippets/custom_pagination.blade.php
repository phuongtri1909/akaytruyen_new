{{-- @dd($has_ajax) --}}
@php
    $class = $has_ajax ? 'story-ajax-paginate' : '';
@endphp
@if ($paginator->hasPages())
    <div class="modern-pagination">
        <div class="pagination-container">
            {{-- Previous Button --}}
            @if ($paginator->currentPage() > 1)
                <a @if (!$has_ajax) href="{{ $paginator->url($paginator->currentPage() - 1) }}"
                @else
                data-url="{{ $paginator->url($paginator->currentPage() - 1) }}"
                @endif
                    class="pagination-btn pagination-arrow {{ $class }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif

            <?php
            $start = $paginator->currentPage() - 1;
            $end = $paginator->currentPage() + 1;
            if ($start < 1) {
                $start = 1;
                $end += 1;
            }
            if ($end >= $paginator->lastPage()) {
                $end = $paginator->lastPage();
            }
            ?>

            {{-- First Page --}}
            @if ($start > 1)
                <a class="pagination-btn {{ $class }}"
                    @if (!$has_ajax) href="{{ $paginator->url(1) }}"
                    @else
                    data-url="{{ $paginator->url(1) }}"
                    @endif>1</a>

                @if ($paginator->currentPage() != 4)
                    <span class="pagination-dots">...</span>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($i = $start; $i <= $end; $i++)
                <a class="pagination-btn {{ $paginator->currentPage() == $i ? 'pagination-active' : '' }} {{ $class }}"
                    @if (!$has_ajax) href="{{ $paginator->url($i) }}"
                    @else
                    data-url="{{ $paginator->url($i) }}"
                    @endif>{{ $i }}</a>
            @endfor

            {{-- Last Page --}}
            @if ($end < $paginator->lastPage())
                @if ($paginator->currentPage() + 3 != $paginator->lastPage())
                    <span class="pagination-dots">...</span>
                @endif
                <a class="pagination-btn {{ $class }}"
                    @if (!$has_ajax) href="{{ $paginator->url($paginator->lastPage()) }}"
                    @else
                    data-url="{{ $paginator->url($paginator->lastPage()) }}"
                    @endif>{{ $paginator->lastPage() }}</a>
            @endif

            {{-- Next Button --}}
            @if ($paginator->currentPage() != $paginator->lastPage())
                <a @if (!$has_ajax)
                href="{{ $paginator->url($paginator->currentPage() + 1) }}"
                @else
                data-url="{{ $paginator->url($paginator->currentPage() + 1) }}"
                @endif
                class="pagination-btn pagination-arrow {{ $class }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif
        </div>

        {{-- Page Jump --}}
        <div class="page-jump">
            <div class="jump-container">
                <input type="number" class="jump-input input-paginate" placeholder="Trang" min="1" max="{{ $paginator->lastPage() }}">
                <button class="jump-btn btn-go-paginate">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12H19M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <style>
        .modern-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .pagination-container {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #495057;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .pagination-btn svg {
            pointer-events: none;
        }

        .pagination-btn:hover {
            background: #f8f9fa;
            color: #007bff;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        .pagination-active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }

        .pagination-active:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
        }

        .pagination-arrow {
            color: #6c757d;
        }

        .pagination-arrow:hover {
            background: #f8f9fa;
            color: #007bff;
        }

        .pagination-dots {
            color: #6c757d;
            font-weight: 600;
            padding: 0 8px;
        }

        .page-jump {
            display: flex;
            align-items: center;
        }

        .jump-container {
            display: flex;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .jump-input {
            width: 80px;
            height: 40px;
            border: none;
            padding: 0 12px;
            font-size: 14px;
            color: #495057;
            background: transparent;
            outline: none;
        }

        .jump-input::placeholder {
            color: #6c757d;
        }

        .jump-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border: none;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .jump-btn:hover {
            background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);
            transform: scale(1.05);
        }

        .jump-btn svg {
            pointer-events: none; /* Prevent SVG from intercepting click events */
        }

        /* Dark theme support */
        .dark-theme .pagination-container {
            background: #2c2c2c;
            border-color: #404040;
        }

        .dark-theme .pagination-btn {
            color: #e9ecef;
        }

        .dark-theme .pagination-btn:hover {
            background: #404040;
            color: #007bff;
        }

        .dark-theme .jump-container {
            background: #2c2c2c;
            border-color: #404040;
        }

        .dark-theme .jump-input {
            color: #e9ecef;
            background: transparent;
        }

        .dark-theme .jump-input::placeholder {
            color: #6c757d;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .modern-pagination {
                flex-direction: column;
                gap: 15px;
            }

            .pagination-container {
                gap: 4px;
                padding: 6px;
            }

            .pagination-btn {
                min-width: 36px;
                height: 36px;
                font-size: 13px;
            }

            .jump-input {
                width: 60px;
                height: 36px;
            }

            .jump-btn {
                width: 36px;
                height: 36px;
            }
                 }
     </style>


@endif
