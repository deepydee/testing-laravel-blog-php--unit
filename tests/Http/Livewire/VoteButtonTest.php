<?php

namespace Tests\Http\Livewire;

use App\Http\Livewire\VoteButton;
use App\Models\BlogPost;
use App\Models\BlogPostLike;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class VoteButtonTest extends TestCase
{
    public function test_like_can_be_toggled(): void
    {
        $post = BlogPost::factory()->create([
            'likes' => 10,
        ]);

        $likerUuid = Uuid::uuid4();

        $voteButton = Livewire::test(VoteButton::class, ['post' => $post, 'likerUuid' => $likerUuid]);

        $voteButton->call('like');

        $this->assertEquals(10 + 1, $post->refresh()->likes);
        $this->assertDatabaseHas(BlogPostLike::class, [
            'blog_post_id' => $post->id,
            'liker_uuid' => $likerUuid,
        ]);

        $voteButton->call('like');

        $this->assertEquals(10, $post->refresh()->likes);
        $this->assertDatabaseMissing(BlogPostLike::class, [
            'blog_post_id' => $post->id,
            'liker_uuid' => $likerUuid,
        ]);
    }
}
