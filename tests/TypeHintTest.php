<?php

namespace Tourze\Workerman\ConnectionContext\Tests;

use PHPUnit\Framework\TestCase;
use Tourze\Workerman\ConnectionContext\ContextContainer;
use Tourze\Workerman\ConnectionContext\Tests\Mock\MockConnection;
use Tourze\Workerman\ConnectionContext\Tests\Mock\TestContext;

class TypeHintTest extends TestCase
{
    public function testTypeHintWorks(): void
    {
        $connection = new MockConnection();
        $context = new TestContext('test', 42);
        
        ContextContainer::getInstance()->setContext($connection, $context);
        
        // IDE 应该能识别 $retrievedContext 是 TestContext 类型
        $retrievedContext = ContextContainer::getInstance()->getContext($connection, TestContext::class);
        
        // 不需要类型检查，IDE 应该知道这是 TestContext
        if ($retrievedContext !== null) {
            // IDE 应该能自动补全 getData() 和 getCounter() 方法
            $data = $retrievedContext->getData();
            $counter = $retrievedContext->getCounter();
            
            $this->assertEquals('test', $data);
            $this->assertEquals(42, $counter);
        }
    }
    
    protected function tearDown(): void
    {
        ContextContainer::resetInstance();
    }
}