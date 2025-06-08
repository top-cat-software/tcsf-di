<?php
declare(strict_types=1);

namespace TCSF\DI;

use ReflectionClass;
use ReflectionParameter;
use ReflectionException;

final class Container implements ContainerInterface
{
    private static ?Container $instance = null;
    private array $bindings = [];
    private array $instances = [];

    /**
     * Private constructor to enforce singleton pattern
     */
    private function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public static function instance(): ContainerInterface
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    public function create(string $className): object
    {
        // If we have a singleton instance, return it
        if (isset($this->instances[$className])) {
            return $this->instances[$className];
        }

        // If we have a binding for this class, use it
        if (isset($this->bindings[$className])) {
            return $this->resolveBinding($className);
        }

        // Otherwise, create a new instance with autowiring
        return $this->autowire($className);
    }

    /**
     * @inheritDoc
     */
    public function bind(string $abstract, string|callable $concrete, bool $singleton = false): self
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton,
        ];

        return $this;
    }

    /**
     * Bind a singleton instance
     * 
     * @param string $abstract The interface or abstract class
     * @param string|callable $concrete The concrete class or factory function
     * @return self
     */
    public function singleton(string $abstract, string|callable $concrete): self
    {
        return $this->bind($abstract, $concrete, true);
    }

    /**
     * Register an existing instance as a singleton
     * 
     * @param string $abstract The interface or abstract class
     * @param object $instance The instance to register
     * @return self
     */
    public function registerInstance(string $abstract, object $instance): self
    {
        $this->instances[$abstract] = $instance;

        return $this;
    }

    /**
     * Resolve a binding to an actual instance
     * 
     * @param string $abstract The interface or abstract class
     * @return object The resolved instance
     * @throws ReflectionException
     */
    private function resolveBinding(string $abstract): object
    {
        $binding = $this->bindings[$abstract];
        $concrete = $binding['concrete'];

        // If concrete is a callable, call it with the container
        if (is_callable($concrete)) {
            $instance = $concrete($this);
        } 
        // If concrete is a class name, autowire it
        else {
            $instance = $this->autowire($concrete);
        }

        // If this is a singleton, store the instance
        if ($binding['singleton']) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Autowire a class by resolving its dependencies
     * 
     * @param string $className The class to autowire
     * @return object The instantiated object
     * @throws ReflectionException
     */
    private function autowire(string $className): object
    {
        $reflector = new ReflectionClass($className);

        if (!$reflector->isInstantiable()) {
            throw new \Exception("Class {$className} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        // If there's no constructor, just return a new instance
        if ($constructor === null) {
            return new $className();
        }

        // Get constructor parameters
        $parameters = $constructor->getParameters();

        // If there are no parameters, just return a new instance
        if (count($parameters) === 0) {
            return new $className();
        }

        // Resolve each parameter
        $dependencies = array_map(function (ReflectionParameter $param) {
            return $this->resolveDependency($param);
        }, $parameters);

        // Create a new instance with the resolved dependencies
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * Resolve a dependency for a parameter
     * 
     * @param ReflectionParameter $parameter The parameter to resolve
     * @return mixed The resolved dependency
     * @throws ReflectionException
     */
    private function resolveDependency(ReflectionParameter $parameter): mixed
    {
        // Get the parameter class type
        $type = $parameter->getType();

        // If the parameter is a class, resolve it
        if (!$type->isBuiltin() && $type instanceof \ReflectionNamedType) {
            $typeName = $type->getName();
            return $this->create($typeName);
        }

        // If the parameter has a default value, use it
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        // If we can't resolve the parameter, throw an exception
        throw new \Exception("Cannot resolve parameter {$parameter->getName()}");
    }

    /**
     * Reset the container (mainly for testing)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
