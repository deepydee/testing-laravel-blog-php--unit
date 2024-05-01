<?php

namespace Tests\Http\Middleware;

use App\Http\Middleware\RedirectMiddleware;
use App\Models\Redirect;
use Illuminate\Http\Response;
use Tests\TestCase;

class RedirectMiddlewareTest extends TestCase
{
    public function test_middleware_in_isolation(): void
    {
        $middleware = app(RedirectMiddleware::class);

        $response = $middleware->handle(
            $this->createRequest('get', '/'),
            fn () => new Response(),
        );

        $this->assertTrue($response->isSuccessful());

        Redirect::factory()->create(['from' => '/', 'to' => '/new-home-page']);

        $response = $middleware->handle(
            $this->createRequest('get', '/'),
            fn () => new Response(),
        );

        $this->assertTrue($response->isRedirect('http://localhost:8000/new-home-page'));
    }

    public function test_middleware_as_integration(): void
    {
        $this->get('/')->assertSuccessful();

        Redirect::factory()->create(['from' => '/', 'to' => '/new-home-page']);

        $this->get('/')->assertRedirect('/new-home-page');

    }
}
