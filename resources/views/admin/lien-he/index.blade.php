@extends('layouts.admin')

@section('title', 'Quản lý liên hệ')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Liên hệ</h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            @include('admin.partials.list-toolbar', ['resource' => 'lien-he'])
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table align-middle table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>MaLH</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
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
                            <td>{{ $contact->NgayGui ? \Carbon\Carbon::parse($contact->NgayGui)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <span class="badge {{ $contact->PhanHoi !== null && $contact->TrangThai !== 'Chờ phản hồi' ? 'bg-success' : 'bg-warning' }}">
                                    {{ $contact->PhanHoi !== null && $contact->TrangThai !== 'Chờ phản hồi' ? 'Đã phản hồi' : 'Chờ phản hồi' }}
                                </span>
                            </td>
                            <td class="text-end">@include('admin.records.actions', ['resource' => 'lien-he', 'id' => $contact->MaLH])</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Chưa có yêu cầu liên hệ nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($contacts) && method_exists($contacts, 'links'))
        @include('admin.partials.list-pagination', ['paginator' => $contacts])
    @endif
</div>
@endsection
