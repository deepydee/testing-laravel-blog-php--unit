<?php

namespace Tests\View\Components;

use App\Models\BlogPost;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Tests\TestCase;

class LastSeenTest extends TestCase
{
    public function test_test_last_seen(): void
    {
        $post = BlogPost::factory()->create();

        $this->travelTo(Carbon::make('2021-01-01'));

        $this->blade('<x-last-seen :post="$post" />', compact('post'))
            ->assertDontSee('Last seen')
            ->assertDontSee('2021-01-01');

        app(Request::class)->cookies->set("last_seen_{$post->slug}", now()->toDateTimeString());

        $this->blade('<x-last-seen :post="$post" />', compact('post'))
            ->assertSee('Last seen')
            ->assertSee('2021-01-01');
    }
}
