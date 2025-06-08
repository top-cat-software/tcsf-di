<?php
declare(strict_types=1);

namespace TCSF\DI\Tests;

use PHPUnit\Framework\TestCase;
use TCSF\DI\Container;
use TCSF\DI\ContainerInterface;
use TCSF\DI\Tests\TestClasses\TestSimple;
use TCSF\DI\Tests\TestClasses\TestDependency;
use TCSF\DI\Tests\TestClasses\TestWithDependency;
use TCSF\DI\Tests\TestClasses\TestInterface;
use TCSF\DI\Tests\TestClasses\TestImplementation;
use TCSF\DI\Tests\TestClasses\TestSingleton;
use TCSF\DI\Tests\TestClasses\TestFactory;
use TCSF\DI\Tests\TestClasses\TestDefaultParams;
use TCSF\DI\Tests\TestClasses\TestUnresolvable;

class ContainerTest extends TestCase
{
    protected function setUp(): void
    {
        Container::reset();
    }

    public function testInstanceReturnsSameInstance(): void
    {
        $instance1 = Container::instance();
        $instance2 = Container::instance();

        $this->assertSame($instance1, $instance2);
    }

    public function testCreateSimpleClass(): void
    {
        // Create an instance using the container
        $container = Container::instance();
        $instance = $container->create(TestSimple::class);

        // Verify it's the correct type
        $this->assertInstanceOf(TestSimple::class, $instance);
        $this->assertEquals('Hello, World!', $instance->hello());
    }

    public function testAutowiring(): void
    {
        // Create an instance using the container
        $container = Container::instance();
        $instance = $container->create(TestWithDependency::class);

        // Verify it's correctly autowired
        $this->assertInstanceOf(TestWithDependency::class, $instance);
        $this->assertEquals(TestDependency::DEPENDENCY_VALUE, $instance->getDependencyValue());
    }

    public function testBinding(): void
    {
        // Bind the interface to the implementation
        $container = Container::instance();
        $container->bind(TestInterface::class, TestImplementation::class);

        // Create an instance of the interface
        $instance = $container->create(TestInterface::class);

        // Verify it's the correct implementation
        $this->assertInstanceOf(TestImplementation::class, $instance);
        $this->assertEquals(TestImplementation::IMPLEMENTATION_VALUE, $instance->getValue());
    }

    public function testSingleton(): void
    {
        // Register as a singleton
        $container = Container::instance();
        $container->singleton(TestSingleton::class, TestSingleton::class);

        // Get two instances
        $instance1 = $container->create(TestSingleton::class);
        $instance1->increment();

        $instance2 = $container->create(TestSingleton::class);

        // Verify they're the same instance
        $this->assertSame($instance1, $instance2);
        $this->assertEquals(1, $instance2->value);

        $instance2->increment();
        $this->assertEquals(2, $instance2->value);
        $this->assertEquals(2, $instance1->value);
    }

    public function testRegisterInstance(): void
    {
        // Create a test instance
        $testInstance = new TestSimple();

        // Register the instance
        $container = Container::instance();
        $container->registerInstance(TestSimple::class, $testInstance);

        // Get the instance from the container
        $retrievedInstance = $container->create(TestSimple::class);

        // Verify it's the same instance
        $this->assertSame($testInstance, $retrievedInstance);
    }

    public function testFactoryBinding(): void
    {
        // Bind with a factory function
        $container = Container::instance();
        $container->bind(TestFactory::class, function() {
            return new TestFactory('Factory Created');
        });

        // Create an instance
        $instance = $container->create(TestFactory::class);

        // Verify it was created by the factory
        $this->assertEquals('Factory Created', $instance->factoryValue);
    }

    public function testDefaultParameterValues(): void
    {
        // Create an instance
        $container = Container::instance();
        $instance = $container->create(TestDefaultParams::class);

        // Verify default value was used
        $this->assertEquals('Default', $instance->value);
    }

    public function testExceptionOnUnresolvableParameter(): void
    {
        // Expect exception when creating
        $this->expectException(\Exception::class);

        $container = Container::instance();
        $container->create(TestUnresolvable::class);
    }
}
