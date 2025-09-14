@if (Route::currentRouteName() == 'chapter')
<div class="floating-wuxia-tools m-3">
    <button class="floating-wuxia-tools__fab" id="wuxiaFab" aria-label="Mở công cụ">
        <i class="fa-solid fa-fan"></i>
    </button>
    <div class="floating-wuxia-tools__panel" id="wuxiaPanel" aria-hidden="true">
        <button class="wuxia-tool-btn" id="fontDec" title="Giảm cỡ chữ">A-</button>
        <button class="wuxia-tool-btn" id="fontInc" title="Tăng cỡ chữ">A+</button>

        <!-- Font Family Selector -->
        <div class="wuxia-tool-group">
            <label class="wuxia-tool-label">Font chữ</label>
            <select class="wuxia-tool-select setting-font">
                <option value="roboto" @if (isset($chapterFont) && $chapterFont == 'roboto') selected @endif>ROBOTO</option>
                <option value="mooli" @if (isset($chapterFont) && $chapterFont == 'mooli') selected @endif>MOOLI</option>
                <option value="patrick_hand" @if (isset($chapterFont) && $chapterFont == 'patrick_hand') selected @endif>PATRICK HAND</option>
                <option value="noto_sans" @if (isset($chapterFont) && $chapterFont == 'noto_sans') selected @endif>NOTO SANS</option>
                <option value="noto_serif" @if (isset($chapterFont) && $chapterFont == 'noto_serif') selected @endif>NOTO SERIF</option>
                <option value="charter" @if (isset($chapterFont) && $chapterFont == 'charter') selected @endif>CHARTER</option>
            </select>
        </div>

        <!-- Font Size Selector -->
        <div class="wuxia-tool-group">
            <label class="wuxia-tool-label">Size chữ</label>
            <select class="wuxia-tool-select setting-font-size">
                @for ($i = 16; $i <= 48; $i += 2)
                    <option value="{{ $i }}" @if (isset($chapterFontSize) && $chapterFontSize == $i) selected @endif>{{ $i }}</option>
                @endfor
            </select>
        </div>

        <!-- Line Height Selector -->
        <div class="wuxia-tool-group">
            <label class="wuxia-tool-label">Chiều cao dòng</label>
            <select class="wuxia-tool-select setting-line-height">
                @for ($i = 100; $i <= 200; $i += 20)
                    <option value="{{ $i }}" @if (isset($chapterLineHeight) && $chapterLineHeight == $i) selected @endif>{{ $i }}%</option>
                @endfor
            </select>
        </div>

        <label class="wuxia-switch" title="Chế độ tối/sáng">
            <input type="checkbox" class="theme_mode">
            <span class="slider"></span>
        </label>
    </div>
</div>
@else
<!-- Theme toggle only for non-chapter pages -->
<div class="floating-wuxia-tools m-3">
    <button class="floating-wuxia-tools__fab" id="wuxiaFab" aria-label="Mở công cụ">
        <i class="fa-solid fa-fan"></i>
    </button>
    <div class="floating-wuxia-tools__panel" id="wuxiaPanel" aria-hidden="true">
        <label class="wuxia-switch" title="Chế độ tối/sáng">
            <input type="checkbox" class="theme_mode">
            <span class="slider"></span>
        </label>
    </div>
</div>
@endif

<div class="floating-wuxia-social m-3 mb-5">
    <!-- Social Buttons Container -->
    <div class="social-buttons-container" id="socialContainer">
        <a href="https://youtube.com/@AkayTruyen?sub_confirmation=1" target="_blank" rel="noreferrer"
            class="wuxia-social wuxia-social--yt text-white" aria-label="Youtube">
            <i class="fa-brands fa-youtube"></i>
        </a>
        <a href="https://www.facebook.com/groups/1134210028188278/" target="_blank" rel="noreferrer"
            class="wuxia-social wuxia-social--fb text-white" aria-label="Facebook">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
        <a href="https://discord.gg/yourInviteCode" target="_blank" rel="noreferrer"
            class="wuxia-social wuxia-social--dc text-white" aria-label="Discord">
            <i class="fa-brands fa-discord"></i>
        </a>
    </div>

    <!-- Toggle Button với hiệu ứng nhấp nháy -->
    <button class="social-toggle-btn" id="socialToggle" aria-label="Mở/Đóng mạng xã hội">
        <i class="fa-solid fa-share-nodes"></i>
        <div class="pulse-ring"></div>
    </button>
