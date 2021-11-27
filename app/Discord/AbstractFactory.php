<?php

namespace App\Discord;

use App\Exceptions\InvalidClassException;
use App\Exceptions\InvalidInstanceException;
use stdClass;

abstract class AbstractFactory
{
    /** @var string */
    protected $namespace = __NAMESPACE__;

    /** @var string */
    protected $instanceType = stdClass::class;

    /** @var object[] */
    private $instances = [];

    /** @throws InvalidClassException|InvalidInstanceException */
    public function __call(string $instanceType, array $arguments = []): object
    {
        if (array_key_exists($instanceType, $this->instances)) {
            return $this->instances[$instanceType];
        }

        $class = sprintf('%s\%s', $this->namespace, ucfirst($instanceType));

        if (!class_exists($class)) {
            throw new InvalidClassException("Invalid class name");
        }

        $instance = $this->createInstance($class);
        $this->validateInstance($instance);
        $this->instances[$class] = $instance;

        return $instance;
    }

    abstract protected function createInstance(string $class): object;

    /** @throws InvalidInstanceException */
    protected function validateInstance(object $instance): void
    {
        if (!($instance instanceof $this->instanceType)) {
            throw new InvalidInstanceException('Invalid instance');
        }
    }
}
