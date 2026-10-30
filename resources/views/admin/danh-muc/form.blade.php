@extends('layouts.admin')
@section('title', $category->exists ? 'Sửa danh mục' : 'Thêm danh mục')
@section('content')
<h1 class="mb-4">{{ $category->exists ? 'Sửa danh mục' : 'Thêm danh mục' }}</h1>

<form method="post" action="{{ $category->exists ? route('admin.danh-muc.update', $category->MaDM) : route('admin.danh-muc.store') }}" class="card shadow-sm" style="max-width:650px">
@csrf @if($category->exists) @method('put') @endif
<div class="card-body"><label class="form-label fw-bold">Tên danh mục</label><input class="form-control" name="TenDM" maxlength="100" required value="{{ old('TenDM', $category->TenDM) }}"></div>
<div class="card-footer bg-white"><button class="btn btn-success">Lưu</button> <a class="btn btn-secondary" href="{{ route('admin.danh-muc.index') }}">Hủy</a></div>
</form>
@endsection
