@extends('Frontend.layouts.default')

@section('title', 'Đăng nhập')
@section('description', 'Đăng nhập AKAY TRUYỆN')
@section('keywords', 'Đăng nhập AKAY TRUYỆN')

@push('custom_schema')
{!! SEO::generate() !!}
@endpush

@section('content')
<style>
    /* Reset & Base */
    .forgot-container {
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

    /* Forgot Password Card */
    .forgot-wrapper {
        position: relative;
        z-index: 2;
        width: 100%;
        max-width: 450px;
    }

    .forgot-card {
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
        background: linear-gradient(135deg, #14425d, #667eea);
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
        width: 100%;
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

    /* Special padding for password input to accommodate eye icon */
    .input-wrapper:has(.password-toggle) .form-input {
        padding-right: 45px;
    }

    /* Fallback for browsers that don't support :has() */
    .form-input[type="password"] {
        padding-right: 45px;
    }

    .form-input:focus {
        border-color: #14425d;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-input:focus + .input-icon {
        color: #14425d;
    }

    .form-input.error {
        border-color: #e53e3e;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% {
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
        background: linear-gradient(135deg, #667eea, #14425d);
        transition: width 0.3s ease;
    }

    .form-input:focus ~ .input-line {
        width: 100%;
    }

    .password-toggle {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #a0aec0;
        font-size: 14px;
        cursor: pointer;
        transition: color 0.3s ease;
        z-index: 2;
        padding: 4px;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
    }

    .password-toggle:hover {
        color: #14425d;
        background: rgba(102, 126, 234, 0.1);
    }

    .password-toggle i {
        font-size: 14px;
        line-height: 1;
        display: block;
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

    /* Submit Button */
    .submit-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #667eea, #14425d);
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
        display: block;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .submit-btn.loading {
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

    .submit-btn.loading .btn-text {
        opacity: 0;
    }

    .submit-btn.loading .btn-loader {
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

    /* OTP Input Styling */
    .otp-container {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin: 20px 0;
        flex-wrap: wrap;
    }

    .otp-input {
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 24px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
        outline: none;
    }

    .otp-input:focus {
        border-color: #14425d;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: scale(1.05);
    }

    .otp-input.error {
        border-color: #e53e3e;
        animation: shake 0.5s ease-in-out;
    }

    .invalid-otp {
        color: #e53e3e;
        font-size: 14px;
        margin-top: 8px;
        text-align: center;
        animation: fadeIn 0.3s ease;
    }

    /* Button Container */
    .forgot-container #buttonContainer {
        width: 100%;
        margin-bottom: 24px;
        display: block;
    }

    /* Ensure button is visible */
    .forgot-container #buttonContainer button {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Login Link Section */
    .login-section {
        text-align: center;
        color: #718096;
        font-size: 14px;
    }

    .login-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .login-link:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .forgot-card {
            padding: 30px 20px;
            margin: 10px;
        }

        .welcome-text {
            font-size: 24px;
        }

        .logo-image {
            height: 60px;
        }

        .otp-container {
            gap: 8px;
        }

        .otp-input {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }
    }

    @media (max-width: 480px) {
        .forgot-card {
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

        .password-toggle {
            right: 10px;
            font-size: 12px;
            width: 20px;
            height: 20px;
            padding: 3px;
        }

        .password-toggle i {
            font-size: 12px;
        }

        .otp-input {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }
    }

    /* Cursor pointer utility */
    .cursor-pointer {
        cursor: pointer;
    }

    /* Avatar preview hover effect */
    .avatar-preview:hover {
        border-color: #0d6efd !important;
        opacity: 0.8;
    }

    /* Additional form styling for forgot password form only */
    .forgot-container .form-floating {
        position: relative;
    }

    .forgot-container .form-control {
        width: 100%;
        padding: 16px 16px 16px 50px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
        outline: none;
    }

    /* Special padding for password input with eye icon */
    .forgot-container .form-control[type="password"] {
        padding-right: 45px;
    }

    /* Ensure password toggle is properly positioned in form-control */
    .forgot-container .form-floating .password-toggle {
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
    }

    .forgot-container .form-control:focus {
        border-color: #14425d;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .forgot-container .form-control.is-invalid {
        border-color: #e53e3e;
        animation: shake 0.5s ease-in-out;
    }

    .forgot-container .invalid-feedback {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #e53e3e;
        font-size: 14px;
        margin-top: 8px;
        animation: fadeIn 0.3s ease;
    }

    /* Button styling for forgot password form only */
    .forgot-container .btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #667eea, #14425d);
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
        display: block;
    }

    .forgot-container .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .forgot-container .btn:active {
        transform: translateY(0);
    }

    /* Ensure all submit buttons in forgot password form have proper styling */
    .forgot-container button[type="submit"],
    .forgot-container button[type="button"] {
        padding: 16px;
        background: linear-gradient(135deg, #667eea, #14425d);
        border: none;
        border-radius: 12px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        display: flex;
    }

    .forgot-container button[type="submit"]:hover,
    .forgot-container button[type="button"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .forgot-container button[type="submit"]:active,
    .forgot-container button[type="button"]:active {
        transform: translateY(0);
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Loading state for buttons */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top: 2px solid white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }
</style>

    <div class="forgot-container">
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

        <!-- Main Forgot Password Form -->
        <div class="forgot-wrapper">
            <div class="forgot-card">
                <!-- Logo Section -->
                <div class="logo-section">
                    <div class="logo-container">
                        <a href="{{ route('home') }}" class="logo-link">
                            <img src="{{ asset('images/logo/Logoakay.png') }}" alt="AKAY TRUYỆN" class="logo-image">
                        </a>
                    </div>
                    <h1 class="welcome-text">Bạn quên mật khẩu rồi à?</h1>
                    <p class="welcome-subtitle">Đừng lo lắng, chúng tôi sẽ giúp bạn lấy lại mật khẩu</p>
                </div>

                <!-- Forgot Password Form -->
                <form id="forgotForm">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" class="form-input @error('email') error @enderror" name="email" id="email" placeholder="Nhập email của bạn" value="{{ old('email') }}" required>
                            <div class="input-line"></div>
                        </div>
                        @error('email')
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div id="otpContainer" class="overflow-hidden text-center"></div>
                    <div id="passwordContainer"></div>

                    <div id="buttonContainer">
                        <button class="submit-btn" type="submit" id="btn-send">
                            <span class="btn-text">Tiếp tục</span>
                            <div class="btn-loader">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    </div>
                </form>

                <!-- Login Link -->
                <div class="login-section">
                    <p>Bạn đã nhớ mật khẩu?
                        <a href="{{ route('login') }}" class="login-link">
                            Đăng nhập
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#forgotForm').on('submit', function(e) {
                e.preventDefault();
                const emailInput = $('#email');
                const email = emailInput.val();
                const submitButton = $('#btn-send');

                // Xóa thông báo lỗi cũ nếu tồn tại
                const oldInvalidFeedback = emailInput.parent().find('.error');
                emailInput.removeClass('error');
                if (oldInvalidFeedback.length) {
                    oldInvalidFeedback.remove();
                }

                // Thay đổi nút submit thành trạng thái loading
                submitButton.prop('disabled', true);
                submitButton.addClass('loading');
                submitButton.find('.btn-text').text('Đang xử lý...');

                $.ajax({
                    url: '{{ route('forgot.password') }}',
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: JSON.stringify({
                        email: email
                    }),
                    success: function(response) {

                        if (response.status === 'success') {
                            showToast(response.message, 'success');
                            // Không cần xóa submitButton nữa vì chúng ta sẽ thay thế nội dung của buttonContainer

                            $('.form-group').remove();

                            $('#otpContainer').html(`
                        <span class="text-center mb-1">${response.message}</span>
                        <div class="otp-container justify-content-center mb-3" id="input-otp">
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <input type="text" maxlength="1" class="otp-input" oninput="handleInput(this)" />
                            <br>
                        </div>
                    `);

                            $('#buttonContainer').html(`
                        <button class="submit-btn" type="button" id="submitOtp">
                            <span class="btn-text">Tiếp tục</span>
                            <div class="btn-loader">
                                <div class="spinner"></div>
                            </div>
                        </button>
                    `);

                            // Debug: Kiểm tra xem button có được tạo không
                            setTimeout(() => {
                                console.log('Button created:', $('#submitOtp').length);
                                console.log('Button visible:', $('#submitOtp').is(':visible'));
                            }, 100);

                            // Add event handler for the new OTP submit button
                            $(document).on('click', '#submitOtp', function() {
                                console.log('OTP button clicked!');
                                const otpInputs = $('.otp-input');
                                const input_otp = $('#input-otp');
                                const submitOtpBtn = $(this);

                                let otp = '';
                                otpInputs.each(function() {
                                    otp += $(this).val();
                                });

                                input_otp.find('.invalid-otp').remove();

                                const oldInvalidFeedbackEmail = emailInput.parent().find('.error');
                                emailInput.removeClass('error');
                                if (oldInvalidFeedbackEmail.length) {
                                    oldInvalidFeedbackEmail.remove();
                                }

                                // Add loading state
                                submitOtpBtn.prop('disabled', true);
                                submitOtpBtn.addClass('loading');
                                submitOtpBtn.find('.btn-text').text('Đang xử lý...');

                                $.ajax({
                                    url: '{{ route('forgot.password') }}',
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    data: JSON.stringify({
                                        email: email,
                                        otp: otp,
                                    }),
                                    success: function(response) {

                                        if (response.status === 'success') {
                                            showToast(response.message, 'success');
                                            // Không cần xóa button nữa vì chúng ta sẽ thay thế nội dung của buttonContainer
                                            $('#otpContainer').remove();

                                                $('#passwordContainer').html(`
                                                <span class="text-center mb-1">${response.message}</span>
                                                <div class="form-group">
                                                    <div class="input-wrapper">
                                                        <div class="input-icon">
                                                            <i class="fas fa-lock"></i>
                                                        </div>
                                                        <input type="password" class="form-input" name="password" id="password" placeholder="Nhập mật khẩu mới" required>
                                                        <div class="input-line"></div>
                                                        <button type="button" class="password-toggle" id="togglePassword">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            `);

                                                    $('#buttonContainer').html(`
                                                <button class="submit-btn" type="button" id="submitPassword">
                                                    <span class="btn-text">Xác nhận</span>
                                                    <div class="btn-loader">
                                                        <div class="spinner"></div>
                                                    </div>
                                                </button>
                                            `);

                                            // Add event handler for the new password submit button
                                            $(document).on('click', '#submitPassword', function() {
                                                const passwordInput = $('#password');
                                                const password = passwordInput.val();
                                                const submitPasswordBtn = $(this);

                                                const oldInvalidFeedback = passwordInput.parent().find('.error');
                                                passwordInput.removeClass('error');
                                                if (oldInvalidFeedback.length) {
                                                    oldInvalidFeedback.remove();
                                                }

                                                // Add loading state
                                                submitPasswordBtn.prop('disabled', true);
                                                submitPasswordBtn.addClass('loading');
                                                submitPasswordBtn.find('.btn-text').text('Đang xử lý...');

                                                $.ajax({
                                                    url: '{{ route('forgot.password') }}',
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                    },
                                                    data: JSON.stringify({
                                                        email: email,
                                                        otp: otp,
                                                        password: password
                                                    }),
                                                    success: function(response) {
                                                        if (response.status === 'success') {
                                                            showToast(response.message,
                                                                'success');
                                                            saveToast(response.message,
                                                                response.status);
                                                            window.location.href = response
                                                                .url;
                                                        } else {
                                                            showToast(response.message, 'error');
                                                            submitPasswordBtn.prop('disabled', false);
                                                            submitPasswordBtn.removeClass('loading');
                                                            submitPasswordBtn.find('.btn-text').text('Xác nhận');
                                                        }
                                                    },
                                                    error: function(xhr) {
                                                        const response = xhr.responseJSON;
                                                        console.log('Error2:', response);

                                                        if (response && response.status === 'error') {
                                                            if (response.message.password) {
                                                                response.message.password.forEach(error => {
                                                                    const invalidFeedback = $('<div class="error"></div>').text(error);
                                                                    passwordInput.addClass('error').parent().append(invalidFeedback);
                                                                });
                                                            }
                                                        } else {
                                                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                                                        }
                                                        submitPasswordBtn.prop('disabled', false);
                                                        submitPasswordBtn.removeClass('loading');
                                                        submitPasswordBtn.find('.btn-text').text('Xác nhận');
                                                    }
                                                });
                                            });


                                        } else {
                                            showToast(response.message, 'error');
                                            submitOtpBtn.prop('disabled', false);
                                            submitOtpBtn.removeClass('loading');
                                            submitOtpBtn.find('.btn-text').text('Tiếp tục');
                                        }
                                    },
                                    error: function(xhr) {
                                        const response = xhr.responseJSON;
                                        console.log('Error2:', response);

                                        if (response && response.status ===
                                            'error') {
                                            if (response.message.email) {
                                                response.message.email.forEach(error => {
                                                    const invalidFeedback = $('<div class="error"></div>').text(error);
                                                    emailInput.addClass('error').parent().append(invalidFeedback);
                                                });
                                            }
                                            if (response.message.otp) {
                                                input_otp.append(`<div class="invalid-otp text-danger fs-7">${response.message.otp[0]}</div>`);
                                            }
                                        } else {
                                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                                        }
                                        submitOtpBtn.prop('disabled', false);
                                        submitOtpBtn.removeClass('loading');
                                        submitOtpBtn.find('.btn-text').text('Tiếp tục');
                                    }
                                });
                            });

                        } else {
                            showToast(response.message, 'error');
                            submitButton.prop('disabled', false);
                            submitButton.removeClass('loading');
                            submitButton.find('.btn-text').text('Tiếp tục');
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
console.log('Error1:', response);

                        if (response && response.message && response.message.email) {
                            response.message.email.forEach(error => {
                                const invalidFeedback = $('<div class="error"></div>').text(error);
                                emailInput.addClass('error').parent().append(invalidFeedback);
                            });
                        } else {
                            showToast('Đã xảy ra lỗi, vui lòng thử lại.', 'error');
                        }
                        submitButton.prop('disabled', false);
                        submitButton.removeClass('loading');
                        submitButton.find('.btn-text').text('Tiếp tục');
                    }
                });
            });
        });

        //hidden password

$(document).on('click', '#togglePassword', function () {
    const passwordField = $('#password')
    const icon = $(this).find('i')
    const type = passwordField.attr('type') === 'password' ? 'text' : 'password'
    passwordField.attr('type', type)

    if (type === 'text') {
        icon.removeClass('fa-eye').addClass('fa-eye-slash')
    } else {
        icon.removeClass('fa-eye-slash').addClass('fa-eye')
    }
})

//hidden password confirm

$(document).on('click', '#togglePasswordConfirm', function () {
    const passwordField = $('#password_confirmation')
    const type = passwordField.attr('type') === 'password' ? 'text' : 'password'
    passwordField.attr('type', type)

    $(this).toggleClass('fa-eye fa-eye-slash')
})

//toast
function showToast(message, status) {
    document.addEventListener('DOMContentLoaded', function() {
        const toastElement = document.getElementById('liveToast');
        if (!toastElement) return;

        const toastBody = toastElement.querySelector('.toast-body');
        if (!toastBody) return;

        // Update message
        toastBody.textContent = message;

        // Remove existing classes
        toastElement.classList.remove('bg-success', 'bg-danger', 'text-white');

        // Add new classes based on status
        if (status === 'success') {
            toastElement.classList.add('bg-success', 'text-white');
        } else if (status === 'error') {
            toastElement.classList.add('bg-danger', 'text-white');
        }

        // Initialize and show toast
        const bsToast = new bootstrap.Toast(toastElement);
        bsToast.show();
    });
}

//save toast

function saveToast (message, status) {
    sessionStorage.setItem('toastMessage', message)
    sessionStorage.setItem('toastStatus', status)
}

//show toast
function showSavedToast() {
    const message = sessionStorage.getItem('toastMessage');
    const status = sessionStorage.getItem('toastStatus');

    if (message && status) {
        showToast(message, status);
        sessionStorage.removeItem('toastMessage');
        sessionStorage.removeItem('toastStatus');
    }
}

//otp

function handleInput(element) {
    $(element).val(
        $(element).val().replace(/[^0-9]/g, '')
    );

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
            // If current input has value, clear it
            $currentInput.val('');
        } else if ($prevInput.length) {
            // If current input is empty, move to previous input and clear it
            $prevInput.val('').focus();
        }
    }
});

$(document).on('input', '.otp-input', function() {
    const $this = $(this);
    const maxLength = parseInt($this.attr('maxlength'));

    if ($this.val().length > maxLength) {
        $this.val($this.val().slice(0, maxLength));
    }

    // Move to next input if value is entered
    if ($this.val().length === maxLength) {
        const $nextInput = $this.next('.otp-input');
        if ($nextInput.length) {
            $nextInput.focus();
        }
    }
});

    </script>

@endpush
