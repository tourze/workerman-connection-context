<?php

namespace Tourze\Workerman\ConnectionContext\Tests\Mock;

class AnotherTestContext
{
    /** @var array<string, mixed> */
    private array $metadata;
    
    /**
     * @param array<string, mixed> $metadata
     */
    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }
    
    /**
     * @return array<string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    /**
     * @param array<string, mixed> $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
    
    public function addMetadata(string $key, mixed $value): void
    {
        $this->metadata[$key] = $value;
    }
}