<?php

namespace Tourze\Workerman\ConnectionContext\Tests\Mock;

class TestContext
{
    private string $data;
    private int $counter;
    
    public function __construct(string $data = 'test', int $counter = 0)
    {
        $this->data = $data;
        $this->counter = $counter;
    }
    
    public function getData(): string
    {
        return $this->data;
    }
    
    public function setData(string $data): void
    {
        $this->data = $data;
    }
    
    public function getCounter(): int
    {
        return $this->counter;
    }
    
    public function increment(): void
    {
        $this->counter++;
    }
}