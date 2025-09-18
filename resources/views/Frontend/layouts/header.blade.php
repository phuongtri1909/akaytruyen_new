<header class="header d-none d-lg-block">
    <nav class="navbar navbar-expand-lg header__navbar p-1 header-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo/Logoakay.png') }}" alt="Logo akaytruyen" srcset="" class="img-fluid"
                    style="width: 200px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Thể loại
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            @foreach ($menu['the_loai'] as $menuItem)
                                <li><a class="dropdown-item"
                                        href="{{ route('category', ['slug' => $menuItem->slug]) }}">{{ $menuItem->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Theo chương
                        </a>
                        <ul class="dropdown-menu dropdown-menu-custom">
                            <li><a class="dropdown-item"
                                    href="{{ route('get.list.story.with.chapters.count', ['value' => [0, 100]]) }}">Dưới
                                    100</a>
                            <li><a class="dropdown-item"
                                    href="{{ route('get.list.story.with.chapters.count', ['value' => [100, 500]]) }}">100
                                    - 500</a>
                            <li><a class="dropdown-item"
                                    href="{{ route('get.list.story.with.chapters.count', ['value' => [500, 1000]]) }}">500
                                    - 1000</a>
                            <li><a class="dropdown-item"
                                    href="{{ route('get.list.story.with.chapters.count', ['value' => [1000, 999999999]]) }}">Trên
                                    1000</a>
                            </li>
                        </ul>
                    </li>

                </ul>



                <!-- Nút thông báo -->
                <!--pc-->
                <div class="dropdown form-check">
                    <button class="btn btn-dark position-relative" id="notificationDropdownPC" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-regular fa-bell"></i>
                        <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger"
                            id="notification-count-PC" data-has-unread="true">
                            0
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationDropdownPC"
                        style="width: 300px; max-height: 300px; overflow-y: auto;">
                        <li><strong class="dropdown-header">🔔 Thông báo mới</strong></li>
                        <div id="notification-list-PC">
                            <li class="text-center p-2 text-muted">Không có thông báo</li>
                        </div>
                    </ul>
                </div>

                <form class="d-flex align-items-center header__form-search wuxia-search"
                    action="{{ route('main.search.story') }}" method="GET">
                    @php
                        $valueDefault = '';
                        if (request()->input('key_word')) {
                            $valueDefault = request()->input('key_word');
                        }
                    @endphp
                    <input class="form-control search-story wuxia-search__input" type="text"
                        placeholder="Tìm kiếm kiếm hiệp..." name="key_word" value="{{ $valueDefault }}">
                    <div class="col-12 search-result shadow no-result d-none">
                        <div class="card text-white bg-light">
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush d-none">
                                    <li class="list-group-item">
                                        <a href="#" class="text-dark hover-title">Tự cẩm</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <button class="btn wuxia-search__submit" type="submit" aria-label="Tìm kiếm">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                            <path
                                d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                        </svg>
                    </button>
                </form>

            </div>
            <div class="auth-buttons" role="group" aria-label="Authentication buttons">
                @if (Auth::check())
                    @if (Auth::user()->hasRole('Admin'))
                        <a href="{{ route('admin.dashboard') }}" class="auth-btn auth-btn--admin">
                            <span class="auth-btn__icon">👑</span>
                            <span class="auth-btn__text">Admin</span>
                        </a>
                    @endif
                    <a href="{{ route('logout') }}" class="auth-btn auth-btn--logout">
                        <span class="auth-btn__icon">🚪</span>
                        <span class="auth-btn__text text-white">Đăng xuất</span>
                    </a>
                    <a href="{{ route('profile') }}" class="auth-btn auth-btn--profile">
                        <span class="auth-btn__icon">👤</span>
                        <span class="auth-btn__text text-white"></span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="auth-btn auth-btn--register">
                        <span class="auth-btn__icon">📝</span>
                        <span class="auth-btn__text text-white">Đăng ký</span>
                    </a>
                    <a href="{{ route('login') }}" class="auth-btn auth-btn--login">
                        <span class="auth-btn__icon">🔑</span>
                        <span class="auth-btn__text text-white">Đăng nhập</span>
                    </a>
                @endif
            </div>
        </div>

    </nav>
</header>

<div class="header-mobile d-sm-block d-lg-none">
    <nav class="navbar navbar-dark header-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/logo/Logoakay.png') }}" alt="akaytruyen" srcset=""
                    class="img-fluid" style="width: 200px;">
            </a>
            <!-- Mobile -->
            <div class="dropdown form-check">
                <button class="btn btn-dark position-relative" id="notificationDropdownMobile"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-regular fa-bell"></i>
                    <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger"
                        id="notification-count-Mobile">
                        0
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="notificationDropdownMobile"
                    style="width: 300px; max-height: 300px; overflow-y: auto;">
                    <li><strong class="dropdown-header">🔔 Thông báo mới</strong></li>
                    <div id="notification-list-Mobile">
                        <li class="text-center p-2 text-muted">Không có thông báo</li>
                    </div>
                </ul>
            </div>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasDarkNavbar" aria-controls="offcanvasDarkNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end header-custom w-75" tabindex="-1" id="offcanvasDarkNavbar"
                aria-labelledby="offcanvasDarkNavbarLabel">
                <div class="offcanvas-header">
                    <img src="{{ asset('images/logo/Logoakay.png') }}" alt="akaytruyen" srcset=""
                        class="img-fluid" style="width: 200px;">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Thể loại
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                @foreach ($menu['the_loai'] as $menuItem)
                                    <li><a class="dropdown-item"
                                            href="{{ route('category', ['slug' => $menuItem->slug]) }}">{{ $menuItem->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

                    </ul>

                    <form class="d-flex align-items-center header__form-search wuxia-search"
                        action="{{ route('main.search.story') }}" method="GET">
                        @php
                            $valueDefault = '';
                            if (request()->input('key_word')) {
                                $valueDefault = request()->input('key_word');
                            }
                        @endphp
                        <input class="form-control search-story wuxia-search__input ms-0" type="text"
                            placeholder="Tìm kiếm kiếm hiệp..." name="key_word" value="{{ $valueDefault }}">
                        <div class="col-12 search-result shadow no-result d-none">
                            <div class="card text-white bg-light">
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush d-none">
                                        <li class="list-group-item">
                                            <a href="#" class="text-dark hover-title">Tự cẩm</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <button class="btn wuxia-search__submit" type="submit" aria-label="Tìm kiếm">
                            <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512">
                                <path
                                    d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z" />
                            </svg>
                        </button>
                    </form>
                    <div class="auth-buttons" role="group" aria-label="Authentication buttons">
                        @if (Auth::check())
                            @if (Auth::user()->hasRole('Admin'))
                                <a href="{{ route('admin.dashboard') }}" class="auth-btn auth-btn--admin">
                                    <span class="auth-btn__icon">👑</span>
                                    <span class="auth-btn__text text-white">Admin</span>
                                </a>
                            @endif
                            <a href="{{ route('logout') }}" class="auth-btn auth-btn--logout">
                                <span class="auth-btn__icon">🚪</span>
                                <span class="auth-btn__text text-white">Đăng xuất</span>
                            </a>
                            <a href="{{ route('profile') }}" class="auth-btn auth-btn--profile">
                                <span class="auth-btn__icon">👤</span>
                                <span class="auth-btn__text text-white">Profile</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="auth-btn auth-btn--register">
                                <span class="auth-btn__icon">📝</span>
                                <span class="auth-btn__text text-white">Đăng ký</span>
                            </a>
                            <a href="{{ route('login') }}" class="auth-btn auth-btn--login">
                                <span class="auth-btn__icon">🔑</span>
                                <span class="auth-btn__text text-white">Đăng nhập</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </nav>

</div>


@push('scripts')
    @vite(['resources/assets/frontend/js/common.js'])

    <script>
        function loadNotifications() {
            fetch('/notifications')
                .then(response => response.json())
                .then(data => {
                    updateNotificationUI(data.notifications, data.tagged_notifications, "PC");
                    updateNotificationUI(data.notifications, data.tagged_notifications, "Mobile");
                })
                .catch(error => console.error('Lỗi khi tải thông báo:', error));
        }

        function updateNotificationUI(notifications, taggedNotifications, device) {
            let notificationList = document.querySelector(`#notification-list-${device}`);
            let notificationCount = document.querySelector(`#notification-count-${device}`);

            if (!notificationList || !notificationCount) return;

            notificationList.innerHTML = "";

            const total = notifications.length + taggedNotifications.length;

            if (total === 0) {
                notificationList.innerHTML = '<li class="text-center p-2 text-muted">Không có thông báo</li>';
                notificationCount.style.display = "none";
            } else {
                notificationCount.innerText = total;
                notificationCount.style.display = "inline";

                // Thêm thông báo chương mới
                notifications.forEach(notification => {
                    let li = document.createElement('li');
                    li.className = "p-2 border-bottom";

                    let chapterInfo = notification.chapter_number ?
                        `- Chương ${notification.chapter_number}: <em>${notification.chapter_title}</em>` :
                        '';

                    let chapterUrl = notification.chapter_number ?
                        `/${notification.story_slug}/${notification.chapter_slug}` :
                        `/${notification.story_slug}`;

                    li.innerHTML = `
                <a href="${chapterUrl}"
                   class="text-decoration-none text-dark notification-item"
                   data-id="${notification.id}"
                   onclick="markSingleNotificationAsRead(${notification.id})">
                    📢 <strong>${notification.story_title}</strong> ${chapterInfo}
                    <br><small class="text-muted">${new Date(notification.created_at).toLocaleString()}</small>
                </a>
            `;

                    notificationList.insertBefore(li, notificationList.firstChild);
                });

                taggedNotifications.forEach(tag => {
                    let li = document.createElement('li');
                    li.className = "p-2 border-bottom";

                    let chapterUrl = `/${tag.story_slug}/${tag.chapter_slug}`;
                    if (tag.comment_id) {
                        chapterUrl += `#comment-${tag.comment_id}`;
                    }
                    let deleteButton =
                        `<button class="btn btn-sm btn-danger delete-notification" data-id="${tag.id}">Xóa</button>`;

                    const commentContent = tag.comment_text?.length > 120 ?
                        tag.comment_text.substring(0, 120) + '...' :
                        tag.comment_text;

                    li.innerHTML = `
    <a href="${chapterUrl}"
       class="text-decoration-none text-dark notification-item"
       data-id="${tag.id}"
       onclick="handleTaggedNotificationClick(${tag.id})">
        🏷️ <strong>${tag.tagger_name || 'Một người nào đó'}</strong> đã nhắc đến bạn trong
        <strong>chương ${tag.chapter_number}: ${tag.chapter_title}</strong> của <strong>${tag.story_title}</strong>.
        <div class="border rounded px-2 py-1 mt-1 bg-light text-dark">
            ${commentContent || '(Không tìm thấy nội dung)'}
        </div>
        <br><small class="text-muted">${new Date(tag.created_at).toLocaleString()}</small>
    </a>
    ${deleteButton}
`;

                    notificationList.insertBefore(li, notificationList.firstChild);

                    li.querySelector('.delete-notification').addEventListener('click', function(e) {
                        e.preventDefault();

                        let notificationId = this.getAttribute('data-id');

                        fetch(`/delete-tagged-notification/${notificationId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify({
                                    notification_id: notificationId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    li.remove();
                                    alert('Thông báo đã được xóa!');
                                } else {
                                    alert('Không thể xóa thông báo này.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('Có lỗi xảy ra khi xóa thông báo.');
                            });
                    });
                });
            }
        }

        function markSingleNotificationAsRead(notificationId) {
            fetch('/notifications/read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        notification_id: notificationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                    }
                })
                .catch(error => console.error('Lỗi khi đánh dấu thông báo đã đọc:', error));
        }

        function handleTaggedNotificationClick(taggedNotificationId) {
            fetch(`/delete-tagged-notification/${taggedNotificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        notification_id: taggedNotificationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadNotifications();
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi xóa thông báo tagged:', error);
                });
        }
        @if (Auth::check())
            document.addEventListener("DOMContentLoaded", loadNotifications);
        @endif
    </script>
@endpush

<style>
    .header-custom {
        background-color: #14425d;
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.3);
    }

    .header-custom .text-white:hover {
        color: #caa83b !important;
    }

    .header-custom .text-white:focus {
        color: #caa83b !important;
    }

    .search-story {
        margin: 3px;
    }

    /* Wuxia search box */
    .wuxia-search {
        position: relative;
    }

    .wuxia-search__input {
        border-radius: 12px;
        border: 1px solid #8fc4e3;
        background:
            linear-gradient(180deg, #fbf6e6 0%, #efe4c9 100%),
            repeating-linear-gradient(0deg, rgba(0, 0, 0, 0.03) 0 1px, rgba(0, 0, 0, 0) 1px 3px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .4);
    }

    .wuxia-search__input::placeholder {
        color: #7a5c2f;
    }

    .wuxia-search__submit {
        border-radius: 0 12px 12px 0;
        border: 1px solid #8fc4e3;
        border-left: 0;
        background: radial-gradient(circle at 30% 30%, #8fc4e3, #14425d 70%);
        color: #4c380b;
    }

    .dark-theme .wuxia-search__input {
        background: linear-gradient(180deg, #2c2a26 0%, #24221f 100%);
        color: #fff;
        border-color: #8fc4e3;
    }

    .dark-theme .wuxia-search__submit {
        background: radial-gradient(circle at 30% 30%, #a58a36, #6b5a22 70%);
        color: #fff;
        border-color: #8fc4e3;
    }

    .auth-buttons {
        margin-left: 6px;
        display: flex;
        gap: 6px;
        align-items: center;
        flex-wrap: nowrap;
        white-space: nowrap;
    }

    .auth-btn {
        align-items: center;
        gap: 4px;
        padding: 6px 10px;
        border: 1px solid #ddd;
        border-radius: 12px;
        font-weight: 500;
        font-size: 12px;
        text-decoration: none;
        color: #333;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
        min-width: 0;
        flex-shrink: 0;
    }

    .auth-btn:hover {
        background: #f8f9fa;
        border-color: #adb5bd;
        color: #495057;
    }

    .auth-btn__icon {
        font-size: 14px;
    }

    .auth-btn__text {
        font-size: 12px;
    }

    /* Button Variants - Simple colors */
    .auth-btn--admin {
        background: #6c757d;
        color: #fff;
        border-color: #6c757d;
    }

    .auth-btn--admin:hover {
        background: #5a6268;
        border-color: #5a6268;
    }

    .auth-btn--logout {
        background: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }

    .auth-btn--logout:hover {
        background: #c82333;
        border-color: #c82333;
    }

    .auth-btn--profile {
        background: #17a2b8;
        color: #fff;
        border-color: #17a2b8;
    }

    .auth-btn--profile:hover {
        background: #138496;
        border-color: #138496;
    }

    .auth-btn--register {
        background: #28a745;
        color: #fff;
        border-color: #28a745;
    }

    .auth-btn--register:hover {
        background: #218838;
        border-color: #218838;
    }

    .auth-btn--login {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    .auth-btn--login:hover {
        background: #0056b3;
        border-color: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .auth-buttons {
            margin-left: 0;
            gap: 6px;
            justify-content: center;
        }

        .auth-btn {
            padding: 6px 10px;
            font-size: 12px;
        }

        .auth-btn__text {
            font-size: 11px;
        }
    }

    @media (max-width: 480px) {
        .auth-buttons {
            flex-direction: column;
            gap: 4px;
        }

        .auth-btn {
            width: 100%;
            justify-content: center;
            padding: 8px 12px;
        }
    }
</style>
