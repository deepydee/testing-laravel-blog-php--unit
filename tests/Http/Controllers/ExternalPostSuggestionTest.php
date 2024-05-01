<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\ExternalPostSuggestionController;
use App\Models\ExternalPost;
use Tests\TestCase;

class ExternalPostSuggestionTest extends TestCase
{
    public function test_external_post_can_be_submitted(): void
    {
        $this->withoutExceptionHandling();

        $this
            ->post(action(ExternalPostSuggestionController::class, [
                'title' => 'test',
                'url' => 'https://example.com',
            ]))
            ->assertRedirect(action([BlogPostController::class, 'index']))
            ->assertSessionHas('laravel_flash_message');

            $this->assertDatabaseHas(ExternalPost::class, [
                'title' => 'test',
                'url' => 'https://example.com',
            ]);
    }
}

