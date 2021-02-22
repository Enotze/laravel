<?php


namespace App\ValueObjects;


class SelectObjectParents
{
    public const TYPE_ID = 'id';
    public const TYPE_NAME = 'name';

    public string $type;
    public string $alias;
    public int $level;
    public ?array $objectTypeCodes = null;

    public function __construct(string $type, string $alias, int $level, ?array $objectTypeCodes = null)
    {
        $this->type = $type;
        $this->alias = $alias;
        $this->level = $level;
        $this->objectTypeCodes = $objectTypeCodes;
    }
}
