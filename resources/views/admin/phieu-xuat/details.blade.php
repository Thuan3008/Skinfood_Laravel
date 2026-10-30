@extends('layouts.admin')

@section('title', 'Chi tiết phiếu xuất #' . $phieu->MaPX)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Chi tiết phiếu xuất #{{ $phieu->MaPX }}</h1>
        <a href="{{ route('admin.phieu-xuat.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3"><span class="text-muted">Người nhận hàng:</span> <strong>{{ $phieu->NguoiNhanHang ?: '—' }}</strong><p class="mb-0 mt-1">Số điện thoại: {{ $phieu->SoDienThoaiNguoiNhan ?: '—' }}</p><p class="mb-0 mt-1">Địa chỉ: {{ $phieu->DiaChiNguoiNhan ?: '—' }}</p><p class="mb-0 mt-1">Ghi chú: {{ $phieu->GhiChu ?: '—' }}</p></div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted">Lý do xuất:</p>
                    <h5 class="fw-bold">{{ $phieu->LyDoXuat ?: 'Xuất bán / điều chuyển' }}</h5>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted">Ngày xuất:</p>
                    <h5 class="fw-bold">{{ $phieu->NgayXuat ? \Carbon\Carbon::parse($phieu->NgayXuat)->format('d/m/Y') : '—' }}</h5>
                </div>
                <div class="col-md-4 text-md-end">
                    <p class="mb-1 text-muted">Tổng giá trị:</p>
                    <h4 class="fw-bold text-danger">{{ number_format($phieu->TongTien ?? 0, 0, ',', '.') }} đ</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">Danh sách mặt hàng xuất</div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="70">Mã SP</th>
                        <th>Tên sản phẩm</th>
                        <th class="text-center">Số lượng xuất</th>
                        <th class="text-end">Đơn giá</th>
                        <th class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phieu->chiTietPhieuXuats as $d)
                        <tr>
                            <td>#{{ $d->MaSP }}</td>
                            <td>
                                <strong>{{ $d->sanPham->TenSP ?? ('SP #' . $d->MaSP) }}</strong>
                            </td>
                            <td class="text-center fw-bold">{{ $d->SoLuong }}</td>
                            <td class="text-end">{{ number_format($d->DonGia, 0, ',', '.') }} đ</td>
                            <td class="text-end fw-bold text-danger">{{ number_format($d->ThanhTien, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
