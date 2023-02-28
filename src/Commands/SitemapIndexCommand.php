<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;

class SitemapIndexCommand extends Command
{
    public $signature = 'core:sitemap-index';

    public $description = 'Generate sitemap index';

    public function handle()
    {
        $files = glob(public_path('xml/').'pages_*.xml');
        $bar = $this->output->createProgressBar(count($files));

        $sitemap = SitemapIndex::create();
        foreach ($files as $filename) {
            $sitemap->add(str_replace(public_path(), '', $filename));
            $bar->advance();
        }

        $sitemap->writeToFile(public_path('xml/sitemap.xml'));
        $bar->finish();

        return self::SUCCESS;
    }
}
