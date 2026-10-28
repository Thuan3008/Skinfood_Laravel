@extends('layouts.admin')
@section('title', 'Thêm loại sản phẩm')

@section('content')
<div class="container py-4">
    <h1>Thêm loại sản phẩm</h1>
    <a href="{{ route('admin.loai-sp.index') }}" class="btn btn-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('admin.loai-sp.store') }}">
        @csrf
        <div class="card col-md-6">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tên loại *</label>
                    <input type="text" name="TenLoai" class="form-control" value="{{ old('TenLoai') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Danh mục *</label>
                    <select name="MaDM" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->MaDM }}" {{ old('MaDM') == $cat->MaDM ? 'selected' : '' }}>{{ $cat->TenDM }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Lưu</button>
            </div>
        </div>
    </form>
</div>
@endsection
