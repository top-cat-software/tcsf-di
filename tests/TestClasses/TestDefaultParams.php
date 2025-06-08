<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestDefaultParams
{
    public string $value;

    public function __construct(string $value = 'Default')
    {
        $this->value = $value;
    }
}
