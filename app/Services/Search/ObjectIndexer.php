<?php

namespace App\Services\Search;

use App\Models\ObjectRecord;
use Carbon\Carbon;
use Elasticsearch\Client;
use Illuminate\Support\Collection;
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
            'body' => $this->convertObject($object),
        ]);
    }

    /**
     * @param Collection|ObjectRecord[] $objects
     */
    public function bulkIndex(Collection $objects): void
    {
        $data['body'] = $objects->map(function (ObjectRecord $object) {
            $params[] = [
                'index' => [
                    '_index' => self::INDEX,
                    '_type' => self::INDEX_TYPE,
                    '_id' => $object->id,
                ]
            ];
            $params[] = $this->convertObject($object);
            return $params;
        })->collapse()->toArray();

        $this->client->bulk($data);
    }

    public function remove(ObjectRecord $object): void
    {
        $this->client->delete([
            'index' => self::INDEX,
            'type' => self::INDEX_TYPE,
            'id' => $object->id,
        ]);
    }

    protected function getDate(?Carbon $date): ?string
    {
        return $date ? $date->format(DATE_ATOM) : null;
    }

    protected function convertObject(ObjectRecord $object): array
    {
        return [
            'id' => $object->id,
            'name' => $object->name,
            'code' => $object->code,
            'object_type_id' => $object->object_type_id,
            'level' => $object->level,
            'parent_id' => $object->parent_id,
            'parents' => $object->parentIds,
            'created_at' => $this->getDate($object->created_at),
            'updated_at' => $this->getDate($object->updated_at),
            'deleted_at' => $this->getDate($object->deleted_at),
        ];
    }
}
