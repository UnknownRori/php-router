<?php

namespace UnknownRori\Router;

use ReflectionClass;
use ReflectionFunction;
use UnexpectedValueException;

/**
 * A class used to run user created code and use dependency injection under the hood
 */
class Runner
{
    protected array $additionalData;

    public function __construct(array $additionalData = [])
    {
        $this->additionalData = $additionalData;
    }

    /**
     * Insert additional data for the Runner Object
     *
     * @param  array $additionalData
     *
     * @return self
     */
    public function data(array $additionalData): self
    {
        $this->additionalData = array_merge($additionalData, $this->additionalData);

        return $this;
    }

    /**
     * Invoke function or anonymous function
     *
     * @param  string|callable $name
     *
     * @return void
     */
    public function function(string|callable $name)
    {
        $reflection = new ReflectionFunction($name);
        $params = $this->resolveDependency($reflection->getParameters());

        return $reflection->invoke(...$params);
    }

    /**
     * Invoke class __invoke method
     *
     * @param  string $namespace
     *
     * @return void
     */
    public function invoke(string $namespace)
    {
        return $this->method($namespace, '__invoke');
    }

    /**
     * Invoke class method
     *
     * @param  string $namespace
     * @param  string $method
     *
     * @return void
     */
    public function method(string $namespace, string $method)
    {
        $classReflect = new ReflectionClass($namespace);
        $invokeReflect = $classReflect->getMethod($method);
        $params = $this->resolveDependency($invokeReflect->getParameters());

        $object = $this->new($classReflect);

        return $invokeReflect->invoke($object, ...$params);
    }

    /**
     * Create new instance
     *
     * @param  ReflectionClass $reflectionClass
     *
     * @return object|null
     */
    protected function new(ReflectionClass $reflectionClass): object
    {
        $constructorReflection = $reflectionClass->getConstructor();

        if (is_null($constructorReflection))
            return $reflectionClass->newInstance();

        $constructorParam = $constructorReflection->getParameters();
        $params = $this->resolveDependency($constructorParam);

        return $reflectionClass->newInstance(...$params);
    }

    /**
     * Resolve dependency using array of ReflectionParameter
     *
     * @param  array<\ReflectionParameter> $parameters
     *
     * @return array
     */
    public function resolveDependency(array $parameters): array
    {
        $param = [];

        foreach ($parameters as $key => $value) {
            if (!$value->hasType())
                $param[$value->name] = $this->additionalData[$value->name];

            $valType = $value->getType();

            $data = $this->additionalData[$value->name];
            $dataType = gettype($data);

            $data = match ($valType) {
                "int" => ctype_digit($data) ? intval($data) : throw new UnexpectedValueException("Key {$value->name} should be type of {$valType} but it was given {$dataType}"),
                "float" | 'double' => ctype_digit($data) ? floatval($data) : throw new UnexpectedValueException("Key {$value->name} should be type of {$valType} but it was given {$dataType}"),
                default => $data,
            };

            $param[$value->name] = $data;
        }

        return $param;
    }
}
