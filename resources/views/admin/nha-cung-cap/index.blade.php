@extends('layouts.admin')
@section('title', 'Quản lý nhà cung cấp')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nhà cung cấp</h1>
        <a href="{{ route('admin.nha-cung-cap.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm mới</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

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
                <td>
                    <a href="{{ route('admin.nha-cung-cap.edit', $s->MaNCC) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" action="{{ route('admin.nha-cung-cap.destroy', $s->MaNCC) }}" class="d-inline" onsubmit="return confirm('Xóa?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Chưa có nhà cung cấp</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>
</div>
@endsection