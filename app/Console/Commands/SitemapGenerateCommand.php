<?php

namespace App\Console\Commands;

use App\Post;
use Spatie\Sitemap\Sitemap;
use Illuminate\Console\Command;

class SitemapGenerateCommand extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap';

    public function handle() : int
    {
        $sitemap = Sitemap::create();

        $sitemap->add(route('home'));

        $sitemap->add(route('posts.index'));

        Post::all()->each(function (Post $post) use ($sitemap) {
            $sitemap->add(route('posts.show', $post->slug));
        });

        $sitemap->writeToFile(public_path('/sitemap.xml'));

        $this->info('Sitemap successfully generated.');

        return Command::SUCCESS;
    }
}