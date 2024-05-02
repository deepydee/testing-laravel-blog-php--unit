<?php

namespace Tests\Console\Commands;

use Tests\TestCase;

class ListExternalPostsCommandTest extends TestCase
{
    public function test_table_is_shown(): void
    {
        config()->set('services.external_feeds', [
            'https://a.test/rss',
            'https://b.test/rss',
        ]);

        $this
            ->artisan('list:externals')
            ->assertExitCode(0)
            ->expectsTable(
                ['Feed'],
                [
                    ['https://a.test/rss'],
                    ['https://b.test/rss'],
                ],
            );
    }
}
