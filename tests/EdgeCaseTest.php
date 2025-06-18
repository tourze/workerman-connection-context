<?php

namespace Tourze\Workerman\ConnectionContext\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\Workerman\ConnectionContext\ContextContainer;
use Tourze\Workerman\ConnectionContext\Tests\Mock\MockConnection;
use Tourze\Workerman\ConnectionContext\Tests\Mock\TestContext;

class EdgeCaseTest extends TestCase
{
    private ContextContainer $container;
    
    public function testGetContextWithNonExistentClass(): void
    {
        $connection = new MockConnection();
        $context = new TestContext('test');

        $this->container->setContext($connection, $context);

        $result = $this->container->getContext($connection, \stdClass::class);
        $this->assertNull($result);
    }
    
    public function testMultipleContextContainerInstances(): void
    {
        $container1 = new ContextContainer();
        $container2 = new ContextContainer();

        $connection = new MockConnection();
        $context = new TestContext('shared');

        $container1->setContext($connection, $context);

        $retrieved1 = $container1->getContext($connection, TestContext::class);
        $retrieved2 = $container2->getContext($connection, TestContext::class);

        $this->assertSame($context, $retrieved1);
        $this->assertSame($context, $retrieved2);
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

            $this->container->setContext($connection, $context);
        }

        for ($i = 0; $i < $connectionCount; $i++) {
            $retrieved = $this->container->getContext($connections[$i], TestContext::class);
            $this->assertSame($contexts[$i], $retrieved);
            $this->assertEquals('data_' . $i, $retrieved->getData());
            $this->assertEquals($i, $retrieved->getCounter());
        }

        $middleIndex = (int)($connectionCount / 2);
        unset($connections[$middleIndex]);

        $stillExists = $this->container->getContext($connections[0], TestContext::class);
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

        $this->container->setContext($connection, $anonymousContext);

        $retrieved = $this->container->getContext($connection, get_class($anonymousContext));
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
            $this->container->setContext($connection, $context);

            $retrieved = $this->container->getContext($connection, TestContext::class);
            $this->assertInstanceOf(TestContext::class, $retrieved);
            $this->assertEquals('iteration_' . $i, $retrieved->getData());
            $this->assertEquals($i, $retrieved->getCounter());
        }

        $finalContext = $this->container->getContext($connection, TestContext::class);
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
            $this->container->setContext($connection, $context);
            $contexts[] = $context;
        }

        $this->assertCount($connectionCount, $contexts);
    }
    
    protected function setUp(): void
    {
        $this->container = new ContextContainer();
    }
}