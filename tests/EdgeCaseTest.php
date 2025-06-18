<?php

namespace Tourze\Workerman\ConnectionContext\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\Workerman\ConnectionContext\ContextContainer;
use Tourze\Workerman\ConnectionContext\Tests\Mock\MockConnection;
use Tourze\Workerman\ConnectionContext\Tests\Mock\TestContext;

class EdgeCaseTest extends TestCase
{
    public function testGetContextWithNonExistentClass(): void
    {
        $connection = new MockConnection();
        $context = new TestContext('test');

        ContextContainer::getInstance()->setContext($connection, $context);

        $result = ContextContainer::getInstance()->getContext($connection, \stdClass::class);
        $this->assertNull($result);
    }
    
    public function testSingletonBehavior(): void
    {
        $container1 = ContextContainer::getInstance();
        $container2 = ContextContainer::getInstance();

        $this->assertSame($container1, $container2);

        $connection = new MockConnection();
        $context = new TestContext('shared');

        $container1->setContext($connection, $context);

        $retrieved = $container2->getContext($connection, TestContext::class);
        $this->assertSame($context, $retrieved);
    }
    
    public function testLargeNumberOfConnections(): void
    {
        $connections = [];
        $contexts = [];
        $connectionCount = 1000;

        for ($i = 0; $i < $connectionCount; $i++) {
            $connection = new MockConnection($i);
            $context = new TestContext('data_' . $i, $i);

            $connections[$i] = $connection;
            $contexts[$i] = $context;

            ContextContainer::getInstance()->setContext($connection, $context);
        }

        for ($i = 0; $i < $connectionCount; $i++) {
            $retrieved = ContextContainer::getInstance()->getContext($connections[$i], TestContext::class);
            $this->assertSame($contexts[$i], $retrieved);
            $this->assertEquals('data_' . $i, $retrieved->getData());
            $this->assertEquals($i, $retrieved->getCounter());
        }

        $middleIndex = (int)($connectionCount / 2);
        unset($connections[$middleIndex]);

        $stillExists = ContextContainer::getInstance()->getContext($connections[0], TestContext::class);
        $this->assertNotNull($stillExists);
    }
    
    public function testContextWithAnonymousClass(): void
    {
        $connection = new MockConnection();

        $anonymousContext = new class {
            private string $value = 'anonymous';

            public function getValue(): string
            {
                return $this->value;
            }
        };

        ContextContainer::getInstance()->setContext($connection, $anonymousContext);

        $retrieved = ContextContainer::getInstance()->getContext($connection, get_class($anonymousContext));
        $this->assertNotNull($retrieved);
        $this->assertSame($anonymousContext, $retrieved);
        $this->assertEquals('anonymous', $anonymousContext->getValue());
    }
    
    public function testRapidContextSwitching(): void
    {
        $connection = new MockConnection();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $context = new TestContext('iteration_' . $i, $i);
            ContextContainer::getInstance()->setContext($connection, $context);

            $retrieved = ContextContainer::getInstance()->getContext($connection, TestContext::class);
            $this->assertInstanceOf(TestContext::class, $retrieved);
            $this->assertEquals('iteration_' . $i, $retrieved->getData());
            $this->assertEquals($i, $retrieved->getCounter());
        }

        $finalContext = ContextContainer::getInstance()->getContext($connection, TestContext::class);
        $this->assertInstanceOf(TestContext::class, $finalContext);
        $this->assertEquals('iteration_' . ($iterations - 1), $finalContext->getData());
    }
    
    public function testMemoryCleanupAfterConnectionRemoval(): void
    {
        $contexts = [];
        $connectionCount = 10;

        for ($i = 0; $i < $connectionCount; $i++) {
            $connection = new MockConnection($i);
            $context = new TestContext('conn_' . $i, $i);
            ContextContainer::getInstance()->setContext($connection, $context);
            $contexts[] = $context;
        }

        $this->assertCount($connectionCount, $contexts);
    }
    
    protected function tearDown(): void
    {
        ContextContainer::resetInstance();
    }
}