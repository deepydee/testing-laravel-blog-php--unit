<?php

namespace Tests\Support;

use League\CommonMark\MarkdownConverter;
use Spatie\Snapshots\MatchesSnapshots;
use Tests\TestCase;

class HighlightCodeBlockRendererTest extends TestCase
{
    use MatchesSnapshots;

    public function test_hljs_tags_can_be_parsed(): void
    {
        $markdown = <<<MD
```php
public function __construct(
    <hljs keyword>public readonly</hljs> <hljs type>string</hljs> <hljs prop>\$title</hljs>,
    <hljs keyword>public readonly</hljs> <hljs type>string</hljs> <hljs prop>\$body</hljs>,
) {}
```
MD;

        $convertor = app(MarkdownConverter::class);

        $html = $convertor->convertToHtml($markdown);

        $this->assertMatchesSnapshot($html);
    }
}
