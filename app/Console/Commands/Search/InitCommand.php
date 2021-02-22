<?php

namespace App\Console\Commands\Search;

use App\Services\Search\ObjectIndexer;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Console\Command;

class InitCommand extends Command
{
    protected $signature = 'search:init';

    protected Client $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle(): bool
    {
        try {
            $this->client->indices()->delete([
                'index' => ObjectIndexer::INDEX,
            ]);
        }
        /** @noinspection PhpRedundantCatchClauseInspection */
        catch (Missing404Exception $exception) {}

        $this->client->indices()->create([
            'index' => ObjectIndexer::INDEX,
            'body' => [
                'mappings' => [
                    'objects' => [
                        '_source' => [
                            'enabled' => true,
                        ],
                        'properties' => [
                            'id' => [
                                'type' => 'integer',
                            ],
                            'name' => [
                                'type' => 'text',
                            ],
                            'code' => [
                                'type' => 'keyword',
                            ],
                            'object_type_id' => [
                                'type' => 'integer',
                            ],
                            'level' => [
                                'type' => 'integer',
                            ],
                            'parent_id' => [
                                'type' => 'integer',
                            ],
                            'parents' => [
                                'type' => 'integer',
                            ],
                            'created_at' => [
                                'type' => 'date',
                            ],
                            'updated_at' => [
                                'type' => 'date',
                            ],
                            'deleted_at' => [
                                'type' => 'date',
                            ],
                        ]
                    ]
                ],
                'settings' => [
                    'analysis' => [
                        'char_filter' => [
                            'replace' => [
                                'type' => 'mapping',
                                'mappings' => [
                                    '&=> and '
                                ],
                            ],
                        ],
                        'filter' => [
                            'word_delimiter' => [
                                'type' => 'word_delimiter',
                                'split_on_numerics' => false,
                                'split_on_case_change' => true,
                                'generate_word_parts' => true,
                                'generate_number_parts' => true,
                                'catenate_all' => true,
                                'preserve_original' => true,
                                'catenate_numbers' => true,
                            ],
                            'trigrams' => [
                                'type' => 'ngram',
                                'min_gram' => 4,
                                'max_gram' => 6,
                            ],
                        ],
                        'analyzer' => [
                            'default' => [
                                'type' => 'custom',
                                'char_filter' => [
                                    'html_strip',
                                    'replace',
                                ],
                                'tokenizer' => 'whitespace',
                                'filter' => [
                                    'lowercase',
                                    'word_delimiter',
                                    'trigrams',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        return true;
    }
}
