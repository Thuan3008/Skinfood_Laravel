<?php

namespace App\Models;

class PhieuXuat extends LegacyModel
{
    protected $table = 'PhieuXuat';
    protected $primaryKey = 'MaPX';
    public function nguoiLap() { return $this->belongsTo(NguoiDung::class, 'MaND', 'MaND'); }

    public function chiTietPhieuXuats() { return $this->hasMany(ChiTietPhieuXuat::class, 'MaPX', 'MaPX'); }
}
