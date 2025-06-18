<?php

namespace Tourze\Workerman\ConnectionContext;

use WeakMap;
use Workerman\Connection\ConnectionInterface;

class ContextContainer
{
    /** @var WeakMap<ConnectionInterface, array<class-string, object>> */
    private static WeakMap $connectionMap;

    /**
     * 保存连接的上下文对象
     *
     * @param ConnectionInterface $connection
     * @param object $context
     * @return void
     */
    public function setContext(ConnectionInterface $connection, object $context): void
    {
        self::initConnectionMap();
        self::initContext($connection);

        $list = self::$connectionMap->offsetGet($connection);
        $list[get_class($context)] = $context;
        self::$connectionMap->offsetSet($connection, $list);
    }

    private static function initConnectionMap(): void
    {
        if (!isset(self::$connectionMap)) {
            self::$connectionMap = new WeakMap();
        }
    }

    private static function initContext(ConnectionInterface $connection): void
    {
        if (!self::$connectionMap->offsetExists($connection)) {
            self::$connectionMap->offsetSet($connection, []);
        }
    }

    /**
     * 获取指定连接的上下文对象
     *
     * @param ConnectionInterface $connection
     * @param class-string $className
     * @return object|null
     */
    public function getContext(ConnectionInterface $connection, string $className): ?object
    {
        self::initConnectionMap();
        self::initContext($connection);

        $list = self::$connectionMap->offsetGet($connection);
        if (isset($list[$className])) {
            return $list[$className];
        }
        return null;
    }
}
