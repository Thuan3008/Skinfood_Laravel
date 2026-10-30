<?php

namespace App\Models\Concerns;

trait VisibleInAdmin
{
    public function scopeVisibleInAdmin($query)
    {
        $table = $this->getTable();
        $key = $this->getKeyName();
        return $query->whereNotExists(fn ($hidden) => $hidden->selectRaw('1')
            ->from('AdminHiddenRecords')->where('ResourceTable', $table)
            ->whereColumn('RecordId', $table.'.'.$key));
    }
}
