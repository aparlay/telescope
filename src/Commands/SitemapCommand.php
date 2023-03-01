<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\User;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command implements Isolatable
{
    public $signature = 'core:sitemap';

    public $description = 'Generate sitemap';

    public $limit = 10000;

    private $lastUserId;
    private $lastMediaId;

    public function handle()
    {
        $this->lastUserId = User::query()->orderBy('_id')->first(['_id'])->_id;
        $this->lastMediaId = Media::query()->orderBy('_id')->first(['_id'])->_id;
        $this->info("Generating media sitemap");
        $this->addMediaUrls();
        $this->info("Generating user sitemap");
        $this->addUserUrls();
        $this->info("Generating index sitemap");
        $this->generateIndex();
        $this->comment(PHP_EOL);

        return self::SUCCESS;
    }

    private function addUserUrls()
    {
        $total = User::query()
            ->where('status', UserStatus::ACTIVE->value)
            ->where('stats.counters.medias', '>=', 1)
            ->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        $i = 1;
        do {
            $sitemap = SitemapGenerator::create(config('app.main_url'))->getSitemap();
            $users = $this->getUserChunk();
            $count = count($users);
            foreach ($users as $user) {
                if (! empty($user['username'])) {
                    $sitemap->add(Url::create(config('app.main_url').'/@'.$user['username'])->setPriority(0.5));
                }
            }
            $bar->advance($count / $total);
            $sitemap->writeToFile(public_path('xml/users_'.($i * $this->limit).'.xml'));
            $i++;
        } while ($count === $this->limit);

        $bar->finish();
        $this->info("");
    }

    private function addMediaUrls()
    {
        $total = Media::query()->public()->confirmed()->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        $i = 1;
        do {
            $sitemap = SitemapGenerator::create(config('app.main_url'))->getSitemap();
            $medias = $this->getMediaChunk();
            $count = count($medias);
            foreach ($medias as $media) {
                if (! empty($media['slug'])) {
                    $sitemap->add(
                        Url::create(config('app.main_url').'/feed/'.$media['slug'])->setPriority(0.5)
                    );
                }
            }
            $bar->advance($count / $total);
            $sitemap->writeToFile(public_path('xml/medias_'.($i * $this->limit).'.xml'));
            $i++;
        } while ($count === $this->limit);

        $bar->finish();
        $this->info("");
    }

    private function getUserChunk()
    {
        $users = User::where('_id', '>', $this->lastUserId)
            ->select(['username'])
            ->active()
            ->where('stats.counters.medias', '>=', 1)
            ->orderBy('_id')
            ->limit($this->limit)
            ->applyScopes()
            ->getQuery()
            ->get()
            ->toArray();
        $lastUser = end($users);
        $this->lastUserId = $lastUser['_id'];

        return $users;
    }

    private function getMediaChunk()
    {
        $medias = Media::where('_id', '>', $this->lastMediaId)
            ->select(['slug'])
            ->public()
            ->confirmed()
            ->orderBy('_id')
            ->limit($this->limit)
            ->applyScopes()
            ->getQuery()
            ->get()
            ->toArray();

        $lastMedia = end($medias);
        $this->lastMediaId = $lastMedia['_id'];

        return $medias;
    }

    public function generateIndex()
    {
        $files = glob(public_path('xml/').'*.xml');
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        $sitemap = SitemapIndex::create();
        foreach ($files as $filename) {
            $sitemap->add(str_replace(public_path(), '', $filename));
            $bar->advance();
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));
        $bar->finish();
        $this->comment(PHP_EOL);

        return self::SUCCESS;
    }
}
