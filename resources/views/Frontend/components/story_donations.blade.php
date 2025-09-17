<div class="donate-container h-100 d-flex flex-column p-3 position-relative">
    <h2 class="text-black text-center border-bottom pb-2"><b>Minh Chủ Bảng</b></h2>
    <!-- Viền -->
    <img src="{{ asset('images/d/nenlogo.png') }}" class="border-img" alt="">

    <div id="story-donate-list" class="position-relative">
        <p class="donate-message">Đang tải dữ liệu...</p>
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
                <!-- Sẽ được load bằng JavaScript -->
            </div>

            <!-- Nút mũi tên phải -->
            <button id="btnNextMonth"
                class="btn btn-sm btn-light position-absolute end-0 top-50 translate-middle-y d-none">
                ▶
            </button>
        </div>
    </div>
</div>

<style>
    .donate-table thead {
        position: sticky;
        top: 0;
        background: rgb(1, 112, 57);
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

    #story-donate-list {
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

        #story-donate-list {
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const storySlug = '{{ $story->slug ?? "" }}';
    if (!storySlug) return;

    let currentMonth = new Date().getMonth() + 1;
    let currentYear = new Date().getFullYear();

    function loadDonations(month = currentMonth, year = currentYear) {
        fetch(`/truyen/${storySlug}/donations?month=${month}&year=${year}`)
            .then(response => response.json())
            .then(data => {
                updateDonationsList(data.topDonors);
                updateMonthSelector(data.months, data.selectedMonth, data.selectedYear);
            })
            .catch(error => {
                console.error('Error loading donations:', error);
                document.getElementById('story-donate-list').innerHTML =
                    '<p class="donate-message">Không thể tải dữ liệu donate.</p>';
            });
    }

    function updateDonationsList(topDonors) {
        const container = document.getElementById('story-donate-list');

        if (!topDonors || topDonors.length === 0) {
            container.innerHTML = '<p class="donate-message">Chưa có ai donate tháng này.</p>';
            return;
        }

        let html = `
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
        `;

        topDonors.forEach((donor, index) => {
            let rankIcon = '';
            if (index === 0) rankIcon = '🏆';
            else if (index === 1) rankIcon = '🥈';
            else if (index === 2) rankIcon = '🥉';
            else rankIcon = `<strong>${index + 1}</strong>`;

            html += `
                <tr>
                    <td>${rankIcon}</td>
                    <td>${donor.name}</td>
                    <td class="text-success fw-bold">${new Intl.NumberFormat('vi-VN').format(donor.donate_amount)} Linh Thạch</td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = html;
    }

    function updateMonthSelector(months, selectedMonth, selectedYear) {
        const container = document.getElementById('monthScrollContainer');

        if (!months || months.length === 0) {
            container.innerHTML = '<span class="text-muted">Không có dữ liệu</span>';
            return;
        }

        let html = '';
        months.forEach(month => {
            const isActive = month.month == selectedMonth && month.year == selectedYear;
            html += `
                <a href="#" class="btn btn-outline-greeen month-item ${isActive ? 'active' : ''}"
                   data-month="${month.month}" data-year="${month.year}">
                    ${month.month}/${month.year.toString().substr(2, 2)}
                </a>
            `;
        });

        container.innerHTML = html;

        // Add click handlers
        container.querySelectorAll('.month-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const month = this.dataset.month;
                const year = this.dataset.year;
                currentMonth = parseInt(month);
                currentYear = parseInt(year);
                loadDonations(currentMonth, currentYear);
            });
        });

        // Update navigation buttons
        updateNavigationButtons();
    }

    function updateNavigationButtons() {
        const container = document.getElementById('monthScrollContainer');
        const btnPrev = document.getElementById('btnPrevMonth');
        const btnNext = document.getElementById('btnNextMonth');
        const items = container.querySelectorAll('.month-item');

        if (items.length === 0) {
            btnPrev.style.display = 'none';
            btnNext.style.display = 'none';
            return;
        }

        const itemWidth = items[0].offsetWidth;
        const visibleCount = 12;
        const scrollPosition = container.scrollLeft;

        btnPrev.style.display = scrollPosition > 0 ? 'inline-block' : 'none';
        btnNext.style.display = scrollPosition < (items.length - visibleCount) * itemWidth ? 'inline-block' : 'none';
    }

    // Navigation button handlers
    document.getElementById('btnPrevMonth').addEventListener('click', function() {
        const container = document.getElementById('monthScrollContainer');
        const items = container.querySelectorAll('.month-item');
        const itemWidth = items.length > 0 ? items[0].offsetWidth : 0;
        const visibleCount = 12;

        container.scrollTo({
            left: Math.max(container.scrollLeft - itemWidth * visibleCount, 0),
            behavior: 'smooth'
        });

        setTimeout(updateNavigationButtons, 300);
    });

    document.getElementById('btnNextMonth').addEventListener('click', function() {
        const container = document.getElementById('monthScrollContainer');
        const items = container.querySelectorAll('.month-item');
        const itemWidth = items.length > 0 ? items[0].offsetWidth : 0;
        const visibleCount = 12;

        container.scrollTo({
            left: Math.min(container.scrollLeft + itemWidth * visibleCount, (items.length - visibleCount) * itemWidth),
            behavior: 'smooth'
        });

        setTimeout(updateNavigationButtons, 300);
    });

    // Initial load
    loadDonations();
});
</script>
