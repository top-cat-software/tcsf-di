<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestDependency
{
    const string DEPENDENCY_VALUE = 'Dependency Value';
    public function getValue(): string
    {
        return self::DEPENDENCY_VALUE;
    }
}
