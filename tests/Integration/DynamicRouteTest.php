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
        $this->routes = new Routes();
        $this->routes->get('/', fn () => "home");
        $this->routes->get('/dashboard', fn () => "dashboard");
        $this->routes->get('/posts', fn () => "posts");
        $this->routes->get('/posts/{posts:alpha}', fn (string $post) => "post {$post}");
        $this->routes->post('/posts/{posts:alpha}', fn (string $post) => "post {$post}");
        $this->routes->get('/users/{name:alpha}/posts/{id:number}', fn (string $name, int $id) => "view posts id {$id} with author {$name}");
        $this->routes->get('/users/{name:alpha}/posts/{id:number}/edit', fn (string $name, int $id) => "view edit form posts id {$id} with author {$name}");
        $this->routes->post('/users/{name:alpha}/posts/{id:number}/edit', fn (string $name, int $id) => "post the edit to post id {$id} owned by {$name}");
        $this->routes->delete('/users/{name:alpha}/posts/{id:number}/delete', fn (string $name, int $id) => "delete post id {$id} owned by {$name}");
        $this->routes->get('/download/files', fn () => "download-files");
        $this->routes->get('/download/files/{name:alpha}', fn (string $name) => $name);
        $this->routes->get('/about', fn () => "about");

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
