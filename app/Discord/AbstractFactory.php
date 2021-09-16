<?php

namespace App\Discord;

use App\Exceptions\InvalidClassException;
use Carbon\Exceptions\InvalidIntervalException;

abstract class AbstractFactory
{
    /** @var string */
    protected $namespace = __NAMESPACE__;

    /** @var object[] */
    private $instances = [];

    /** @throws InvalidClassException|InvalidIntervalException */
    public function __call(string $instanceType, array $arguments = []): object
    {
        $class = sprintf('%s\%s', $this->namespace, ucfirst($instanceType));

        if (array_key_exists($class, $this->instances)) {
            return $this->instances[$class];
        }

        if (!class_exists($class)) {
            throw new InvalidClassException("Invalid class name");
        }

        $instance = $this->createInstance($class);

        $this->validateInstance($instance);

        $this->instances[$class] = $instance;

        return $instance;
    }

    abstract protected function createInstance(string $class): object;

    /** @throws InvalidIntervalException */
    abstract protected function validateInstance(object $instance): void;
}
