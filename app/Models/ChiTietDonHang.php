<?php

namespace App\Models;

class ChiTietDonHang extends LegacyModel
{
    protected $table = 'ChiTietDonHangs';
    protected $primaryKey = null;
    public $incrementing = false;
    protected $casts = ['DonGia' => 'decimal:2'];
    public function sanPham() { return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP'); }
}
