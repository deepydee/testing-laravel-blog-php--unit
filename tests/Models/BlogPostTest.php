<?php

namespace Tests\Models;

use App\Models\BlogPost;
use App\Models\BlogPostLike;
use Tests\TestCase;

class BlogPostTest extends TestCase
{
    public function test_with_factories(): void
    {
        $post = BlogPost::factory()
            ->has(BlogPostLike::factory()->count(5), 'postLikes')
            ->create();

        $this->assertCount(5, $post->postLikes);

        $postLike = BlogPostLike::factory()
            ->for(BlogPost::factory()->published())
            ->create();

        $this->assertTrue($postLike->blogPost->isPublished());
    }
}
