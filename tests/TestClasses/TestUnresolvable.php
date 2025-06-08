<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestUnresolvable
{
    public function __construct(int $value)
    {
        // This parameter can't be autowired
    }
}
