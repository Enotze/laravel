<?php

namespace App\Models;

/**
 * @property $living_complex_id
 * @property $corpus_id
 * @property $section_id
 * @property $floor_id
 */
class ParentObjectRecord extends ObjectRecord
{
    protected $visible = [
        'lvl_1_id',
        'lvl_2_id',
        'lvl_3_id',
        'lvl_4_id',
    ];
}
