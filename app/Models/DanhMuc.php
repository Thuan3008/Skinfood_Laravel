<?php

namespace App\Models;

class DanhMuc extends LegacyModel
{
    protected $table = 'DanhMuc';
    protected $primaryKey = 'MaDM';

    public function loaiSanPhams() { return $this->hasMany(LoaiSP::class, 'MaDM', 'MaDM'); }
    public function sanPhams() { return $this->hasMany(SanPham::class, 'MaDM', 'MaDM'); }
}
