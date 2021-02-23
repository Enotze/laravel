<?php


namespace App\Repositories;


use App\Models\ObjectRecord;
use App\Models\ObjectType;
use App\ValueObjects\SelectObjectParents;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;

class ObjectsRepositoryEloquent
{
    /**
     * @param EloquentBuilder|null $query
     * @return EloquentBuilder
     */
    public function parentsDataQuery($query = null)
    {
        $objectTable = (new ObjectRecord())->getTable();

        $query = empty($query) ? ObjectRecord::query() : $query;

        $query
            ->distinct()
            ->select(['objects.id'])
            ->from($objectTable . ' as objects');


        $this->parentsDataSelect($query);

        $this->joinObjects($query, 'parent1', 'objects', 'parent1', 'parent1');
        $this->joinObjects($query, 'parent2', 'parent1', 'parent2', 'parent2');
        $this->joinObjects($query, 'parent3', 'parent2', 'parent3', 'parent3');
        $this->joinObjects($query, 'parent4', 'parent3', 'parent4', 'parent4');

        return $query;
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     */
    protected function parentsDataSelect($query): void
    {
        $this->buildSelectParents($query, [
            new SelectObjectParents(
                SelectObjectParents::TYPE_ID,
                'lvl_1_id',
                ObjectRecord::LVL_1
            ),
            new SelectObjectParents(
                SelectObjectParents::TYPE_ID,
                'lvl_2_id',
                ObjectRecord::LVL_2
            ),
            new SelectObjectParents(
                SelectObjectParents::TYPE_ID,
                'lvl_3_id',
                ObjectRecord::LVL_3
            ),
            new SelectObjectParents(
                SelectObjectParents::TYPE_ID,
                'lvl_4_id',
                ObjectRecord::LVL_4
            ),
        ]);
    }

    /**
     * @param EloquentBuilder $query
     * @param $alias
     * @param $first
     * @param $second
     * @param $deleted
     * @param $with_inactive
     * @param $additional_join
     */
    public function joinObjects(
        $query,
        $alias,
        $first,
        $second,
        $deleted,
        $with_inactive = false,
        string $additional_join = null
    ) {
        $objectTable = (new ObjectRecord())->getTable();
        $query->leftJoin("{$objectTable} as {$alias}", function (JoinClause $join) use (
            $first,
            $second,
            $deleted,
            $with_inactive,
            $additional_join
        ) {
            $join->on("{$first}.parent_id", '=', "{$second}.id");
            if (!$with_inactive) {
                $join->whereNull("{$deleted}.deleted_at");
            }
            if (!empty($additional_join)) {
                $join->whereRaw($additional_join);
            }
        });
    }

    /**
     * @param EloquentBuilder|QueryBuilder $query
     * @param SelectObjectParents[] $selects
     * @param bool $without_current
     */
    public function buildSelectParents($query, array $selects, bool $without_current = true)
    {
        $object_types = $this->getObjectTypesForSelect($selects);

        foreach ($selects as $key => $select) {
            $select_string = 'CASE ';
            $parent_iter = $without_current ? 1 : 0;
            $object_type_ids = implode(',', $object_types[$key] ?? []);

            for ($i = 1; $i <= 5; $i++) {
                $select_string .= "WHEN objects.level = {$i} THEN ";
                if ($select->level > $i) {
                    $select_string .= 'NULl ';
                } else {
                    if ($without_current && $select->level == $i) {
                        $select_string .= 'NULl ';
                        continue;
                    } else {
                        $parent_column = $parent_iter == 0 ? 'objects' : "parent{$parent_iter}";
                    }

                    if (!empty($object_types)) {
                        if (empty($object_type_ids)) {
                            $select_string .= 'NULl ';
                            $parent_iter++;
                            continue;
                        } else {
                            $select_string .=
                                "CASE WHEN {$parent_column}.object_type_id NOT IN ({$object_type_ids}) THEN NULL ELSE ";
                        }

                        if ($select->type == SelectObjectParents::TYPE_ID) {
                            $select_string .= "{$parent_column}.id END ";
                        } elseif ($select->type == SelectObjectParents::TYPE_NAME) {
                            $select_string .=
                                "CASE WHEN {$parent_column}.display_name = '' THEN {$parent_column}.name " .
                                "ELSE {$parent_column}.display_name END END ";
                        }
                    } else {
                        $select_string .= "{$parent_column}.id ";
                    }

                    $parent_iter++;
                }
            }
            $select_string .= "END as {$select->alias}";

            $query->selectRaw($select_string);
        }
    }

    /**
     * @param SelectObjectParents[] $selects
     * @return array
     */
    private function getObjectTypesForSelect(array $selects): array
    {
        $objectTypeCodes = [];
        foreach ($selects as $select) {
            $objectTypeCodes = array_merge(
                $objectTypeCodes,
                $select->objectTypeCodes ?? []
            );
        }

        if (empty($objectTypeCodes)) {
            return [];
        }

        /** @var ObjectType[] $objectTypes */
        $objectTypes = ObjectType::whereIn('code', $objectTypeCodes)->get();

        $objectTypesForSelect = [];
        foreach ($selects as $key => $select) {
            foreach ($objectTypes as $objectType) {
                if (in_array($objectType->code, $select->objectTypeCodes)) {
                    $objectTypesForSelect[$key][] = $objectType->id;
                }
            }
        }

        return $objectTypesForSelect;
    }
}
