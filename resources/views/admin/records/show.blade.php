@extends('layouts.admin')
@section('title', 'Xem '.$title)
@section('content')
<h1 class="h4 mb-4">{{ $title }} #{{ $record->getKey() }}</h1>
@if($resource === 'don-hang')
    @include('admin.orders.details-content')
@else
@php
    $labels = ['MaSP'=>'Mã sản phẩm','TenSP'=>'Tên sản phẩm','GiaBan'=>'Giá bán','SoLuongTon'=>'Tồn kho','GiamGia'=>'Giảm giá (%)','MoTa'=>'Mô tả','TrangThaiSanPham'=>'Tình trạng','MaDM'=>'Mã danh mục','TenDM'=>'Tên danh mục','MaLoai'=>'Mã loại','TenLoai'=>'Tên loại','MaTH'=>'Mã thương hiệu','TenTH'=>'Thương hiệu','QuocGia'=>'Quốc gia','MaNCC'=>'Mã nhà cung cấp','TenNCC'=>'Nhà cung cấp','DiaChi'=>'Địa chỉ','SoDienThoai'=>'Số điện thoại','Email'=>'Email','NguoiLienHe'=>'Người liên hệ','MaND'=>'Mã người dùng','HoTen'=>'Họ tên','NgaySinh'=>'Ngày sinh','GioiTinh'=>'Giới tính','NgayTao'=>'Ngày tạo','MaTK'=>'Mã tài khoản','TenDangNhap'=>'Tên đăng nhập','VaiTro'=>'Vai trò','TrangThai'=>'Trạng thái','MaDH'=>'Mã đơn hàng','NgayDat'=>'Ngày đặt','TongTien'=>'Tổng tiền','TenNguoiNhan'=>'Người nhận','DiaChiGiaoHang'=>'Địa chỉ giao hàng','GhiChu'=>'Ghi chú','TrangThaiThanhToan'=>'Thanh toán','PhuongThucThanhToan'=>'Phương thức thanh toán','NgayThanhToan'=>'Ngày thanh toán','MaDG'=>'Mã đánh giá','NoiDung'=>'Nội dung','Diem'=>'Số sao','DuocApprove'=>'Đã duyệt','NgayDanhGia'=>'Ngày đánh giá','MaLH'=>'Mã liên hệ','NgayGui'=>'Ngày gửi','MaPN'=>'Mã phiếu nhập','NgayNhap'=>'Ngày nhập','MaPX'=>'Mã phiếu xuất','NgayXuat'=>'Ngày xuất','LyDoXuat'=>'Lý do xuất','MaKM'=>'Mã khuyến mãi','TenChuongTrinh'=>'Chương trình','MaCode'=>'Mã giảm giá','LoaiGiamGia'=>'Loại giảm giá','GiaTriGiam'=>'Giá trị giảm','NgayBatDau'=>'Ngày bắt đầu','NgayKetThuc'=>'Ngày kết thúc','DonHangToiThieu'=>'Đơn hàng tối thiểu','GiamToiDa'=>'Giảm tối đa','SoLuongPhatHanh'=>'Số lượng phát hành','SoLuongDaDung'=>'Số lượng đã dùng','SoTienGiam'=>'Số tiền giảm'];
    $lines = match($resource) { 'don-hang' => $record->chiTiet()->with('sanPham')->get(), 'phieu-nhap' => $record->chiTietPhieuNhaps()->with('sanPham')->get(), 'phieu-xuat' => $record->chiTietPhieuXuats()->with('sanPham')->get(), default => collect() };
    $labels['PhanHoi'] = 'Phản hồi';
    $labels['NgayPhanHoi'] = 'Ngày phản hồi';
    $labels['NguoiNhanHang'] = 'Người nhận hàng';
    $labels['SoDienThoaiNguoiNhan'] = 'Số điện thoại người nhận';
    $labels['DiaChiNguoiNhan'] = 'Địa chỉ người nhận';
@endphp
<dl class="row bg-white rounded border p-3">
@foreach($record->attributesToArray() as $key => $value)
    @if(isset($labels[$key]))
        <dt class="col-sm-4 py-2">{{ $labels[$key] }}</dt><dd class="col-sm-8 py-2 mb-0" style="white-space:pre-wrap;overflow-wrap:anywhere">{{ is_bool($value) ? ($value ? 'Có' : 'Không') : ($value ?? '—') }}</dd>
    @endif
@endforeach
</dl>
@if($resource === 'san-pham' && $record->HinhAnh)
<img src="{{ asset('legacy/Content/images/'.$record->HinhAnh) }}" alt="{{ $record->TenSP }}" class="img-thumbnail mb-3" style="max-height:220px">
@endif
@if($lines->isNotEmpty())
<h2 class="h5">Danh sách sản phẩm</h2>
<div class="table-responsive"><table class="table"><thead><tr><th>Sản phẩm</th><th>Số lượng</th><th>Đơn giá</th></tr></thead><tbody>
@foreach($lines as $line)<tr><td>{{ $line->sanPham?->TenSP ?? '#'.$line->MaSP }}</td><td>{{ $line->SoLuong }}</td><td>{{ number_format($line->GiaNhap ?? $line->DonGia ?? 0, 0, ',', '.') }} đ</td></tr>@endforeach
</tbody></table></div>
@endif
@endif
@endsection
