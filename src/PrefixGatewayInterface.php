<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

interface PrefixGatewayInterface
{
    /**
     * @throws PrefixAlreadyLockedException
     * @return string A lock ID that can be used to unlock the generator.
     */
    public function lockPrefix(Prefix $prefix): string;

    /**
     * @throws PrefixNotLockedException
     */
    public function unlockPrefix(Prefix $prefix, string $lockId): void;

    /**
     * @throws PrefixNotLockedException
     */
    public function getNextSequence(Prefix $prefix, string $lockId): int;

    /**
     * @throws PrefixNotLockedException
     */
    public function saveNextSequence(Prefix $prefix, string $lockId, int $sequence);
}
