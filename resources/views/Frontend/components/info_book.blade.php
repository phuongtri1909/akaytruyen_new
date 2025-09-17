<section id="info-book ">
    <div class="container">
        @php
            $rating = auth()->check() ? auth()->user()->rating ?? 0 : 0;
            $fullStars = floor($rating);
            $hasHalfStar = $rating - $fullStars >= 0.5;
        @endphp
        <div class="row g-2 mt-2">
            <!-- Cột chứa THỐNG KÊ và ĐÁNH GIÁ -->
            <div class="col-12 col-lg-6 d-flex gap-3 flex-column" style="margin-top: 4.5rem !important;">
                <div class="compact-stats">
                    <div class="stats-title">Thống Kê</div>
                    <div class="stats-row">
                        <div class="stat-item">
                            <i class="fas fa-book-open"></i>
                            <span class="stat-number counter" data-target="{{ $totalStory }}">0</span>
                            <span class="stat-label">Truyện</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-list-ol"></i>
                            <span class="stat-number counter" data-target="{{ $totalChapter }}">0</span>
                            <span class="stat-label">Chương</span>
                        </div>
                    </div>
                    <div class="stats-row">
                        @if (auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Mod') || auth()->user()->hasRole('Content')))
                            <div class="stat-item">
                                <i class="fas fa-eye"></i>
                                <span class="stat-number counter" data-target="{{ $totalViews }}">0</span>
                                <span class="stat-label">Lượt Xem</span>
                            </div>
                        @endif
                        <div class="stat-item">
                            <i class="fas fa-star"></i>
                            <span class="stat-number counter" data-target="{{ $totalRating }}">0</span>
                            <span class="stat-label">Đánh Giá</span>
                        </div>
                    </div>
                </div>

                <div class="compact-rating">
                    <div class="rating-title">Đánh Giá</div>
                    <div class="rating-content">
                        <div class="stars-line">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star star {{ $i <= $fullStars ? 'filled' : 'empty' }}"
                                    data-rating="{{ $i }}"></i>
                            @endfor
                            <span class="rating-score">{{ number_format($rating, 1) }}/5</span>
                        </div>
                        <div class="rating-bar">
                            <div class="bar-fill" style="width: {{ ($rating / 5) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột chứa TOP Donate (Chiều cao bằng với THỐNG KÊ + ĐÁNH GIÁ) -->
            <div class="col-lg-6">
                <div class="donate-container h-100 d-flex flex-column p-3 position-relative">
                    <h2 class="text-black text-center border-bottom pb-2"><b>Minh Chủ Bảng</b></h2>
                    <!-- Viền -->
                    <img src="{{ asset('images/d/nenlogo.png') }}" class="border-img" alt="">

                    <div id="top-donate-list" class="position-relative">
                        @if ($topDonors->isEmpty())
                            <p class="donate-message">Chưa có ai donate tháng này.</p>
                        @else
                            <div class="table-responsive scrollable-table">
                                <table class="table table-bordered text-center align-middle donate-table">
                                    <thead>
                                        <tr>
                                            <th>Hạng</th>
                                            <th>Danh Tính</th>
                                            <th>Ủng Hộ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topDonors as $index => $donor)
                                            <tr>
                                                <td>
                                                    @if ($index == 0)
                                                        🏆
                                                    @elseif($index == 1)
                                                        🥈
                                                    @elseif($index == 2)
                                                        🥉
                                                    @else
                                                        <strong>{{ $index + 1 }}</strong>
                                                    @endif
                                                </td>
                                                <td>{{ $donor->name }}</td>
                                                <td class="text-success fw-bold">
                                                    {{ number_format($donor->donate_amount, 0) }} Linh Thạch</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="text-center mb-3">
                        <div class="position-relative d-inline-block">
                            <!-- Nút mũi tên trái -->
                            <button id="btnPrevMonth"
                                class="btn btn-sm btn-light position-absolute start-0 top-50 translate-middle-y d-none">
                                ◀
                            </button>

                            <!-- Danh sách tháng có thể cuộn -->
                            <div id="monthScrollContainer" class="btn-group overflow-auto d-flex"
                                style="max-width: 334px; white-space: nowrap; scroll-behavior: smooth;">
                                @foreach ($months as $month)
                                    <a href="{{ route('home', ['month' => $month->month, 'year' => $month->year]) }}"
                                        class="btn btn-outline-greeen month-item {{ $month->month == $selectedMonth && $month->year == $selectedYear ? 'active' : '' }}">
                                        {{ $month->month }}/{{ substr($month->year, 2, 2) }}
                                    </a>
                                @endforeach
                            </div>

                            <!-- Nút mũi tên phải -->
                            <button id="btnNextMonth"
                                class="btn btn-sm btn-light position-absolute end-0 top-50 translate-middle-y d-none">
                                ▶
                            </button>
                        </div>
                    </div>



                </div>

            </div>

