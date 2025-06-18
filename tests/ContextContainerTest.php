<?php

namespace Tourze\Workerman\ConnectionContext\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\Workerman\ConnectionContext\ContextContainer;
use Tourze\Workerman\ConnectionContext\Tests\Mock\AnotherTestContext;
use Tourze\Workerman\ConnectionContext\Tests\Mock\MockConnection;
use Tourze\Workerman\ConnectionContext\Tests\Mock\TestContext;

class ContextContainerTest extends TestCase
{
    private ContextContainer $container;
    
    public function testSetAndGetContext(): void
    {
        $connection = new MockConnection(1);
        $context = new TestContext('hello', 42);

        $this->container->setContext($connection, $context);

        $retrievedContext = $this->container->getContext($connection, TestContext::class);

        $this->assertInstanceOf(TestContext::class, $retrievedContext);
        $this->assertSame($context, $retrievedContext);
        $this->assertEquals('hello', $retrievedContext->getData());
        $this->assertEquals(42, $retrievedContext->getCounter());
    }
    
    public function testGetContextReturnsNullWhenNotSet(): void
    {
        $connection = new MockConnection();

        $context = $this->container->getContext($connection, TestContext::class);

        $this->assertNull($context);
    }
    
    public function testMultipleContextsPerConnection(): void
    {
        $connection = new MockConnection();
        $context1 = new TestContext('test1', 10);
        $context2 = new AnotherTestContext(['key' => 'value']);

        $this->container->setContext($connection, $context1);
        $this->container->setContext($connection, $context2);

        $retrievedContext1 = $this->container->getContext($connection, TestContext::class);
        $retrievedContext2 = $this->container->getContext($connection, AnotherTestContext::class);

        $this->assertSame($context1, $retrievedContext1);
        $this->assertSame($context2, $retrievedContext2);
        $this->assertEquals('test1', $retrievedContext1->getData());
        $this->assertEquals(['key' => 'value'], $retrievedContext2->getMetadata());
    }
    
    public function testMultipleConnectionsWithSameContextType(): void
    {
        $connection1 = new MockConnection(1);
        $connection2 = new MockConnection(2);

        $context1 = new TestContext('conn1', 100);
        $context2 = new TestContext('conn2', 200);

        $this->container->setContext($connection1, $context1);
        $this->container->setContext($connection2, $context2);

        $retrievedContext1 = $this->container->getContext($connection1, TestContext::class);
        $retrievedContext2 = $this->container->getContext($connection2, TestContext::class);

        $this->assertSame($context1, $retrievedContext1);
        $this->assertSame($context2, $retrievedContext2);
        $this->assertEquals('conn1', $retrievedContext1->getData());
        $this->assertEquals('conn2', $retrievedContext2->getData());
    }
    
    public function testContextUpdateOverwritesPrevious(): void
    {
        $connection = new MockConnection();
        $context1 = new TestContext('initial', 1);
        $context2 = new TestContext('updated', 2);

        $this->container->setContext($connection, $context1);
        $this->container->setContext($connection, $context2);

        $retrievedContext = $this->container->getContext($connection, TestContext::class);

        $this->assertSame($context2, $retrievedContext);
        $this->assertEquals('updated', $retrievedContext->getData());
        $this->assertEquals(2, $retrievedContext->getCounter());
    }
    
    public function testContextIsolationBetweenConnections(): void
    {
        $connection1 = new MockConnection(1);
        $connection2 = new MockConnection(2);
        $context = new TestContext('shared', 50);

        $this->container->setContext($connection1, $context);

        $retrievedFromConn1 = $this->container->getContext($connection1, TestContext::class);
        $retrievedFromConn2 = $this->container->getContext($connection2, TestContext::class);

        $this->assertSame($context, $retrievedFromConn1);
        $this->assertNull($retrievedFromConn2);
    }
    
    public function testWeakMapBehavior(): void
    {
        $connection = new MockConnection();
        $context = new TestContext('test', 1);

        $this->container->setContext($connection, $context);

        $retrievedContext = $this->container->getContext($connection, TestContext::class);
        $this->assertNotNull($retrievedContext);

        unset($connection);

        $newConnection = new MockConnection();
        $retrievedContext = $this->container->getContext($newConnection, TestContext::class);
        $this->assertNull($retrievedContext);
    }
    
    protected function setUp(): void
    {
        $this->container = new ContextContainer();
    }
}