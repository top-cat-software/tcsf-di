# Top Cat Software Framework Dependency Injection

A lightweight, powerful dependency injection container for PHP 8.4+.

## Features

- Simple, intuitive API
- Autowiring of dependencies
- Support for singleton instances
- Factory bindings
- Interface to implementation binding
- PSR-4 compliant

## Installation

```bash
composer require top-cat-software/tcsf-di
```

## Basic Usage

```php
use TCSF\DI\Container;

// Get the container instance
$container = Container::instance();

// Create an instance with autowiring
$myService = $container->create(MyService::class);
```

## Binding Interfaces to Implementations

```php
use TCSF\DI\Container;

// Bind an interface to a concrete implementation
$container = Container::instance();
$container->bind(MyInterface::class, MyImplementation::class);

// Now you can create instances of the interface
$instance = $container->create(MyInterface::class);
// $instance will be an instance of MyImplementation
```

## Singleton Bindings

```php
use TCSF\DI\Container;

// Bind a class as a singleton
$container = Container::instance();
$container->singleton(MyService::class, MyService::class);

// Get the same instance each time
$instance1 = $container->create(MyService::class);
$instance2 = $container->create(MyService::class);
// $instance1 === $instance2
```

## Factory Bindings

```php
use TCSF\DI\Container;

// Bind a class to a factory function
$container = Container::instance();
$container->bind(MyService::class, function($container) {
    return new MyService('custom', 'parameters');
});

// Create an instance using the factory
$instance = $container->create(MyService::class);
```

## Registering Existing Instances

```php
use TCSF\DI\Container;

// Create an instance manually
$myService = new MyService('custom', 'parameters');

// Register it with the container
$container = Container::instance();
$container->registerInstance(MyService::class, $myService);

// Get the same instance back
$instance = $container->create(MyService::class);
// $instance === $myService
```

## Autowiring

The container automatically resolves dependencies by type-hinting:

```php
class UserRepository
{
    // Implementation...
}

class UserService
{
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
}

// The container will automatically create a UserRepository when creating a UserService
$userService = $container->create(UserService::class);
```

## License

MIT
