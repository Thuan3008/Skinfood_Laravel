<?php

namespace App\Support;

use App\Models\{SanPham, DanhMuc, LoaiSP, ThuongHieu, NhaCungCap, TaiKhoan, NguoiDung, DonHang, DanhGia, LienHe, KhuyenMai, PhieuNhap, PhieuXuat};

class AdminResources
{
    public const MAP = [
        'san-pham' => [SanPham::class, 'Sản phẩm', 'admin.products.edit'],
        'danh-muc' => [DanhMuc::class, 'Danh mục', 'admin.danh-muc.edit'],
        'loai-sp' => [LoaiSP::class, 'Loại sản phẩm', 'admin.loai-sp.edit'],
        'thuong-hieu' => [ThuongHieu::class, 'Thương hiệu', 'admin.thuong-hieu.edit'],
        'nha-cung-cap' => [NhaCungCap::class, 'Nhà cung cấp', 'admin.nha-cung-cap.edit'],
        'tai-khoan' => [TaiKhoan::class, 'Tài khoản', 'admin.tai-khoan.edit'],
        'khuyen-mai' => [KhuyenMai::class, 'Khuyến mãi', 'admin.khuyen-mai.edit'],
        'nguoi-dung' => [NguoiDung::class, 'Người dùng', null],
        'don-hang' => [DonHang::class, 'Đơn hàng', null],
        'danh-gia' => [DanhGia::class, 'Đánh giá', null],
        'lien-he' => [LienHe::class, 'Liên hệ', null],
        'phieu-nhap' => [PhieuNhap::class, 'Phiếu nhập', null],
        'phieu-xuat' => [PhieuXuat::class, 'Phiếu xuất', null],
    ];

    public static function get(string $resource): array
    {
        abort_unless(isset(self::MAP[$resource]), 404);
        return self::MAP[$resource];
    }

    public static function fields(string $resource): array
    {
        return match ($resource) {
            'nguoi-dung' => ['HoTen' => ['Họ tên', 'required|string|max:100'], 'SoDienThoai' => ['Số điện thoại', 'nullable|string|max:20'], 'DiaChi' => ['Địa chỉ', 'nullable|string|max:255'], 'NgaySinh' => ['Ngày sinh', 'nullable|date'], 'GioiTinh' => ['Giới tính', 'nullable|in:Nam,Nữ,Khác']],
            'don-hang' => ['status' => ['Trạng thái thanh toán', 'required|in:Chưa thanh toán,Đã chuyển khoản - chờ xác nhận,Đã thanh toán,Đã hủy']],
            'danh-gia' => ['Diem' => ['Số sao', 'required|integer|between:1,5'], 'NoiDung' => ['Nội dung', 'required|string|max:500'], 'DuocApprove' => ['Đã duyệt', 'required|boolean']],
            'lien-he' => ['PhanHoi' => ['Nội dung phản hồi', 'required|string|max:5000']],
            'phieu-nhap' => ['NgayNhap' => ['Ngày nhập', 'required|date'], 'MaNCC' => ['Nhà cung cấp', 'required|integer|exists:NhaCungCap,MaNCC']],
            'phieu-xuat' => ['NguoiNhanHang' => ['Người nhận hàng', 'required|string|max:150'], 'SoDienThoaiNguoiNhan' => ['Số điện thoại người nhận', 'nullable|string|max:20'], 'DiaChiNguoiNhan' => ['Địa chỉ người nhận', 'nullable|string|max:255'], 'NgayXuat' => ['Ngày xuất', 'required|date'], 'GhiChu' => ['Ghi chú', 'nullable|string|max:500'], 'LyDoXuat' => ['Lý do xuất', 'nullable|string|max:255']],
            default => [],
        };
    }
}
