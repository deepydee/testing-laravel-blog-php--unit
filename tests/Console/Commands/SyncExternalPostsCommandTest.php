<?php

namespace Tests\Console\Commands;

use App\Models\ExternalPost;
use Tests\Fakes\RssRepositoryFake;
use Tests\TestCase;

class SyncExternalPostsCommandTest extends TestCase
{
    public function test_sync_several_repositories_at_once(): void
    {
        RssRepositoryFake::setUp();

        $urls = [
            'https://test-a.com',
            'https://test-b.com',
            'https://test-c.com',
        ];

        config()->set('services.external_feeds', $urls);

        $this->artisan('sync:externals')
            ->assertExitCode(0)
            ->expectsOutput('Fetching 3 feeds')
            ->expectsOutput("\t- https://test-a.com")
            ->expectsOutput("\t- https://test-b.com")
            ->expectsOutput("\t- https://test-c.com")
            ->expectsOutput('Done');

        $this->assertEquals($urls, RssRepositoryFake::getUrls());

        $this->assertDatabaseCount(ExternalPost::class, 3);
    }
}
