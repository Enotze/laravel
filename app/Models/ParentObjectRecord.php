<?php

namespace App\Models;

/**
 * @property $lvl_1_id
 * @property $lvl_2_id
 * @property $lvl_3_id
 * @property $lvl_4_id
 *
 * @property-read array $parentIds
 */
class ParentObjectRecord extends ObjectRecord
{
    protected $visible = [
        'lvl_1_id',
        'lvl_2_id',
        'lvl_3_id',
        'lvl_4_id',
    ];

    public function getParentIdsAttribute(): array
    {
        return array_filter([
            $this->lvl_1_id,
            $this->lvl_2_id,
            $this->lvl_3_id,
            $this->lvl_4_id,
        ]);
    }
}
