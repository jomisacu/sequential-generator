<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

final class PrefixBuilder
{
    private string $template;
    private array $processors;

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function appendProcessor(\Closure $closure): self
    {
        $this->processors[] = $closure;

        return $this;
    }

    public function prependProcessor(\Closure $closure): self
    {
        array_unshift($this->processors, $closure);

        return $this;
    }

    public function build(): Prefix
    {
        return new Prefix($this->template, $this->processors);
    }
}
