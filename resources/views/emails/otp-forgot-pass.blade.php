<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mã OTP Active - Đổi mật khẩu</title>
    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            text-align: center;
        }

        .logo {
            max-width: 120px;
            margin-bottom: 20px;
        }

        h1 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .otp {
            font-size: 32px;
            font-weight: bold;
            color: #2a9d8f;
            margin: 20px 0;
        }

        p {
            font-size: 16px;
            color: #4b5563;
            margin: 10px 0;
        }

        .footer {
            font-size: 14px;
            color: #9ca3af;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://akaytruyen.com/images/logo/Logoakay.png" alt="Website Logo" class="logo">
        <h1>Yêu cầu đổi mật khẩu</h1>
        <p>Mã OTP của bạn là:</p>
        <div class="otp">{{ $otp }}</div>
        <p>Mã này có hiệu lực trong 3 phút</p>
        <div class="footer">
            © {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
