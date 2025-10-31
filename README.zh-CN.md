# Workerman 连接上下文管理

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

为 Workerman 连接提供上下文管理解决方案。该包提供了一种存储和管理连接特定上下文对象的方法，使用 PHP 的 WeakMap 特性进行自动内存管理。

## 特性

- 连接特定的上下文存储
- 每个连接支持多种上下文类型
- 使用 WeakMap 自动清理内存
- 类型安全的上下文检索
- 单例模式的全局访问

## 安装

```bash
composer require tourze/workerman-connection-context
```

## 快速开始

### 基本用法

```php
use Tourze\Workerman\ConnectionContext\ContextContainer;

// 定义你的上下文类
class UserContext
{
    public function __construct(
        private string $userId,
        private array $permissions
    ) {}

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}

// 在你的 Workerman 回调中
$worker->onConnect = function ($connection) {
    $context = new UserContext('user123', ['read', 'write']);
    ContextContainer::getInstance()->setContext($connection, $context);
};

$worker->onMessage = function ($connection, $data) {
    $context = ContextContainer::getInstance()->getContext($connection, UserContext::class);
    if ($context) {
        echo "用户: " . $context->getUserId() . "\n";
        echo "权限: " . implode(', ', $context->getPermissions()) . "\n";
    }
};
```

### 多种上下文类型

```php
class SessionContext
{
    public function __construct(private string $sessionId) {}
    
    public function getSessionId(): string
    {
        return $this->sessionId;
    }
}

class AuthContext
{
    public function __construct(private bool $isAuthenticated) {}
    
    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }
}

// 为同一个连接存储多种上下文类型
$sessionContext = new SessionContext('sess_123');
$authContext = new AuthContext(true);

ContextContainer::getInstance()->setContext($connection, $sessionContext);
ContextContainer::getInstance()->setContext($connection, $authContext);

// 检索特定的上下文类型
$session = ContextContainer::getInstance()->getContext($connection, SessionContext::class);
$auth = ContextContainer::getInstance()->getContext($connection, AuthContext::class);
```

### 上下文清理

```php
// 清除特定的上下文类型
ContextContainer::getInstance()->clearContext($connection, UserContext::class);

// 清除连接的所有上下文
ContextContainer::getInstance()->clearAllContexts($connection);
```

## API 参考

### ContextContainer

#### `getInstance(): ContextContainer`
获取上下文容器的单例实例。

#### `setContext(ConnectionInterface $connection, object $context): void`
为指定连接存储上下文对象。

#### `getContext(ConnectionInterface $connection, string $className): ?object`
为连接检索指定类型的上下文对象。

#### `clearContext(ConnectionInterface $connection, string $className): void`
移除连接的特定上下文类型。

#### `clearAllContexts(ConnectionInterface $connection): void`
移除连接的所有上下文对象。

## 内存管理

该包使用 PHP 的 WeakMap 在连接对象被垃圾回收时自动清理上下文数据。这防止了长时间运行的 Workerman 应用程序中的内存泄漏。

## 许可证

MIT License