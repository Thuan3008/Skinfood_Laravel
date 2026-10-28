@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->MaDH)

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0">📄 Chi tiết đơn hàng #{{ $order->MaDH }}</h3>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Ngày đặt:</strong> {{ optional($order->NgayDat)->format('d/m/Y H:i') }}</p>
                    <p class="mb-1"><strong>Người nhận:</strong> {{ $order->TenNguoiNhan }} ({{ $order->SoDienThoai }})</p>
                    <p class="mb-1"><strong>Địa chỉ giao:</strong> {{ $order->DiaChiGiaoHang }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Phương thức:</strong> {{ $order->PhuongThucThanhToan }}</p>
                    <p class="mb-1"><strong>Trạng thái:</strong> 
                        <span class="badge {{ in_array($order->TrangThaiThanhToan, ['Đã thanh toán', 'Paid']) ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ $order->TrangThaiThanhToan }}
                        </span>
                    </p>
                    @if($order->GhiChu)
                        <p class="mb-1"><strong>Ghi chú:</strong> {{ $order->GhiChu }}</p>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Hình</th>
                            <th>Sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Giá</th>
                            <th class="text-end">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->chiTiet as $sp)
                            <tr>
                                <td width="80" class="text-center">
                                    <img src="{{ $sp->sanPham?->HinhAnh ? asset('legacy/Content/images/' . $sp->sanPham->HinhAnh) : 'https://via.placeholder.com/70' }}" width="70" class="img-fluid rounded" alt="{{ $sp->sanPham?->TenSP }}">
                                </td>
                                <td>
                                    <a href="{{ $sp->sanPham ? route('home.product', $sp->sanPham->MaSP) : '#' }}" class="text-decoration-none text-dark fw-bold">
                                        {{ $sp->sanPham?->TenSP ?? 'Sản phẩm' }}
                                    </a>
                                </td>
                                <td class="text-center">{{ $sp->SoLuong }}</td>
                                <td class="text-end">{{ number_format($sp->DonGia, 0, ',', '.') }} đ</td>
                                <td class="text-end fw-bold">{{ number_format($sp->SoLuong * $sp->DonGia, 0, ',', '.') }} đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                @if($order->SoTienGiam > 0)
                    <div class="text-success">
                        Giảm giá voucher: -{{ number_format($order->SoTienGiam, 0, ',', '.') }} đ
                    </div>
                @else
                    <div></div>
                @endif
                <p class="fw-bold fs-5 mb-0 text-end">
                    Tổng đơn: <strong class="text-danger">{{ number_format($order->TongTien, 0, ',', '.') }} đ</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
