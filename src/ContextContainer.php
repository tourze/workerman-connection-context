<?php

namespace Tourze\Workerman\ConnectionContext;

use WeakMap;
use Workerman\Connection\ConnectionInterface;

class ContextContainer
{
    private static ?self $instance = null;
    
    /** @var WeakMap<ConnectionInterface, array<class-string, object>> */
    private WeakMap $connectionMap;

    private function __construct()
    {
        $this->connectionMap = new WeakMap();
    }

    /**
     * 获取单例实例
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
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
     *
     * @param ConnectionInterface $connection
     * @param object $context
     * @return void
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
     * @param ConnectionInterface $connection
     * @param class-string<T> $className
     * @return T|null
     */
    public function getContext(ConnectionInterface $connection, string $className): ?object
    {
        $this->initContext($connection);

        $list = $this->connectionMap->offsetGet($connection);
        if (isset($list[$className])) {
            return $list[$className];
        }
        return null;
    }

    /**
     * 清理指定连接的特定上下文对象
     *
     * @param ConnectionInterface $connection
     * @param class-string $className
     * @return void
     */
    public function clearContext(ConnectionInterface $connection, string $className): void
    {
        if (!$this->connectionMap->offsetExists($connection)) {
            return;
        }

        $list = $this->connectionMap->offsetGet($connection);
        unset($list[$className]);
        
        if (empty($list)) {
            $this->connectionMap->offsetUnset($connection);
        } else {
            $this->connectionMap->offsetSet($connection, $list);
        }
    }

    /**
     * 清理指定连接的所有上下文对象
     *
     * @param ConnectionInterface $connection
     * @return void
     */
    public function clearAllContexts(ConnectionInterface $connection): void
    {
        if ($this->connectionMap->offsetExists($connection)) {
            $this->connectionMap->offsetUnset($connection);
        }
    }
}
