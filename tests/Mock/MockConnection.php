<?php

declare(strict_types=1);

namespace Tourze\Workerman\ConnectionContext\Tests\Mock;

use Workerman\Connection\ConnectionInterface;

/**
 * 模拟连接类，用于测试目的
 *
 * @internal
 */
final class MockConnection extends ConnectionInterface
{
    private ?int $id;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function send(mixed $sendBuffer, bool $raw = false): ?bool
    {
        return true;
    }

    public function getRemoteIp(): string
    {
        return '127.0.0.1';
    }

    public function getRemotePort(): int
    {
        return 8080;
    }

    public function getRemoteAddress(): string
    {
        return '127.0.0.1:8080';
    }

    public function getLocalIp(): string
    {
        return '127.0.0.1';
    }

    public function getLocalPort(): int
    {
        return 9000;
    }

    public function getLocalAddress(): string
    {
        return '127.0.0.1:9000';
    }

    public function close(mixed $data = null, bool $raw = false): void
    {
        // Mock implementation - do nothing
    }

    public function isIpV4(): bool
    {
        return true;
    }

    public function isIpV6(): bool
    {
        return false;
    }
}
