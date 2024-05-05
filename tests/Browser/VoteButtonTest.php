<?php

namespace Tests\Browser;

use App\Http\Controllers\BlogPostController;
use App\Models\BlogPost;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class VoteButtonTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_vote_button()
    {
        $post = BlogPost::factory()->create([
            'likes' => 10,
        ]);

        $this->browse(function (Browser $browser) use ($post)  {
            $browser
                ->visit(action([BlogPostController::class, 'show'], $post->slug))
                ->with('@vote-button', function (Browser $button) {
                    $button->assertSee('10');
                })

                ->click('@vote-button')
                ->pause(1000)
                ->with('@vote-button', function (Browser $button) {
                    $button->assertSee('11');
                })

                ->click('@vote-button')
                ->pause(1000)
                ->with('@vote-button', function (Browser $button) {
                    $button->assertSee('10');
                });
        });
    }

    public function test_vote_toggle(): void
    {
        $post = BlogPost::factory()->create([
            'likes' => 10,
        ]);

        $this->browse(function (Browser $browser) use ($post) {
            $browser
                ->visit(action([BlogPostController::class, 'show'], $post->slug))
                ->click('@vote-button')
                ->pause(1000)
                ->click('@vote-button')
                ->pause(1000);

            $this->assertEquals(10, $post->refresh()->likes);
        });
    }
}
