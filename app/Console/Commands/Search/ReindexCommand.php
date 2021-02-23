<?php

namespace App\Console\Commands\Search;

use App\Models\ObjectRecord;
use App\Services\Bench\BenchConsole;
use App\Services\Search\ObjectIndexer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
        BenchConsole::start();

        $this->indexer->clear();

        BenchConsole::mark('search:reindex - clear');

        ObjectRecord::withTrashed()
            ->with('parentsData')
            ->orderBy('id')
            ->chunk(1000, function (Collection $objects) {
                $this->indexer->bulkIndex($objects);
            });

        BenchConsole::mark('search:reindex - index');

        BenchConsole::stop();
        Log::info('search:reindex', BenchConsole::getStats());

        return true;
    }
}
