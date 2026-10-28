<?php

namespace App\Models;

class ChiTietPhieuXuat extends LegacyModel
{
    protected $table = 'ChiTietPhieuXuat';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    public function phieuXuat() { return $this->belongsTo(PhieuXuat::class, 'MaPX', 'MaPX'); }
    public function sanPham() { return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP'); }
}
