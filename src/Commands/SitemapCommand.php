<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\Enums\UserStatus;
use Aparlay\Core\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Jenssegers\Mongodb\Collection;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapCommand extends Command implements Isolatable
{
    public $signature = 'core:sitemap';

    public $description = 'Generate sitemap';

    public $limit = 10000;

    private $lastUsername = '';

    public function handle()
    {
        $sitemap = SitemapGenerator::create(config('app.frontend_url'))->getSitemap();
        $total = User::where('status', UserStatus::ACTIVE->value)->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        $i = 1;
        do {
            $users = $this->getUserChunk();
            $count = count($users);
            foreach ($users as $user) {
                $sitemap->add(
                    Url::create(config('app.frontend_url').'/@'.$user['username'])
                        ->setPriority(0.5)
                );
            }
            $bar->advance($count / $total);
            $sitemap->writeToFile(public_path('xml/pages_'.($i * $this->limit).'.xml'));
            $i++;
        } while ($count === $this->limit);

        $bar->finish();
        $this->comment(PHP_EOL);

        return self::SUCCESS;
    }

    private function getUserChunk()
    {
        $users = User::raw(function (Collection $collection) {
            return $collection->aggregate([
                [
                    '$match' => [
                        'status' => UserStatus::ACTIVE->value,
                        'username' => ['$gt' => $this->lastUsername],
                    ],
                ],
                [
                    '$project' => [
                        '_id' => 0,
                        'username' => 1,
                    ],
                ],
                [
                    '$sort' => [
                        'username' => 1,
                    ],
                ],
                [
                    '$limit' =>$this->limit,
                ],
            ]);
        })->toArray();

        $this->lastUsername = end($users)['username'];

        return $users;
    }
}
