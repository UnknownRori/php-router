<?php

namespace UnknownRori\Tests\Integration;

use PHPUnit\Framework\TestCase;
use UnknownRori\Router\RouteResolver;
use UnknownRori\Router\Routes;

class Home
{
    public function __invoke()
    {
        return "Home pages!";
    }
}

/**
 * @covers \UnknownRori\Router\RouteResolver
 * @covers \UnknownRori\Router\Utility\URL
 * @covers \UnknownRori\Router\RouteArray
 * @covers \UnknownRori\Router\Routes
 * @covers \UnknownRori\Router\Route
 */
class InvokableControllerTest extends TestCase
{
    public Routes $routes;
    public RouteResolver $resolver;

    public function setUp(): void
    {
        $routes = new Routes();
        $routes->get('/', Home::class);

        $this->routes = $routes;
        $this->resolver = new RouteResolver($this->routes);
    }

    public function tearDown(): void
    {
        unset($this->routes);
        unset($this->resolver);
    }

    public function test_invoke1()
    {
        $this->assertEquals("Home pages!", $this->resolver->resolve("GET", "/"));
    }
}
