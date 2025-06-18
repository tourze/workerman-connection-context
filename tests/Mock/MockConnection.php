<?php

namespace Tourze\Workerman\ConnectionContext\Tests\Mock;

use Workerman\Connection\ConnectionInterface;

class MockConnection extends ConnectionInterface
{
    /** @phpstan-ignore property.onlyWritten */
    private int $id;
    
    public function __construct(int $id = 1)
    {
        $this->id = $id;
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
        return 9090;
    }
    
    public function getLocalAddress(): string
    {
        return '127.0.0.1:9090';
    }
    
    public function close(mixed $data = null, bool $raw = false): void
    {
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