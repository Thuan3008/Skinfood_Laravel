@extends('layouts.admin')

@section('title', 'Quản lý phiếu xuất')
@push('styles')
<style>
.export-list { min-width: 900px; }
.export-list th { white-space: nowrap; }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Phiếu xuất kho</h1>
        <a class="btn btn-primary" href="{{ route('admin.phieu-xuat.create') }}">
            <i class="fa-solid fa-plus me-1"></i> Tạo phiếu xuất
        </a>
    </div>
@include('admin.partials.list-toolbar', ['resource' => 'phieu-xuat'])


    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 export-list">
                <thead class="table-light">
                    <tr>
                        <th width="100">Mã phiếu</th>
                        <th>Ngày xuất</th>

                        <th>Người nhận</th>
                        <th>SĐT người nhận</th>

                        <th class="text-center">Tổng số SP xuất</th>

                        <th class="text-end">Tổng tiền</th>
                        <th class="text-end" width="160">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($phieus as $p)
                        <tr>
                            <td><strong>#{{ $p->MaPX }}</strong></td>
                            <td>{{ $p->NgayXuat ? \Carbon\Carbon::parse($p->NgayXuat)->format('d/m/Y') : '—' }}</td>

                            <td>{{ $p->NguoiNhanHang ?: '—' }}</td>
                            <td class="text-nowrap">{{ $p->SoDienThoaiNguoiNhan ?: '—' }}</td>
                            <td class="text-center fw-bold">{{ $p->chi_tiet_phieu_xuats_sum_so_luong ?? 0 }}</td>


                            <td class="text-end fw-bold text-danger">{{ number_format($p->TongTien ?? 0, 0, ',', '.') }} đ</td>
                            <td class="text-end">@include('admin.records.actions', ['resource' => 'phieu-xuat', 'id' => $p->MaPX])</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có phiếu xuất nào</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        @include('admin.partials.list-pagination', ['paginator' => $phieus])
    </div>
</div>
@endsection
