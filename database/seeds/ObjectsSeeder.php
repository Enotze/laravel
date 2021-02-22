<?php

namespace Database\Seeds;

use App\Models\ObjectRecord;
use App\Models\ObjectType;
use Illuminate\Database\Seeder;

class ObjectsSeeder extends Seeder
{
    const LVL_COUNTS = [
        ObjectRecord::LVL_1 => 50,
        ObjectRecord::LVL_2 => 10,
        ObjectRecord::LVL_3 => 10,
        ObjectRecord::LVL_4 => 25,
        ObjectRecord::LVL_5 => 5,
    ];

    public function run()
    {
        $this->createObjects(
            null,
            ObjectRecord::LVL_1,
            self::LVL_COUNTS[ObjectRecord::LVL_1]
        );
    }

    protected function createObjects(?int $parentId, int $lvl, int $count): void
    {
        $objectTypeId = factory(ObjectType::class)->create()->getAttribute('id');

        factory(ObjectRecord::class, $count)->create([
            'parent_id' => $parentId,
            'object_type_id' => $objectTypeId,
            'level' => $lvl,
        ])->each(function (ObjectRecord $object) use ($lvl) {
            $nextLevel = $lvl + 1;
            if (empty($count = self::LVL_COUNTS[$nextLevel] ?? null)) {
                return;
            }
            $this->createObjects($object->id, $nextLevel, $count);
        });
    }
}
