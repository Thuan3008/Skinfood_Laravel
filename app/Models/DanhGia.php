<?php

namespace App\Models;

class DanhGia extends LegacyModel
{
    protected $table = 'DanhGia';
    protected $primaryKey = 'MaDG';
    protected $casts = ['NgayDanhGia' => 'datetime', 'DuocApprove' => 'boolean'];
    public function sanPham() { return $this->belongsTo(SanPham::class, 'MaSP', 'MaSP'); }
    public function nguoiDung() { return $this->belongsTo(NguoiDung::class, 'MaND', 'MaND'); }
}
