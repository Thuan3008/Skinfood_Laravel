<?php

namespace App\Models;

class LoaiSP extends LegacyModel
{
    protected $table = 'LoaiSP';
    protected $primaryKey = 'MaLoai';

    public function danhMuc() { return $this->belongsTo(DanhMuc::class, 'MaDM', 'MaDM'); }
    public function sanPhams() { return $this->hasMany(SanPham::class, 'MaLoai', 'MaLoai'); }
}
