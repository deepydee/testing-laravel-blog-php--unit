<?php

namespace Tests\View\Components;

use App\Models\BlogPost;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class LastSeenTest extends TestCase
{
    use MatchesSnapshots;

    public function test_test_last_seen(): void
    {
        $post = BlogPost::factory()->create();

        $this->travelTo(Carbon::make('2021-01-01'));

        $this->assertMatchesSnapshot((string) $this->blade('<x-last-seen :post="$post" />', compact('post')));

        app(Request::class)->cookies->set("last_seen_{$post->slug}", now()->toDateTimeString());

        $this->assertMatchesSnapshot((string) $this->blade('<x-last-seen :post="$post" />', compact('post')));
    }
}
