<?php

namespace App\Traits;

trait HasLegacyData
{
    public function initializeHasLegacyData()
    {
        $this->fillable[] = 'legacy_id';
        $this->fillable[] = 'legacy_table';
    }

    public function scopeFromLegacy($query, $id, $table = null)
    {
        $query->where('legacy_id', $id);
        
        if ($table) {
            $query->where('legacy_table', $table);
        }
        
        return $query;
    }
}