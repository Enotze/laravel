<?php


namespace App\Console\Commands\Search;


use App\Services\Search\ObjectIndexer;
use Elasticsearch\Client;
use Illuminate\Console\Command;

class GetChildrenCommand extends Command
{
    protected $signature = 'search:objects_children';

    private Client $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle()
    {
        $response = $this->client->search([
            'index' => ObjectIndexer::INDEX,
            'type' => ObjectIndexer::INDEX_TYPE,
            'body' => [
//                '_source' => ['id'],
                'size' => 1,
                'query' => [
                    'bool' => [
                        'must' => [
                            ['terms' => ['parents' => [73]]],
//                            ['terms' => ['parents' => [1]]],
//                            ['term' => ['level' => 3]],
                        ],
                    ],
//                    'match_all' => new \stdClass(),
                ]
            ]
        ]);

//        dd(array_column($response['hits']['hits'], '_id'));
        dd($response);
    }
}
