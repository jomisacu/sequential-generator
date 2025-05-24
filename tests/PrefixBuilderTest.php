<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator\Tests;

use PHPUnit\Framework\TestCase;

final class PrefixBuilderTest extends TestCase
{
    public function testPrefixBuilderCreateAPrefixWithOrderedProcessors()
    {
        $builder = new \Jomisacu\SequentialGenerator\PrefixBuilder();

        $builder->setTemplate('test')
            ->appendProcessor(fn($value) => $value . '1')
            ->appendProcessor(fn($value) => $value . '2')
            ->appendProcessor(fn($value) => $value . '3')
            ->prependProcessor(fn($value) => '3' . $value)
            ->prependProcessor(fn($value) => '2' . $value)
            ->prependProcessor(fn($value) => '1' . $value);

        $prefix = $builder->build();

        $this->assertTrue($prefix->compile() === '321test123');
    }
}