</div>


<style>
    .floating-wuxia-tools {
        position: fixed;
        left: 0;
        bottom: 0;
        z-index: 1050;
    }

    .floating-wuxia-tools__fab {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 1px solid #caa83b;
        background: radial-gradient(circle at 30% 30%, #ffe8a6, #d4af37 70%);
        box-shadow: 0 8px 18px rgba(0, 0, 0, .2), inset 0 0 0 1px rgba(255, 255, 255, .4);
        color: #5c3e06;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .floating-wuxia-tools__fab:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 22px rgba(0, 0, 0, .25), inset 0 0 0 1px rgba(255, 255, 255, .5);
    }

    .floating-wuxia-tools__fab.rotated {
        transform: rotate(180deg);
    }

    .floating-wuxia-tools__fab.rotated:hover {
        transform: rotate(180deg);
    }

    .floating-wuxia-tools__panel {
        position: absolute;
        left: 0;
        bottom: 60px;
        display: grid;
        grid-auto-flow: row;
        gap: 8px;
        padding: 15px;
        border-radius: 12px;
        width: max-content;
        background: linear-gradient(180deg, #fbf6e6 0%, #efe4c9 100%);
        border: 1px solid #caa83b;
        box-shadow: 0 12px 24px rgba(0, 0, 0, .2), inset 0 0 0 1px rgba(255, 255, 255, .35);
        transform-origin: bottom left;
        transform: scale(.9) translateY(8px);
        opacity: 0;
        pointer-events: none;
        transition: transform .2s ease, opacity .2s ease;
    }

    .floating-wuxia-tools__panel.active {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .wuxia-tool-btn {
        min-width: 44px;
        height: 36px;
        padding: 0 10px;
        border-radius: 8px;
        border: 1px solid #b78f2f;
        background: linear-gradient(180deg, #fff6d7, #f0d98c);
        color: #4c380b;
        font-weight: 700;
        box-shadow: 0 2px 0 #b78f2f;
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .wuxia-tool-btn:active {
        transform: translateY(1px);
        box-shadow: 0 1px 0 #b78f2f;
    }

    /* Tool groups for font controls */
    .wuxia-tool-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .wuxia-tool-label {
        font-size: 12px;
        font-weight: 600;
        color: #4c380b;
        margin: 0;
    }

    .wuxia-tool-select {
        padding: 6px 8px;
        border-radius: 6px;
        border: 1px solid #b78f2f;
        background: linear-gradient(180deg, #fff6d7, #f0d98c);
        color: #4c380b;
        font-size: 12px;
        font-weight: 600;
        box-shadow: 0 1px 0 #b78f2f;
        transition: all .15s ease;
    }

    .wuxia-tool-select:focus {
        outline: none;
        border-color: #caa83b;
        box-shadow: 0 0 0 2px rgba(202, 168, 59, 0.3);
    }

    /* switch */
    .wuxia-switch {
        position: relative;
        display: inline-block;
        width: 56px;
        height: 30px;
    }

    .wuxia-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #2f8f83;
        border: 1px solid #1e6b62;
        border-radius: 999px;
        transition: .2s;
    }

    .slider:before {
        content: "";
        position: absolute;
        height: 22px;
        width: 22px;
        left: 4px;
        top: 3px;
        background: #fff6d7;
        border-radius: 50%;
        transition: .2s;
        box-shadow: 0 2px 4px rgba(0, 0, 0, .2);
    }

    .wuxia-switch input:checked+.slider {
        background: #1e1e1e;
        border-color: #555;
    }

    .wuxia-switch input:checked+.slider:before {
        transform: translateX(26px);
        background: #f9e07d;
    }

    /* socials */
    .floating-wuxia-social {
        position: fixed;
        right: 0;
        bottom: 25px;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 10px;
    }

    /* Toggle Button với hiệu ứng nhấp nháy */
    .social-toggle-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 1px solid #caa83b;
        background: radial-gradient(circle at 30% 30%, #ffe8a6, #d4af37 70%);
        box-shadow: 0 8px 18px rgba(0, 0, 0, .2), inset 0 0 0 1px rgba(255, 255, 255, .4);
        color: #5c3e06;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .2s ease, box-shadow .2s ease;
        position: relative;
        cursor: pointer;
        z-index: 1051;
    }

    .social-toggle-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 22px rgba(0, 0, 0, .25), inset 0 0 0 1px rgba(255, 255, 255, .5);
    }

    .social-toggle-btn.active {
        transform: rotate(180deg);
    }

    .social-toggle-btn.active:hover {
        transform: rotate(180deg) translateY(-2px);
    }

    /* Hiệu ứng nhấp nháy */
    .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid #ff6b6b;
        animation: pulse 2s infinite;
        pointer-events: none;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.2);
            opacity: 0.5;
        }
        100% {
            transform: scale(1.4);
            opacity: 0;
        }
    }

    /* Social Buttons Container */
    .social-buttons-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        transform: translateX(100%);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
        pointer-events: none;
    }

    .social-buttons-container.active {
        transform: translateX(0);
        opacity: 1;
        pointer-events: auto;
    }

    .wuxia-social {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 18px;
        text-decoration: none;
        box-shadow: 0 8px 18px rgba(0, 0, 0, .25);
        transition: transform .2s ease, box-shadow .2s ease;
        animation: slideInRight 0.3s ease forwards;
    }

    .wuxia-social:nth-child(1) { animation-delay: 0.1s; }
    .wuxia-social:nth-child(2) { animation-delay: 0.2s; }
    .wuxia-social:nth-child(3) { animation-delay: 0.3s; }

    @keyframes slideInRight {
        from {
            transform: translateX(50px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .wuxia-social:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, .3);
    }

    .wuxia-social--yt {
        background: radial-gradient(circle at 30% 30%, #ff7a7a, #cc0000 70%);
    }

    .wuxia-social--fb {
        background: radial-gradient(circle at 30% 30%, #7aa7ff, #2952a3 70%);
    }

    .wuxia-social--dc {
        background-color: #5865F2;
    }

    /* dark */
    .dark-theme .floating-wuxia-tools__panel {
        background: linear-gradient(180deg, #2c2a26 0%, #24221f 100%);
        border-color: rgba(212, 175, 55, .35);
    }

    .dark-theme .wuxia-tool-label {
        color: #f0d98c;
    }

    .dark-theme .wuxia-tool-select {
        background: linear-gradient(180deg, #3a3a3a, #2a2a2a);
        color: #f0d98c;
        border-color: #caa83b;
    }

    /* Dark theme cho social buttons */
    .dark-theme .social-toggle-btn {
        background: radial-gradient(circle at 30% 30%, #4a4a4a, #2a2a2a 70%);
        border-color: #caa83b;
        color: #f0d98c;
    }

    .dark-theme .pulse-ring {
        border-color: #ff6b6b;
    }

    /* Mobile responsive */
    @media (max-width: 768px) {

        .social-buttons-container {
            gap: 8px;
        }
    }

    /* Hiệu ứng loading cho social buttons */
    .wuxia-social {
        opacity: 0;
        transform: translateX(50px);
    }

    .social-buttons-container.active .wuxia-social {
        opacity: 1;
        transform: translateX(0);
    }
</style>

<script>
    (function() {
        var fab = document.getElementById('wuxiaFab');
        var panel = document.getElementById('wuxiaPanel');
        if (fab && panel) {
            fab.addEventListener('click', function() {
                panel.classList.toggle('active');
                fab.classList.toggle('rotated');
            });
        }

        // Font configuration (only for chapter pages)
        if (window.location.pathname.includes('/chuong-') || window.location.pathname.includes('/chapter')) {
            window.objConfigFont = [{
                    name: 'roboto',
                    value: "'Roboto Condensed', sans-serif"
                },
                {
                    name: 'mooli',
                    value: "'Mooli', sans-serif"
                },
                {
                    name: 'patrick_hand',
                    value: "'Patrick Hand', cursive"
                },
                {
                    name: 'noto_sans',
                    value: "'Noto Sans', sans-serif"
                },
                {
                    name: 'noto_serif',
                    value: "'Noto Serif', serif"
                },
                {
                    name: 'charter',
                    value: "'Charter', serif"
                }
            ];
        }

        // Font controls (only for chapter pages)
        if (window.location.pathname.includes('/chuong-') || window.location.pathname.includes('/chapter')) {
            function applyChapterFontSize(px) {
                localStorage.setItem('chapter_fs_px', String(px));
                // Also save to cookie for compatibility
                if (window.setCookie) {
                    window.setCookie('font_size_chapter', px, 1);
                }
                var nodes = document.querySelectorAll('.chapter-content');
                nodes.forEach(function(n) {
                    n.style.fontSize = px + 'px';
                });
                // Update the select dropdown if it exists
                var fontSizeSelect = document.querySelector('.setting-font-size');
                if (fontSizeSelect) {
                    fontSizeSelect.value = px;
                }
            }

            function getChapterFontSize() {
                var nodes = document.querySelector('.chapter-content');
                if (!nodes) return null;
                var stored = localStorage.getItem('chapter_fs_px');
                if (stored) return parseInt(stored, 10);
                var cs = window.getComputedStyle(nodes);
                return parseInt(cs.fontSize, 10) || 18;
            }

            // Font size controls
            var inc = document.getElementById('fontInc');
            var dec = document.getElementById('fontDec');
            if (inc) inc.addEventListener('click', function() {
                var v = getChapterFontSize() || 18;
                applyChapterFontSize(Math.min(v + 2, 48));
            });
            if (dec) dec.addEventListener('click', function() {
                var v = getChapterFontSize() || 18;
                applyChapterFontSize(Math.max(v - 2, 12));
            });

            // Font family selector
            var fontSelect = document.querySelector('.setting-font');
            if (fontSelect) {
                fontSelect.addEventListener('change', function() {
                    var selectedFont = this.value;
                    var fontObj = window.objConfigFont.find(f => f.name === selectedFont);
                    if (fontObj) {
                        document.querySelectorAll('.chapter-content').forEach(function(el) {
                            el.style.fontFamily = fontObj.value;
                        });
                        localStorage.setItem('chapterFont', selectedFont);
                        // Also save to cookie for compatibility
                        if (window.setCookie) {
                            window.setCookie('font_chapter', selectedFont, 1);
                        }
                    }
                });
            }

            // Font size selector
            var fontSizeSelect = document.querySelector('.setting-font-size');
            if (fontSizeSelect) {
                fontSizeSelect.addEventListener('change', function() {
                    var size = this.value;
                    document.querySelectorAll('.chapter-content').forEach(function(el) {
                        el.style.fontSize = size + 'px';
                    });
                    localStorage.setItem('chapter_fs_px', size);
                    // Also save to cookie for compatibility
                    if (window.setCookie) {
                        window.setCookie('font_size_chapter', size, 1);
                    }
                });
            }

            // Line height selector
            var lineHeightSelect = document.querySelector('.setting-line-height');
            if (lineHeightSelect) {
                lineHeightSelect.addEventListener('change', function() {
                    var height = this.value;
                    document.querySelectorAll('.chapter-content').forEach(function(el) {
                        el.style.lineHeight = height + '%';
                    });
                    localStorage.setItem('chapter_line_height', height);
                    // Also save to cookie for compatibility
                    if (window.setCookie) {
                        window.setCookie('line_height_chapter', height, 1);
                    }
                });
            }
        }

        // Apply stored settings on load (only for chapter pages)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.pathname.includes('/chuong-') || window.location.pathname.includes('/chapter')) {
                // Apply stored font size (check localStorage first, then cookie)
                var storedFontSize = localStorage.getItem('chapter_fs_px');
                if (!storedFontSize) {
                    // Try to get from cookie as fallback
                    var cookies = document.cookie.split(';');
                    for (var i = 0; i < cookies.length; i++) {
                        var cookie = cookies[i].trim();
                        if (cookie.startsWith('font_size_chapter=')) {
                            storedFontSize = cookie.substring('font_size_chapter='.length);
                            break;
                        }
                    }
                }
                if (storedFontSize) {
                    applyChapterFontSize(parseInt(storedFontSize, 10));
                    var fontSizeSelect = document.querySelector('.setting-font-size');
                    if (fontSizeSelect) {
                        fontSizeSelect.value = storedFontSize;
                    }
                }

                // Apply stored font family (check localStorage first, then cookie)
                var storedFont = localStorage.getItem('chapterFont');
                if (!storedFont) {
                    // Try to get from cookie as fallback
                    var cookies = document.cookie.split(';');
                    for (var i = 0; i < cookies.length; i++) {
                        var cookie = cookies[i].trim();
                        if (cookie.startsWith('font_chapter=')) {
                            storedFont = cookie.substring('font_chapter='.length);
                            break;
                        }
                    }
                }
                if (storedFont) {
                    var fontSelect = document.querySelector('.setting-font');
                    var fontObj = window.objConfigFont.find(f => f.name === storedFont);
                    if (fontObj && fontSelect) {
                        document.querySelectorAll('.chapter-content').forEach(function(el) {
                            el.style.fontFamily = fontObj.value;
                        });
                        fontSelect.value = storedFont;
                    }
                }

                // Apply stored line height (check localStorage first, then cookie)
                var storedLineHeight = localStorage.getItem('chapter_line_height');
                if (!storedLineHeight) {
                    // Try to get from cookie as fallback
                    var cookies = document.cookie.split(';');
                    for (var i = 0; i < cookies.length; i++) {
                        var cookie = cookies[i].trim();
                        if (cookie.startsWith('line_height_chapter=')) {
                            storedLineHeight = cookie.substring('line_height_chapter='.length);
                            break;
                        }
                    }
                }
                if (storedLineHeight) {
                    var lineHeightSelect = document.querySelector('.setting-line-height');
                    document.querySelectorAll('.chapter-content').forEach(function(el) {
                        el.style.lineHeight = storedLineHeight + '%';
                    });
                    if (lineHeightSelect) {
                        lineHeightSelect.value = storedLineHeight;
                    }
                }
            }
        });

        // Social Toggle Functionality
        var socialToggle = document.getElementById('socialToggle');
        var socialContainer = document.getElementById('socialContainer');

        if (socialToggle && socialContainer) {
            // Lấy trạng thái từ localStorage
            var isSocialOpen = localStorage.getItem('socialOpen') === 'true';

            // Áp dụng trạng thái ban đầu
            if (isSocialOpen) {
                socialContainer.classList.add('active');
                socialToggle.classList.add('active');
            }

            socialToggle.addEventListener('click', function() {
                socialContainer.classList.toggle('active');
                socialToggle.classList.toggle('active');

                // Lưu trạng thái vào localStorage
                var isOpen = socialContainer.classList.contains('active');
                localStorage.setItem('socialOpen', isOpen);

                // Thêm hiệu ứng click
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });

            // Tự động ẩn sau 5 giây nếu không tương tác
            var hideTimeout;
            socialContainer.addEventListener('mouseenter', function() {
                clearTimeout(hideTimeout);
            });

            socialContainer.addEventListener('mouseleave', function() {
                if (socialContainer.classList.contains('active')) {
                    hideTimeout = setTimeout(function() {
                        socialContainer.classList.remove('active');
                        socialToggle.classList.remove('active');
                        localStorage.setItem('socialOpen', 'false');
                    }, 5000);
                }
            });
        }
    })();
</script>
