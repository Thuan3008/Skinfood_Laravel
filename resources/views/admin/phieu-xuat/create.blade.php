@extends('layouts.admin')
@section('title', 'Lập phiếu xuất mới')
@push('styles')
<style>
.export-form .form-control,.export-form .form-select{max-width:none}
.export-form .export-table{min-width:760px}
.export-form .export-table th{font-size:13px;text-align:center;background:#f7f8fa}
.export-form .export-table td{vertical-align:middle}
.export-form .export-total{font-size:18px;color:#dc3545}
</style>
@endpush
@section('content')
<div class="container py-3 export-form">
    <div class="card shadow-sm">
        <form method="post" action="{{ route('admin.phieu-xuat.store') }}" id="export-form" data-confirm="Xác nhận lưu phiếu xuất và trừ số lượng hàng trong kho?">
            @csrf
            <input type="hidden" name="NgayXuat" value="{{ now()->toDateString() }}">
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><label class="form-label fw-bold" for="export-recipient">Người nhận hàng <span class="text-danger">*</span></label><input id="export-recipient" class="form-control" name="NguoiNhanHang" value="{{ old('NguoiNhanHang') }}" maxlength="150" placeholder="Ví dụ: Kho Hà Nội, Nhân viên A..." required></div>
                    <div class="col-md-6"><label class="form-label fw-bold" for="export-phone">Số điện thoại người nhận</label><input id="export-phone" type="tel" class="form-control" name="SoDienThoaiNguoiNhan" value="{{ old('SoDienThoaiNguoiNhan') }}" maxlength="20" placeholder="Nhập số điện thoại người nhận"></div>
                    <div class="col-md-6"><label class="form-label fw-bold" for="export-address">Địa chỉ người nhận</label><input id="export-address" class="form-control" name="DiaChiNguoiNhan" value="{{ old('DiaChiNguoiNhan') }}" maxlength="255" placeholder="Nhập địa chỉ nhận hàng"></div>
                    <div class="col-md-6"><label class="form-label fw-bold" for="export-note">Ghi chú</label><textarea id="export-note" class="form-control" name="GhiChu" rows="1" maxlength="500" placeholder="Lý do xuất kho...">{{ old('GhiChu') }}</textarea></div>
                </div>
                <hr>
                <div class="d-flex align-items-center justify-content-between my-3"><h2 class="h6 text-secondary fw-bold mb-0">Chi tiết sản phẩm</h2><button type="button" class="btn btn-sm btn-success" id="export-add-row"><i class="fa-solid fa-plus me-1"></i>Thêm dòng</button></div>
                <div class="table-responsive">
                    <table class="table table-bordered export-table mb-0">
                        <thead><tr><th style="width:40%">Sản phẩm (Tồn kho)</th><th style="width:15%">Số lượng xuất</th><th>Đơn giá (VNĐ)</th><th style="width:20%">Thành tiền</th><th>Xóa</th></tr></thead>
                        <tbody id="export-lines">
                        @foreach(old('details', [['MaSP' => '', 'SoLuong' => 1, 'DonGia' => '']]) as $index => $line)
                            <tr>
                                <td><select class="form-select export-product" name="details[{{ $index }}][MaSP]" required aria-label="Sản phẩm"><option value="">-- Chọn sản phẩm --</option>@foreach($products as $product)<option value="{{ $product->MaSP }}" data-stock="{{ $product->SoLuongTon ?? 0 }}" data-price="{{ round((float) ($product->GiaBan ?? 0)) }}" @selected(($line['MaSP'] ?? '') == $product->MaSP) @disabled(($product->SoLuongTon ?? 0) <= 0)>{{ $product->TenSP }} (Tồn: {{ $product->SoLuongTon ?? 0 }})</option>@endforeach</select><small class="export-stock-note text-danger" role="status"></small></td>
                                <td><input type="number" class="form-control text-center export-quantity" name="details[{{ $index }}][SoLuong]" value="{{ $line['SoLuong'] ?? 1 }}" min="1" step="1" required aria-label="Số lượng xuất"></td>
                                <td><input type="number" class="form-control text-end export-price" name="details[{{ $index }}][DonGia]" value="{{ isset($line['DonGia']) && $line['DonGia'] !== '' ? round((float) $line['DonGia']) : '' }}" min="0" max="999999999999" step="1" required aria-label="Đơn giá"></td>
                                <td><input class="form-control text-end bg-light export-line-total" value="0" readonly aria-label="Thành tiền"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger export-remove-row" title="Xóa dòng" aria-label="Xóa dòng"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot><tr><td colspan="3" class="text-end fw-bold">TỔNG CỘNG:</td><td colspan="2" class="fw-bold export-total" id="export-total" aria-live="polite">0 đ</td></tr></tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('admin.phieu-xuat.index') }}" class="btn btn-secondary" data-close-admin-popup><i class="fa-solid fa-arrow-left me-1"></i>Hủy bỏ</a><button class="btn btn-primary" type="submit"><i class="fa-solid fa-floppy-disk me-1"></i>Lưu phiếu xuất</button></div>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('js/export-form.js') }}"></script>
@endpush
