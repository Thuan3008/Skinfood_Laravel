<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DanhMuc', function (Blueprint $table) {
            $table->increments('MaDM');
            $table->string('TenDM', 100);
        });

        Schema::create('ThuongHieu', function (Blueprint $table) {
            $table->increments('MaTH');
            $table->string('TenTH', 100)->nullable();
            $table->string('QuocGia', 100)->nullable();
        });

        Schema::create('LoaiSP', function (Blueprint $table) {
            $table->increments('MaLoai');
            $table->string('TenLoai', 100);
            $table->unsignedInteger('MaDM');
            $table->foreign('MaDM')->references('MaDM')->on('DanhMuc');
        });

        Schema::create('NguoiDung', function (Blueprint $table) {
            $table->increments('MaND');
            $table->string('HoTen', 100);
            $table->string('SoDienThoai', 20)->nullable();
            $table->string('DiaChi', 255)->nullable();
            $table->string('GioiTinh', 10)->nullable();
            $table->date('NgaySinh')->nullable();
            $table->string('Avatar', 50)->nullable();
            $table->dateTime('NgayTao')->nullable();
        });

        Schema::create('NhaCungCap', function (Blueprint $table) {
            $table->increments('MaNCC');
            $table->string('TenNCC', 100);
            $table->string('DiaChi', 255)->nullable();
            $table->string('SoDienThoai', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->string('NguoiLienHe', 100)->nullable();
        });

        Schema::create('SanPham', function (Blueprint $table) {
            $table->increments('MaSP');
            $table->string('TenSP', 200)->nullable();
            $table->decimal('GiamGia', 18, 2)->nullable();
            $table->decimal('GiaBan', 18, 2)->nullable();
            $table->text('MoTa')->nullable();
            $table->string('HinhAnh', 255)->nullable();
            $table->integer('SoLuongTon')->nullable();
            $table->unsignedInteger('MaDM')->nullable();
            $table->unsignedInteger('MaTH')->nullable();
            $table->unsignedInteger('MaLoai')->nullable();
            $table->string('LoaiDa', 50)->nullable();
            $table->string('VanDeChiRi', 255)->nullable();
            $table->string('TonDaMau', 50)->nullable();
            $table->text('ThanhPhanChiNhYeu')->nullable();
            $table->integer('SoLanSuDungMoiTuan')->nullable();
            $table->decimal('DoTinCay', 3, 1)->nullable();
            $table->string('KichCoTieuChuan', 50)->nullable();
            $table->dateTime('NgayNhapKho')->nullable();
            $table->string('TrangThaiSanPham', 50)->nullable();
            $table->foreign('MaDM')->references('MaDM')->on('DanhMuc')->nullOnDelete();
            $table->foreign('MaTH')->references('MaTH')->on('ThuongHieu')->nullOnDelete();
            $table->foreign('MaLoai')->references('MaLoai')->on('LoaiSP')->nullOnDelete();
        });

        Schema::create('KhuyenMai', function (Blueprint $table) {
            $table->increments('MaKM');
            $table->string('TenChuongTrinh', 200);
            $table->string('MaCode', 50);
            $table->string('LoaiGiamGia', 20)->nullable();
            $table->decimal('GiaTriGiam', 18, 2);
            $table->decimal('GiamToiDa', 18, 2)->nullable();
            $table->decimal('DonHangToiThieu', 18, 2)->nullable();
            $table->integer('SoLuongPhatHanh')->nullable();
            $table->integer('SoLuongDaDung')->nullable();
            $table->dateTime('NgayBatDau')->nullable();
            $table->dateTime('NgayKetThuc')->nullable();
            $table->boolean('TrangThai')->nullable();
        });

        Schema::create('KhuyenMai_SanPham', function (Blueprint $table) {
            $table->unsignedInteger('MaKM');
            $table->unsignedInteger('MaSP');
            $table->primary(['MaKM', 'MaSP']);
            $table->foreign('MaKM')->references('MaKM')->on('KhuyenMai')->cascadeOnDelete();
            $table->foreign('MaSP')->references('MaSP')->on('SanPham')->cascadeOnDelete();
        });

        Schema::create('TaiKhoan', function (Blueprint $table) {
            $table->increments('MaTK');
            $table->string('TenDangNhap', 100)->unique();
            $table->string('MatKhauHash', 500);
            $table->string('VaiTro', 20)->nullable();
            $table->unsignedInteger('MaND');
            $table->foreign('MaND')->references('MaND')->on('NguoiDung');
        });

        Schema::create('DonHang', function (Blueprint $table) {
            $table->increments('MaDH');
            $table->unsignedInteger('MaND')->nullable();
            $table->dateTime('NgayDat')->nullable();
            $table->decimal('TongTien', 18, 2)->nullable();
            $table->string('DiaChiGiaoHang', 255)->nullable();
            $table->string('TenNguoiNhan', 100)->nullable();
            $table->string('SoDienThoai', 20)->nullable();
            $table->string('GhiChu', 255)->nullable();
            $table->string('TrangThaiThanhToan', 50)->nullable();
            $table->dateTime('NgayThanhToan')->nullable();
            $table->string('PhuongThucThanhToan', 100)->nullable();
            $table->unsignedInteger('MaKM')->nullable();
            $table->decimal('SoTienGiam', 18, 2)->nullable();
            $table->foreign('MaND')->references('MaND')->on('NguoiDung')->nullOnDelete();
            $table->foreign('MaKM')->references('MaKM')->on('KhuyenMai')->nullOnDelete();
        });

        Schema::create('ChiTietDonHangs', function (Blueprint $table) {
            $table->unsignedInteger('MaDH');
            $table->unsignedInteger('MaSP');
            $table->integer('SoLuong')->nullable();
            $table->decimal('DonGia', 18, 2)->nullable();
            $table->primary(['MaDH', 'MaSP']);
            $table->foreign('MaDH')->references('MaDH')->on('DonHang')->cascadeOnDelete();
            $table->foreign('MaSP')->references('MaSP')->on('SanPham');
        });

        Schema::create('DanhGia', function (Blueprint $table) {
            $table->increments('MaDG');
            $table->unsignedInteger('MaSP')->nullable();
            $table->unsignedInteger('MaND')->nullable();
            $table->string('NoiDung', 500)->nullable();
            $table->integer('Diem')->nullable();
            $table->dateTime('NgayDanhGia')->nullable();
            $table->boolean('DuocApprove')->nullable();
            $table->string('TraLoiAdmin', 500)->nullable();
            $table->dateTime('ThoiGianTraLoi')->nullable();
            $table->foreign('MaSP')->references('MaSP')->on('SanPham')->nullOnDelete();
            $table->foreign('MaND')->references('MaND')->on('NguoiDung')->nullOnDelete();
        });

        Schema::create('LienHe', function (Blueprint $table) {
            $table->increments('MaLH');
            $table->string('HoTen', 100)->nullable();
            $table->string('Email', 100)->nullable();
            $table->string('SoDienThoai', 20)->nullable();
            $table->string('NoiDung', 1000)->nullable();
            $table->dateTime('NgayGui')->nullable();
            $table->unsignedInteger('MaND')->nullable();
            $table->string('TrangThai', 50)->nullable();
            $table->foreign('MaND')->references('MaND')->on('NguoiDung')->nullOnDelete();
        });

        Schema::create('PhieuNhap', function (Blueprint $table) {
            $table->increments('MaPN');
            $table->unsignedInteger('MaND')->nullable();
            $table->unsignedInteger('MaNCC')->nullable();
            $table->dateTime('NgayNhap')->nullable();
            $table->decimal('TongTien', 18, 2)->nullable();
            $table->string('GhiChu', 500)->nullable();
            $table->foreign('MaND')->references('MaND')->on('NguoiDung')->nullOnDelete();
            $table->foreign('MaNCC')->references('MaNCC')->on('NhaCungCap')->nullOnDelete();
        });

        Schema::create('ChiTietPhieuNhap', function (Blueprint $table) {
            $table->unsignedInteger('MaPN');
            $table->unsignedInteger('MaSP');
            $table->integer('SoLuong')->nullable();
            $table->decimal('GiaNhap', 18, 2)->nullable();
            $table->decimal('ThanhTien', 18, 2)->nullable();
            $table->date('NgaySanXuat')->nullable();
            $table->date('HanSuDung')->nullable();
            $table->primary(['MaPN', 'MaSP']);
            $table->foreign('MaPN')->references('MaPN')->on('PhieuNhap')->cascadeOnDelete();
            $table->foreign('MaSP')->references('MaSP')->on('SanPham');
        });

        Schema::create('PhieuXuat', function (Blueprint $table) {
            $table->increments('MaPX');
            $table->unsignedInteger('MaND')->nullable();
            $table->dateTime('NgayXuat')->nullable();
            $table->string('LyDoXuat', 255)->nullable();
            $table->string('GhiChu', 500)->nullable();
            $table->foreign('MaND')->references('MaND')->on('NguoiDung')->nullOnDelete();
        });

        Schema::create('ChiTietPhieuXuat', function (Blueprint $table) {
            $table->unsignedInteger('MaPX');
            $table->unsignedInteger('MaSP');
            $table->integer('SoLuong')->nullable();
            $table->primary(['MaPX', 'MaSP']);
            $table->foreign('MaPX')->references('MaPX')->on('PhieuXuat')->cascadeOnDelete();
            $table->foreign('MaSP')->references('MaSP')->on('SanPham');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach (['ChiTietPhieuXuat', 'PhieuXuat', 'ChiTietPhieuNhap', 'PhieuNhap', 'LienHe', 'DanhGia', 'ChiTietDonHangs', 'DonHang', 'TaiKhoan', 'KhuyenMai_SanPham', 'KhuyenMai', 'SanPham', 'NhaCungCap', 'NguoiDung', 'LoaiSP', 'ThuongHieu', 'DanhMuc'] as $table) {
            Schema::dropIfExists($table);
        }
        Schema::enableForeignKeyConstraints();
    }
};
