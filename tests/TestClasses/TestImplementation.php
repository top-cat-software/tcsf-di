<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestImplementation implements TestInterface
{
    const string IMPLEMENTATION_VALUE = 'Implementation Value';

    public function getValue(): string
    {
        return self::IMPLEMENTATION_VALUE;
    }
}
