<?php

namespace App\Models;

class PhieuNhap extends LegacyModel
{
    protected $table = 'PhieuNhap';
    protected $primaryKey = 'MaPN';

    public function nhaCungCap() { return $this->belongsTo(NhaCungCap::class, 'MaNCC', 'MaNCC'); }
    public function chiTietPhieuNhaps() { return $this->hasMany(ChiTietPhieuNhap::class, 'MaPN', 'MaPN'); }
}