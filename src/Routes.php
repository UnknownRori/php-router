<?php

namespace UnknownRori\Router;

use Closure;
use Serializable;
use UnknownRori\Router\Contracts\FromArray;
use UnknownRori\Router\Contracts\ToArray;
use UnknownRori\Router\Exceptions\BadHttpMethodException;
use UnknownRori\Router\Exceptions\InvalidRouteConstraintException;
use UnknownRori\Router\Exceptions\RouteNotFoundException;

class Routes implements ToArray, FromArray, Serializable
{
    /**
     * Registered Constraints and it's handler
     *
     * @var array<string, \Closure>
     */
    public array                $constraints = [];

    public RouteArray           $GET;
    public RouteArray           $POST;
    public RouteArray           $PATCH;
    public RouteArray           $DELETE;

    /**
     * A Collection of named route
     *
     * @var array<string, \UnknownRori\Router\Route::class>
     */
    public array                $namedRoute = [];
    protected ?Route            $lastAdded;

    public function __construct()
    {
        $this->GET = new RouteArray();
        $this->POST = new RouteArray();
        $this->PATCH = new RouteArray();
        $this->DELETE = new RouteArray();
        $this->lastAdded = null;
    }

    public function addDefaultConstraints(): self
    {
        return $this->constraint('alphanum', fn (string $s) => preg_match("/^[a-zA-Z0-9_-]+/", $s))
            ->constraint('alpha', fn (string $s) => preg_match("/^[a-zA-Z_-]+/", $s))
            ->constraint('numeric', fn (string $s) => preg_match("/^[0-9_-]+/", $s));
    }

    public static function fromArray(array $deserialize): self
    {
        $collect = new self();

        $collect->GET = RouteArray::fromArray($deserialize['GET']);
        $collect->POST = RouteArray::fromArray($deserialize['POST']);
        $collect->PATCH = RouteArray::fromArray($deserialize['PATCH']);
        $collect->DELETE = RouteArray::fromArray($deserialize['DELETE']);

        return $collect;
    }

    public function toArray(): array
    {
        $result = [];

        $result['GET'] = $this->GET->toArray();
        $result['POST'] = $this->POST->toArray();
        $result['PATCH'] = $this->PATCH->toArray();
        $result['DELETE'] = $this->DELETE->toArray();
        $result['constraints'] = $this->constraints;

        return $result;
    }

    public function serialize()
    {
        return json_encode($this->toArray());
    }

    public function unserialize(string $data)
    {
        $data = json_decode($data, true);
        $this->GET = RouteArray::fromArray($data['GET']);
        $this->POST = RouteArray::fromArray($data['POST']);
        $this->PATCH = RouteArray::fromArray($data['PATCH']);
        $this->DELETE = RouteArray::fromArray($data['DELETE']);
        $this->constraints = $data['constraints'];
    }

    public function constraint(string $key, callable $handler): self
    {
        $this->constraints[$key] = $handler;

        return $this;
    }

    public function getConstraints(string $key): Closure
    {
        if (array_key_exists($key, $this->constraints)) {
            return $this->constraints[$key];
        }

        throw new InvalidRouteConstraintException($key);
    }

    public function add(string $method, string $url, string|array|callable $handler): self
    {
        $route = new Route($method, $url, $handler);
        $this->lastAdded = $route;

        $method = strtoupper($method);
        match ($method) {
            "GET" => $this->GET->push($route),
            "POST" => $this->POST->push($route),
            "PATCH" => $this->PATCH->push($route),
            "DELETE" => $this->DELETE->push($route),
            default => throw new BadHttpMethodException($method)
        };

        return $this;
    }

    public function get(string $url, string|array|callable $handler): self
    {
        return $this->add("GET", $url, $handler);
    }

    public function post(string $url, string|array|callable $handler): self
    {
        return $this->add("POST", $url, $handler);
    }

    public function patch(string $url, string|array|callable $handler): self
    {
        return $this->add("PATCH", $url, $handler);
    }

    public function delete(string $url, string|array|callable $handler): self
    {
        return $this->add("DELETE", $url, $handler);
    }

    /**
     * Register constraint to the route
     *
     * @param  array<string, string> $constraintKey
     *
     * @return self
     */
    public function where(array $constraintKey): self
    {
        $this->lastAdded->addConstraint($constraintKey);

        return $this;
    }

    public function name(string $name): self
    {
        if (is_null($this->lastAdded))
            return $this;

        $this->lastAdded->setName($name);
        $this->namedRoute[$name] = $this->lastAdded;
        return $this;
    }

    public function redirect(string $name, array $data = [])
    {
        if (!array_key_exists($name, $this->namedRoute))
            throw new RouteNotFoundException(key: $name);

        $route = $this->namedRoute[$name];
        $url = $route->getUrl();
        $routeConstraint = $route->getConstraints();

        return preg_replace_callback("/{[a-zA-Z]+}/", function (array $matches) use ($data, $routeConstraint) {
            $key = ltrim($matches[0], '{');
            $key = rtrim($key, '}');

            return $data[$key];
        }, $url);
    }
}
