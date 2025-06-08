<?php
declare(strict_types=1);

namespace TCSF\DI\Tests\TestClasses;

class TestSimple
{
    public function hello(): string
    {
        return 'Hello, World!';
    }
}
