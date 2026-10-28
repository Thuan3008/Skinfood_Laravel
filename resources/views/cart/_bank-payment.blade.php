<div class="container py-5">
    <h2 class="text-center mb-4">{{ $heading }}</h2>
    <div class="row justify-content-center"><div class="col-lg-7"><div class="card shadow"><div class="card-body text-center p-4">
    @if($paid)
        <div class="alert alert-success mb-0"><div class="fs-1">✓</div><h4>Thanh toán thành công</h4><p>Đơn hàng #{{ $order->MaDH }} đã được xác nhận.</p><a href="{{ route('orders.show', $order->MaDH) }}" class="btn btn-success">Xem đơn hàng</a></div>
    @else
        <div id="payment-success" class="alert alert-success d-none"><div class="fs-1">✓</div><h4>Thanh toán thành công</h4><p class="mb-0">Đơn hàng đã được xác nhận. Mã QR đã được đóng.</p></div>
        <div id="payment-panel" data-expires-at="{{ $expiresAt->timestamp * 1000 }}" data-status-url="{{ route('checkout.status', $order->MaDH) }}">
            <h5 class="mb-3">Quét mã để thanh toán</h5>
            <div class="d-flex justify-content-center mb-3">
                <img id="vietqr-api-image" src="{{ $qrApiUrl }}" alt="VietQR thanh toán đơn #{{ $order->MaDH }}" width="300" height="300" class="img-fluid rounded" referrerpolicy="no-referrer">
                <div id="vietqr-code" data-payload="{{ $payload }}" class="d-none justify-content-center"></div>
            </div>
            <small id="qr-source" class="d-block text-muted mb-2">QR động được tạo qua VietQR API; có chế độ dự phòng khi mất kết nối.</small>
            <p>QR còn hiệu lực trong <strong id="qr-countdown" class="text-danger">15:00</strong></p>
            <div id="qr-expired" class="alert alert-warning d-none">Mã QR đã hết hạn. <button class="btn btn-sm btn-warning ms-2" onclick="window.location.reload()">Tạo QR mới</button></div>
            <hr>
            <div class="text-start">
                <p><b>Ngân hàng:</b> <span id="bank-name">{{ $bank['bank_name'] }}</span></p>
                <p><b>Số tài khoản:</b> <span id="bank-account">{{ $bank['account_number'] }}</span></p>
                <p><b>Chủ tài khoản:</b> <span id="bank-owner">{{ $bank['account_name'] }}</span></p>
                <p><b>Số tiền:</b> <span id="transfer-amount" data-amount="{{ (int) round($order->TongTien) }}" class="text-danger fw-bold">{{ number_format($order->TongTien, 0, ',', '.') }} đ</span></p>
                <p><b>Nội dung:</b> <span id="transfer-content" class="fw-bold">{{ $description }}</span></p>
                <p><b>Mã đơn:</b> #{{ $order->MaDH }}</p>
            </div>
            <button type="button" class="btn btn-outline-secondary w-100" id="copy-transfer-info">Sao chép thông tin chuyển khoản</button>
            <form class="mt-3" method="post" action="{{ route('checkout.confirm', $order->MaDH) }}">@csrf<button class="btn btn-success w-100" type="submit">Tôi đã chuyển khoản</button></form>
            <small class="d-block text-muted mt-2">Shop sẽ kiểm tra giao dịch; chỉ admin hoặc webhook ngân hàng mới xác nhận đã thanh toán.</small>
        </div>
    @endif
    </div></div></div></div>
</div>
@if(!$paid)
@push('scripts')
<script src="{{ asset('js/qrcode.min.js') }}"></script>
<script>
(()=>{const qr=document.getElementById('vietqr-code'),apiImage=document.getElementById('vietqr-api-image'),panel=document.getElementById('payment-panel');if(!qr||!panel)return;
new QRCode(qr,{text:qr.dataset.payload,width:300,height:300,colorDark:'#000',colorLight:'#fff',correctLevel:QRCode.CorrectLevel.M});
const useLocalQr=()=>{apiImage.classList.add('d-none');qr.classList.remove('d-none');qr.classList.add('d-flex');document.getElementById('qr-source').textContent='Đang dùng QR dự phòng được tạo trực tiếp trên trình duyệt.'};apiImage.addEventListener('error',useLocalQr);if(apiImage.complete&&apiImage.naturalWidth===0)useLocalQr();
let countdownTimer,statusTimer;const expiresAt=Number(panel.dataset.expiresAt);const update=()=>{const remaining=Math.max(0,Math.ceil((expiresAt-Date.now())/1000));document.getElementById('qr-countdown').textContent=String(Math.floor(remaining/60)).padStart(2,'0')+':'+String(remaining%60).padStart(2,'0');if(remaining<=0){clearInterval(countdownTimer);clearInterval(statusTimer);apiImage.removeAttribute('src');apiImage.classList.add('d-none');qr.replaceChildren();qr.classList.add('d-none');document.getElementById('qr-expired').classList.remove('d-none');document.getElementById('copy-transfer-info').disabled=true;panel.querySelector('button[type=submit]').disabled=true}};update();countdownTimer=setInterval(update,1000);
const check=async()=>{try{const response=await fetch(panel.dataset.statusUrl,{credentials:'same-origin',cache:'no-store'}),result=await response.json();if(result.paid){clearInterval(countdownTimer);clearInterval(statusTimer);panel.classList.add('d-none');document.getElementById('payment-success').classList.remove('d-none')}}catch(_){}};statusTimer=setInterval(check,3000);
document.getElementById('copy-transfer-info').addEventListener('click',async function(){const text=['Ngân hàng: '+document.getElementById('bank-name').textContent.trim(),'Số tài khoản: '+document.getElementById('bank-account').textContent.trim(),'Chủ tài khoản: '+document.getElementById('bank-owner').textContent.trim(),'Số tiền: '+document.getElementById('transfer-amount').dataset.amount+' VND','Nội dung: '+document.getElementById('transfer-content').textContent.trim()].join('\n');try{await navigator.clipboard.writeText(text);this.textContent='Đã sao chép'}catch(_){window.prompt('Sao chép thông tin:',text)}});
panel.querySelector('form').addEventListener('submit',async function(event){event.preventDefault();const button=this.querySelector('button');try{const response=await fetch(this.action,{method:'POST',body:new FormData(this),headers:{Accept:'application/json'},credentials:'same-origin'}),result=await response.json();if(!response.ok)throw new Error(result.message||'Không gửi được xác nhận.');button.textContent='Đã báo shop kiểm tra';button.disabled=true}catch(error){alert(error.message)}});
window.addEventListener('pageshow',event=>{if(event.persisted)window.location.reload()});})();
</script>
@endpush
@endif
