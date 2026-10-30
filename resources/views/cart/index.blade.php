@extends('layouts.app')
@section('title', 'Giỏ hàng')
@section('content')
<div class="container py-5">
    <div class="d-flex align-items-center mb-4">
        <i class="bi bi-cart3 fs-3 me-2 text-primary"></i>
        <h2 class="mb-0 fw-bold">Giỏ hàng của bạn ({{ count($cart) }} sản phẩm)</h2>
    </div>

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
                                    <tr data-stock-url="{{ route('cart.stock', $item['id']) }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item['image'] ?? asset('legacy/Content/images/placeholder.png') }}" class="rounded border me-3" style="width:70px;height:70px;object-fit:cover;" alt="{{ $item['name'] }}" onerror="this.src='https://via.placeholder.com/70'">
                                                <div>
                                                    <a href="{{ route('home.product', $item['id']) }}" class="text-dark text-decoration-none fw-bold">{{ $item['name'] }}</a><br>
                                                    <small class="text-muted">Mã: {{ $item['id'] }}</small>
                                                    <div class="cart-stock-status small {{ $item['available'] < $item['quantity'] ? 'text-danger' : 'text-muted' }}" role="status">{{ $item['available'] === 0 ? 'Hết hàng' : ($item['available'] < $item['quantity'] ? 'Chỉ còn '.$item['available'].' sản phẩm. Vui lòng giảm số lượng.' : '') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center">
                                                <!-- Nút giảm số lượng (-) -->
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-qty-minus px-2 py-1" data-id="{{ $item['id'] }}" title="Giảm số lượng" @disabled($item['available'] === 0)>-</button>

                                                <!-- Form nhập số lượng -->
                                                <form method="post" action="{{ route('cart.update', $item['id']) }}" class="d-inline-block mx-1 quantity-form">
                                                    @csrf
                                                    @method('put')
                                                    <input class="form-control form-control-sm quantity-input text-center" 
                                                           type="number" min="1" name="quantity" 
                                                           value="{{ $item['quantity'] }}" 
                                                           max="{{ $item['available'] }}" @disabled($item['available'] === 0)
                                                           data-id="{{ $item['id'] }}"
                                                           style="width: 55px; display: inline-block;">
                                                </form>

                                                <!-- Nút tăng số lượng (+) -->
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-qty-plus px-2 py-1" data-id="{{ $item['id'] }}" title="Tăng số lượng" @disabled($item['available'] <= $item['quantity'])>+</button>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4 fw-bold text-danger">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ</td>
                                        <td class="text-center">
                                            <form method="post" action="{{ route('cart.remove', $item['id']) }}" class="d-inline">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="text-danger border-0 bg-transparent p-1" data-confirm="Xóa sản phẩm này khỏi giỏ hàng?" title="Xóa sản phẩm">
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

                        <div id="discount-row" class="d-flex justify-content-between text-success {{ $discount > 0 ? '' : 'd-none' }}">
                            <span>Giảm giá</span>
                            <span id="discount-value">-{{ number_format($discount, 0, ',', '.') }}đ</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                            <span>Tổng cộng</span>
                            <span class="text-primary" id="cart-total">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>

                        @php($stockBlocked = collect($cart)->contains(fn ($item) => $item['available'] < $item['quantity']))
                        <div id="cart-stock-warning" class="small text-danger mb-2 {{ $stockBlocked ? '' : 'd-none' }}" role="status">Có sản phẩm hết hàng hoặc không đủ số lượng. Vui lòng cập nhật hoặc xóa sản phẩm để tiếp tục.</div>
                        <a id="cart-checkout" href="{{ route('checkout.review') }}" class="btn btn-primary w-100 fw-bold py-2 {{ $stockBlocked ? 'disabled' : '' }}" aria-disabled="{{ $stockBlocked ? 'true' : 'false' }}">
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
    let checkingStock = false;
    const stockRows = [...document.querySelectorAll('tr[data-stock-url]')];
    const checkout = document.getElementById('cart-checkout');
    async function refreshStock() {
        if (checkingStock || document.hidden || !stockRows.length) return;
        checkingStock = true;
        try {
            await Promise.all(stockRows.map(async row => {
                try {
                    const response = await fetch(row.dataset.stockUrl, {headers: {'Accept': 'application/json'}, cache: 'no-store'});
                    if (!response.ok) return;
                    const result = await response.json();
                    const available = Math.max(0, Number(result.available) || 0);
                    const input = row.querySelector('.quantity-input');
                    const quantity = Number(input.value);
                    input.max = available;
                    input.disabled = available === 0;
                    row.querySelector('.btn-qty-minus').disabled = available === 0;
                    row.querySelector('.btn-qty-plus').disabled = available <= quantity;
                    row.dataset.stockBlocked = available < quantity ? 'true' : 'false';
                    const status = row.querySelector('.cart-stock-status');
                    status.textContent = available === 0 ? 'Hết hàng' : (available < quantity ? `Chỉ còn ${available} sản phẩm. Vui lòng giảm số lượng.` : '');
                    status.classList.toggle('text-danger', available < quantity);
                } catch (_) { /* Keep the last known stock; checkout checks again on the server. */ }
            }));
            const blocked = stockRows.some(row => row.dataset.stockBlocked === 'true' || row.querySelector('.quantity-input').disabled || Number(row.querySelector('.quantity-input').max) < Number(row.querySelector('.quantity-input').value));
            checkout.classList.toggle('disabled', blocked);
            checkout.setAttribute('aria-disabled', String(blocked));
            document.getElementById('cart-stock-warning').classList.toggle('d-none', !blocked);
        } finally { checkingStock = false; }
    }
    if (checkout) checkout.addEventListener('click', event => {
        if (checkout.getAttribute('aria-disabled') === 'true') event.preventDefault();
    });
    refreshStock();
    if (stockRows.length) setInterval(refreshStock, 5000);
    document.addEventListener('visibilitychange', refreshStock);
    // Nút giảm (-)
    document.querySelectorAll('.btn-qty-minus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var container = this.closest('td');
            var form = container.querySelector('.quantity-form');
            var input = form.querySelector('.quantity-input');
            var val = parseInt(input.value, 10) || 1;
            if (val > 1) {
                input.value = Math.min(val - 1, Number(input.max));
                form.requestSubmit();
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
            form.requestSubmit();
        });
    });

    // Thay đổi số lượng trực tiếp trong ô input
    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            var val = parseInt(this.value, 10) || 1;
            if (val < 1) this.value = 1;
            this.closest('form').requestSubmit();
        });
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.closest('form').requestSubmit();
            }
        });
    });

    // Xử lý mã giảm giá Ajax
    var discountForm = document.getElementById('discount-form');
    if (discountForm) {
        discountForm.addEventListener('submit', async function(event) {
            event.preventDefault();
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
                window.showToast('Áp dụng mã thành công!');
            } catch (error) {
                window.showToast(error.message, 'error');
            }
        });
    }
});
</script>
@endsection
