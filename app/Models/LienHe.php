<?php

namespace App\Models;

class LienHe extends LegacyModel
{
    protected $table = 'LienHe';
    protected $primaryKey = 'MaLH';
    protected $casts = ['NgayGui' => 'datetime'];
    public function nguoiDung() { return $this->belongsTo(NguoiDung::class, 'MaND', 'MaND'); }
}
