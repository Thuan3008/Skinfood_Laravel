<?php

namespace App\Models;

class KhuyenMai extends LegacyModel
{
    protected $table = 'KhuyenMai';
    protected $primaryKey = 'MaKM';
    protected $casts = [
        'TrangThai' => 'boolean',
        'NgayBatDau' => 'datetime',
        'NgayKetThuc' => 'datetime',
        'GiaTriGiam' => 'decimal:2',
    ];

    public function sanPhams() { return $this->belongsToMany(SanPham::class, 'KhuyenMai_SanPham', 'MaKM', 'MaSP'); }
}
