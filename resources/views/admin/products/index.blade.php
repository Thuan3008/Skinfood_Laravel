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


        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get" action="{{ route('admin.products') }}" class="row g-3 admin-product-filters">
                    <div class="col-12 col-xl-4">
                        <label class="form-label" for="productKeyword">Tên hoặc mã sản phẩm</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <input id="productKeyword" type="text" name="q" class="form-control" placeholder="Nhập tên hoặc mã SP..." value="{{ $q ?? '' }}">
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label" for="productCategory">Danh mục</label>
                        <select id="productCategory" name="category" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->MaDM }}" @selected(($filters['categoryId'] ?? 0) === $category->MaDM)>{{ $category->TenDM }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label" for="productType">Loại sản phẩm</label>
                        <select id="productType" name="type" class="form-select">
                            <option value="">Tất cả loại</option>
                            @foreach($types as $type)
                                <option value="{{ $type->MaLoai }}" data-category="{{ $type->MaDM }}" @selected(($filters['typeId'] ?? 0) === $type->MaLoai)>{{ $type->TenLoai }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label" for="productBrand">Thương hiệu</label>
                        <select id="productBrand" name="brand" class="form-select">
                            <option value="">Tất cả thương hiệu</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->MaTH }}" @selected(($filters['brandId'] ?? 0) === $brand->MaTH)>{{ $brand->TenTH }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <label class="form-label" for="productStock">Tồn kho</label>
                        <select id="productStock" name="stock" class="form-select">
                            <option value="">Tất cả tồn kho</option>
                            <option value="in_stock" @selected(($filters['stock'] ?? '') === 'in_stock')>Còn hàng</option>
                            <option value="low_stock" @selected(($filters['stock'] ?? '') === 'low_stock')>Sắp hết (1–10)</option>
                            <option value="out_of_stock" @selected(($filters['stock'] ?? '') === 'out_of_stock')>Hết hàng</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 col-xl-2">
                        <label class="form-label" for="productMinPrice">Giá từ</label>
                        <input id="productMinPrice" type="number" name="min_price" class="form-control" min="0" step="1000" placeholder="0" value="{{ $filters['minPrice'] ?? '' }}">
                    </div>
                    <div class="col-6 col-md-3 col-xl-2">
                        <label class="form-label" for="productMaxPrice">Giá đến</label>
                        <input id="productMaxPrice" type="number" name="max_price" class="form-control" min="0" step="1000" placeholder="Không giới hạn" value="{{ $filters['maxPrice'] ?? '' }}">
                    </div>
                    <div class="col-12 col-md-3 col-xl-3">
                        <label class="form-label" for="productStatus">Trạng thái kinh doanh</label>
                        <select id="productStatus" name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            @foreach(['Còn hàng', 'Hết hàng', 'Tạm ngưng', 'Ngừng kinh doanh'] as $productStatus)
                                <option value="{{ $productStatus }}" @selected(($filters['status'] ?? '') === $productStatus)>{{ $productStatus }}</option>
                            @endforeach
                        </select>
                    </div>
                    @include('admin.partials.list-toolbar', ['resource' => 'san-pham', 'extrasOnly' => true])
                    <div class="col-12 col-sm-auto d-flex align-items-end gap-2 admin-filter-actions">
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter me-1"></i> Lọc sản phẩm</button>
                        <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left me-1"></i> Xóa lọc</a>
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
                            <th class="col-product-stock text-center">Tồn kho / Trạng thái</th>
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
                                    <span class="admin-stock-number">{{ $product->SoLuongTon ?? 0 }}</span>
                                    <span class="badge admin-stock-status {{ ($product->SoLuongTon ?? 0) > 0 ? 'bg-success' : 'bg-danger' }}">{{ $product->TrangThaiSanPham ?: (($product->SoLuongTon ?? 0) > 0 ? 'Còn hàng' : 'Hết hàng') }}</span>
                                </td>
                                <td class="text-end">@include('admin.records.actions', ['resource' => 'san-pham', 'id' => $product->MaSP])</td>
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
            <div>{{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}</div>
        </div>
        <div class="mt-4">
        @include('admin.partials.restock')
        </div>
    </div>
@endsection
