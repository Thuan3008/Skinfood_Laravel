@extends('layouts.admin')
@section('title', 'Quản lý tài khoản')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tài khoản</h1>
        <a href="{{ route('admin.tai-khoan.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Thêm mới</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

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
                <td>
                    <a href="{{ route('admin.tai-khoan.edit', $a->MaTK) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>
                    <form method="post" action="{{ route('admin.tai-khoan.destroy', $a->MaTK) }}" class="d-inline" onsubmit="return confirm('Xóa?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted">Chưa có tài khoản</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $accounts->onEachSide(1)->links() }}
</div>
@endsection
