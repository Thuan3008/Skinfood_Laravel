<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng ký tài khoản - SkinFood Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="{{ asset('legacy/CSS/SaoRoi.css') }}" rel="stylesheet" />

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0b0f19;
            color: #e2e8f0;
            height: 100vh;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .register-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .glass-panel {
            background: rgba(30, 41, 59, 0.45);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 28px 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            max-height: 92vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.2) transparent;
        }

        .glass-panel::-webkit-scrollbar {
            width: 4px;
        }

        .glass-panel::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 4px;
        }

        .header-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .header-title h4 {
            font-weight: 600;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            color: #fff;
            margin-bottom: 5px;
        }

        .header-title p {
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 0;
        }

        .form-floating {
            margin-bottom: 12px;
        }

        .form-control, .form-select {
            background: rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #fff !important;
            font-size: 0.9rem;
            height: 50px;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(0, 0, 0, 0.45) !important;
            border-color: #38bdf8;
            box-shadow: none;
        }

        .form-floating label {
            color: #94a3b8;
            font-size: 0.85rem;
            padding-top: 0.85rem;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            opacity: 0.85;
            transform: scale(0.85) translateY(-0.7rem) translateX(0.15rem);
            color: #38bdf8;
        }

        .avatar-scroll {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 15px 0;
            padding: 5px 0;
        }

        .avatar-item {
            cursor: pointer;
            opacity: 0.55;
            transition: all 0.3s ease;
            transform: scale(0.9);
        }

        .avatar-item img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid transparent;
            object-fit: cover;
        }

        .avatar-item:hover {
            opacity: 1;
            transform: scale(1);
        }

        .avatar-item.active {
            opacity: 1;
            transform: scale(1.15);
        }

        .avatar-item.active img {
            border-color: #38bdf8;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.5);
        }

        .btn-submit {
            background: #38bdf8;
            color: #0f172a;
            font-weight: 600;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            transition: all 0.2s;
            margin-top: 8px;
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #7dd3fc;
            transform: translateY(-1px);
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .login-link a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 30px #161e2e inset !important;
            -webkit-text-fill-color: white !important;
        }
        .invalid-feedback { display: block; color: #ffb4b4; font-size: .8rem; text-align: left; }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #ff8585 !important; }
        .password-help { color: #cbd5e1; font-size: .78rem; margin: -6px 0 12px; }
    </style>
</head>
<body>

    <!-- Hiệu ứng sao rơi ban đêm -->
    <div class="night">
        @for($i = 0; $i < 40; $i++)
            <div class="shooting_star"></div>
        @endfor
    </div>

    <div class="register-container">
        <form method="post" action="{{ route('register.store') }}" id="registerForm">
            @csrf

            <div class="glass-panel">

                <div class="header-title">
                    <h4>Tạo Tài Khoản</h4>
                    <p>Nhập thông tin để bắt đầu hành trình mua sắm</p>
                </div>

                <div class="row g-2">
                    <div class="col-7">
                        <div class="form-floating">
                            <input type="text" name="HoTen" id="HoTen" value="{{ old('HoTen') }}" required minlength="2" maxlength="100" autocomplete="name" class="form-control @error('HoTen') is-invalid @enderror" placeholder="Họ tên" aria-describedby="HoTen-error" />
                            <label for="HoTen">Họ và tên</label>
                        </div>
                        @error('HoTen')<div id="HoTen-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-5">
                        <div class="form-floating">
                            <select name="GioiTinh" id="GioiTinh" class="form-select ps-3 @error('GioiTinh') is-invalid @enderror" aria-describedby="GioiTinh-error">
                                <option value="Nam" {{ old('GioiTinh', 'Nam') == 'Nam' ? 'selected' : '' }}>Nam</option>
                                <option value="Nữ" {{ old('GioiTinh') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                                <option value="Khác" {{ old('GioiTinh') == 'Khác' ? 'selected' : '' }}>Khác</option>
                            </select>
                            <label for="GioiTinh">Giới tính</label>
                        </div>
                        @error('GioiTinh')<div id="GioiTinh-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="date" name="NgaySinh" id="NgaySinh" value="{{ old('NgaySinh') }}" max="{{ now()->toDateString() }}" autocomplete="bday" class="form-control @error('NgaySinh') is-invalid @enderror" placeholder="Ngày sinh" aria-describedby="NgaySinh-error" />
                            <label for="NgaySinh">Ngày sinh</label>
                        </div>
                        @error('NgaySinh')<div id="NgaySinh-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-6">
                        <div class="form-floating">
                            <input type="tel" name="SoDienThoai" id="SoDienThoai" value="{{ old('SoDienThoai') }}" maxlength="12" pattern="(0|\+84)[35789][0-9]{8}" title="Số di động Việt Nam, ví dụ 0901234567 hoặc +84901234567" autocomplete="tel" class="form-control @error('SoDienThoai') is-invalid @enderror" placeholder="SĐT" aria-describedby="SoDienThoai-error" />
                            <label for="SoDienThoai">Điện thoại</label>
                        </div>
                        @error('SoDienThoai')<div id="SoDienThoai-error" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-floating">
                    <input type="text" name="DiaChi" id="DiaChi" value="{{ old('DiaChi') }}" maxlength="255" autocomplete="street-address" class="form-control @error('DiaChi') is-invalid @enderror" placeholder="Địa chỉ" aria-describedby="DiaChi-error" />
                    <label for="DiaChi">Địa chỉ</label>
                </div>
                @error('DiaChi')<div id="DiaChi-error" class="invalid-feedback">{{ $message }}</div>@enderror

                <div class="my-2 border-bottom border-secondary opacity-25"></div>

                <div class="form-floating">
                    <input type="email" name="TenDangNhap" id="TenDangNhap" value="{{ old('TenDangNhap') }}" required maxlength="100" autocomplete="username" class="form-control @error('TenDangNhap') is-invalid @enderror" placeholder="Email" aria-describedby="TenDangNhap-error" />
                    <label for="TenDangNhap">Email đăng nhập</label>
                </div>
                @error('TenDangNhap')<div id="TenDangNhap-error" class="invalid-feedback">{{ $message }}</div>@enderror

                <div class="form-floating">
                    <input type="password" name="MatKhau" id="MatKhau" required minlength="8" maxlength="72" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{8,72}" title="Từ 8–72 ký tự, gồm chữ hoa, chữ thường và số" autocomplete="new-password" class="form-control @error('MatKhau') is-invalid @enderror" placeholder="Mật khẩu" aria-describedby="password-help MatKhau-error" />
                    <label for="MatKhau">Mật khẩu</label>
                </div>
                <p class="password-help" id="password-help">Từ 8–72 ký tự, gồm chữ hoa, chữ thường và số.</p>
                @error('MatKhau')<div id="MatKhau-error" class="invalid-feedback">{{ $message }}</div>@enderror

                <div class="form-floating">
                    <input type="password" name="NhapLaiMatKhau" id="NhapLaiMatKhau" required minlength="8" maxlength="72" autocomplete="new-password" class="form-control @error('NhapLaiMatKhau') is-invalid @enderror" placeholder="Nhập lại" aria-describedby="NhapLaiMatKhau-error" />
                    <label for="NhapLaiMatKhau">Xác nhận mật khẩu</label>
                </div>
                @error('NhapLaiMatKhau')<div id="NhapLaiMatKhau-error" class="invalid-feedback">{{ $message }}</div>@enderror

                <div class="text-center mt-3 mb-1">
                    <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8;">Chọn Avatar</span>
                </div>
                <div class="avatar-scroll">
                    @foreach(['1.jpg', '2.jpg', '3.jpg', '4.jpg'] as $avatar)
                        <label class="avatar-item {{ old('Avatar', '1.jpg') === $avatar ? 'active' : '' }}">
                            <input type="radio" name="Avatar" value="{{ $avatar }}" hidden @checked(old('Avatar', '1.jpg') === $avatar) />
                            <img src="{{ asset('legacy/Content/avatars/'.$avatar) }}" alt="Avatar {{ $loop->iteration }}" />
                        </label>
                    @endforeach
                </div>
                @error('Avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror

                <button type="submit" class="btn-submit">Đăng Ký</button>

                <div class="login-link">
                    Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a>
                </div>

                <div class="text-center mt-2">
                    <a href="{{ route('home') }}" class="text-white-50 text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i>Về trang chủ
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Chọn avatar
        const items = document.querySelectorAll('.avatar-item');
        items.forEach(item => {
            item.addEventListener('click', () => {
                items.forEach(i => i.classList.remove('active'));
                item.classList.add('active');
                item.querySelector('input').checked = true;
            });
        });

        // Kiểm tra khớp mật khẩu
        (function () {
            const form = document.getElementById('registerForm');
            const pwd = document.getElementById('MatKhau');
            const confirm = document.getElementById('NhapLaiMatKhau');

            if (!form || !pwd || !confirm) return;

            function validatePasswords() {
                const p = pwd.value || '';
                const c = confirm.value || '';

                if (p === '' || c === '') {
                    confirm.setCustomValidity('');
                    return true;
                }

                if (p !== c) {
                    confirm.setCustomValidity('Mật khẩu xác nhận không khớp.');
                    return false;
                }

                confirm.setCustomValidity('');
                return true;
            }

            pwd.addEventListener('input', validatePasswords);
            confirm.addEventListener('input', validatePasswords);

            form.addEventListener('submit', function (e) {
                if (!validatePasswords()) {
                    e.preventDefault();
                    confirm.reportValidity();
                    confirm.focus();
                }
            });
        })();
    </script>
@include('partials.notifications')
</body>
</html>
