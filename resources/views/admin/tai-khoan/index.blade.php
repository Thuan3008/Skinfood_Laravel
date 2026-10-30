@extends('layouts.admin')
@section('title', 'Quản lý tài khoản')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tài khoản</h1>
        <a href="{{ route('admin.tai-khoan.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm mới</a>
    </div>
@include('admin.partials.list-toolbar', ['resource' => 'tai-khoan'])


    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr><th>Tên đăng nhập</th><th>MaND</th><th>Họ tên</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th><th width="150">Hành động</th></tr>
        </thead>
        <tbody>
            @forelse($accounts as $a)
            <tr>
                <td>{{ $a->TenDangNhap }}</td>
                <td>{{ $a->MaND }}</td>
                <td>{{ $a->nguoiDung->HoTen ?? '—' }}</td>
                <td><span class="badge bg-{{ strtolower((string)$a->VaiTro) == 'admin' ? 'danger' : 'secondary' }}">{{ $a->VaiTro }}</span></td>
                <td><span class="badge bg-{{ $a->TrangThai == 'active' ? 'success' : 'warning' }}">{{ $a->TrangThai }}</span></td>
                <td>{{ $a->nguoiDung?->NgayTao ? $a->nguoiDung->NgayTao->format('d/m/Y') : '—' }}</td>
                <td class="text-end">@include('admin.records.actions', ['resource' => 'tai-khoan', 'id' => $a->MaTK])</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Chưa có tài khoản</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('admin.partials.list-pagination', ['paginator' => $accounts])
</div>
@endsection
