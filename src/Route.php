<?php

namespace UnknownRori\Router;

use Closure;
use Serializable;
use UnknownRori\Router\Contracts\FromArray;
use UnknownRori\Router\Contracts\ToArray;

class Route implements ToArray, FromArray, Serializable
{
    protected string $url;
    protected string $method;
    protected string $name;
    protected array  $middleware;
    protected string|array|Closure $handler;

    /**
     * Constraint that will be used in this route
     *
     * @var array<string>
     */
    protected array $constraints = [];

    public function __construct(string $method, string $url, string|callable|array $handler)
    {
        $this->url = $url;
        $this->method = $method;
        $this->name = "";
        $this->middleware = [];
        $this->handler = $handler;
    }

    public static function fromArray(array $deserialize): self
    {
        $route = new Route($deserialize['url'], $deserialize['method'], $deserialize['handler']);
        $route->middleware = $deserialize['middleware'];
        $route->name = $deserialize['name'];

        return $route;
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'name' => $this->name,
            'method' => $this->method,
            'middleware' => $this->middleware,
            'handler' => $this->handler,
            'constraints' => $this->constraints
        ];
    }

    public function serialize()
    {
        return json_encode($this->toArray());
    }

    public function unserialize(string $data)
    {
        $data = json_decode($data, true);
        $this->url = $data['url'];
        $this->method = $data['method'];
        $this->name = $data['name'];
        $this->middleware = $data['middleware'];
        $this->handler = $data['handler'];
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function getHandler(): string|array|callable
    {
        return $this->handler;
    }

    public function getConstraints(): array
    {
        return $this->constraints;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function addMiddleware(string $middleware)
    {
        $this->middleware[] = $middleware;
    }

    /**
     * Register constraint to this route
     *
     * @param  array<string, string> $constraint
     *
     * @return void
     */
    public function addConstraint(array $constraint)
    {
        $this->constraints = array_merge($this->constraints, $constraint);
    }
}
