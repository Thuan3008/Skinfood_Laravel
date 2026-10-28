@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')
    <div class="container-fluid py-2 admin-products-page">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">Sản phẩm</h1>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i> Thêm sản phẩm
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get" action="{{ route('admin.products') }}" class="row g-2">
                    <div class="col-md-5">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Tìm theo tên sản phẩm hoặc mã SP..." value="{{ $q ?? '' }}">
                            <button class="btn btn-secondary" type="submit">
                                <i class="fa-solid fa-search"></i> Tìm
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm admin-products-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table align-middle table-hover mb-0 admin-products-table">
                    <thead class="table-light">
                        <tr>
                            <th class="col-product-id">ID</th>
                            <th class="col-product-image">Hình</th>
                            <th class="col-product-name">Tên sản phẩm</th>
                            <th class="col-product-meta">Danh mục / Thương hiệu</th>
                            <th class="col-product-price text-end">Giá bán</th>
                            <th class="col-product-stock text-center">Tồn kho</th>
                            <th class="col-product-actions text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="admin-product-row">
                                <td><strong>#{{ $product->MaSP }}</strong></td>
                                <td>
                                    <img src="{{ $product->HinhAnh ? asset('legacy/Content/images/' . $product->HinhAnh) : 'https://via.placeholder.com/60' }}" class="admin-product-thumb" alt="{{ $product->TenSP }}">
                                </td>
                                <td>
                                    <a href="{{ route('home.product', $product->MaSP) }}" target="_blank" class="admin-product-name" title="{{ $product->TenSP }} — Xem trên web">
                                        {{ $product->TenSP }}
                                    </a>
                                    <small class="admin-product-type">{{ $product->loai?->TenLoai ?? 'Chưa phân loại' }}</small>
                                </td>
                                <td class="admin-product-meta">
                                    <small>{{ $product->danhMuc?->TenDM ?? 'Chưa có danh mục' }}</small>
                                    <strong>{{ $product->thuongHieu?->TenTH ?? 'Chưa có thương hiệu' }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">{{ number_format($product->GiaBan, 0, ',', '.') }} đ</strong>
                                    @if($product->GiamGia > 0)
                                        <small class="badge bg-danger d-block mt-1 ms-auto" style="width: fit-content;">-{{ (int)$product->GiamGia }}%</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ ($product->SoLuongTon ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->SoLuongTon ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="admin-product-actions">
                                    <a href="{{ route('admin.products.edit', $product->MaSP) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form method="post" action="{{ route('admin.products.delete', $product->MaSP) }}" onsubmit="return confirm('Xóa sản phẩm này?')">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Không tìm thấy sản phẩm nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="admin-products-pagination mt-3">
            <p class="mb-0 text-muted small">
                Hiển thị <strong>{{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}</strong>
                trong tổng số <strong>{{ $products->total() }}</strong> sản phẩm
            </p>
            <div>{{ $products->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
@endsection
