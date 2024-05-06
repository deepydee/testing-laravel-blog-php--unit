<?php

namespace Tests\Policies;

use App\Http\Controllers\BlogPostAdminController;
use App\Http\Controllers\DeletePostController;
use App\Http\Controllers\UpdatePostSlugController;
use App\Models\BlogPost;
use App\Models\User;
use Closure;
use Generator;
use Tests\TestCase;

class BlogPostPolicyTest extends TestCase
{
    /**
     * @dataProvider requests
     */
    public function test_guests_are_forbidden(Closure $sendRequest): void
    {
        $this->login(User::factory()->guest()->create());

        $post = BlogPost::factory()->create();

        /** @var \Illuminate\Testing\TestResponse $response */
        $response = $sendRequest->call($this, $post);

        $response->assertForbidden();
    }

    /**
     * @dataProvider requests
     */
    public function test_admins_are_allowed(Closure $sendRequest): void
    {
        $this->login(User::factory()->admin()->create());

        $post = BlogPost::factory()->create();

        /** @var \Illuminate\Testing\TestResponse $response */
        $response = $sendRequest->call($this, $post);

        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));
    }

    public function requests(): Generator
    {
            yield [fn (BlogPost $post) => $this->get(action([BlogPostAdminController::class, 'index']))];
            yield [fn (BlogPost $post) => $this->get(action([BlogPostAdminController::class, 'create']))];
            yield [fn (BlogPost $post) => $this->post(action([BlogPostAdminController::class, 'store']))];
            yield [fn (BlogPost $post) => $this->get(action([BlogPostAdminController::class, 'edit'], $post->slug))];
            yield [fn (BlogPost $post) => $this->post(action([BlogPostAdminController::class, 'update'], $post->slug))];
            yield [fn (BlogPost $post) => $this->post(action([BlogPostAdminController::class, 'publish'], $post->slug))];
            yield [fn (BlogPost $post) => $this->post(action(UpdatePostSlugController::class, $post->slug))];
            yield [fn (BlogPost $post) => $this->post(action(DeletePostController::class, $post->slug))];
    }
}
