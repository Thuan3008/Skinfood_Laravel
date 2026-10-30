@extends('layouts.admin')

@section('title', 'Quản trị')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
    <div class="container py-4 dashboard-container fade-in-up">
        <div class="admin-dashboard-intro">
            <div><h2>Tổng quan cửa hàng</h2><p>Theo dõi hoạt động kinh doanh và quản lý SkinFood.</p></div>
            <a href="{{ route('admin.bao-cao.doanh-thu') }}" class="btn btn-outline-primary"><i class="fa-solid fa-chart-simple me-2" aria-hidden="true"></i>Xem báo cáo doanh thu</a>
        </div>


        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card border-start-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-light-warning text-warning">
                                <i class="fas fa-dollar-sign fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Doanh Thu</div>
                                <h3 class="text-warning fw-bold mb-0">{{ number_format($stats['revenue'], 0, ',', '.') }} đ</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card border-start-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-light-success text-success">
                                <i class="fas fa-shopping-bag fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Tổng Đơn Hàng</div>
                                <h3 class="text-success fw-bold mb-0">{{ $stats['orders'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card border-start-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-light-primary text-primary">
                                <i class="fas fa-box-open fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Tổng Sản Phẩm</div>
                                <h3 class="text-primary fw-bold mb-0">{{ $stats['products'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card stat-card border-start-info h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-light-info text-info">
                                <i class="fas fa-star fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-uppercase text-muted small fw-bold mb-1">Chờ Duyệt</div>
                                <h3 class="text-info fw-bold mb-0">{{ $stats['reviews'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3 col-6">
                <div class="card shadow-sm h-100"><div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-square bg-light-primary text-primary"><i class="fas fa-folder-open"></i></div>
                    <div><div class="text-muted small">Danh mục</div><div class="fs-4 fw-bold">{{ $stats['categories'] }}</div></div>
                </div></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm h-100"><div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-square bg-light-success text-success"><i class="fas fa-layer-group"></i></div>
                    <div><div class="text-muted small">Loại sản phẩm</div><div class="fs-4 fw-bold">{{ $stats['types'] }}</div></div>
                </div></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm h-100"><div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-square bg-light-info text-info"><i class="fas fa-tags"></i></div>
                    <div><div class="text-muted small">Thương hiệu</div><div class="fs-4 fw-bold">{{ $stats['brands'] }}</div></div>
                </div></div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card shadow-sm h-100"><div class="card-body d-flex align-items-center gap-3">
                    <div class="icon-square bg-light-danger text-danger"><i class="fas fa-triangle-exclamation"></i></div>
                    <div><div class="text-muted small">Sắp hết hàng</div><div class="fs-4 fw-bold">{{ $stats['low_stock'] }}</div></div>
                </div></div>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div><h5 class="mb-1 fw-bold">Sản phẩm mới cập nhật</h5><small class="text-muted">Các sản phẩm có mã mới nhất trong kho</small></div>
                        <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-datatable">
                            <thead><tr><th>Sản phẩm</th><th>Danh mục</th><th>Giá bán</th><th class="text-center">Tồn kho</th></tr></thead>
                            <tbody>
                            @foreach($recentProducts as $product)
                                <tr>
                                    <td><div class="d-flex align-items-center gap-3">
                                        <img class="product-thumbnail" src="{{ $product->HinhAnh ? asset('legacy/Content/images/' . $product->HinhAnh) : asset('favicon.ico') }}" alt="{{ $product->TenSP }}">
                                        <div><div class="fw-semibold">{{ Str::limit($product->TenSP, 38) }}</div><small class="text-muted">#{{ $product->MaSP }} · {{ $product->loai?->TenLoai }}</small></div>
                                    </div></td>
                                    <td>{{ $product->danhMuc?->TenDM }}</td>
                                    <td class="fw-semibold text-danger">{{ number_format($product->GiaBan, 0, ',', '.') }} đ</td>
                                    <td class="text-center"><span class="badge {{ $product->SoLuongTon <= 10 ? 'bg-danger' : 'bg-success' }}">{{ $product->SoLuongTon }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header"><h5 class="mb-1 fw-bold">Phân bố sản phẩm</h5><small class="text-muted">Theo danh mục hiện có</small></div>
                    <div class="card-body">
                        @foreach($categoryStats as $category)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1"><span>{{ $category->TenDM }}</span><strong>{{ $category->san_phams_count }}</strong></div>
                                <div class="progress" role="progressbar" aria-valuenow="{{ $category->percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar bg-primary" style="width: {{ $category->percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
        @include('admin.partials.restock')
        </div>
    </div>
@endsection
