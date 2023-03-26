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
    protected ?Route            $lastAdded = null;

    public function __construct(
        RouteArray $GET = new RouteArray(),
        RouteArray $POST = new RouteArray(),
        RouteArray $PATCH = new RouteArray(),
        RouteArray $DELETE = new RouteArray(),
    ) {
        $this->GET = $GET;
        $this->POST = $POST;
        $this->PATCH = $PATCH;
        $this->DELETE = $DELETE;
    }

    /**
     * Adding default constraint like `alphanum`, `alpha`, `numeric`
     *
     * @return self
     */
    public function addDefaultConstraints(): self
    {
        return $this->constraint('alphanum', fn (string $s) => preg_match("/^[a-zA-Z0-9_-]+/", $s))
            ->constraint('alpha', fn (string $s) => preg_match("/^[a-zA-Z_-]+/", $s))
            ->constraint('numeric', fn (string $s) => preg_match("/^[0-9_-]+/", $s));
    }

    /**
     * Create instance of `\UnknownRori\Router\Routes::class` from deserialized Routes in form of Array
     *
     * @param  array $deserialize
     *
     * @return self
     */
    public static function fromArray(array $deserialize): self
    {
        return new self(
            GET: RouteArray::fromArray($deserialize['GET']),
            POST: RouteArray::fromArray($deserialize['POST']),
            PATCH: RouteArray::fromArray($deserialize['PATCH']),
            DELETE: RouteArray::fromArray($deserialize['DELETE']),
        );
    }

    /**
     * Serialize the `\UnknownRori\Router\Routes::class` in form of Array
     *
     * @return array
     */
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

    /**
     * Implementation of `\Serializable` interface, it will return JSON
     *
     * @return string
     */
    public function serialize(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Implementation of `\Serializable` interface, fill the current Routes with serialized Routes
     *
     * @param  string $data
     *
     * @return void
     */
    public function unserialize(string $data)
    {
        $data = json_decode($data, true);
        $this->GET = RouteArray::fromArray($data['GET']);
        $this->POST = RouteArray::fromArray($data['POST']);
        $this->PATCH = RouteArray::fromArray($data['PATCH']);
        $this->DELETE = RouteArray::fromArray($data['DELETE']);
        $this->constraints = $data['constraints'];
    }

    /**
     * Create new constraint
     *
     * @param  string   $key
     * @param  callable $handler
     *
     * @return self
     */
    public function constraint(string $key, callable $handler): self
    {
        $this->constraints[$key] = $handler;

        return $this;
    }

    /**
     * Get specific constraint
     *
     * @param  string  $key
     *
     * @return Closure
     */
    public function getConstraints(string $key): Closure
    {
        if (array_key_exists($key, $this->constraints)) {
            return $this->constraints[$key];
        }

        throw new InvalidRouteConstraintException($key);
    }

    /**
     * Register new route, please use method that named using HTTP Method!
     *
     * @param  string                $method
     * @param  string                $url
     * @param  string|array|callable $handler
     *
     * @return self
     */
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

    /**
     * Register new Route using HTTP Method GET
     *
     * @param  string                $url
     * @param  string|array|callable $handler
     *
     * @return self
     */
    public function get(string $url, string|array|callable $handler): self
    {
        return $this->add("GET", $url, $handler);
    }

    /**
     * Register new Route using HTTP Method POST
     *
     * @param  string                $url
     * @param  string|array|callable $handler
     *
     * @return self
     */
    public function post(string $url, string|array|callable $handler): self
    {
        return $this->add("POST", $url, $handler);
    }

    /**
     * Register new Route using HTTP Method PATCH
     *
     * @param  string                $url
     * @param  string|array|callable $handler
     *
     * @return self
     */
    public function patch(string $url, string|array|callable $handler): self
    {
        return $this->add("PATCH", $url, $handler);
    }

    /**
     * Register new Route using HTTP Method DELETE
     *
     * @param  string                $url
     * @param  string|array|callable $handler
     *
     * @return self
     */
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

    /**
     * Register the last registered Route into named route so it can be referenced using name in some method
     *
     * @param  string $name
     *
     * @return self
     */
    public function name(string $name): self
    {
        if (is_null($this->lastAdded))
            return $this;

        $this->lastAdded->setName($name);
        $this->namedRoute[$name] = $this->lastAdded;
        return $this;
    }

    /**
     * For easy typing on built in constraint
     *
     * @param  string|array $placeholder
     *
     * @return self
     */
    public function whereAlphaNum(string|array $placeholder): self
    {
        if (!is_array($placeholder)) {
            return $this->where([$placeholder => 'alphanum']);
        }

        foreach ($placeholder as $key => $value) {
            $this->where([$value => 'alphanum']);
        }

        return $this;
    }

    /**
     * For easy typing on built in constraint
     *
     * @param  string|array $placeholder
     *
     * @return self
     */
    public function whereNumeric(string|array $placeholder): self
    {
        if (!is_array($placeholder)) {
            return $this->where([$placeholder => 'numeric']);
        }

        foreach ($placeholder as $key => $value) {
            $this->where([$value => 'numeric']);
        }

        return $this;
    }

    /**
     * For easy typing on built in constraint
     *
     * @param  string|array $placeholder
     *
     * @return self
     */
    public function whereAlpha(string|array $placeholder): self
    {
        if (!is_array($placeholder)) {
            return $this->where([$placeholder => 'alpha']);
        }

        foreach ($placeholder as $key => $value) {
            $this->where([$value => 'alpha']);
        }

        return $this;
    }

    /**
     * Generate URL using named route and some data as args for building the URL (needed for generating dynamic route)
     * ## Experimental API
     *
     * @param  string $name
     * @param  array  $data
     *
     * @return string
     */
    public function generate(string $name, array $data = []): string
    {
        if (!array_key_exists($name, $this->namedRoute))
            throw new RouteNotFoundException(key: $name);

        return $this->namedRoute[$name]->generate($data);
    }
}
