@extends('Frontend.layouts.default')

@section('title', 'Hồ sơ cá nhân')
@section('description', 'Quản lý hồ sơ cá nhân AKAY TRUYỆN')
@section('keywords', 'Hồ sơ cá nhân AKAY TRUYỆN')

@push('custom_schema')
    {!! SEO::generate() !!}
@endpush

@section('content')
    @include('components.toast')

    <div class="profile-container">
        <!-- Background Animation -->
        <div class="background-animation">
            <div class="floating-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
                <div class="shape shape-4"></div>
                <div class="shape shape-5"></div>
            </div>
        </div>

        <!-- Main Profile Content -->
        <div class="profile-wrapper">
            <div class="profile-card">
                <!-- Header Section -->
                <div class="header-section">
                    <h1 class="profile-title">Hồ sơ cá nhân</h1>
                    <p class="profile-subtitle">Quản lý thông tin tài khoản của bạn</p>
                </div>

                <!-- Avatar Section -->
                <div class="avatar-section">
                    <div class="avatar-container" id="avatar">
                        @if (!empty($user->avatar_url))
                            <div class="avatar-wrapper">
                                <!-- Avatar Image -->
                                <img id="avatarImage" class="avatar-image"
                                    src="{{ $user->avatar_url }}"
                                    alt="Avatar">

                                <!-- Role Border -->
                                @php
                                    $role = $user->roles->first()->name ?? null;
                                    $email = $user->email ?? null;

                                    $borderMap = [
                                        'Admin' => 'admin-vip-8.png',
                                        'Mod' => 'avt_mod.png',
                                        'Content' => 'avt_content.png',
                                        'vip' => 'avt_admin.png',
                                        'VIP PRO' => 'avt_pro_vip.png',
                                        'VIP PRO MAX' => 'avt_vip_pro_max.gif',
                                        'VIP SIÊU VIỆT' => 'khung-sieu-viet.png',
                                    ];
                                    $border = null;
                                    $borderStyle = '';

                                    if ($role === 'Admin' && $email === 'nang2025@gmail.com') {
                                        $border = asset('images/roles/vien-thanh-nu.png');
                                    } elseif ($role === 'Admin' && $email === 'nguyenphuochau12t2@gmail.com') {
                                        $border = asset('images/roles/akay.png');
                                        $borderStyle = 'width: 280%; height: 280%; top: 31%;';
                                    } else {
                                        $border = isset($borderMap[$role]) ? asset('images/roles/' . $borderMap[$role]) : null;
                                    }
                                @endphp

                                @if ($border)
                                    <img src="{{ $border }}" alt="Border {{ $role }}" class="role-border" style="{{ $borderStyle }}">
                                @endif
                            </div>
                        @else
                            <div class="avatar-placeholder">
                                <i class="fa-solid fa-user" id="defaultIcon"></i>
                            </div>
                        @endif

                        <!-- Upload Overlay -->
                        <div class="avatar-overlay">
                            <i class="fas fa-camera"></i>
                            <span>Thay đổi ảnh</span>
                        </div>
                    </div>
                    <input type="file" id="avatarInput" style="display: none;" accept="image/*">

                    <div class="user-info">
                        <h3 class="user-name">{{ $user->name }}</h3>
                        <p class="user-email">{{ $user->email }}</p>
                        <div class="user-role">
                            <span class="role-badge">{{ $user->roles->first()->name ?? 'Người dùng' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="profile-info">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-id-card"></i>
                            <span>ID người dùng</span>
                        </div>
                        <div class="info-value">{{ $user->id }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user-tag"></i>
                            <span>Vai trò</span>
                        </div>
                        <div class="info-value">{{ $user->roles->first()->name ?? 'Người dùng' }}</div>
                    </div>

                    <div class="info-item clickable" data-bs-toggle="modal" data-bs-target="#editModal" data-type="name">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            <span>Họ và tên</span>
                        </div>
                        <div class="info-value">
                            <span>{{ $user->name ?: 'Chưa cập nhật' }}</span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>

                    <div class="info-item clickable" data-bs-toggle="modal" data-bs-target="#otpPWModal">
                        <div class="info-label">
                            <i class="fas fa-lock"></i>
                            <span>Mật khẩu</span>
                        </div>
                        <div class="info-value">
                            <span>••••••••</span>
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('logout') }}" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Chỉnh sửa thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="{{ route('update.name.or.phone') }}" method="post">
                        @csrf
                        <div class="mb-3" id="formContent">
                            <!-- Nội dung sẽ được cập nhật dựa trên loại dữ liệu được chọn -->
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary" id="saveChanges">
                                <span class="btn-text">Lưu thay đổi</span>
                                <div class="btn-loader">
                                    <div class="spinner"></div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Password Modal -->
    <div class="modal fade" id="otpPWModal" tabindex="-1" aria-labelledby="otpPWModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpPWModalLabel">Xác thực OTP để đổi mật khẩu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="otpPWForm">
                        @csrf
                        <div class="mb-3 d-flex flex-column align-items-center" id="formOTPPWContent">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary" id="btn-send-otpPW">
                                <span class="btn-text">Tiếp tục</span>
                                <div class="btn-loader">
                                    <div class="spinner"></div>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Reset & Base */
        .profile-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

        /* Background Animation */
        .background-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .floating-shapes {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape-4 {
            width: 100px;
            height: 100px;
            top: 10%;
            right: 30%;
            animation-delay: 1s;
        }

        .shape-5 {
            width: 40px;
            height: 40px;
            top: 40%;
            left: 60%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        /* Profile Card */
        .profile-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 600px;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header Section */
        .header-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-container {
            margin-bottom: 20px;
        }

        .logo-link {
            display: inline-block;
            transition: transform 0.3s ease;
        }

        .logo-link:hover {
            transform: scale(1.05);
        }

        .logo-image {
            height: 60px;
            width: auto;
            object-fit: contain;
        }

        .profile-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #14425d, #667eea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .profile-subtitle {
            color: #718096;
            font-size: 16px;
            margin: 0;
        }

        /* Avatar Section */
        .avatar-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .avatar-container {
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .avatar-container:hover {
            transform: scale(1.05);
        }

        .avatar-wrapper {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .role-border {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 180px;
            height: 180px;
            transform: translate(-50%, -50%);
            pointer-events: none;
            z-index: 1;
            border-radius: 50%;
        }

        .avatar-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }

        .avatar-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .avatar-container:hover .avatar-overlay {
            opacity: 1;
        }

        .avatar-overlay i {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .avatar-overlay span {
            font-size: 12px;
            font-weight: 500;
        }

        .user-info {
            text-align: center;
        }

        .user-name {
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }

        .user-email {
            color: #718096;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .user-role {
            margin-bottom: 20px;
        }

        .role-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Profile Information */
        .profile-info {
            margin-bottom: 40px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-item.clickable {
            cursor: pointer;
            border-radius: 12px;
            padding: 16px;
            margin: 8px 0;
            border: 1px solid transparent;
        }

        .info-item.clickable:hover {
            background: rgba(102, 126, 234, 0.05);
            border-color: rgba(102, 126, 234, 0.2);
            transform: translateX(5px);
        }

        .info-label {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #4a5568;
            font-weight: 500;
        }

        .info-label i {
            width: 20px;
            color: #667eea;
            font-size: 16px;
        }

        .info-value {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2d3748;
            font-weight: 600;
        }

        .info-value i {
            color: #a0aec0;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .info-item.clickable:hover .info-value i {
            color: #667eea;
        }

        /* Action Buttons */
        .action-buttons {
            text-align: center;
        }

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 16px 32px;
            background: linear-gradient(135deg, #e53e3e, #c53030);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
            color: white;
        }


        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-outline-secondary {
            border: 2px solid #e2e8f0;
            color: #4a5568;
        }

        .btn-outline-secondary:hover {
            background: #f7fafc;
            border-color: #cbd5e0;
        }

        /* Loading States */
        .btn.loading {
            pointer-events: none;
        }

        .btn-text {
            transition: opacity 0.3s ease;
        }

        .btn-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn.loading .btn-text {
            opacity: 0;
        }

        .btn.loading .btn-loader {
            opacity: 1;
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* OTP Styles */
        .otp-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin: 24px 0;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: white;
        }

        .otp-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-card {
                padding: 30px 20px;
                margin: 10px;
            }

            .profile-title {
                font-size: 24px;
            }

            .logo-image {
                height: 50px;
            }

            .avatar-wrapper {
                width: 100px;
                height: 100px;
            }

            .role-border {
                width: 150px;
                height: 150px;
            }

            .avatar-placeholder {
                width: 100px;
                height: 100px;
                font-size: 40px;
            }

            .info-item.clickable {
                padding: 12px;
            }

            .modal-actions {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            .profile-card {
                padding: 24px 16px;
            }

            .profile-title {
                font-size: 20px;
            }

            .user-name {
                font-size: 20px;
            }

            .info-label {
                font-size: 14px;
            }

            .info-value {
                font-size: 14px;
            }

            .otp-container {
                gap: 8px;
            }

            .otp-input {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }

            .avatar-wrapper {
                width: 80px;
                height: 80px;
            }

            .role-border {
                width: 120px;
                height: 120px;
            }

            .avatar-placeholder {
                width: 80px;
                height: 80px;
                font-size: 32px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Show toast messages
            @if (session('success'))
                showToast('{{ session('success') }}', 'success');
            @elseif (session('error'))
                showToast('{{ session('error') }}', 'error');
            @endif

            // Avatar upload
            $('#avatar').on('click', function() {
                $('#avatarInput').click();
            });

            $('#avatarInput').on('change', function() {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        if (!$('#avatarImage').length) {
                            $('#avatar').html(`
                                <div class="avatar-wrapper">
                                    <img id="avatarImage" class="avatar-image" src="${e.target.result}" alt="Avatar">
                                </div>
                                <div class="avatar-overlay">
                                    <i class="fas fa-camera"></i>
                                    <span>Thay đổi ảnh</span>
                                </div>
                            `);
                        } else {
                            $('#avatarImage').attr('src', e.target.result);
                        }
                    };
                    reader.readAsDataURL(file);

                    var formData = new FormData();
                    formData.append('avatar', file);

                    $.ajax({
                        url: "{{ route('update.avatar') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                showToast(response.message, 'success');
                            } else {
                                showToast(response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            showToast('Có lỗi xảy ra khi cập nhật ảnh đại diện', 'error');
                        }
                    });
                }
            });

            // Edit modal
            $('#editModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var type = button.data('type');
                var modal = $(this);

                var formContent = $('#formContent');
                formContent.empty();

                if (type == 'name') {
                    modal.find('.modal-title').text('Chỉnh sửa Họ và Tên');
                    formContent.append(`
                        <div class="form-group">
                            <label for="editValue" class="form-label">Họ và Tên</label>
                            <input type="text" class="form-control" id="editValue" name="name" value="{{ $user->name }}" required>
                        </div>
                    `);
                } else if (type == 'phone') {
                    modal.find('.modal-title').text('Chỉnh sửa Số điện thoại');
                    formContent.append(`
                        <div class="form-group">
                            <label for="editValue" class="form-label">Số điện thoại</label>
                            <input type="number" class="form-control" id="editValue" name="phone" value="{{ $user->phone }}" required>
                        </div>
                    `);
                }
            });

            // OTP Password modal
            $('#otpPWModal').on('show.bs.modal', function(event) {
                var modal = $(this);
                $('#btn-send-otpPW .btn-text').text('Tiếp tục');

                var formOTPContent = $('#formOTPPWContent');
                formOTPContent.empty();
                formOTPContent.append(`
                    <span class="text-center mb-3 title-otp-pw">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </span>
                    <div class="otp-container" id="input-otp-pw">
                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                        <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                    </div>
                `);

                $.ajax({
                    url: "{{ route('update.password') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            $('.title-otp-pw').text(response.message).removeClass('text-danger').addClass('text-success');
                        } else {
                            $('.title-otp-pw').text(response.message).removeClass('text-success').addClass('text-danger');
                        }

                        // Handle OTP submission
                        $('#otpPWForm').on('submit', function(e) {
                            e.preventDefault();
                            var otp = '';
                            $('#otpPWForm .otp-input').each(function() {
                                otp += $(this).val();
                            });

                            $.ajax({
                                url: "{{ route('update.password') }}",
                                type: 'POST',
                                data: { otp: otp },
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        showToast(response.message, 'success');
                                        formOTPContent.empty();
                                        formOTPContent.append(`
                                            <span class="text-center mb-3 title-otp-pw">Hãy thay đổi mật khẩu mới!</span>
                                            <div class="form-group">
                                                <label for="password" class="form-label">Mật khẩu mới</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password" id="password" required>
                                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                                                    <button type="button" class="btn btn-outline-secondary" id="togglePasswordConfirm">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        `);

                                        $('#btn-send-otpPW .btn-text').text('Lưu thay đổi');

                                        // Handle password change submission
                                        $('#otpPWForm').off('submit').on('submit', function(e) {
                                            e.preventDefault();
                                            var formData = new FormData(this);
                                            formData.append('otp', otp);

                                            $.ajax({
                                                url: "{{ route('update.password') }}",
                                                type: 'POST',
                                                data: formData,
                                                processData: false,
                                                contentType: false,
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                },
                                                success: function(response) {
                                                    if (response.status === 'success') {
                                                        showToast(response.message, 'success');
                                                        $('#otpPWModal').modal('hide');
                                                    } else {
                                                        showToast(response.message, 'error');
                                                    }
                                                },
                                                error: function(xhr) {
                                                    showToast('Có lỗi xảy ra, vui lòng thử lại.', 'error');
                                                }
                                            });
                                        });
                                    } else {
                                        showToast(response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    const response = xhr.responseJSON;
                                    if (response && response.status === 'error' && response.message.otp) {
                                        $('#input-otp-pw').append(`
                                            <div class="invalid-otp text-danger mt-2">${response.message.otp[0]}</div>
                                        `);
                                    } else {
                                        showToast('Thao tác sai, hãy thử lại', 'error');
                                    }
                                }
                            });
                        });
                    },
                    error: function(xhr) {
                        showToast('Thao tác sai, hãy thử lại', 'error');
                    }
                });
            });

            // Password toggle
            $(document).on('click', '#togglePassword, #togglePasswordConfirm', function() {
                const passwordField = $(this).siblings('input');
                const icon = $(this).find('i');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Add floating animation to shapes
            $('.shape').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.5) + 's',
                    'animation-duration': (6 + index) + 's'
                });
            });
        });

        // OTP input handling
        function handleInput(element) {
            $(element).val($(element).val().replace(/[^0-9]/g, ''));

            if ($(element).val().length === 1) {
                const nextInput = $(element).next('.otp-input');
                if (nextInput.length) {
                    nextInput.focus();
                }
            }
        }

        $(document).on('keydown', '.otp-input', function(e) {
            if (e.key === 'Backspace' || e.key === 'Delete') {
                e.preventDefault();
                const $currentInput = $(this);
                const $prevInput = $currentInput.prev('.otp-input');

                if ($currentInput.val()) {
                    $currentInput.val('');
                } else if ($prevInput.length) {
                    $prevInput.val('').focus();
                }
            }
        });

        // Toast function
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
