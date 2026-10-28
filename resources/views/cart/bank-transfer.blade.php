@extends('layouts.app')

@section('title', 'Chuyển khoản')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">Chuyển khoản ngân hàng</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">
                    @if($paid)
                        <div id="payment-success" class="alert alert-success mb-0">
                            <div class="fs-1 mb-2">✓</div>
                            <h4>Thanh toán thành công</h4>
                            <p class="mb-3">Đơn hàng #{{ $order->MaDH }} đã được xác nhận thanh toán.</p>
                            <a href="{{ route('orders.show', $order->MaDH) }}" class="btn btn-success">Xem đơn hàng</a>
                        </div>
                    @else
                        <div id="payment-success" class="alert alert-success d-none">
                            <div class="fs-1 mb-2">✓</div>
                            <h4>Thanh toán thành công</h4>
                            <p class="mb-0">Đơn hàng đã được xác nhận. Mã QR đã được đóng.</p>
                        </div>

                        <div id="payment-panel"
                             data-lifetime="900"
                             data-expires-at="{{ $expiresAt->timestamp * 1000 }}"
                             data-status-url="{{ route('checkout.status', $order->MaDH) }}">
                            <h5 class="mb-3">Quét mã để thanh toán</h5>
                            <div class="d-flex justify-content-center mb-3">
                                @if(!empty($qrApiUrl))
                                    <img id="vietqr-api-image" src="{{ $qrApiUrl }}" alt="VietQR đơn hàng #{{ $order->MaDH }}" width="300" height="300" class="img-fluid rounded" referrerpolicy="no-referrer" onerror="this.classList.add('d-none'); document.getElementById('vietqr-code').classList.remove('d-none'); document.getElementById('vietqr-code').classList.add('d-flex');">
                                @endif
                                <div id="vietqr-code"
                                     data-payload="{{ $payload }}"
                                     class="{{ !empty($qrApiUrl) ? 'd-none' : 'd-flex' }} justify-content-center"
                                     aria-label="VietQR đơn hàng #{{ $order->MaDH }}"></div>
                            </div>
                            <p class="mb-3">QR còn hiệu lực trên trang trong <strong id="qr-countdown" class="text-danger">15:00</strong></p>
                            <div id="qr-expired" class="alert alert-warning d-none">
                                Mã QR đã hết hạn.
                                <button type="button" class="btn btn-warning btn-sm ms-2" onclick="window.location.reload()">Tạo QR mới</button>
                            </div>

                            <hr />

                            <div class="text-start">
                                <p><b>Ngân hàng:</b> <span id="bank-name">{{ $bank['bank_name'] ?? 'Ngân hàng' }}</span></p>
                                <p><b>Số tài khoản:</b> <span id="bank-account">{{ $bank['account_number'] ?? '' }}</span></p>
                                <p><b>Chủ tài khoản:</b> <span id="bank-owner">{{ $bank['account_name'] ?? '' }}</span></p>
                                <p><b>Số tiền:</b> <span id="transfer-amount" data-amount="{{ (int) round($order->TongTien) }}" class="text-danger fw-bold">{{ number_format($order->TongTien, 0, ',', '.') }} đ</span></p>
                                <p><b>Nội dung:</b> <span id="transfer-content" class="fw-bold">{{ $description }}</span></p>
                                <p><b>Mã đơn:</b> #{{ $order->MaDH }}</p>
                            </div>

                            <button type="button" class="btn btn-outline-secondary w-100" id="copy-transfer-info">Sao chép thông tin chuyển khoản</button>

                            <form method="post" action="{{ route('checkout.confirm', $order->MaDH) }}">
                                @csrf
                                <input type="hidden" name="ajax" value="1">
                                <button type="submit" class="btn btn-success w-100 mt-3">Tôi đã chuyển khoản</button>
                            </form>
                            <small class="d-block text-muted mt-2">Shop sẽ kiểm tra giao dịch trước khi xác nhận đã thanh toán.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if(!$paid)
@push('scripts')
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
    const qrElement = document.getElementById('vietqr-code');
    const apiImage = document.getElementById('vietqr-api-image');
    const paymentPanel = document.getElementById('payment-panel');
    let countdownTimer;
    let statusTimer;

    if (qrElement && window.QRCode && qrElement.dataset.payload) {
        new QRCode(qrElement, {
            text: qrElement.dataset.payload,
            width: 300,
            height: 300,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.M
        });
    }

    if (paymentPanel) {
        const expiresAt = Number(paymentPanel.dataset.expiresAt || (Date.now() + 900000));
        const countdown = document.getElementById('qr-countdown');
        const updateCountdown = function () {
            const remaining = Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000));
            const minutes = Math.floor(remaining / 60).toString().padStart(2, '0');
            const seconds = (remaining % 60).toString().padStart(2, '0');
            if (countdown) countdown.textContent = minutes + ':' + seconds;
            if (remaining <= 0) {
                clearInterval(countdownTimer);
                clearInterval(statusTimer);
                if (apiImage) {
                    apiImage.removeAttribute('src');
                    apiImage.classList.add('d-none');
                }
                if (qrElement) qrElement.replaceChildren();
                document.getElementById('qr-expired')?.classList.remove('d-none');
                const copyBtn = document.getElementById('copy-transfer-info');
                if (copyBtn) copyBtn.disabled = true;
                const submitBtn = paymentPanel.querySelector('form button[type="submit"]');
                if (submitBtn) submitBtn.disabled = true;
                return;
            }
        };
        updateCountdown();
        countdownTimer = setInterval(updateCountdown, 1000);

        const checkPaymentStatus = async function () {
            try {
                const response = await fetch(paymentPanel.dataset.statusUrl, {credentials: 'same-origin', cache: 'no-store'});
                const result = await response.json();
                if (result.paid) {
                    clearInterval(countdownTimer);
                    clearInterval(statusTimer);
                    paymentPanel.classList.add('d-none');
                    document.getElementById('payment-success')?.classList.remove('d-none');
                }
            } catch (_) {
                // Giữ QR hiển thị nếu lần kiểm tra trạng thái tạm thời thất bại.
            }
        };
        statusTimer = setInterval(checkPaymentStatus, 3000);

        document.getElementById('copy-transfer-info')?.addEventListener('click', async function () {
            const text = [
                'Ngân hàng: ' + (document.getElementById('bank-name')?.textContent.trim() || ''),
                'Số tài khoản: ' + (document.getElementById('bank-account')?.textContent.trim() || ''),
                'Chủ tài khoản: ' + (document.getElementById('bank-owner')?.textContent.trim() || ''),
                'Số tiền: ' + (document.getElementById('transfer-amount')?.dataset.amount || '') + ' VND',
                'Nội dung: ' + (document.getElementById('transfer-content')?.textContent.trim() || '')
            ].join('\n');
            try {
                await navigator.clipboard.writeText(text);
                this.textContent = 'Đã sao chép';
            } catch (_) {
                window.prompt('Sao chép thông tin chuyển khoản:', text);
            }
        });

        paymentPanel.querySelector('form')?.addEventListener('submit', async function (event) {
            event.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            try {
                const response = await fetch(this.action, {method: 'POST', body: new FormData(this), credentials: 'same-origin'});
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'Không gửi được xác nhận.');
                button.textContent = 'Đã báo shop kiểm tra';
                button.disabled = true;
            } catch (error) {
                window.alert(error.message || 'Không gửi được xác nhận. Vui lòng thử lại.');
            }
        });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) window.location.reload();
        });
    }
</script>
@endpush
@endif
