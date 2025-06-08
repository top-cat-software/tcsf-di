<?php
declare(strict_types=1);

namespace TCSF\DI;

interface ContainerInterface
{
    /**
     * Get the singleton instance of the container
     */
    public static function instance(): ContainerInterface;

    /**
     * Create an instance of the specified class with its dependencies resolved
     *
     * @template T
     * @param class-string<T> $className
     * @return T
     */
    public function create(string $className): object;

    /**
     * Bind an interface to a concrete implementation or factory
     *
     * @param string $abstract The interface or abstract class
     * @param string|callable $concrete The concrete class or factory function
     * @param bool $singleton Whether to store as a singleton
     * @return ContainerInterface
     */
    public function bind(string $abstract, string|callable $concrete, bool $singleton = false): ContainerInterface;
}
