<?php

declare(strict_types=1);

namespace Tourze\Workerman\ConnectionContext\Tests\Mock;

/**
 * 测试上下文类，用于测试目的
 *
 * @internal
 */
final class TestContext
{
    private string $data;

    private int $counter;

    public function __construct(string $data, int $counter = 0)
    {
        $this->data = $data;
        $this->counter = $counter;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setData(string $data): void
    {
        $this->data = $data;
    }

    public function setCounter(int $counter): void
    {
        $this->counter = $counter;
    }

    public function incrementCounter(): void
    {
        ++$this->counter;
    }
}
