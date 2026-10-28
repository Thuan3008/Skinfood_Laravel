@extends('layouts.admin')
@section('title', 'Sửa thương hiệu')

@section('content')
<div class="container py-4">
    <h1>Sửa thương hiệu</h1>
    <a href="{{ route('admin.thuong-hieu.index') }}" class="btn btn-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('admin.thuong-hieu.update', $brand->MaTH) }}">
        @csrf @method('PUT')
        <div class="card col-md-6">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tên thương hiệu *</label>
                    <input type="text" name="TenTH" class="form-control" value="{{ old('TenTH', $brand->TenTH) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quốc gia</label>
                    <input name="QuocGia" class="form-control" value="{{ old('QuocGia', $brand->QuocGia) }}">
                </div>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Cập nhật</button>
            </div>
        </div>
    </form>
</div>
@endsection
