<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator;

use Symfony\Component\Uid\Uuid;

final readonly class PrefixGatewayMySql implements PrefixGatewayInterface
{
    public function __construct(private \PDO $pdo)
    {
    }

    /**
     * @inheritDoc
     */
    public function lockPrefix(Prefix $prefix): string
    {
        $stmt = $this->pdo->prepare('SELECT lock_id FROM jomisacu_sequential_generator_prefixes WHERE prefix = :prefix');
        $stmt->execute([':prefix' => $prefix->compile()]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($row && $row['lock_id']) {
            throw PrefixAlreadyLockedException::fromPrefix($prefix);
        }

        $lockId = Uuid::v4()->toRfc4122();

        if ($row) {
            $stmt = $this->pdo->prepare('UPDATE jomisacu_sequential_generator_prefixes SET lock_id = :lock_id, locked = true WHERE prefix = :prefix');
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO jomisacu_sequential_generator_prefixes (prefix, lock_id, locked) VALUES (:prefix, :lock_id, true)');
        }

        $stmt->execute([':prefix' => $prefix->compile(), ':lock_id' => $lockId]);

        return $lockId;
    }

    /**
     * @inheritDoc
     */
    public function unlockPrefix(Prefix $prefix, string $lockId): void
    {
        $stmt = $this->pdo->prepare('SELECT lock_id FROM jomisacu_sequential_generator_prefixes WHERE prefix = :prefix');
        $stmt->execute([':prefix' => $prefix->compile()]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row || $row['lock_id'] !== $lockId) {
            throw PrefixNotLockedException::fromPrefixAndLockId($prefix, $lockId);
        }

        $stmt = $this->pdo->prepare('UPDATE jomisacu_sequential_generator_prefixes SET lock_id = NULL WHERE prefix = :prefix');
        $stmt->execute([':prefix' => $prefix->compile()]);
    }

    /**
     * @inheritDoc
     */
    public function getNextSequence(Prefix $prefix, string $lockId): int
    {
        $stmt = $this->pdo->prepare('SELECT sequence FROM jomisacu_sequential_generator_prefixes WHERE prefix = :prefix AND lock_id = :lock_id');
        $stmt->execute([':prefix' => $prefix->compile(), ':lock_id' => $lockId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            throw PrefixNotLockedException::fromPrefixAndLockId($prefix, $lockId);
        }

        return ((int)$row['sequence']) + 1;
    }

    /**
     * @inheritDoc
     */
    public function saveNextSequence(Prefix $prefix, string $lockId, int $sequence): void
    {
        $stmt = $this->pdo->prepare('UPDATE jomisacu_sequential_generator_prefixes SET sequence = :sequence WHERE prefix = :prefix AND lock_id = :lock_id');
        $stmt->execute([':prefix' => $prefix->compile(), ':lock_id' => $lockId, ':sequence' => $sequence]);

        if ($stmt->rowCount() === 0) {
            throw PrefixNotLockedException::fromPrefixAndLockId($prefix, $lockId);
        }
    }
}
