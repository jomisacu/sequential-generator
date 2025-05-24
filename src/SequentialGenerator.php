<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

use Jomisacu\SequentialGenerator\SequentialGeneratorInterface;

final class SequentialGenerator implements SequentialGeneratorInterface
{
    public function __construct(private readonly PrefixGatewayInterface $prefixGateway)
    {
    }

    public function generate(Prefix $prefix): string
    {
        $lockId = $this->prefixGateway->lockPrefix($prefix);

        $sequential = $this->prefixGateway->getNextSequence($prefix, $lockId);

        $this->prefixGateway->saveNextSequence($prefix, $lockId, $sequential);

        $this->prefixGateway->unlockPrefix($prefix, $lockId);

        return $prefix->compile() . $sequential;
    }
}
