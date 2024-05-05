<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\BlogPostAdminController;
use App\Http\Controllers\UpdatePostSlugController;
use App\Models\BlogPost;
use App\Models\Redirect;
use Tests\Factories\BlogPostRequestDataFactory;
use Tests\TestCase;

class BlogPostAdminControllerTest extends TestCase
{
    private BlogPost $blogPost;
    private BlogPostRequestDataFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->blogPost = BlogPost::factory()->create();
        $this->factory = BlogPostRequestDataFactory::new()->withPost($this->blogPost);
    }
    public function test_only_a_logged_in_user_can_make_changes_to_a_post(): void
    {
        $sendRequest = fn() => $this->post(
            action([BlogPostAdminController::class, 'update'], $this->blogPost->slug),
            $this->factory
                ->withTitle('test')
                ->create()
        );

        $sendRequest()
            ->assertRedirect(route('login'));

        $this->assertNotEquals('test', $this->blogPost->refresh()->title);

        $this->login();

        $sendRequest();

        $this->assertEquals('test', $this->blogPost->refresh()->title);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->login();

        // $this->withoutExceptionHandling();

        $this
            ->post(action([BlogPostAdminController::class, 'update'], $this->blogPost->slug), [])
            ->assertSessionHasErrors(['title', 'author', 'body', 'date']);

        $this
            ->post(action([BlogPostAdminController::class, 'update'], $this->blogPost->slug),
                $this->factory->create()
            )
            ->assertSessionHasNoErrors();
    }

    public function test_date_format_is_validated(): void
    {
        $this->login();

        $this
            ->post(
                action([BlogPostAdminController::class, 'update'], $this->blogPost->slug),
                $this->factory
                    ->withDate('01/01/2021')
                    ->create()
            )
            // ->dumpSession()
            ->assertSessionHasErrors([
                'date' => 'The date does not match the format Y-m-d.',
            ]);
    }

    public function test_slug_update_creates_redirect(): void
    {
        $this->login();

        $post = BlogPost::factory()->create([
            'slug' => 'slug-a',
        ]);

        $this->withoutExceptionHandling();

        $this
            ->post(action(UpdatePostSlugController::class, [$post->slug]), [
                'slug' => 'slug-b',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas(Redirect::class, [
                'from' => '/blog/slug-a',
                'to' => '/blog/slug-b',
        ]);

        $this->assertEquals('slug-b', $post->refresh()->slug);
    }
}
