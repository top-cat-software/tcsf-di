<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestFactory
{
    public ?string $factoryValue;

    public function __construct(?string $value = null)
    {
        $this->factoryValue = $value;
    }
}
