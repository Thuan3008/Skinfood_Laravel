<?php

namespace App\Models;

class PhieuXuat extends LegacyModel
{
    protected $table = 'PhieuXuat';
    protected $primaryKey = 'MaPX';

    public function chiTietPhieuXuats() { return $this->hasMany(ChiTietPhieuXuat::class, 'MaPX', 'MaPX'); }
}