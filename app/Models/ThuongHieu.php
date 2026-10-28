<?php

namespace App\Models;

class ThuongHieu extends LegacyModel
{
    protected $table = 'ThuongHieu';
    protected $primaryKey = 'MaTH';

    public function sanPhams() { return $this->hasMany(SanPham::class, 'MaTH', 'MaTH'); }
}
