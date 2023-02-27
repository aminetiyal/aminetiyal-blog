<?php

namespace Feature\Tests\App\Models;

use Tests\TestCase;
use App\Models\Post;
use App\Models\Redirect;

class PostTest extends TestCase
{
    public function test_it_creates_a_redirect_when_slug_changes() : void
    {
        $post = Post::factory()->create();

        $original = $post->slug;

        $post->update(['slug' => fake()->slug()]);

        $this->assertDatabaseHas(Redirect::class, [
            'from' => $original,
            'to' => $post->slug,
        ]);
    }

    public function test_it_does_not_create_a_redirect_when_slug_does_not_change() : void
    {
        $post = Post::factory()->create();

        $post->update(['slug' => $post->slug]);

        $this->assertDatabaseMissing(Redirect::class, [
            'from' => $post->slug,
            'to' => $post->slug,
        ]);
    }

    public function test_it_gets_posts_as_a_sequence() : void
    {
        $posts = Post::factory(30)->create();

        $ids = $posts->shuffle()->take(4)->pluck('id');

        $sequence = Post::asSequence($ids)->get();

        $this->assertEquals($ids->get(0), $sequence->get(0)->id);
        $this->assertEquals($ids->get(1), $sequence->get(1)->id);
        $this->assertEquals($ids->get(2), $sequence->get(2)->id);
        $this->assertEquals($ids->get(3), $sequence->get(3)->id);
    }

    public function test_it_has_a_with_user_scope() : void
    {
        Post::factory()->create();

        $post = Post::withUser()->first();

        $this->assertEquals($post->user->name, $post->user_name);
        $this->assertEquals($post->user->email, $post->user_email);
    }

    public function test_it_feeds_the_feed() : void
    {
        Post::factory(10)->create();

        $this
            ->getJson('/feed')
            ->assertOk();
    }
}
