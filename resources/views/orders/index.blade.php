@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h3 class="fw-bold mb-4">📦 Đơn hàng của tôi</h3>

            @if($orders->isEmpty())
                <div class="alert alert-warning">Bạn chưa có đơn hàng nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $d)
                                <tr>
                                    <td><strong>#{{ $d->MaDH }}</strong></td>
                                    <td>{{ optional($d->NgayDat)->format('d/m/Y') }}</td>
                                    <td class="text-danger fw-bold">{{ number_format($d->TongTien, 0, ',', '.') }} đ</td>
                                    <td>
                                        <span class="badge {{ in_array($d->TrangThaiThanhToan, ['Đã thanh toán', 'Paid']) ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ $d->TrangThaiThanhToan ?? 'Chờ xử lý' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-primary" href="{{ route('orders.show', $d->MaDH) }}">
                                            Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
