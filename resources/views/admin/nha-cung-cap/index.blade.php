@extends('layouts.admin')
@section('title', 'Quản lý nhà cung cấp')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nhà cung cấp</h1>
        <a href="{{ route('admin.nha-cung-cap.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm mới</a>
    </div>
@include('admin.partials.list-toolbar', ['resource' => 'nha-cung-cap'])


    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr><th>MaNCC</th><th>Tên NCC</th><th>Địa chỉ</th><th>Điện thoại</th><th>Email</th><th>Người LH</th><th width="150">Hành động</th></tr>
        </thead>
        <tbody>
            @forelse($suppliers as $s)
            <tr>
                <td>{{ $s->MaNCC }}</td>
                <td>{{ $s->TenNCC }}</td>
                <td>{{ $s->DiaChi }}</td>
                <td>{{ $s->SoDienThoai }}</td>
                <td>{{ $s->Email }}</td>
                <td>{{ $s->NguoiLienHe }}</td>
                <td class="text-end">@include('admin.records.actions', ['resource' => 'nha-cung-cap', 'id' => $s->MaNCC])</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Chưa có nhà cung cấp</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        @include('admin.partials.list-pagination', ['paginator' => $suppliers])
    </div>
</div>
@endsection
