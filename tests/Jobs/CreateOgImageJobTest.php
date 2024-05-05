<?php

declare(strict_types=1);

namespace Tests\Jobs;

use App\Jobs\CreateOgImageJob;
use App\Models\BlogPost;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateOgImageJobTest extends TestCase
{
    public function test_job_is_dispatched_correctly(): void
    {
        Bus::fake();

        $post = BlogPost::factory()->create();

        Bus::assertDispatched(CreateOgImageJob::class);

        Bus::fake();

        $post->fresh()->save();

        Bus::assertNotDispatched(CreateOgImageJob::class);

        Bus::fake();

        $post->fresh()->update([
            'title' => 'New Title',
        ]);

        Bus::assertDispatched(CreateOgImageJob::class);
    }

    public function test_file_is_generated_correctly(): void
    {
        Bus::swap(app(Dispatcher::class));

        Storage::fake('public');

        $post = BlogPost::factory()->create();

        Storage::disk('public')->assertExists("blog/{$post->slug}.png");
    }
}
