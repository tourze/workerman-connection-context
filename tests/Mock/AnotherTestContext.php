<?php

declare(strict_types=1);

namespace Tourze\Workerman\ConnectionContext\Tests\Mock;

/**
 * 另一个测试上下文类，用于测试目的
 *
 * @internal
 */
final class AnotherTestContext
{
    /**
     * @var array<string, mixed>
     */
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

    public function removeMetadata(string $key): void
    {
        unset($this->metadata[$key]);
    }

    public function hasMetadata(string $key): bool
    {
        return array_key_exists($key, $this->metadata);
    }

    public function getMetadataValue(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }
}
