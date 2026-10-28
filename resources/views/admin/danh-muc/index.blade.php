@extends('layouts.admin')
@section('title', 'Quản lý danh mục')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý danh mục</h1>
    <a href="{{ route('admin.danh-muc.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm danh mục</a>
</div>
<div class="table-responsive bg-white shadow-sm rounded">
<table class="table table-hover align-middle mb-0"><thead><tr><th>Mã</th><th>Tên danh mục</th><th>Loại SP</th><th>Sản phẩm</th><th></th></tr></thead><tbody>
@forelse($categories as $category)
<tr><td>{{ $category->MaDM }}</td><td>{{ $category->TenDM }}</td><td>{{ $category->loai_san_phams_count }}</td><td>{{ $category->san_phams_count }}</td><td class="text-end">
<a class="btn btn-sm btn-warning" href="{{ route('admin.danh-muc.edit', $category->MaDM) }}"><i class="fa-solid fa-pen"></i></a>
<form class="d-inline" method="post" action="{{ route('admin.danh-muc.destroy', $category->MaDM) }}" onsubmit="return confirm('Xóa danh mục này?')">@csrf @method('delete')<button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button></form>
</td></tr>
@empty<tr><td colspan="5" class="text-center text-muted py-4">Chưa có danh mục</td></tr>@endforelse
</tbody></table></div><div class="mt-3">{{ $categories->links() }}</div>
@endsection
