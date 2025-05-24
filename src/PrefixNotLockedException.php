<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

final class PrefixNotLockedException extends \RuntimeException
{
    public static function fromPrefixAndLockId(Prefix $prefix, string $lockId): self
    {
        return new self(sprintf('Prefix %s is not locked by the lock id %s', $prefix->compile(), $lockId));
    }
}
