<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class LegacyModel extends Model
{
    use \App\Models\Concerns\VisibleInAdmin;
    public $timestamps = false;
    protected $guarded = [];

    public function getRouteKeyName()
    {
        return $this->getKeyName();
    }
}
