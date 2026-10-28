@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="container my-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mx-auto mb-4" style="max-width: 700px;" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mx-auto mb-4" style="max-width: 700px;" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $user = optional($account->nguoiDung);
        $avatarName = $user->Avatar ?? '1.jpg';
        $avatarPath = asset('legacy/Content/avatars/' . $avatarName);
    @endphp

    <div class="card shadow-lg mx-auto border-0" style="max-width: 700px; border-radius: 15px; overflow: hidden;">

        <!-- Card Header Gradient -->
        <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="mb-3">
                <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center text-primary shadow" style="width: 100px; height: 100px; overflow: hidden; border: 3px solid rgba(255,255,255,0.8);">
                    <img src="{{ $avatarPath }}" alt="avatar" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->HoTen ?? $account->TenDangNhap) }}&background=random'" />
                </div>
            </div>
            <h3 class="fw-bold mb-1">{{ $user->HoTen ?? 'Chưa cập nhật tên' }}</h3>
            <span class="badge bg-light text-dark px-3 py-2 mt-1 fs-6">{{ $account->VaiTro ?? 'Khách hàng' }}</span>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4 p-md-5">

            <!-- Thông tin cá nhân -->
            <h5 class="text-muted mb-4 border-bottom pb-2">
                <i class="fas fa-id-card me-2 text-primary"></i>Thông tin cá nhân
            </h5>

            <div class="row mb-3 align-items-center">
                <div class="col-sm-4 text-muted">
                    <i class="fas fa-phone-alt me-2 text-info"></i> Số điện thoại
                </div>
                <div class="col-sm-8 fw-bold">
                    {{ $user->SoDienThoai ?: 'Chưa cập nhật' }}
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <div class="col-sm-4 text-muted">
                    <i class="fas fa-map-marker-alt me-2 text-danger"></i> Địa chỉ
                </div>
                <div class="col-sm-8">
                    {{ $user->DiaChi ?: 'Chưa cập nhật' }}
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <div class="col-sm-4 text-muted">
                    <i class="fas fa-venus-mars me-2 text-warning"></i> Giới tính
                </div>
                <div class="col-sm-8">
                    {{ $user->GioiTinh ?: 'Bí mật' }}
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <div class="col-sm-4 text-muted">
                    <i class="fas fa-birthday-cake me-2 text-primary"></i> Ngày sinh
                </div>
                <div class="col-sm-8">
                    {{ $user->NgaySinh ? \Carbon\Carbon::parse($user->NgaySinh)->format('d/m/Y') : 'Chưa cập nhật' }}
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <div class="col-sm-4 text-muted">
                    <i class="fas fa-user-circle me-2 text-secondary"></i> Username / Email
                </div>
                <div class="col-sm-8">
                    {{ $account->TenDangNhap }}
                </div>
            </div>

            <!-- Khối form chỉnh sửa (Collapse) -->
            <div class="collapse mt-4" id="editProfileBox">
                <div class="p-4 rounded border bg-light">
                    <h5 class="fw-bold mb-3 text-secondary">
                        <i class="fas fa-user-edit me-2"></i> Cập nhật thông tin
                    </h5>
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Họ và tên</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-user text-primary"></i></span>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->HoTen) }}" placeholder="Nhập họ tên của bạn" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-secondary">Số điện thoại</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-phone-alt text-info"></i></span>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->SoDienThoai) }}" placeholder="09xxxxxxxx">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-secondary">Ngày sinh</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-birthday-cake text-danger"></i></span>
                                    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $user->NgaySinh ? \Carbon\Carbon::parse($user->NgaySinh)->format('Y-m-d') : '') }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Giới tính</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-venus-mars text-warning"></i></span>
                                <select name="gender" class="form-select">
                                    <option value="Nam" {{ (old('gender', $user->GioiTinh) == 'Nam') ? 'selected' : '' }}>Nam</option>
                                    <option value="Nữ" {{ (old('gender', $user->GioiTinh) == 'Nữ') ? 'selected' : '' }}>Nữ</option>
                                    <option value="Khác" {{ (old('gender', $user->GioiTinh) == 'Khác') ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Địa chỉ nhận hàng</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-success"></i></span>
                                <textarea name="address" class="form-control" rows="3" placeholder="Số nhà, tên đường, phường/xã...">{{ old('address', $user->DiaChi) }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-outline-secondary px-3" data-bs-toggle="collapse" data-bs-target="#editProfileBox">
                                Hủy
                            </button>
                            <button type="submit" class="btn text-white px-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="fas fa-save me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4">

            <!-- Các nút hành động chính -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <a href="{{ route('orders.index') }}" class="btn btn-info text-white w-100 py-2">
                    <i class="fas fa-shopping-bag me-1"></i> Đơn hàng của tôi
                </a>

                <button type="button" class="btn btn-outline-primary w-100 py-2" data-bs-toggle="collapse" data-bs-target="#editProfileBox">
                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                </button>

                <a href="{{ route('logout') }}" class="btn btn-outline-danger w-100 py-2" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="post" style="display: none;">
                    @csrf
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
