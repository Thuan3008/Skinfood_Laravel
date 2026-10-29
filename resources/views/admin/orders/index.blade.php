@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4">Đơn hàng</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="get" action="{{ route('admin.orders') }}" class="row g-2">
                    <div class="col-md-5">
                        <input type="text" name="q" class="form-control" placeholder="Tìm theo mã ĐH, tên người nhận hoặc SĐT..." value="{{ $q ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="Chưa thanh toán" @selected(($currentStatus ?? '') === 'Chưa thanh toán')>Chưa thanh toán</option>
                            <option value="Đã chuyển khoản - chờ xác nhận" @selected(($currentStatus ?? '') === 'Đã chuyển khoản - chờ xác nhận')>Đã chuyển khoản - chờ xác nhận</option>
                            <option value="Đã thanh toán" @selected(($currentStatus ?? '') === 'Đã thanh toán')>Đã thanh toán</option>
                            <option value="Đã hủy" @selected(($currentStatus ?? '') === 'Đã hủy')>Đã hủy</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-secondary w-100" type="submit">
                            <i class="fa-solid fa-search"></i> Lọc
                        </button>
                    </div>
                </form>
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
                            <th class="text-end" width="90">Chi tiết</th>
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
                                    <form method="post" action="{{ route('admin.orders.update', $order->MaDH) }}">
                                        @csrf
                                        @method('put')
                                        <select name="status" class="form-select form-select-sm {{ in_array($order->TrangThaiThanhToan, ['Đã thanh toán', 'Paid']) ? 'border-success text-success fw-bold' : ($order->TrangThaiThanhToan === 'Đã chuyển khoản - chờ xác nhận' ? 'border-primary text-primary fw-bold' : '') }}" onchange="this.form.submit()">
                                            <option @selected($order->TrangThaiThanhToan === 'Chưa thanh toán')>Chưa thanh toán</option>
                                            <option @selected($order->TrangThaiThanhToan === 'Đã chuyển khoản - chờ xác nhận')>Đã chuyển khoản - chờ xác nhận</option>
                                            <option @selected($order->TrangThaiThanhToan === 'Đã thanh toán')>Đã thanh toán</option>
                                            <option @selected($order->TrangThaiThanhToan === 'Đã hủy')>Đã hủy</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $order->MaDH) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $orders->onEachSide(1)->links() }}
        </div>
    </div>
@endsection
