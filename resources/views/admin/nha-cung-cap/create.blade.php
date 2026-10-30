@extends('layouts.admin')
@section('title', 'Thêm nhà cung cấp')

@section('content')
<div class="container py-4">
    <h1>Thêm nhà cung cấp</h1>
    <a href="{{ route('admin.nha-cung-cap.index') }}" class="btn btn-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>

    <form method="post" action="{{ route('admin.nha-cung-cap.store') }}">
        @csrf
        <div class="card col-md-7">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên NCC *</label>
                        <input type="text" name="TenNCC" class="form-control" value="{{ old('TenNCC') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Người liên hệ</label>
                        <input type="text" name="NguoiLienHe" class="form-control" value="{{ old('NguoiLienHe') }}">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Điện thoại</label>
                        <input type="text" name="SoDienThoai" class="form-control" value="{{ old('SoDienThoai') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="Email" class="form-control" value="{{ old('Email') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="DiaChi" class="form-control" value="{{ old('DiaChi') }}">
                </div>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Lưu</button>
            </div>
        </div>
    </form>
</div>
@endsection