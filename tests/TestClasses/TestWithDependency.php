<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestWithDependency
{
    private TestDependency $dependency;

    public function __construct(TestDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    public function getDependencyValue(): string
    {
        return $this->dependency->getValue();
    }
}
