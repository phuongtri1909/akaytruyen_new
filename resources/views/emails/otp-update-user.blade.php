<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Mã OTP</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f6f8;
      color: #333;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      padding: 30px;
      text-align: center;
    }

    .logo {
      max-width: 150px;
      margin-bottom: 20px;
    }

    h2 {
      font-size: 32px;
      color: #2a9d8f;
      margin: 20px 0;
    }

    p {
      font-size: 16px;
      line-height: 1.6;
    }

    .footer {
      font-size: 14px;
      color: #999;
      margin-top: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <img src="https://akaytruyen.com/images/logo/Logoakay.png" alt="Logo Website" class="logo"> <!-- Thay link hình logo tại đây -->
    <p>Xin chào,</p>
    <p>
      Mã OTP của bạn để 
      <strong>{{ $type === 'password' ? 'đổi mật khẩu' : 'xác thực tài khoản' }}</strong> là:
    </p>
    <h2>{{ $otp }}</h2>
    <p>Mã này có hiệu lực trong 3 phút.</p>
    <div class="footer">
      © {{ date('Y') }} akaytruyen.com . All rights reserved.
    </div>
  </div>
</body>
</html>
