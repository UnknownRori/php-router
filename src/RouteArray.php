<?php

namespace UnknownRori\Router;

use ArrayObject;
use Serializable;
use UnknownRori\Router\Contracts\FromArray;
use UnknownRori\Router\Contracts\ToArray;

/**
 * A simple wrapper for array containing route
 */
class RouteArray extends ArrayObject implements ToArray, FromArray, Serializable
{
    /**
     * Collection of Route
     *
     * @var array<UnknownRori\Router\Route::class>
     */
    private array $storage = [];

    public function __construct(array $array = [], int $flag = 0, string $iteratorClass = "ArrayIterator")
    {
        $this->storage = $array;
        $this->setFlags($flag);
        $this->setIteratorClass($iteratorClass);
    }

    public static function fromArray(array $deserialize): self
    {
        $result = array_map(fn (array $deserialize) => Route::fromArray($deserialize), $deserialize);
        return new RouteArray($result);
    }

    public function toArray(): array
    {
        $array = [];

        foreach ($this->storage as $key => $route) {
            $array[] = $route->toArray();
        }

        return $array;
    }

    public function first(): ?Route
    {
        $key = array_key_first($this->storage);
        if (is_null($key))
            return null;


        return $this->storage[$key];
    }

    public function push(Route $route)
    {
        $this->storage[] = $route;
    }

    /**
     * Undocumented function
     *
     * @param  callable   $closure
     *
     * @return RouteArray
     */
    public function filter(callable $closure): RouteArray
    {
        $result = array_filter($this->storage, $closure);
        $result = new RouteArray($result);

        return $result;
    }
}
