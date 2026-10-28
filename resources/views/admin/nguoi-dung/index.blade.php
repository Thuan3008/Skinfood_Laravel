@extends('layouts.admin')
@section('title', 'Quản lý người dùng')

@section('content')
<div class="container py-4">
    <h1>Quản lý người dùng</h1>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

    <form method="get" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="q" class="form-control" placeholder="Tìm kiếm..." value="{{ $q }}">
        </div>
        <div class="col-auto">
            <button class="btn btn-secondary"><i class="fa-solid fa-search"></i> Tìm</button>
        </div>
    </form>

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-light">
            <tr>
                <th>MaND</th>
                <th>Họ tên</th>
                <th>Tài khoản / Email</th>
                <th>Điện thoại</th>
                <th>Ngày sinh</th>
                <th>Giới tính</th>
                <th>Ngày tạo</th>
                <th width="80">Xóa</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr>
                <td>{{ $u->MaND }}</td>
                <td>{{ $u->HoTen }}</td>
                <td>{{ $u->taiKhoans->first()?->TenDangNhap ?? '—' }}</td>
                <td>{{ $u->SoDienThoai ?: '—' }}</td>
                <td>{{ $u->NgaySinh ? $u->NgaySinh->format('d/m/Y') : '—' }}</td>
                <td>{{ $u->GioiTinh ?: '—' }}</td>
                <td>{{ $u->NgayTao ? $u->NgayTao->format('d/m/Y') : '—' }}</td>
                <td>
                    <form method="post" action="{{ route('admin.nguoi-dung.destroy', $u->MaND) }}" onsubmit="return confirm('Xóa người dùng?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted">Không có người dùng</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection