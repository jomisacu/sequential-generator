<?php

declare(strict_types=1);

namespace Jomisacu\SequentialGenerator\Tests;

use Jomisacu\SequentialGenerator\Prefix;
use Jomisacu\SequentialGenerator\PrefixGatewayMySql;
use PHPUnit\Framework\TestCase;

final class PrefixGatewayMySqlTest extends TestCase
{
    private \PDO $pdo;

    protected function setUp(): void
    {
        $dbHost = getenv('DB_HOST') ?: '127.0.0.1';
        $dbPort = getenv('DB_PORT') ?: '3308';
        $dbName = getenv('DB_NAME') ?: 'jomisacu_sequential_generator';
        $dbUser = getenv('DB_USER') ?: 'root';
        $dbPassword = getenv('DB_PASSWORD') ?: '';

        $this->pdo = new \PDO(sprintf('mysql:host=%s;port=%s;dbname=%s', $dbHost, $dbPort, $dbName), $dbUser, $dbPassword, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }

    protected function tearDown(): void
    {
        $this->pdo->exec('DELETE FROM jomisacu_sequential_generator_prefixes WHERE 1');
    }

    public function testPrefixCanBeLocked()
    {
        $gateway = $this->getPrefixGateway();
        $prefix = $this->getPrefix();

        $lockId = $gateway->lockPrefix($prefix);

        $stmt = $this->pdo->prepare('SELECT lock_id FROM jomisacu_sequential_generator_prefixes WHERE prefix = :prefix');
        $stmt->execute([':prefix' => $prefix->compile()]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($row);
        $this->assertTrue($row['lock_id'] === $lockId);
    }

    public function testPrefixAlreadyLockedExceptionIsThrown()
    {
        $gateway = $this->getPrefixGateway();
        $prefix = $this->getPrefix();

        $gateway->lockPrefix($prefix);

        $this->expectException(\Jomisacu\SequentialGenerator\PrefixAlreadyLockedException::class);

        $gateway->lockPrefix($prefix);
    }

    public function testPrefixCanBeUnlocked()
    {
        $gateway = $this->getPrefixGateway();
        $prefix = $this->getPrefix();

        $lockId = $gateway->lockPrefix($prefix);
        $gateway->unlockPrefix($prefix, $lockId);

        $stmt = $this->pdo->prepare('SELECT lock_id FROM jomisacu_sequential_generator_prefixes WHERE prefix = :prefix');
        $stmt->execute([':prefix' => $prefix->compile()]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertNotEmpty($row);
        $this->assertNull($row['lock_id']);
    }

    private function getPrefixGateway(): PrefixGatewayMySql
    {
        return new PrefixGatewayMySql(
            $this->pdo
        );
    }

    private function getPrefix(): Prefix
    {
        return new Prefix('test_prefix', []);
    }
}
