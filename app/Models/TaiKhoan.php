<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class TaiKhoan extends Authenticatable implements AuthenticatableContract
{
    use \App\Models\Concerns\VisibleInAdmin;
    protected $table = 'TaiKhoan';
    protected $primaryKey = 'MaTK';
    public $timestamps = false;
    protected $guarded = [];
    protected $hidden = ['MatKhauHash'];
    public function getAuthPassword() { return $this->MatKhauHash; }
    public function getAuthPasswordName() { return 'MatKhauHash'; }
    public function getRememberToken() { return null; }
    public function setRememberToken($value): void { }
    public function nguoiDung() { return $this->belongsTo(NguoiDung::class, 'MaND', 'MaND'); }
}
