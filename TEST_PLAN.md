# 测试计划 - Workerman Connection Context

## 概述

本文档定义了 `workerman-connection-context` 包的测试计划，该包提供了 Workerman 连接的上下文管理功能。

## 测试覆盖范围

### 1. 核心功能测试 (ContextContainerTest)

#### 1.1 基本上下文操作
- **testSetAndGetContext**: 测试设置和获取单个连接的上下文
- **testGetContextReturnsNullWhenNotSet**: 测试获取未设置的上下文返回 null

#### 1.2 多上下文管理
- **testMultipleContextsPerConnection**: 测试单个连接可以存储多种类型的上下文
- **testMultipleConnectionsWithSameContextType**: 测试多个连接可以存储相同类型的上下文

#### 1.3 上下文更新
- **testContextUpdateOverwritesPrevious**: 测试更新上下文会覆盖之前的值

#### 1.4 隔离性测试
- **testContextIsolationBetweenConnections**: 测试不同连接之间的上下文相互隔离

#### 1.5 WeakMap 特性
- **testWeakMapBehavior**: 测试连接对象被释放后，相关上下文自动清理

### 2. 边界情况测试 (EdgeCaseTest)

#### 2.1 异常输入处理
- **testGetContextWithNonExistentClass**: 测试获取不存在的类名返回 null

#### 2.2 并发和性能
- **testMultipleContextContainerInstances**: 测试多个容器实例共享同一个静态存储
- **testLargeNumberOfConnections**: 测试大量连接（1000+）的性能和正确性

#### 2.3 特殊对象支持
- **testContextWithAnonymousClass**: 测试匿名类作为上下文的支持

#### 2.4 高频操作
- **testRapidContextSwitching**: 测试快速切换上下文的正确性

#### 2.5 内存管理
- **testMemoryCleanupAfterConnectionRemoval**: 测试连接移除后的内存清理

## 测试环境

- PHP 版本: 8.1+
- PHPUnit 版本: 10.0+
- Workerman 版本: 5.1+

## 运行测试

```bash
# 在包目录下运行所有测试
../../vendor/bin/phpunit

# 运行特定测试类
../../vendor/bin/phpunit tests/ContextContainerTest.php

# 生成代码覆盖率报告
../../vendor/bin/phpunit --coverage-html coverage
```

## 质量保证

### PHPStan 静态分析
```bash
php -d memory_limit=2G ../../vendor/bin/phpstan analyse src tests --level=max
```

### 预期测试结果
- 所有测试应该通过
- 代码覆盖率应达到 100%
- PHPStan 分析应无错误（level max）

## 测试数据

### Mock 对象
- **MockConnection**: 模拟 Workerman ConnectionInterface 的实现
- **TestContext**: 简单的上下文对象，包含字符串数据和计数器
- **AnotherTestContext**: 另一种上下文对象，包含元数据数组

## 性能基准

- 1000 个连接的上下文设置和获取应在 100ms 内完成
- 内存使用应随连接数线性增长
- WeakMap 确保无内存泄漏

## 已知限制

- 上下文必须是对象类型，不支持标量值
- 使用 WeakMap 存储，连接对象必须保持引用才能访问上下文
- 依赖 PHP 8.0+ 的 WeakMap 特性