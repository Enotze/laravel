<?php

namespace App\Console\Commands\Search;

use App\Models\ObjectRecord;
use App\Services\Search\ObjectIndexer;
use Illuminate\Console\Command;

class ReindexCommand extends Command
{
    protected $signature = 'search:reindex';

    private ObjectIndexer $indexer;

    public function __construct(ObjectIndexer $indexer)
    {
        parent::__construct();
        $this->indexer = $indexer;
    }

    public function handle(): bool
    {
        $this->indexer->clear();

        foreach (ObjectRecord::withTrashed()->orderBy('id')->cursor() as $advert) {
            $this->indexer->index($advert);
        }

        return true;
    }
}
