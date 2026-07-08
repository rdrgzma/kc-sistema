<?php

namespace App\Traits;

trait HasLegacyData
{
    public function initializeHasLegacyData(): void
    {
        if (! in_array('legacy_id', $this->fillable)) {
            $this->fillable[] = 'legacy_id';
        }

        if (! in_array('legacy_table', $this->fillable)) {
            $this->fillable[] = 'legacy_table';
        }
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
