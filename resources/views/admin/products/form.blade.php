@extends('layouts.admin')

@section('title', $product->exists ? 'Sửa sản phẩm' : 'Thêm sản phẩm')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ $product->exists ? 'Sửa sản phẩm' : 'Thêm sản phẩm' }}</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="post" action="{{ $product->exists ? route('admin.products.update', $product->MaSP) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if($product->exists)
            @method('put')
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Thông tin sản phẩm</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input name="TenSP" class="form-control" required value="{{ old('TenSP', $product->TenSP) }}" placeholder="Nhập tên sản phẩm">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" min="0" step="1000" name="GiaBan" class="form-control" required value="{{ old('GiaBan', $product->GiaBan) }}" placeholder="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giảm giá (%)</label>
                                <input type="number" min="0" max="99" name="GiamGia" class="form-control" value="{{ old('GiamGia', $product->GiamGia ?? 0) }}" placeholder="0">
                                <small class="text-muted">Để 0 nếu không giảm giá</small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mô tả sản phẩm</label>
                            <textarea name="MoTa" rows="5" class="form-control" placeholder="Nhập mô tả chi tiết sản phẩm (hỗ trợ HTML)">{{ old('MoTa', $product->MoTa) }}</textarea>
                            <small class="text-muted">Có thể sử dụng HTML để định dạng</small>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Hình ảnh sản phẩm</h5></div>
                    <div class="card-body">
                        @if($product->exists && $product->HinhAnh)
                            <div class="mb-3">
                                <label class="form-label">Ảnh hiện tại</label>
                                <div>
                                    <img src="{{ asset('legacy/Content/images/' . $product->HinhAnh) }}" alt="{{ $product->TenSP }}" style="max-height:150px;object-fit:contain;border:1px solid #ddd;padding:10px;border-radius:8px">
                                </div>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Tải ảnh mới</label>
                            <input type="file" name="imageFile" id="imageFile" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
                            <small class="text-muted">JPG, PNG, WebP hoặc GIF; tối đa 5 MB.</small>
                            <img id="imagePreview" class="img-fluid rounded border mt-3 d-none" style="max-height:220px" alt="Xem trước ảnh">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-white"><h5 class="mb-0">Phân loại</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Danh mục</label>
                            <select name="MaDM" class="form-select">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->MaDM }}" @selected(old('MaDM', $product->MaDM) == $category->MaDM)>{{ $category->TenDM }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Loại sản phẩm</label>
                            <select name="MaLoai" class="form-select">
                                <option value="">-- Chọn loại --</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->MaLoai }}" @selected(old('MaLoai', $product->MaLoai) == $type->MaLoai)>{{ $type->TenLoai }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Thương hiệu</label>
                            <select name="MaTH" class="form-select">
                                <option value="">-- Chọn thương hiệu --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->MaTH }}" @selected(old('MaTH', $product->MaTH) == $brand->MaTH)>{{ $brand->TenTH }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tình trạng</label>
                            <select name="TrangThaiSanPham" class="form-select">
                                <option value="">Mặc định (Còn hàng)</option>
                                <option value="Còn hàng" @selected(old('TrangThaiSanPham', $product->TrangThaiSanPham) === 'Còn hàng')>Còn hàng</option>
                                <option value="Hết hàng" @selected(old('TrangThaiSanPham', $product->TrangThaiSanPham) === 'Hết hàng')>Hết hàng</option>
                                <option value="Tạm ngưng" @selected(old('TrangThaiSanPham', $product->TrangThaiSanPham) === 'Tạm ngưng')>Tạm ngưng</option>
                                <option value="Ngừng kinh doanh" @selected(old('TrangThaiSanPham', $product->TrangThaiSanPham) === 'Ngừng kinh doanh')>Ngừng kinh doanh</option>
                            </select>
                        </div>
                        @if($product->exists)
                            <div class="alert alert-info mb-0">
                                <strong>Tồn kho hiện tại:</strong> {{ $product->SoLuongTon ?? 0 }} sản phẩm<br>
                                <small>Tồn kho được cập nhật thông qua phiếu nhập hàng.</small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger flex-grow-1">
                        <i class="bi bi-check-circle me-1"></i>{{ $product->exists ? 'Cập nhật' : 'Thêm mới' }}
                    </button>
                    <a href="{{ route('admin.products') }}" class="btn btn-secondary">Hủy</a>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>document.getElementById('imageFile')?.addEventListener('change',event=>{const file=event.target.files[0],preview=document.getElementById('imagePreview');if(!file){preview.classList.add('d-none');return}preview.src=URL.createObjectURL(file);preview.classList.remove('d-none')});</script>
@endpush
@endsection
