@extends('layouts.admin')

@section('title', 'Quản lý liên hệ')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Liên hệ</h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" action="{{ route('admin.lien-he.index') }}" class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" value="{{ request('q') }}" placeholder="Tìm theo tên, email, nội dung...">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fa-solid fa-search"></i> Tìm
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>MaLH</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Nội dung</th>
                        <th>Ngày gửi</th>
                        <th>Trạng thái</th>
                        <th style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contacts as $contact)
                        <tr>
                            <td>{{ $contact->MaLH }}</td>
                            <td>{{ $contact->HoTen }}</td>
                            <td>{{ $contact->Email }}</td>
                            <td>{{ $contact->SoDienThoai }}</td>
                            <td>{{ Str::limit($contact->NoiDung, 50) }}</td>
                            <td>{{ $contact->NgayGui ? \Carbon\Carbon::parse($contact->NgayGui)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $contact->TrangThai === 'Mới' ? 'info' : 'success' }}">
                                    {{ $contact->TrangThai }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.lien-he.details', $contact->MaLH) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                                    <form method="post" action="{{ route('admin.lien-he.destroy', $contact->MaLH) }}" onsubmit="return confirm('Xóa yêu cầu liên hệ này?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có yêu cầu liên hệ nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($contacts) && method_exists($contacts, 'links'))
        {{ $contacts->onEachSide(1)->links() }}
    @endif
</div>
@endsection
