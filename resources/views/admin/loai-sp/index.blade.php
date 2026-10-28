@extends('layouts.admin')
@section('title', 'Quản lý loại sản phẩm')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý loại sản phẩm</h1>
        <a href="{{ route('admin.loai-sp.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Thêm mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr>
                <th>MaLoai</th>
                <th>Tên loại</th>
                <th>Danh mục</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($types as $type)
            <tr>
                <td>{{ $type->MaLoai }}</td>
                <td>{{ $type->TenLoai }}</td>
                <td>{{ $type->danhMuc->TenDM ?? '—' }}</td>
                <td>
                    <a href="{{ route('admin.loai-sp.edit', $type->MaLoai) }}" class="btn btn-sm btn-warning">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form method="post" action="{{ route('admin.loai-sp.destroy', $type->MaLoai) }}" class="d-inline" onsubmit="return confirm('Xóa loại này?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">Chưa có loại sản phẩm nào</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $types->links() }}</div>
</div>
@endsection
