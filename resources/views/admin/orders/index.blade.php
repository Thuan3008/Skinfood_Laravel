@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Đơn hàng</h1>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                @include('admin.partials.list-toolbar', ['resource' => 'don-hang'])
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="80">Mã</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Phương thức</th>
                            <th width="260">Thanh toán</th>
                            <th class="text-end" width="90">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><strong>#{{ $order->MaDH }}</strong></td>
                                <td>
                                    <strong>{{ $order->TenNguoiNhan ?: ($order->nguoiDung?->HoTen ?? 'Khách') }}</strong>
                                    @if($order->SoDienThoai)
                                        <small class="text-muted d-block">{{ $order->SoDienThoai }}</small>
                                    @endif
                                </td>
                                <td>{{ optional($order->NgayDat)->format('d/m/Y H:i') }}</td>
                                <td><strong class="text-danger">{{ number_format($order->TongTien, 0, ',', '.') }} đ</strong></td>
                                <td><span class="badge bg-light text-dark border">{{ $order->PhuongThucThanhToan ?? 'COD' }}</span></td>
                                <td>
                                    <span class="badge bg-secondary">{{ $order->TrangThaiThanhToan }}</span>
                                </td>
                                <td class="text-end">@include('admin.records.actions', ['resource' => 'don-hang', 'id' => $order->MaDH])</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            @include('admin.partials.list-pagination', ['paginator' => $orders])
        </div>
    </div>
@endsection
