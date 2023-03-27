<?php

namespace UnknownRori\Router;

use UnknownRori\Router\Contracts\RoutesContracts;

class RouteResourcePending implements RoutesContracts
{

    protected Routes $routes;
    protected string $url;
    protected string $controller;
    protected string $bindName = 'id';
    protected ?string $name;
    protected array $only;
    protected array $constraint = [];

    public function __construct(
        Routes $routes,
        string $url,
        string $controller,
        array $only = ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']
    ) {
        $this->only = $only;
        $this->routes = $routes;
        $this->url = $url;
        $this->controller = $controller;
        $this->name = null;
    }

    public function __destruct()
    {
        $this->insertRoute("get", "index", "", false);
        $this->insertRoute("get", "create", "/create", false);
        $this->insertRoute("post", "store", "", false);
        $this->insertRoute("get", "edit", "/{{$this->bindName}}/edit", true);
        $this->insertRoute("patch", "update", "/{{$this->bindName}}", true);
        $this->insertRoute("delete", "destroy", "/{{$this->bindName}}", true);
        $this->insertRoute("get", "show", "/{{$this->bindName}}", true);
    }

    public function bind(string $name): self
    {
        $this->bindName = $name;
        return $this;
    }

    /**
     * @param string $name
     * @return RouteResourcePending
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function only(array $only): self
    {
        $this->only = $only;

        return $this;
    }

    public function except(array $except): self
    {
        $this->only = array_diff($this->only, $except);

        return $this;
    }

    /**
     *
     * @param array $constraintKey
     * @return RouteResourcePending
     */
    public function where(array $constraintKey): self
    {
        $this->constraint[] = $constraintKey;

        return $this;
    }

    /**
     *
     * @param array|string $placeholder
     * @return RouteResourcePending
     */
    public function whereAlphaNum(array|string $placeholder): self
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
     *
     * @param array|string $placeholder
     * @return RouteResourcePending
     */
    public function whereAlpha(array|string $placeholder): self
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
     *
     * @param array|string $placeholder
     * @return RouteResourcePending
     */
    public function whereNumeric(array|string $placeholder): self
    {
        if (!is_array($placeholder)) {
            return $this->where([$placeholder => 'numeric']);
        }

        foreach ($placeholder as $key => $value) {
            $this->where([$value => 'numeric']);
        }

        return $this;
    }

    protected function insertRoute(string $http, string $method, string $url, bool $constraint)
    {
        if (in_array($method, $this->only)) {
            $this->routes->$http("{$this->url}{$url}", [$this->controller, $method]);

            if ($constraint) {
                $this->routes->where($this->constraint);
            }

            if (!is_null($this->name)) {
                $this->routes->name("{$this->name}.{$method}");
            }
        }
    }
}
