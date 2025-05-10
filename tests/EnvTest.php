<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    public function testEnv()
    {
        $this->assertEquals('test', $_ENV['APP_ENV']);
    }
}
