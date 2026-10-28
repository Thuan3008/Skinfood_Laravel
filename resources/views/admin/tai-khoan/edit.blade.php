@extends('layouts.admin')
@section('title', 'Sửa tài khoản')

@section('content')
<div class="container py-4">
    <h1>Sửa tài khoản</h1>
    <a href="{{ route('admin.tai-khoan.index') }}" class="btn btn-secondary mb-3"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>

    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <form method="post" action="{{ route('admin.tai-khoan.update', $account->MaTK) }}">
        @csrf @method('PUT')
        <div class="card col-md-7">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" class="form-control" value="{{ $account->TenDangNhap }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                    <input type="password" name="MatKhau" class="form-control">
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Người dùng</label>
                        <select name="MaND" class="form-select">
                            <option value="">— Chọn —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->MaND }}" {{ old('MaND', $account->MaND) == $u->MaND ? 'selected' : '' }}>{{ $u->HoTen }} — {{ $u->SoDienThoai }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vai trò</label>
                        <select name="VaiTro" class="form-select">
                            <option value="User" {{ old('VaiTro', $account->VaiTro) == 'User' ? 'selected' : '' }}>User</option>
                            <option value="Admin" {{ old('VaiTro', $account->VaiTro) == 'Admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="TrangThai" class="form-select">
                        <option value="active" {{ old('TrangThai', $account->TrangThai) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('TrangThai', $account->TrangThai) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Cập nhật</button>
            </div>
        </div>
    </form>
</div>
@endsection
