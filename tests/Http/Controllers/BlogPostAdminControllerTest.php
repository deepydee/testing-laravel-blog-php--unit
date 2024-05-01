<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\BlogPostAdminController;
use App\Models\BlogPost;
use App\Models\User;
use Tests\TestCase;

class BlogPostAdminControllerTest extends TestCase
{
    public function test_only_a_logged_in_user_can_make_changes_to_a_post(): void
    {
        $post = BlogPost::factory()->create();

        $sendRequest = fn() => $this->post(
            action([BlogPostAdminController::class, 'update'], $post->slug),
            [
                'title' => 'test',
                'author' => $post->author,
                'body' => $post->body,
                'date' => $post->date->format('Y-m-d'),
            ]
        );

        $sendRequest()
            ->assertRedirect(route('login'));

        $this->assertNotEquals('test', $post->refresh()->title);

        $this->login();

        $sendRequest();

        $this->assertEquals('test', $post->refresh()->title);
    }
}
