@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container text-center py-5" style="margin-top: 50px;">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 80px;"></i>
    </div>

    <h2 class="text-success fw-bold">ĐẶT HÀNG THÀNH CÔNG!</h2>

    @if(isset($order) && $order->MaDH)
        <p class="fs-5">Mã đơn hàng của bạn: <strong>#{{ $order->MaDH }}</strong></p>
    @elseif(session('current_order'))
        <p class="fs-5">Mã đơn hàng của bạn: <strong>#{{ session('current_order') }}</strong></p>
    @endif

    <p class="text-muted">Cảm ơn bạn đã mua sắm tại SkinFood.</p>

    <div class="alert alert-info d-inline-block mt-3">
        Tự động về trang chủ trong <span id="countdown" class="fw-bold text-danger">5</span> giây...
    </div>

    <div class="mt-4">
        <a href="{{ route('home') }}" class="btn btn-primary">Quay về trang chủ ngay</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var seconds = 5;
    var element = document.getElementById("countdown");
    var interval = setInterval(function() {
        seconds--;
        if (element) {
            element.textContent = seconds;
        }
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "{{ route('home') }}";
        }
    }, 1000);
</script>
@endpush
