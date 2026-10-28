@extends('layouts.admin')

@section('title', 'Chi tiết liên hệ')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Chi tiết liên hệ</h1>
        <a href="{{ route('admin.lien-he.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Thông tin khách hàng</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th width="30%">Họ tên:</th><td>{{ $contact->HoTen }}</td></tr>
                        <tr><th>Email:</th><td>{{ $contact->Email }}</td></tr>
                        <tr><th>Số điện thoại:</th><td>{{ $contact->SoDienThoai }}</td></tr>
                        <tr><th>Ngày gửi:</th><td>{{ $contact->NgayGui ? \Carbon\Carbon::parse($contact->NgayGui)->format('d/m/Y H:i') : '-' }}</td></tr>
                        <tr><th>Trạng thái:</th><td><span class="badge bg-info">{{ $contact->TrangThai }}</span></td></tr>
                    </table>
                    <h5 class="mt-3">Nội dung:</h5>
                    <div class="p-3 bg-light border rounded" style="white-space: pre-wrap;">{{ $contact->NoiDung }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">Phản hồi</div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.lien-he.reply', $contact->MaLH) }}">
                        @csrf
                        <div class="mb-3">
                            <label for="PhanHoi" class="form-label">Nội dung phản hồi</label>
                            <textarea class="form-control" id="PhanHoi" name="PhanHoi" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-paper-plane"></i> Gửi phản hồi
                        </button>
                    </form>

                    @if(Str::startsWith($contact->TrangThai, 'PHANHOI|'))
                        <hr class="my-4">
                        <h5>Lịch sử phản hồi:</h5>
                        <div class="p-3 bg-light border rounded text-muted small">
                            {{ $contact->TrangThai }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection