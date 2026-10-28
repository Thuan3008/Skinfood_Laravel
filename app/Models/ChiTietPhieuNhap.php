<?php

namespace App\Models;

class ChiTietPhieuNhap extends LegacyModel
{
    protected $table = 'ChiTietPhieuNhap';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    public function phieuNhap() { return $this->belongsTo(PhieuNhap::class, 'MaPN', 'MaPN'); }
    public function sanPham() { return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP'); }
}
