<?php

namespace UnknownRori\Router;

use ReflectionClass;
use ReflectionFunction;
use UnexpectedValueException;
use UnknownRori\Router\Exceptions\BadHttpMethodException;
use UnknownRori\Router\Exceptions\RouteNotFoundException;
use UnknownRori\Router\Utility\Url;

/**
 * A simple route matching using Routes as args when constructed
 */
class RouteResolver
{
    public readonly Routes $routes;
    protected array $additionalData = [];

    public function __construct(Routes $routes)
    {
        $this->routes = $routes;
    }

    public function data(array $additionalData): self
    {
        $this->additionalData = array_merge($additionalData, $this->additionalData);
        return $this;
    }

    public function resolve(string $method, string $url, array $additionalData = []): mixed
    {
        $collection = $this->match($method, $url, $additionalData);
        $route = $collection->first();

        if (is_null($route))
            throw new RouteNotFoundException($url, $method);

        $additionalData = array_merge($this->additionalData, $additionalData);

        $additionalData['url'] = $url;
        $additionalData['method'] = $method;

        // Do some middleware stuff

        $handler = $route->getHandler();
        $runner = new Runner($additionalData);
        if (is_array($handler))
            $additionalData['response'] = $runner->method($handler[0], $handler[1]);
        else if (is_callable($handler))
            $additionalData['response'] = $runner->function($handler);
        else
            $additionalData['response'] = $runner->invoke($handler);

        // Do some middleware stuff

        return $additionalData['response'];
    }

    public function match(string $method, string $url, array &$additionalData): RouteArray
    {
        $method = strtoupper($method);

        $targetUrlArray = Url::splitUrl($url);
        $targetUrlArrayLen = count($targetUrlArray);

        $func = function (Route $route) use (&$additionalData, $url, $targetUrlArray, $targetUrlArrayLen) {
            return $this->checkRoute($route, $url, $targetUrlArray, $targetUrlArrayLen, $additionalData);
        };

        $result = match ($method) {
            "GET" => $this->routes->GET->filter($func),
            "POST" => $this->routes->POST->filter($func),
            "PATCH" => $this->routes->PATCH->filter($func),
            "DELETE" => $this->routes->DELETE->filter($func),
            default => throw new BadHttpMethodException($method)
        };

        return $result;
    }

    protected function checkRoute(Route $route, string $url, array $targetUrlArray, int $targetUrlArrayLen, array &$additionalData): bool
    {
        if ($route->getUrl() == $url)
            return true;

        $routeUrlArray = Url::splitUrl($route->getUrl());
        $routeUrlArrayLen = count($routeUrlArray);
        $routeConstraint = $route->getConstraints();


        if ($routeUrlArrayLen != $targetUrlArrayLen)
            return false;

        $correctness = 0;
        $data = [];
        foreach ($routeUrlArray as $key => $routeUrl) {
            $targetUrl = $targetUrlArray[$key];
            if ($routeUrl == $targetUrl) {

                $correctness++;
            } else if (substr($routeUrl, -1) == '}' && substr($routeUrl, 0, 1) == '{') {

                $routeKey = ltrim($routeUrl, '{');
                $routeKey = rtrim($routeKey, '}');
                $result = ltrim($targetUrl, "/");


                if (
                    array_key_exists($routeKey, $routeConstraint) &&
                    !call_user_func($this->routes->getConstraints($routeConstraint[$routeKey]), $result)
                ) {
                    break;
                }

                $data[$routeKey] = $result;
                $correctness++;
            }
        }
        if ($correctness == $targetUrlArrayLen) {
            $additionalData = array_merge($additionalData, $data);
            return true;
        }

        return false;
    }
}
