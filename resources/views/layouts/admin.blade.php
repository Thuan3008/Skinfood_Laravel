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
    <link rel="stylesheet" href="{{ asset('css/contact-chat.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-records.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-premium.css') }}">
    <script>if(window.self !== window.top) document.documentElement.classList.add('admin-embedded');</script>
</head>
<body class="admin-shell">
<div class="admin-wrapper">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-brand"><span class="admin-brand-mark"><i class="fa-solid fa-seedling" aria-hidden="true"></i></span><span>SKINFOOD<small>QUẢN TRỊ CỬA HÀNG</small></span></div>
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
            @include('admin.partials.breadcrumb')
            <div class="ms-auto d-flex align-items-center gap-3"><a href="{{ route('home') }}" class="text-decoration-none text-secondary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Xem cửa hàng</a><span class="text-secondary admin-greeting">Xin chào, <strong>{{ auth()->user()->TenDangNhap ?? 'Admin' }}</strong></span><span class="admin-avatar"><i class="fa-regular fa-user"></i></span></div>
        </header>
        <section class="admin-content">@yield('content')</section>
    </main>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>document.getElementById('adminSidebarToggle')?.addEventListener('click',()=>document.body.classList.toggle('admin-sidebar-collapsed'));</script>
@include('partials.notifications')
@stack('scripts')
<dialog id="adminConfirmDialog" class="admin-confirm-dialog" aria-labelledby="adminConfirmTitle">
    <span class="admin-confirm-icon"><i class="fa-solid fa-check" aria-hidden="true"></i></span>
    <h2 id="adminConfirmTitle">Xác nhận thao tác</h2><p data-confirm-message></p>
    <div class="admin-confirm-actions"><button type="button" class="btn btn-outline-secondary" data-confirm-cancel>Hủy bỏ</button><button type="button" class="btn btn-primary" data-confirm-accept>Xác nhận</button></div>
</dialog>
<script src="{{ asset('js/admin-ui.js') }}"></script>
<script src="{{ asset('js/action-confirmation.js') }}"></script>
<div class="modal fade" id="adminRecordModal" tabindex="-1" aria-labelledby="adminRecordModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-lg-down"><div class="modal-content">
        <div class="modal-header"><h2 class="modal-title fs-5" id="adminRecordModalTitle">Thông tin</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button></div>
        <div class="modal-body p-0"><div class="admin-popup-loading" id="adminPopupLoading" hidden role="status"><span class="spinner-border spinner-border-sm" aria-hidden="true"></span>Đang tải thông tin…</div><iframe id="adminRecordFrame" title="Thông tin quản trị"></iframe></div>
    </div></div>
</div>
<script src="{{ asset('js/admin-records.js') }}"></script>
<script src="{{ asset('js/table-alignment.js') }}"></script>
</body>
</html>
