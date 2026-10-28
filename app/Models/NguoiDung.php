<?php

namespace App\Models;

class NguoiDung extends LegacyModel
{
    protected $table = 'NguoiDung';
    protected $primaryKey = 'MaND';
    protected $casts = ['NgaySinh' => 'date', 'NgayTao' => 'datetime'];

    public function taiKhoans() { return $this->hasMany(TaiKhoan::class, 'MaND', 'MaND'); }
    public function donHangs() { return $this->hasMany(DonHang::class, 'MaND', 'MaND'); }
}
