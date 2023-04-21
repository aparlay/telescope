<?php

namespace Aparlay\Core\Commands;

use Illuminate\Console\Command;
use MeiliSearch\Client;

class MeilisearchSettingCommand extends Command
{
    public $signature   = 'meilisearch:setting';
    public $description = 'This command is responsible for recreating Search Engine Settings';

    public function handle()
    {
        $client   = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));

        $settings = [
            'displayedAttributes' => ['*'],
            'searchableAttributes' => ['username', 'full_name', 'description', 'hashtags'],
            'filterableAttributes' => ['title', 'type'],
            'sortableAttributes' => ['_geo', 'score'],
            'rankingRules' => ['words', 'typo', 'proximity', 'attribute', 'sort', 'exactness'],
        ];

        $client->index('global')->updateSettings($settings);

        return self::SUCCESS;
    }
}
