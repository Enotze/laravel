<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Builder;

interface ObjectsRepository
{
    /**
     * @param Builder|null $query
     * @return Builder
     */
    public function parentsDataQuery($query = null);
}
