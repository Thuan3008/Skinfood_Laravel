@extends('layouts.app')

@section('title', 'Xác nhận và Thanh toán')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">Xác nhận đơn hàng</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('checkout.place') }}">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">1. Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Họ và tên (*)</label>
                            <input type="text" name="tenNguoiNhan" class="form-control" value="{{ old('tenNguoiNhan', old('recipient', auth()->user()->nguoiDung?->HoTen)) }}" required placeholder="Nhập họ tên người nhận" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại (*)</label>
                            <input type="text" name="soDienThoai" class="form-control" value="{{ old('soDienThoai', old('phone', auth()->user()->nguoiDung?->SoDienThoai)) }}" required placeholder="Nhập số điện thoại" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ nhận hàng (*)</label>
                            <textarea name="diaChi" class="form-control" rows="3" required placeholder="Số nhà, đường, phường, quận...">{{ old('diaChi', old('address', auth()->user()->nguoiDung?->DiaChi)) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="ghiChu" class="form-control" placeholder="Ghi chú thêm (tùy chọn)">{{ old('ghiChu', old('note')) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">2. Đơn hàng của bạn</h5>
                    </div>
                    <div class="card-body">
                        @if(!empty($cart))
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>SL</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $item)
                                        <tr>
                                            <td>{{ $item['name'] }}</td>
                                            <td>{{ $item['quantity'] }}</td>
                                            <td class="text-end">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}₫</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <hr />

                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính:</span>
                                <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                            </div>

                            @if(($discount ?? 0) > 0)
                                <div class="d-flex justify-content-between mb-2 text-success">
                                    <span>Voucher giảm ({{ session('discount_code') }}):</span>
                                    <span>-{{ number_format($discount, 0, ',', '.') }}₫</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between fs-4 fw-bold text-danger border-top pt-2">
                                <span>Tổng thanh toán:</span>
                                <span>{{ number_format($total ?? max(0, $subtotal - ($discount ?? 0)), 0, ',', '.') }}₫</span>
                            </div>

                            <hr />

                            <div class="mb-3">
                                <label class="form-label">Chọn phương thức thanh toán</label>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="phuongThuc" id="payCOD" value="COD" checked />
                                        <label class="form-check-label" for="payCOD">Thanh toán khi nhận hàng (COD)</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="phuongThuc" id="payBank" value="BankTransfer" />
                                        <label class="form-check-label" for="payBank">Chuyển khoản ngân hàng</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="phuongThuc" id="payQR" value="QR" />
                                        <label class="form-check-label" for="payQR">Thanh toán QR</label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="btn btn-success w-100 mt-4 btn-lg" onclick="return confirm('Xác nhận đặt hàng?');">
                            <i class="bi bi-check-circle-fill me-2"></i> XÁC NHẬN ĐẶT HÀNG
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
