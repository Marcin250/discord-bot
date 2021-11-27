<?php

namespace App\Cache;

use App\Exceptions\InvalidClassException;

class CacheFactory implements CacheFactoryInterface
{
    /** @var CacheServiceInterface[] */
    private $cacheServiceInstances = [];

    /** @throws InvalidClassException */
    public function __call(string $cacheService, array $arguments = []): CacheServiceInterface
    {
        if (array_key_exists($cacheService, $this->cacheServiceInstances)) {
            return $this->cacheServiceInstances[$cacheService];
        }

        $class = sprintf('%s\%s', __NAMESPACE__, ucfirst($cacheService));

        if (!class_exists($class)) {
            throw new InvalidClassException("Class does not exists");
        }

        if (!in_array(CacheServiceInterface::class, class_implements($class), true)) {
            throw new InvalidClassException("Class does not implements CacheServiceInterface");
        }

        $instance = new $class;

        $this->cacheServiceInstances[$cacheService] = $instance;

        return $instance;
    }
}
