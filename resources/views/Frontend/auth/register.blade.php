@extends('Frontend.layouts.default')

@section('title', 'Đăng ký')
@section('description', 'Đăng ký AKAY TRUYỆN')
@section('keywords', 'Đăng ký AKAY TRUYỆN')

@push('custom_schema')
    {!! SEO::generate() !!}
@endpush

@section('content')
    <div class="login-container">
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

        <!-- Main Register Form -->
        <div class="login-wrapper">
            <div class="login-card">
                <!-- Logo Section -->
                <div class="logo-section">
                    <div class="logo-container">
                        <a href="{{ route('home') }}" class="logo-link">
                            <img src="{{ asset('images/logo/Logoakay.png') }}" alt="AKAY TRUYỆN"
                                class="logo-image">
                        </a>
                    </div>
                    <h1 class="welcome-text">Chào mừng bạn!</h1>
                    <p class="welcome-subtitle">Đăng ký để bắt đầu hành trình đọc truyện</p>
                </div>

                <!-- Register Form -->
                <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="login-form" id="registerForm">
                    @csrf

                    <!-- Email Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" name="email" id="email"
                                class="form-input @error('email') error @enderror" placeholder="Nhập email của bạn"
                                value="{{ old('email') }}" required>
                            <div class="input-line"></div>
                        </div>
                        @error('email')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Name Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <input type="text" name="name" id="name"
                                class="form-input @error('name') error @enderror" placeholder="Nhập họ và tên"
                                value="{{ old('name') }}" required>
                            <div class="input-line"></div>
                        </div>
                        @error('name')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" name="password" id="password"
                                class="form-input @error('password') error @enderror" placeholder="Nhập mật khẩu" required>
                            <div class="input-line"></div>
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="login-btn" id="registerBtn">
                        <span class="btn-text">Đăng ký</span>
                        <div class="btn-loader">
                            <div class="spinner"></div>
                        </div>
                    </button>
                </form>

                <!-- Login Link -->
                <div class="register-section">
                    <p>Bạn đã có tài khoản?
                        <a href="{{ route('login') }}" class="register-link">
                            Đăng nhập ngay
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Reset & Base */

        .login-container {
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

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        /* Login Card */
        .login-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
        }

        .login-card {
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

        /* Logo Section */
        .logo-section {
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
            height: 80px;
            width: auto;
            object-fit: contain;
        }

        .welcome-text {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #667eea, #14425d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .welcome-subtitle {
            color: #718096;
            font-size: 16px;
            margin: 0;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 24px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #a0aec0;
            font-size: 18px;
            z-index: 2;
            transition: color 0.3s ease;
        }

        .form-input {
            width: 100%;
            padding: 16px 16px 16px 50px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            background: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: #14425d;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input:focus+.input-icon {
            color: #14425d;
        }

        .form-input.error {
            border-color: #e53e3e;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .input-line {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #14425d, #667eea);
            transition: width 0.3s ease;
        }

        .form-input:focus~.input-line {
            width: 100%;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            background: none;
            border: none;
            color: #a0aec0;
            font-size: 18px;
            cursor: pointer;
            transition: color 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #14425d;
        }

        .error-message {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #e53e3e;
            font-size: 14px;
            margin-top: 8px;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #14425d, #667eea);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .login-btn.loading {
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

        .login-btn.loading .btn-text {
            opacity: 0;
        }

        .login-btn.loading .btn-loader {
            opacity: 1;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Divider */
        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #14425d;
        }

        .divider span {
            background: #14425d;
            padding: 0 16px;
            color: #718096;
            font-size: 14px;
        }

        /* Social Login */
        .social-login {
            margin-bottom: 24px;
        }

        .social-btn {
            width: 100%;
            padding: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            color: #4a5568;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .social-btn:hover {
            border-color: #14425d;
            background: #f7fafc;
            transform: translateY(-1px);
        }

        .google-btn i {
            color: #ea4335;
            font-size: 18px;
        }

        /* Register Section */
        .register-section {
            text-align: center;
            color: #718096;
            font-size: 14px;
        }

        .register-link {
            color: #14425d;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .register-link:hover {
            color: #14425d;
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-card {
                padding: 30px 20px;
                margin: 10px;
            }

            .welcome-text {
                font-size: 24px;
            }

            .logo-image {
                height: 60px;
            }
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 24px 16px;
            }

            .welcome-text {
                font-size: 20px;
            }

            .form-input {
                padding: 14px 14px 14px 45px;
                font-size: 14px;
            }

            .input-icon {
                font-size: 16px;
                left: 14px;
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

            // Password toggle
            $('#togglePassword').on('click', function() {
                const passwordInput = $('#password');
                const icon = $(this).find('i');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Form submission with loading state
            $('#registerForm').on('submit', function() {
                const btn = $('#registerBtn');
                const btnText = $('.btn-text');

                // Add loading state
                btn.addClass('loading');
                btnText.text('Đang đăng ký...');

                // Re-enable after 3 seconds if no response
                setTimeout(() => {
                    if (btn.hasClass('loading')) {
                        btn.removeClass('loading');
                        btnText.text('Đăng ký');
                    }
                }, 3000);
            });

            // Input focus effects
            $('.form-input').on('focus', function() {
                $(this).parent().addClass('focused');
            }).on('blur', function() {
                $(this).parent().removeClass('focused');
            });

            // Add floating animation to shapes
            $('.shape').each(function(index) {
                $(this).css({
                    'animation-delay': (index * 0.5) + 's',
                    'animation-duration': (6 + index) + 's'
                });
            });
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
