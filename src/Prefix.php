<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

final readonly class Prefix
{
    public function __construct(private string $template, private array $processors)
    {
    }

    public function compile(): string
    {
        $compiled = $this->template;

        foreach ($this->processors as $processor) {
            $compiled = $processor($compiled);
        }

        return $compiled;
    }

    public function __toString(): string
    {
        return $this->compile();
    }
}
