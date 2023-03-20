<?php

namespace UnknownRori\Router;

use Serializable;
use UnknownRori\Router\Contracts\FromArray;
use UnknownRori\Router\Contracts\ToArray;
use UnknownRori\Router\Exceptions\BadHttpMethodException;

class Routes implements ToArray, FromArray, Serializable
{
    public RouteArray           $GET;
    public RouteArray           $POST;
    public RouteArray           $PATCH;
    public RouteArray           $DELETE;

    protected ?Route            $lastAdded;

    public function __construct()
    {
        $this->GET = new RouteArray();
        $this->POST = new RouteArray();
        $this->PATCH = new RouteArray();
        $this->DELETE = new RouteArray();
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

        return $result;
    }

    public function serialize()
    {
        return json_encode($this->toArray());
    }

    public function unserialize(string $data)
    {
        $data = json_decode($data, true);
        $this->GET = $data['GET'];
        $this->POST = $data['POST'];
        $this->PATCH = $data['PATCH'];
        $this->DELETE = $data['DELETE'];
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

    public function name(string $name): self
    {
        if (is_null($this->lastAdded))
            return $this;

        $this->lastAdded->setName($name);
        return $this;
    }
}
