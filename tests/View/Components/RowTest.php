<?php

namespace Tests\View\Components;

use Tests\TestCase;

class RowTest extends TestCase
{
    public function test_header_row_is_rendered(): void
    {
        $this->blade('<x-row header />')
            ->assertSee('sticky')
            ->assertSee('bg-gray');

        $this->blade('<x-row />')
            ->assertDontSee('sticky')
            ->assertSee('bg-white');
    }
}
