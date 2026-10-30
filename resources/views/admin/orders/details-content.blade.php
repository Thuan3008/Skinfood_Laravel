@php
    $record->loadMissing(['nguoiDung', 'chiTiet.sanPham']);
    $subtotal = $record->chiTiet->sum(fn ($line) => (float) $line->DonGia * (int) $line->SoLuong);
@endphp
<div class="row g-3 mb-4">
    <div class="col-md-6"><section class="card h-100"><div class="card-header fw-semibold">Thông tin người nhận</div><div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-5 mb-2">Người nhận</dt><dd class="col-sm-7 mb-2">{{ $record->TenNguoiNhan ?: '—' }}</dd>
            <dt class="col-sm-5 mb-2">Số điện thoại</dt><dd class="col-sm-7 mb-2">{{ $record->SoDienThoai ?: '—' }}</dd>
            <dt class="col-sm-5 mb-2">Địa chỉ giao hàng</dt><dd class="col-sm-7 mb-2" style="overflow-wrap:anywhere">{{ $record->DiaChiGiaoHang ?: '—' }}</dd>
            <dt class="col-sm-5 mb-2">Khách đặt hàng</dt><dd class="col-sm-7 mb-2">{{ $record->nguoiDung?->HoTen ?: '—' }} @if($record->MaND)<small class="text-muted">(#{{ $record->MaND }})</small>@endif</dd>
            <dt class="col-sm-5 mb-0">Ghi chú</dt><dd class="col-sm-7 mb-0" style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $record->GhiChu ?: '—' }}</dd>
        </dl>
    </div></section></div>
    <div class="col-md-6"><section class="card h-100"><div class="card-header fw-semibold">Thông tin đơn hàng</div><div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-5 mb-2">Mã đơn hàng</dt><dd class="col-sm-7 mb-2">#{{ $record->MaDH }}</dd>
            <dt class="col-sm-5 mb-2">Ngày đặt</dt><dd class="col-sm-7 mb-2">{{ $record->NgayDat?->format('d/m/Y H:i') ?? '—' }}</dd>
            <dt class="col-sm-5 mb-2">Phương thức</dt><dd class="col-sm-7 mb-2">{{ match($record->PhuongThucThanhToan) { 'COD' => 'Thanh toán khi nhận hàng (COD)', 'BankTransfer' => 'Chuyển khoản', 'QR' => 'Thanh toán QR', default => $record->PhuongThucThanhToan ?: '—' } }}</dd>
            <dt class="col-sm-5 mb-2">Trạng thái</dt><dd class="col-sm-7 mb-2"><span class="badge {{ $record->TrangThaiThanhToan === 'Đã thanh toán' ? 'bg-success' : 'bg-secondary' }}">{{ $record->TrangThaiThanhToan ?: '—' }}</span></dd>
            <dt class="col-sm-5 mb-2">Ngày thanh toán</dt><dd class="col-sm-7 mb-2">{{ $record->NgayThanhToan?->format('d/m/Y H:i') ?? '—' }}</dd>
            <dt class="col-sm-5 mb-0">Mã tham chiếu</dt><dd class="col-sm-7 mb-0">{{ $record->MaThamChieuThanhToan ?: '—' }}</dd>
        </dl>
    </div></section></div>
</div>
<section class="card mb-4">
    <div class="card-header fw-semibold">Sản phẩm trong đơn hàng</div>
    <div class="table-responsive"><table class="table mb-0"><thead><tr><th>Sản phẩm</th><th class="text-center">Số lượng</th><th class="text-end">Đơn giá</th><th class="text-end">Thành tiền</th></tr></thead><tbody>
    @forelse($record->chiTiet as $line)
        <tr><td><div class="d-flex align-items-center gap-3"><img src="{{ $line->sanPham?->HinhAnh ? asset('legacy/Content/images/'.$line->sanPham->HinhAnh) : asset('favicon.ico') }}" alt="" width="45" height="50" style="object-fit:cover;border-radius:6px"><div><strong>{{ $line->sanPham?->TenSP ?? 'Sản phẩm không còn hiển thị' }}</strong><small class="d-block text-muted">#{{ $line->MaSP }}</small></div></div></td><td class="text-center">{{ $line->SoLuong }}</td><td class="text-end text-nowrap">{{ number_format($line->DonGia, 0, ',', '.') }} đ</td><td class="text-end text-nowrap">{{ number_format($line->DonGia * $line->SoLuong, 0, ',', '.') }} đ</td></tr>
    @empty <tr><td colspan="4">Đơn hàng chưa có sản phẩm.</td></tr> @endforelse
    </tbody></table></div>
    <div class="card-body border-top"><div class="ms-auto" style="max-width:380px"><div class="d-flex justify-content-between mb-2"><span>Tạm tính</span><strong>{{ number_format($subtotal, 0, ',', '.') }} đ</strong></div><div class="d-flex justify-content-between mb-2"><span>Giảm giá @if($record->MaKM)<small>(#{{ $record->MaKM }})</small>@endif</span><span>−{{ number_format($record->SoTienGiam ?? 0, 0, ',', '.') }} đ</span></div><div class="d-flex justify-content-between border-top pt-3"><strong>Tổng thanh toán</strong><strong class="text-danger">{{ number_format($record->TongTien ?? 0, 0, ',', '.') }} đ</strong></div></div></div>
</section>
