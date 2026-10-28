@extends('layouts.admin')

@section('title', 'Thêm khuyến mãi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Thêm khuyến mãi mới</h1>
        <a href="{{ route('admin.khuyen-mai.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="{{ route('admin.khuyen-mai.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="TenChuongTrinh" class="form-label">Tên chương trình <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('TenChuongTrinh') is-invalid @enderror" id="TenChuongTrinh" name="TenChuongTrinh" value="{{ old('TenChuongTrinh') }}" required>
                        @error('TenChuongTrinh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="MaCode" class="form-label">Mã code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('MaCode') is-invalid @enderror" id="MaCode" name="MaCode" value="{{ old('MaCode') }}" required placeholder="VD: SUMMER2025">
                        @error('MaCode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="LoaiGiamGia" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                        <select class="form-select @error('LoaiGiamGia') is-invalid @enderror" id="LoaiGiamGia" name="LoaiGiamGia" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="PhanTram" @selected(old('LoaiGiamGia') == 'PhanTram')>Phần trăm (%)</option>
                            <option value="TienMat" @selected(old('LoaiGiamGia') == 'TienMat')>Tiền mặt (đ)</option>
                        </select>
                        @error('LoaiGiamGia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="GiaTriGiam" class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('GiaTriGiam') is-invalid @enderror" id="GiaTriGiam" name="GiaTriGiam" value="{{ old('GiaTriGiam') }}" min="0" required>
                        @error('GiaTriGiam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="SoLuongPhatHanh" class="form-label">Số lượng phát hành</label>
                        <input type="number" class="form-control @error('SoLuongPhatHanh') is-invalid @enderror" id="SoLuongPhatHanh" name="SoLuongPhatHanh" value="{{ old('SoLuongPhatHanh', 0) }}" min="0">
                        @error('SoLuongPhatHanh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="NgayBatDau" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('NgayBatDau') is-invalid @enderror" id="NgayBatDau" name="NgayBatDau" value="{{ old('NgayBatDau') }}" required>
                        @error('NgayBatDau')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="NgayKetThuc" class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('NgayKetThuc') is-invalid @enderror" id="NgayKetThuc" name="NgayKetThuc" value="{{ old('NgayKetThuc') }}" required>
                        @error('NgayKetThuc')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-center pt-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="TrangThai" name="TrangThai" value="1" @checked(old('TrangThai'))>
                            <label class="form-check-label" for="TrangThai">Hoạt động</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="GiamToiDa" class="form-label">Giảm tối đa (đ)</label>
                        <input type="number" class="form-control @error('GiamToiDa') is-invalid @enderror" id="GiamToiDa" name="GiamToiDa" value="{{ old('GiamToiDa', 0) }}" min="0">
                        @error('GiamToiDa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="DonHangToiThieu" class="form-label">Đơn hàng tối thiểu (đ)</label>
                        <input type="number" class="form-control @error('DonHangToiThieu') is-invalid @enderror" id="DonHangToiThieu" name="DonHangToiThieu" value="{{ old('DonHangToiThieu', 0) }}" min="0">
                        @error('DonHangToiThieu')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <hr>
                <h5 class="mb-3">Áp dụng cho sản phẩm</h5>
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="products[]" id="product_{{ $product->MaSP }}" value="{{ $product->MaSP }}" @checked(in_array($product->MaSP, old('products', [])))>
                                <label class="form-check-label" for="product_{{ $product->MaSP }}">{{ $product->TenSP }}</label>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted">Chưa có sản phẩm nào.</div>
                    @endforelse
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-floppy-disk"></i> Lưu</button>
                    <a href="{{ route('admin.khuyen-mai.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection