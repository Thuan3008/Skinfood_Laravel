<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - SkinFood Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="{{ asset('legacy/CSS/SaoRoi.css') }}" rel="stylesheet">
</head>
<body>

    <!-- Hiệu ứng sao rơi ban đêm -->
    <div class="night">
        @for($i = 0; $i < 60; $i++)
            <div class="shooting_star"></div>
        @endfor
    </div>

    <!-- Khung đăng nhập kính mờ -->
    <div class="login-wrapper">

        <form method="post" action="{{ route('login.authenticate') }}">
            @csrf

            <div class="card shadow-lg p-4" style="width: 380px;">
                <h3 class="card-title text-center fw-bold text-primary mb-4">Đăng nhập</h3>

                <div class="mb-3">
                    <label class="form-label" style="color: white">Email / Tên đăng nhập</label>
                    <input type="text" name="Email" value="{{ old('Email') }}" placeholder="Nhập email hoặc tên đăng nhập" class="form-control" required autofocus />
                </div>

                <div class="mb-3">
                    <label class="form-label" style="color: white">Mật khẩu</label>
                    <input type="password" name="MatKhau" placeholder="Nhập mật khẩu" class="form-control" required />
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">ĐĂNG NHẬP</button>

                <p class="text-center mb-0">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="text-decoration-none fw-bold text-info">
                        Đăng ký ngay
                    </a>
                </p>

                <div class="text-center mt-3">
                    <a href="{{ route('home') }}" class="text-white-50 text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i>Về trang chủ
                    </a>
                </div>
            </div>
        </form>
    </div>

@include('partials.notifications')
</body>
</html>
