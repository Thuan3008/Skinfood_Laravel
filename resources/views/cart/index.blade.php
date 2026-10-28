@extends('layouts.app')
@section('title', 'Giỏ hàng')
@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-cart3 fs-3 me-2 text-primary"></i>
        <h2 class="mb-0 fw-bold">Giỏ hàng của bạn ({{ count($cart) }} sản phẩm)</h2>
    </div>

    @if(session('error'))
        <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif

    @if(!$cart || count($cart) === 0)
        <div class="alert alert-warning text-center">
            <p class="mb-2">Giỏ hàng đang trống.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Sản phẩm</th>
                                        <th class="text-center">Giá</th>
                                        <th class="text-center" style="min-width: 160px;">Số lượng</th>
                                        <th class="text-end pe-4">Thành tiền</th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($cart as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item['image'] ?? asset('legacy/Content/images/placeholder.png') }}" class="rounded border me-3" style="width:70px;height:70px;object-fit:cover;" alt="{{ $item['name'] }}" onerror="this.src='https://via.placeholder.com/70'">
                                                <div>
                                                    <a href="{{ route('home.product', $item['id']) }}" class="text-dark text-decoration-none fw-bold">{{ $item['name'] }}</a><br>
                                                    <small class="text-muted">Mã: {{ $item['id'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center">
                                                <!-- Nút giảm số lượng (-) -->
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-qty-minus px-2 py-1" data-id="{{ $item['id'] }}" title="Giảm số lượng">-</button>

                                                <!-- Form nhập số lượng -->
                                                <form method="post" action="{{ route('cart.update', $item['id']) }}" class="d-inline-block mx-1 quantity-form">
                                                    @csrf
                                                    @method('put')
                                                    <input class="form-control form-control-sm quantity-input text-center" 
                                                           type="number" min="1" name="quantity" 
                                                           value="{{ $item['quantity'] }}" 
                                                           data-id="{{ $item['id'] }}"
                                                           style="width: 55px; display: inline-block;">
                                                </form>

                                                <!-- Nút tăng số lượng (+) -->
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-qty-plus px-2 py-1" data-id="{{ $item['id'] }}" title="Tăng số lượng">+</button>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-danger">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</td>
                                        <td class="text-center">
                                            <form method="post" action="{{ route('cart.remove', $item['id']) }}" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="text-danger border-0 bg-transparent p-1" onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')" title="Xóa sản phẩm">
                                                    <i class="bi bi-trash fs-5"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <span class="fw-bold">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                        </div>

                        <form id="discount-form" class="input-group my-3" action="{{ route('cart.discount') }}" method="post">
                            @csrf
                            <input class="form-control" name="code" placeholder="Mã giảm giá" value="{{ session('discount_code') }}">
                            <button class="btn btn-outline-danger">Áp dụng</button>
                        </form>
                        <div id="discount-message" class="small mb-2"></div>

                        <div id="discount-row" class="d-flex justify-content-between text-success {{ $discount > 0 ? '' : 'd-none' }}">
                            <span>Giảm giá</span>
                            <span id="discount-value">-{{ number_format($discount, 0, ',', '.') }}đ</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Tổng cộng</span>
                            <span class="text-primary" id="cart-total">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>

                        <a href="{{ route('checkout.review') }}" class="btn btn-primary w-100 fw-bold py-2">
                            Thanh toán
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-arrow-return-left me-2"></i>Tiếp tục mua sắm
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.quantity-input::-webkit-inner-spin-button, 
.quantity-input::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
}
.quantity-input {
    -moz-appearance: textfield;
}
.quantity-input:focus { 
    outline: none; 
    border-color: #0d6efd; 
    box-shadow: 0 0 0 0.15rem rgba(13,110,253,.25); 
}
.btn-qty-minus, .btn-qty-plus {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    line-height: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Nút giảm (-)
    document.querySelectorAll('.btn-qty-minus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var container = this.closest('td');
            var form = container.querySelector('.quantity-form');
            var input = form.querySelector('.quantity-input');
            var val = parseInt(input.value, 10) || 1;
            if (val > 1) {
                input.value = val - 1;
                form.submit();
            }
        });
    });

    // Nút tăng (+)
    document.querySelectorAll('.btn-qty-plus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var container = this.closest('td');
            var form = container.querySelector('.quantity-form');
            var input = form.querySelector('.quantity-input');
            var val = parseInt(input.value, 10) || 1;
            input.value = val + 1;
            form.submit();
        });
    });

    // Thay đổi số lượng trực tiếp trong ô input
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var val = parseInt(this.value, 10) || 1;
            if (val < 1) this.value = 1;
            this.closest('form').submit();
        });
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.blur();
                this.closest('form').submit();
            }
        });
    });

    // Xử lý mã giảm giá Ajax
    var discountForm = document.getElementById('discount-form');
    if (discountForm) {
        discountForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            var message = document.getElementById('discount-message');
            try {
                var response = await fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                var result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Mã không hợp lệ');
                document.getElementById('discount-row').classList.remove('d-none');
                document.getElementById('discount-value').textContent = '-' + new Intl.NumberFormat('vi-VN').format(result.discount) + 'đ';
                document.getElementById('cart-total').textContent = new Intl.NumberFormat('vi-VN').format(result.total) + 'đ';
                message.className = 'small mb-2 text-success';
                message.textContent = 'Áp dụng mã thành công!';
            } catch (error) {
                message.className = 'small mb-2 text-danger';
                message.textContent = error.message;
            }
        });
    }
});
</script>
@endsection
