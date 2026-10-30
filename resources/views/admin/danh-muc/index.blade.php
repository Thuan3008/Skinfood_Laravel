@extends('layouts.admin')
@section('title', 'Quản lý danh mục')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý danh mục</h1>
    <a href="{{ route('admin.danh-muc.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm danh mục</a>
</div>
@include('admin.partials.list-toolbar', ['resource' => 'danh-muc'])

<div class="table-responsive bg-white shadow-sm rounded">
<table class="table table-hover align-middle mb-0"><thead><tr><th>Mã</th><th>Tên danh mục</th><th data-align="center">Loại SP</th><th data-align="center">Sản phẩm</th><th></th></tr></thead><tbody>
@forelse($categories as $category)
<tr><td>{{ $category->MaDM }}</td><td>{{ $category->TenDM }}</td><td>{{ $category->loai_san_phams_count }}</td><td>{{ $category->san_phams_count }}</td><td class="text-end">@include('admin.records.actions', ['resource' => 'danh-muc', 'id' => $category->MaDM])</td></tr>
@empty<tr><td colspan="5" class="text-center text-muted py-4">Chưa có danh mục</td></tr>@endforelse
</tbody></table></div><div class="mt-3">@include('admin.partials.list-pagination', ['paginator' => $categories])</div>
@endsection
