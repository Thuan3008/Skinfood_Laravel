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
@include('admin.partials.list-toolbar', ['resource' => 'loai-sp'])


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
                <td class="text-end">@include('admin.records.actions', ['resource' => 'loai-sp', 'id' => $type->MaLoai])</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">Chưa có loại sản phẩm nào</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">@include('admin.partials.list-pagination', ['paginator' => $types])</div>
</div>
@endsection
