<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator\Tests;

use Jomisacu\SequentialGenerator\Prefix;
use PHPUnit\Framework\TestCase;

class PrefixTest extends TestCase
{
    public function testPrefixCanBeCompiled()
    {
        $prefix = new Prefix('test', [
            fn($value) => str_replace('test', 'TEST', $value),
        ]);

        $this->assertEquals('TEST', $prefix->compile());
    }
}
