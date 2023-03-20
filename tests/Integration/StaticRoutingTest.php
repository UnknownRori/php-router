<?php

namespace UnknownRori\Tests\Integration;

use PHPUnit\Framework\TestCase;
use UnknownRori\Router\RouteResolver;
use UnknownRori\Router\Routes;

/**
 * @covers \UnknownRori\Router\RouteCollection
 * @covers \UnknownRori\Router\RouteResolver
 */
final class StaticRoutingTest extends TestCase
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
        $this->routes->get('/contacts-us', fn () => "contacts-us");
        $this->routes->get('/download/files', fn () => "download-files");
        $this->routes->get('/download/files/ubuntu', fn () => "ubuntu");
        $this->routes->get('/about', fn () => "about");

        $this->resolver = new RouteResolver($this->routes);
    }

    protected function tearDown(): void
    {
        unset($this->resolver);
        unset($this->routes);
    }

    /**
     * It will try to resolve route '/' with http method 'GET'
     * @test
     *
     * @return void
     */
    public function test_getHome()
    {
        $this->assertTrue($this->resolver->resolve('GET', '/') == "home");
    }
    /**
     * It will try to resolve route '/dashboard' with http method 'GET'
     * @test
     *
     * @return void
     */
    public function test_getDashboard()
    {
        $this->assertTrue($this->resolver->resolve('GET', '/dashboard') == "dashboard");
    }
    /**
     * It will try to resolve route '/posts' with http method 'GET'
     * @test
     *
     * @return void
     */
    public function test_getPosts()
    {
        $this->assertTrue($this->resolver->resolve('GET', '/posts') == "posts");
    }
    /**
     * It will try to resolve route '/download/files' with http method 'GET'
     * @test
     *
     * @return void
     */
    public function test_getDownloadFiles()
    {
        $this->assertTrue($this->resolver->resolve('GET', '/download/files') == "download-files");
    }
    /**
     * It will try to resolve route '/' with http method 'GET'
     * @test
     *
     * @return void
     */
    public function test_getDownloadFilesUbuntu()
    {
        $this->assertTrue($this->resolver->resolve('GET', '/download/files/ubuntu') == "ubuntu");
    }
}
