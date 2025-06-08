<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestSingleton
{
    public int $value = 0;

    public function increment(): void
    {
        $this->value++;
    }
}
