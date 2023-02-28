<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Models\User;
use Flow\FileOpenException;
use Flow\Uploader;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;

class SitemapCommand extends Command
{
    public $signature = 'core:sitemap';

    public $description = 'Generate sitemap';

    private $lastUsername = '';

    public function handle()
    {
        foreach (range('a', 'z') as $letter) {
            $counts = User::query()->where('username', 'regexp', '/^'.$letter.'.*/')->count();
            if ($counts === 0) {
                continue;
            }

            $offset = 0;
            do {
                foreach ($this->getUserChunk($letter, $offset) as $user) {
                }
            } while ($counts > 10000);
        }
        SitemapGenerator::create(config('app.frontend_url'))
            ->getSitemap()
            // here we add one extra link, but you can add as many as you'd like
            ->add(
                Url::create(config('app.frontend_url').'/@'.$user->username)
                    ->addImage($user->avatar, '@'.$user->username.' profile page')
                    ->setPriority(0.5)
            )
            ->writeToFile($sitemapPath);

        SitemapIndex::create()
            ->add('/pages_sitemap.xml')
            ->add('/posts_sitemap.xml')
            ->writeToFile(public_path('xml/sitemap.xml'));

        return self::SUCCESS;
    }

    private function getUserChunk($limit = 0)
    {
        return User::query()->select(['username'])
            ->where('username', '>', $this->lastUsername)
            ->orderBy('username')
            ->limit($limit)
            ->get(['username']);
    }
}