</section>



@push('styles')
    <style>
        .donate-table thead {
            position: sticky;
            top: 0;
            background: rgb(1, 112, 57);
            /* Màu nền để nổi bật */
            color: white;
            z-index: 2;
        }

        @font-face {
            font-family: 'FzSVGame';
            src: url('/fonts/FzSVGame.ttf') format('truetype'),
                url('/fonts/FzSVGame.ttf') format('woff'),
                url('/fonts/FzSVGame.woff2') format('woff2');
            font-weight: normal;
            font-style: normal;
        }

        .donate-container h2,
        .donate-table {
            font-smooth: always;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        .donate-container h2 {
            font-family: 'FzSVGame', sans-serif;
            font-size: 26px;
            font-weight: bold;
            color: #004085;
            text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);
        }

        .donate-table {
            font-family: 'FzSVGame';
            font-size: 20px;
            color: #333;
        }

        .donate-table tbody tr:hover {
            background-color: #f0f8ff;
        }

        .scrollable-table {
            max-height: 263px;
            overflow-y: auto;
        }

        .scrollable-table::-webkit-scrollbar {
            width: 8px;
        }

        .scrollable-table::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 4px;
        }

        .scrollable-table::-webkit-scrollbar-track {
            background-color: #f8f9fa;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            max-height: 400px;
        }

        .donate-table tbody tr:nth-child(odd) {
            background-color: #fffbe6;
        }

        .donate-table tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        .border-img {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            max-width: 300px;
            height: auto;
            z-index: 1;
            pointer-events: none;
            image-rendering: crisp-edges;
            image-rendering: -webkit-optimize-contrast;
        }

        #top-donate-list {
            position: relative;
            z-index: 2;
            padding-top: 50px;
            top: -9%;
        }

        .text-center .btn-group {
            background-color: white;
            color: #333;
            top: -129%;
        }

        @media (max-width: 768px) {
            .donate-container h2 {
                font-size: 20px;
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
            }

            .donate-table {
                font-size: 16px;
            }

            .scrollable-table {
                max-height: 200px;
            }

            .border-img {
                width: 90%;
                max-width: 250px;
            }

            #top-donate-list {
                padding-top: 30px;
                top: 0;
            }

            .text-center .btn-group {
                align-items: center;
                top: 50%;
                margin-top: 4%;
            }
        }

        .text-center .btn-group {
            top: 50%;
            border-radius: 0 !important;
        }


        #monthScrollContainer .month-item.active {
            background-color: green;
            color: black;
            border: 0px solid green;
        }




        @media (max-width: 480px) {
            .donate-container h2 {
                font-size: 18px;
            }

            .donate-table {
                font-size: 14px;
            }

            .border-img {
                max-width: 200px;
                top: 2%;
            }

            .scrollable-table {
                max-height: 316px;
            }
        }

        .donate-message {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            border: 2px dashed #fff;
            padding: 15px;
            border-radius: 10px;
            color: white;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Compact Stats Design */
        .compact-stats {
            background: #ffffff;
            border: 2px dashed #14425d;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px #8fc4e3;
        }

        .compact-stats .stats-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #14425d;
        }

        .stats-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .stats-row:last-child {
            margin-bottom: 0;
        }

        .stat-item {
            flex: 1;
            background: radial-gradient(circle at 30% 30%, #8fc4e3, #14425d 70%);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .stat-item:hover::before {
            left: 100%;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px #8fc4e3;
        }

        .stat-item i {
            font-size: 20px;
            color: white;
            margin-bottom: 8px;
            display: block;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 900;
            color: white;
            display: block;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            color: #d4af37;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Compact Rating Design */
        .compact-rating {
            background: #ffffff;
            border: 2px dashed #14425d;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px #8fc4e3;
        }

        .compact-rating .rating-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #14425d;
        }

        .rating-content {
            text-align: center;
        }

        .stars-line {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
        }

        .star {
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .star.filled {
            color: #d4af37;
        }

        .star.empty {
            color: #ddd;
        }

        .star:hover {
            transform: scale(1.1);
            color: #d4af37;
        }

        .rating-score {
            margin-left: 15px;
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }

        .rating-bar {
            width: 100%;
            height: 6px;
            background: #f0f0f0;
            border-radius: 3px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: radial-gradient(circle at 30% 30%, #8fc4e3, #14425d 70%);
            border-radius: 3px;
            transition: width 1.5s ease;
        }

        /* Counter Animation */
        .counter {
            display: inline-block;
            animation: countUp 2s ease-out forwards;
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .stats-row {
                flex-direction: column;
                gap: 15px;
            }

            .stat-item {
                padding: 12px;
            }

            .stat-number {
                font-size: 20px;
            }

            .rating-score {
                margin-left: 0;
            }

            .star {
                font-size: 18px;
            }
        }

        /* AOS Animation Overrides */
        [data-aos="fade-up"] {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        [data-aos="fade-up"].aos-animate {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.star').hover(
                function() {
                    let rating = $(this).data('rating');
                    highlightStars(rating);
                },
                function() {
                    let currentRating = {{ $rating }};
                    highlightStars(currentRating);
                }
            );

            // Click handler
            $('.star').click(function() {
                @if (!auth()->check())
                    window.location.href = '{{ route('login') }}';
                    return;
                @endif

                let rating = $(this).data('rating');

                $.ajax({
                    url: '{{ route('ratings.store') }}',
                    type: 'POST',
                    data: {
                        rating: rating,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            highlightStars(rating);
                            $('.rating-score').text(rating + '/5');
                            showToast('Đánh giá thành công!', 'success');
                        } else {
                            showToast(res.message || 'Có lỗi xảy ra', 'error');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        if (response && response.message) {
                            showToast(response.message, 'error');
                        } else {
                            showToast('Có lỗi xảy ra, vui lòng thử lại', 'error');
                        }
                    }
                });
            });

            function highlightStars(rating) {
                $('.star').each(function(index) {
                    if (index < rating) {
                        $(this).removeClass('empty').addClass('filled');
                    } else {
                        $(this).removeClass('filled').addClass('empty');
                    }
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const container = document.getElementById("monthScrollContainer");
            const btnPrev = document.getElementById("btnPrevMonth");
            const btnNext = document.getElementById("btnNextMonth");
            const items = document.querySelectorAll(".month-item");
            const itemWidth = items.length > 0 ? items[0].offsetWidth : 0;
            const visibleCount = 12;
            let scrollPosition = 0;

            function updateButtons() {
                btnPrev.style.display = scrollPosition > 0 ? "inline-block" : "none";
                btnNext.style.display = scrollPosition < (items.length - visibleCount) * itemWidth ?
                    "inline-block" : "none";
            }

            btnPrev.addEventListener("click", function() {
                scrollPosition = Math.max(scrollPosition - itemWidth * visibleCount, 0);
                container.scrollTo({
                    left: scrollPosition,
                    behavior: "smooth"
                });
                updateButtons();
            });

            btnNext.addEventListener("click", function() {
                scrollPosition = Math.min(scrollPosition + itemWidth * visibleCount, (items.length -
                    visibleCount) * itemWidth);
                container.scrollTo({
                    left: scrollPosition,
                    behavior: "smooth"
                });
                updateButtons();
            });

            updateButtons();
        });


        document.addEventListener('DOMContentLoaded', function() {
            const counters = document.querySelectorAll('.counter');

            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                const duration = 2000; // 2 seconds
                const increment = target / (duration / 16); // 60fps
                let current = 0;

                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.textContent = Math.floor(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };

                // Start animation when element is in view
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCounter();
                            observer.unobserve(entry.target);
                        }
                    });
                });

                observer.observe(counter);
            });
        });

        // Star Rating Interaction
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');

            stars.forEach(star => {
                star.addEventListener('mouseenter', function() {
                    const rating = this.getAttribute('data-rating');
                    highlightStars(rating);
                });

                star.addEventListener('mouseleave', function() {
                    resetStars();
                });

                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-rating');
                    setRating(rating);
                });
            });

            function highlightStars(rating) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('filled');
                        star.classList.remove('empty');
                    } else {
                        star.classList.remove('filled');
                        star.classList.add('empty');
                    }
                });
            }

            function resetStars() {
                // Reset to original state based on current rating
                const currentRating = {{ $fullStars }};
                stars.forEach((star, index) => {
                    if (index < currentRating) {
                        star.classList.add('filled');
                        star.classList.remove('empty');
                    } else {
                        star.classList.remove('filled');
                        star.classList.add('empty');
                    }
                });
            }

            function setRating(rating) {

                const ratingScore = document.querySelector('.rating-score');
                if (ratingScore) {
                    ratingScore.textContent = rating + '/5';
                }

                // Update the progress bar
                const progressBar = document.querySelector('.bar-fill');
                if (progressBar) {
                    const percentage = (rating / 5) * 100;
                    progressBar.style.width = percentage + '%';
                }

                // Visual feedback for stars
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('filled');
                        star.classList.remove('empty');
                    } else {
                        star.classList.remove('filled');
                        star.classList.add('empty');
                    }
                });
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toast = $(`
                <div class="toast-notification ${type}" style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? '#48bb78' : '#e53e3e'};
                    color: white;
                    padding: 16px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    z-index: 9999;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                    max-width: 300px;
                    font-weight: 500;
                ">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `);

            $('body').append(toast);

            // Animate in
            setTimeout(() => {
                toast.css('transform', 'translateX(0)');
            }, 100);

            // Remove after 4 seconds
            setTimeout(() => {
                toast.css('transform', 'translateX(100%)');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }
    </script>
@endpush
