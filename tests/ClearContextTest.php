<?php

namespace Tourze\Workerman\ConnectionContext\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\Workerman\ConnectionContext\ContextContainer;
use Tourze\Workerman\ConnectionContext\Tests\Mock\AnotherTestContext;
use Tourze\Workerman\ConnectionContext\Tests\Mock\MockConnection;
use Tourze\Workerman\ConnectionContext\Tests\Mock\TestContext;

class ClearContextTest extends TestCase
{
    public function testClearSpecificContext(): void
    {
        $connection = new MockConnection();
        $context1 = new TestContext('test1', 10);
        $context2 = new AnotherTestContext(['key' => 'value']);

        $container = ContextContainer::getInstance();
        $container->setContext($connection, $context1);
        $container->setContext($connection, $context2);

        // 清理特定的上下文
        $container->clearContext($connection, TestContext::class);

        // TestContext 应该被清理
        $this->assertNull($container->getContext($connection, TestContext::class));
        // AnotherTestContext 应该还在
        $this->assertNotNull($container->getContext($connection, AnotherTestContext::class));
        $this->assertSame($context2, $container->getContext($connection, AnotherTestContext::class));
    }

    public function testClearNonExistentContext(): void
    {
        $connection = new MockConnection();
        $context = new TestContext('test', 1);

        $container = ContextContainer::getInstance();
        $container->setContext($connection, $context);

        // 清理不存在的上下文类型不应该报错
        $container->clearContext($connection, AnotherTestContext::class);

        // 原有的上下文应该还在
        $this->assertNotNull($container->getContext($connection, TestContext::class));
    }

    public function testClearAllContexts(): void
    {
        $connection = new MockConnection();
        $context1 = new TestContext('test1', 10);
        $context2 = new AnotherTestContext(['key' => 'value']);

        $container = ContextContainer::getInstance();
        $container->setContext($connection, $context1);
        $container->setContext($connection, $context2);

        // 清理所有上下文
        $container->clearAllContexts($connection);

        // 所有上下文都应该被清理
        $this->assertNull($container->getContext($connection, TestContext::class));
        $this->assertNull($container->getContext($connection, AnotherTestContext::class));
    }

    public function testClearContextForNonExistentConnection(): void
    {
        $connection = new MockConnection();
        $container = ContextContainer::getInstance();

        // 清理不存在的连接不应该报错
        $container->clearContext($connection, TestContext::class);
        $container->clearAllContexts($connection);
        
        // 不应该有任何影响
        $this->assertNull($container->getContext($connection, TestContext::class));
    }

    public function testMultipleConnectionsIndependence(): void
    {
        $connection1 = new MockConnection(1);
        $connection2 = new MockConnection(2);
        $context1 = new TestContext('conn1', 100);
        $context2 = new TestContext('conn2', 200);

        $container = ContextContainer::getInstance();
        $container->setContext($connection1, $context1);
        $container->setContext($connection2, $context2);

        // 清理 connection1 的上下文
        $container->clearContext($connection1, TestContext::class);

        // connection1 的上下文应该被清理
        $this->assertNull($container->getContext($connection1, TestContext::class));
        // connection2 的上下文应该还在
        $this->assertNotNull($container->getContext($connection2, TestContext::class));
        $this->assertSame($context2, $container->getContext($connection2, TestContext::class));
    }

    protected function tearDown(): void
    {
        ContextContainer::resetInstance();
    }
}