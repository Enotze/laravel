<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $name
 * @property $code
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @mixin Eloquent
 */
class ObjectType extends Model
{
    protected $table = 'object_types';

    protected $fillable = [
        'name',
        'code',
    ];

//    protected $casts = [
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime',
//        'deleted_at' => 'datetime',
//    ];
}
