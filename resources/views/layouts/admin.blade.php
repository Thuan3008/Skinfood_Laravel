<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('legacy/Content/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/site-overrides.css') }}">
    @stack('styles')
</head>
<body class="admin-shell">
<div class="admin-wrapper">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand"><i class="fa-solid fa-user-gear"></i><span>ADMIN PANEL</span></div>
        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a href="{{ route('admin.bao-cao.doanh-thu') }}" class="{{ request()->routeIs('admin.bao-cao.*') ? 'active' : '' }}"><i class="fa-solid fa-chart-column"></i> Báo cáo doanh thu</a>
            <div class="admin-nav-heading">Kho hàng</div>
            <a href="{{ route('admin.products') }}" class="{{ request()->routeIs('admin.products*') ? 'active' : '' }}"><i class="fa-solid fa-box-open"></i> Sản phẩm</a>
            <a href="{{ route('admin.danh-muc.index') }}" class="{{ request()->routeIs('admin.danh-muc.*') ? 'active' : '' }}"><i class="fa-solid fa-list"></i> Danh mục</a>
            <a href="{{ route('admin.loai-sp.index') }}" class="{{ request()->routeIs('admin.loai-sp.*') ? 'active' : '' }}"><i class="fa-solid fa-layer-group"></i> Loại sản phẩm</a>
            <a href="{{ route('admin.thuong-hieu.index') }}" class="{{ request()->routeIs('admin.thuong-hieu.*') ? 'active' : '' }}"><i class="fa-solid fa-tags"></i> Thương hiệu</a>
            <a href="{{ route('admin.nha-cung-cap.index') }}" class="{{ request()->routeIs('admin.nha-cung-cap.*') ? 'active' : '' }}"><i class="fa-solid fa-truck-field"></i> Nhà cung cấp</a>
            <a href="{{ route('admin.phieu-nhap.index') }}" class="{{ request()->routeIs('admin.phieu-nhap.*') ? 'active' : '' }}"><i class="fa-solid fa-file-import"></i> Nhập hàng</a>
            <a href="{{ route('admin.phieu-xuat.index') }}" class="{{ request()->routeIs('admin.phieu-xuat.*') ? 'active' : '' }}"><i class="fa-solid fa-file-export"></i> Xuất kho</a>
            <div class="admin-nav-heading">Kinh doanh</div>
            <a href="{{ route('admin.orders') }}" class="{{ request()->routeIs('admin.orders*') ? 'active' : '' }}"><i class="fa-solid fa-receipt"></i> Đơn hàng</a>
            <a href="{{ route('admin.khuyen-mai.index') }}" class="{{ request()->routeIs('admin.khuyen-mai.*') ? 'active' : '' }}"><i class="fa-solid fa-percent"></i> Khuyến mãi</a>
            <a href="{{ route('admin.reviews') }}" class="{{ request()->routeIs('admin.reviews*') ? 'active' : '' }}"><i class="fa-solid fa-star"></i> Đánh giá</a>
            <a href="{{ route('admin.lien-he.index') }}" class="{{ request()->routeIs('admin.lien-he.*') ? 'active' : '' }}"><i class="fa-solid fa-envelope"></i> Liên hệ</a>
            <div class="admin-nav-heading">Tài khoản</div>
            <a href="{{ route('admin.nguoi-dung.index') }}" class="{{ request()->routeIs('admin.nguoi-dung.*') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Người dùng</a>
            <a href="{{ route('admin.tai-khoan.index') }}" class="{{ request()->routeIs('admin.tai-khoan.*') ? 'active' : '' }}"><i class="fa-solid fa-id-card"></i> Tài khoản</a>
        </nav>
        <div class="admin-sidebar-footer">
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm w-100 mb-2"><i class="fa-solid fa-store"></i> Về cửa hàng</a>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-danger btn-sm w-100"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</button></form>
        </div>
    </aside>
    <main class="admin-main">
        <header class="admin-topbar">
            <button type="button" class="btn btn-outline-dark" id="adminSidebarToggle" aria-label="Ẩn hiện menu"><i class="fa-solid fa-bars"></i></button>
            <div class="ms-auto d-flex align-items-center gap-3"><a href="{{ route('home') }}" class="text-decoration-none text-secondary"><i class="fa-solid fa-arrow-up-right-from-square"></i> Xem cửa hàng</a><span class="text-secondary">Xin chào, <strong>{{ auth()->user()->TenDangNhap ?? 'Admin' }}</strong></span><span class="admin-avatar"><i class="fa-solid fa-user"></i></span></div>
        </header>
        <section class="admin-content">@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif @yield('content')</section>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>document.getElementById('adminSidebarToggle')?.addEventListener('click',()=>document.body.classList.toggle('admin-sidebar-collapsed'));</script>
@stack('scripts')
</body>
</html>
