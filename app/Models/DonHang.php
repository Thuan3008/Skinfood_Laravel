<?php

namespace App\Models;

class DonHang extends LegacyModel
{
    protected $table = 'DonHang';
    protected $primaryKey = 'MaDH';
    protected $casts = ['NgayDat' => 'datetime', 'NgayThanhToan' => 'datetime', 'QrHetHanLuc' => 'datetime', 'TongTien' => 'decimal:2', 'SoTienGiam' => 'decimal:2'];
    public function chiTiet() { return $this->hasMany(ChiTietDonHang::class, 'MaDH', 'MaDH'); }
    public function nguoiDung() { return $this->belongsTo(NguoiDung::class, 'MaND', 'MaND'); }
    public function getRouteKeyName() { return 'MaDH'; }
}
