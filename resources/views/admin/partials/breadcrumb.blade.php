@php
    $section = match(request()->segment(2)) {
        'don-hang', 'khuyen-mai', 'danh-gia', 'lien-he' => 'Kinh doanh',
        'nguoi-dung', 'tai-khoan' => 'Tài khoản',
        'bao-cao' => 'Báo cáo',
        null => 'Tổng quan',
        default => 'Kho hàng',
    };
@endphp
<nav class="admin-header-breadcrumb" aria-label="Breadcrumb"><ol class="admin-breadcrumb"><li><a href="{{ route('admin.dashboard') }}">SkinFood</a></li><li>{{ $section }}</li><li aria-current="page">@yield('title', 'Tổng quan')</li></ol></nav>
