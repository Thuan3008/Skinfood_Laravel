@extends('layouts.admin')
@section('title', 'Quản lý người dùng')

@section('content')
<div class="container py-4">
    <h1>Quản lý người dùng</h1>

    @include('admin.partials.list-toolbar', ['resource' => 'nguoi-dung'])

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
                <th width="140">Hành động</th>
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
                <td class="text-end">@include('admin.records.actions', ['resource' => 'nguoi-dung', 'id' => $u->MaND])</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted">Không có người dùng</td></tr>
            @endforelse
        </tbody>
    </table>
    @include('admin.partials.list-pagination', ['paginator' => $users])
</div>
@endsection
