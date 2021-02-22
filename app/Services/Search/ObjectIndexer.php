<?php


namespace App\Services\Search;


use App\Models\ObjectRecord;
use Carbon\Carbon;
use Elasticsearch\Client;
use stdClass;

class ObjectIndexer
{
    const INDEX = 'app';
    const INDEX_TYPE = 'objects';

    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function clear(): void
    {
        $this->client->deleteByQuery([
            'index' => self::INDEX,
            'type' => self::INDEX_TYPE,
            'body' => [
                'query' => [
                    'match_all' => new stdClass(),
                ],
            ],
        ]);
    }

    public function index(ObjectRecord $object): void
    {
        $this->client->index([
            'index' => self::INDEX,
            'type' => self::INDEX_TYPE,
            'id' => $object->id,
            'body' => [
                'id' => $object->id,
                'name' => $object->name,
                'code' => $object->code,
                'object_type_id' => $object->object_type_id,
                'level' => $object->level,
                'parent_id' => $object->parent_id,
                'parents' => $this->getParents($object),
                'created_at' => $this->getDate($object->created_at),
                'updated_at' => $this->getDate($object->updated_at),
                'deleted_at' => $this->getDate($object->deleted_at),
            ],
        ]);
    }

    public function remove(ObjectRecord $object): void
    {
        $this->client->delete([
            'index' => self::INDEX,
            'type' => self::INDEX_TYPE,
            'id' => $object->id,
        ]);
    }

    private function getParents(ObjectRecord $object): array
    {
        $parents = [];
        if ($parent = $object->allParent) {
            do {
                $parents[] = $parent->id;
            } while ($parent = $parent->allParent);
        }
        return $parents;
    }

    protected function getDate(?Carbon $date): ?string
    {
        return $date ? $date->format(DATE_ATOM) : null;
    }
}
