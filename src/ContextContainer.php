<?php

namespace Tourze\Workerman\ConnectionContext;

use Workerman\Connection\ConnectionInterface;

class ContextContainer
{
    private static ?self $instance = null;

    /** @var \WeakMap<ConnectionInterface, array<class-string, object>> */
    private \WeakMap $connectionMap;

    private function __construct()
    {
        $this->connectionMap = new \WeakMap();
    }

    /**
     * 获取单例实例
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 重置单例实例（仅用于测试）
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }

    /**
     * 保存连接的上下文对象
     */
    public function setContext(ConnectionInterface $connection, object $context): void
    {
        $this->initContext($connection);

        $list = $this->connectionMap->offsetGet($connection);
        $list[get_class($context)] = $context;
        $this->connectionMap->offsetSet($connection, $list);
    }

    private function initContext(ConnectionInterface $connection): void
    {
        if (!$this->connectionMap->offsetExists($connection)) {
            $this->connectionMap->offsetSet($connection, []);
        }
    }

    /**
     * 获取指定连接的上下文对象
     *
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T|null
     */
    public function getContext(ConnectionInterface $connection, string $className)
    {
        $this->initContext($connection);

        $list = $this->connectionMap->offsetGet($connection);
        if (isset($list[$className])) {
            $context = $list[$className];
            \assert($context instanceof $className);

            return $context;
        }

        return null;
    }

    /**
     * 清理指定连接的特定上下文对象
     *
     * @param class-string $className
     */
    public function clearContext(ConnectionInterface $connection, string $className): void
    {
        if (!$this->connectionMap->offsetExists($connection)) {
            return;
        }

        $list = $this->connectionMap->offsetGet($connection);
        unset($list[$className]);

        if ([] === $list) {
            $this->connectionMap->offsetUnset($connection);
        } else {
            $this->connectionMap->offsetSet($connection, $list);
        }
    }

    /**
     * 清理指定连接的所有上下文对象
     */
    public function clearAllContexts(ConnectionInterface $connection): void
    {
        if ($this->connectionMap->offsetExists($connection)) {
            $this->connectionMap->offsetUnset($connection);
        }
    }
}
