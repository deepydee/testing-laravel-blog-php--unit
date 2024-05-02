<?php

namespace Tests\Policies;

use App\Http\Controllers\BlogPostAdminController;
use App\Http\Controllers\DeletePostController;
use App\Http\Controllers\UpdatePostSlugController;
use App\Models\BlogPost;
use App\Models\User;
use Tests\TestCase;

class BlogPostPolicyTest extends TestCase
{
    public function test_only_admin_users_are_allowed(): void
    {
        [$guest, $admin] = User::factory()
            ->count(2)
            ->sequence(
                ['is_admin' => false],
                ['is_admin' => true],
            )
            ->create();

        $post = BlogPost::factory()->create();

        $this->login($guest);

        $this->get(action([BlogPostAdminController::class, 'index']))->assertForbidden();
        $this->get(action([BlogPostAdminController::class, 'create']))->assertForbidden();
        $this->post(action([BlogPostAdminController::class, 'store']))->assertForbidden();
        $this->get(action([BlogPostAdminController::class, 'edit'], $post->slug))->assertForbidden();
        $this->post(action([BlogPostAdminController::class, 'update'], $post->slug))->assertForbidden();
        $this->post(action([BlogPostAdminController::class, 'publish'], $post->slug))->assertForbidden();
        $this->post(action(UpdatePostSlugController::class, $post->slug))->assertForbidden();
        $this->post(action(DeletePostController::class, $post->slug))->assertForbidden();

        $this->login($admin);

        $this->get(action([BlogPostAdminController::class, 'index']))->assertSuccessful();
        $this->get(action([BlogPostAdminController::class, 'create']))->assertSuccessful();
        $this->post(action([BlogPostAdminController::class, 'store']))->assertRedirect();
        $this->get(action([BlogPostAdminController::class, 'edit'], $post->slug))->assertSuccessful();
        $this->post(action([BlogPostAdminController::class, 'update'], $post->slug))->assertRedirect();
        $this->post(action([BlogPostAdminController::class, 'publish'], $post->slug))->assertRedirect();
        $this->post(action(UpdatePostSlugController::class, $post->slug))->assertRedirect();
        $this->post(action(DeletePostController::class, $post->slug))->assertRedirect();
    }
}
