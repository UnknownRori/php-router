<?php

namespace UnknownRori\Tests\Integration;

use PHPUnit\Framework\TestCase;
use UnknownRori\Router\RouteResolver;
use UnknownRori\Router\Routes;

/**
 * @covers \UnknownRori\Router\RouteCollection
 * @covers \UnknownRori\Router\RouteResolver
 */
final class DynamicRouteTest extends TestCase
{
    protected Routes $routes;
    protected RouteResolver   $resolver;

    /**
     * Summary of setUp
     * @return void
     */
    protected function setUp(): void
    {
        $routes = new Routes();
        $routes->get('/', fn () => "home");
        $routes->get('/dashboard', fn () => "dashboard");
        $routes->get('/posts', fn () => "posts");
        $routes->get('/posts/{posts}', fn (string $posts) => "posts {$posts}")
            ->where(['posts' => 'alpha']);
        $routes->post('/posts/{posts}', fn (string $posts) => "posts {$posts}")
            ->where(['post' => 'alpha']);
        $routes->get('/users/{name}/posts/{id}', fn (string $name, int $id) => "view posts id {$id} with author {$name}")
            ->where(['name' => 'alpha', 'id' => 'numeric']);
        $routes->get('/users/{name}/posts/{id}/edit', fn (string $name, int $id) => "view edit form posts id {$id} with author {$name}")
            ->where(['name' => 'alpha', 'id' => 'number']);
        $routes->post('/users/{name}/posts/{id}/edit', fn (string $name, int $id) => "post the edit to post id {$id} owned by {$name}")
            ->where(['name' => 'alpha', 'id' => 'number']);
        $routes->delete('/users/{name}/posts/{id}/delete', fn (string $name, int $id) => "delete post id {$id} owned by {$name}")
            ->where(['name' => 'alpha', 'id' => 'number']);
        $routes->get('/download/files', fn () => "download-files");
        $routes->get('/download/files/{name}', fn (string $name) => $name)
            ->where(['name' => 'alpha']);
        $routes->get('/about', fn () => "about");

        $this->routes = $routes;
        $this->resolver = new RouteResolver($this->routes);
    }

    protected function tearDown(): void
    {
        unset($this->resolver);
        unset($this->routes);
    }

    /**
     * Test the route '/posts/{name:alpha}' with method 'GET'
     * @test
     */
    public function test_getPosts()
    {
        $this->assertEquals('posts lorem-ipsum', $this->resolver->resolve('GET', '/posts/lorem-ipsum'));
    }

    /**
     * Test the route '/posts/{name:alpha}' with method 'POST'
     * @test
     */
    public function test_postPosts()
    {
        $this->assertEquals('posts lorem-ipsum', $this->resolver->resolve('POST', '/posts/lorem-ipsum'));
    }
    /**
     * Test the route '/posts' with method 'GET'
     * @test
     */
    public function test_getUserPost()
    {
        $this->assertEquals("view posts id 4 with author unknownrori", $this->resolver->resolve('GET', '/users/unknownrori/posts/4'));
    }
}
