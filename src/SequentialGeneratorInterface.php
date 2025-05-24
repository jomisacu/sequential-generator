<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

interface SequentialGeneratorInterface
{
    public function generate(Prefix $prefix): string;
}
