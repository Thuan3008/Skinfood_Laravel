<?php

namespace App\Models;

class SanPham extends LegacyModel
{
    protected $table = 'SanPham';
    protected $primaryKey = 'MaSP';
    protected $casts = [
        'GiamGia' => 'decimal:2',
        'GiaBan' => 'decimal:2',
        'DoTinCay' => 'decimal:1',
        'NgayNhapKho' => 'datetime',
    ];

    public function danhMuc() { return $this->belongsTo(DanhMuc::class, 'MaDM', 'MaDM'); }
    public function loai() { return $this->belongsTo(LoaiSP::class, 'MaLoai', 'MaLoai'); }
    public function thuongHieu() { return $this->belongsTo(ThuongHieu::class, 'MaTH', 'MaTH'); }
    public function khuyenMais() { return $this->belongsToMany(KhuyenMai::class, 'KhuyenMai_SanPham', 'MaSP', 'MaKM'); }
    public function danhGias() { return $this->hasMany(DanhGia::class, 'MaSP', 'MaSP'); }
}
