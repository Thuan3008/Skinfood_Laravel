<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class LegacyModel extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }
}
