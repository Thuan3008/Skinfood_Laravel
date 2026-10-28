@extends('layouts.admin')
@section('title', 'Quản lý thương hiệu')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Quản lý thương hiệu</h1>
        <a href="{{ route('admin.thuong-hieu.create') }}" class="btn btn-primary">
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
                <th>MaTH</th>
                <th>Tên thương hiệu</th>
                <th>Quốc gia</th>
                <th width="150">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brands as $brand)
            <tr>
                <td>{{ $brand->MaTH }}</td>
                <td>{{ $brand->TenTH }}</td>
                <td>{{ $brand->QuocGia ?: '—' }}</td>
                <td>
                    <a href="{{ route('admin.thuong-hieu.edit', $brand->MaTH) }}" class="btn btn-sm btn-warning">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form method="post" action="{{ route('admin.thuong-hieu.destroy', $brand->MaTH) }}" class="d-inline" onsubmit="return confirm('Xóa thương hiệu này?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted">Chưa có thương hiệu nào</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $brands->links() }}</div>
</div>
@endsection
