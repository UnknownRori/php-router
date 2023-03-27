<?php

namespace UnknownRori\Tests\Integration;

use PHPUnit\Framework\TestCase;
use UnknownRori\Router\RouteResolver;
use UnknownRori\Router\Routes;

class PostsController
{
    public function index()
    {
        return "PostsController::index";
    }

    public function show(string $id)
    {
        return "PostsController::show($id)";
    }


    public function edit(string $id)
    {
        return "PostsController::edit($id)";
    }


    public function update(string $id)
    {
        return "PostsController::update($id)";
    }

    public function create()
    {
        return "PostsController::create";
    }

    public function store()
    {
        return "PostsController::store";
    }

    public function destroy(string $id)
    {
        return "PostsController::destroy($id)";
    }
}

/**
 * @covers \UnknownRori\Router\RouteResolver
 * @covers \UnknownRori\Router\Utility\URL
 * @covers \UnknownRori\Router\RouteArray
 * @covers \UnknownRori\Router\Routes
 * @covers \UnknownRori\Router\Route
 */
class ResourceRouteTest extends TestCase
{
    protected Routes $routes;
    protected RouteResolver   $resolver;

    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void
    {
        $this->routes = new Routes();
        $this->routes->resource('/posts', \UnknownRori\Tests\Integration\PostsController::class)
            ->name('posts');
        $this->routes->resource('/posts2', \UnknownRori\Tests\Integration\PostsController::class)
            ->only(['store', 'destroy', 'index'])
            ->name('posts2');
        $this->routes->resource('/posts3', \UnknownRori\Tests\Integration\PostsController::class)
            ->except(['edit', 'update'])
            ->name('posts3');

        $this->resolver = new RouteResolver($this->routes);
    }

    protected function tearDown(): void
    {
        unset($this->resolver);
        unset($this->routes);
    }

    public function test_resource1()
    {
        $result = $this->resolver->resolve('GET', '/posts');
        $this->assertEquals('PostsController::index', $result);

        $result = $this->resolver->resolve('GET', '/posts/2');
        $this->assertEquals('PostsController::show(2)', $result);

        $result = $this->resolver->resolve('GET', '/posts/create');
        $this->assertEquals('PostsController::create', $result);

        $result = $this->resolver->resolve('POST', '/posts');
        $this->assertEquals('PostsController::store', $result);

        $result = $this->resolver->resolve('DELETE', '/posts/5');
        $this->assertEquals('PostsController::destroy(5)', $result);
    }
}
