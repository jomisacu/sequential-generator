<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

final class PrefixAlreadyLockedException extends \RuntimeException
{
    public static function fromPrefix(Prefix $prefix): self
    {
        return new self(
            sprintf(
                'Cant lock prefix "%s".',
                $prefix->compile()
            )
        );
    }
}
