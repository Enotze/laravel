<?php


namespace App\Models;


use App\Repositories\ObjectsRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @property $id
 * @property $name
 * @property $code
 * @property $object_type_id
 * @property $level
 * @property $parent_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @property-read Collection|ObjectRecord $parent
 * @property-read Collection|ObjectRecord $allParent
 */
class ObjectRecord extends Model
{
    use SoftDeletes;

    const LVL_1 = 1,
          LVL_2 = 2,
          LVL_3 = 3,
          LVL_4 = 4,
          LVL_5 = 5;

    protected $table = 'objects';

    protected $fillable = [
        'name',
        'code',
        'object_type_id',
        'level',
        'parent_id',
    ];

    public function getParent(): HasOne
    {
        return $this->hasOne(static::class, 'id', 'parent_id');
    }

    public function getAllParent(): HasOne
    {
        return $this->getParent()->withTrashed()->with('allParent');
    }

    public function parentsData(bool $with_inactive = false): HasOne
    {
        $query = $this->hasOne(ParentObjectRecord::class, 'id', 'id');

        $repository = app()->make(ObjectsRepository::class);
        $repository->parentsDataQuery($query);

        return $query;
    }

//    protected $casts = [
//        'created_at' => 'datetime',
//        'updated_at' => 'datetime',
//        'deleted_at' => 'datetime',
//    ];
}
