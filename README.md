# Workerman Connection Context

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?branch=master)](https://github.com/tourze/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/php-monorepo)](https://codecov.io/gh/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

A context management solution for Workerman connections. This package provides a way to store and manage connection-specific context objects using PHP's WeakMap feature for automatic memory management.

## Features

- Connection-specific context storage
- Multiple context types per connection
- Automatic cleanup using WeakMap
- Type-safe context retrieval
- Singleton pattern for global access

## Installation

```bash
composer require tourze/workerman-connection-context
```

## Quick Start

### Basic Usage

```php
use Tourze\Workerman\ConnectionContext\ContextContainer;

// Define your context class
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

// In your Workerman callback
$worker->onConnect = function ($connection) {
    $context = new UserContext('user123', ['read', 'write']);
    ContextContainer::getInstance()->setContext($connection, $context);
};

$worker->onMessage = function ($connection, $data) {
    $context = ContextContainer::getInstance()->getContext($connection, UserContext::class);
    if ($context) {
        echo "User: " . $context->getUserId() . "\n";
        echo "Permissions: " . implode(', ', $context->getPermissions()) . "\n";
    }
};
```

### Multiple Context Types

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

// Store multiple context types for the same connection
$sessionContext = new SessionContext('sess_123');
$authContext = new AuthContext(true);

ContextContainer::getInstance()->setContext($connection, $sessionContext);
ContextContainer::getInstance()->setContext($connection, $authContext);

// Retrieve specific context types
$session = ContextContainer::getInstance()->getContext($connection, SessionContext::class);
$auth = ContextContainer::getInstance()->getContext($connection, AuthContext::class);
```

### Context Cleanup

```php
// Clear specific context type
ContextContainer::getInstance()->clearContext($connection, UserContext::class);

// Clear all contexts for a connection
ContextContainer::getInstance()->clearAllContexts($connection);
```

## API Reference

### ContextContainer

#### `getInstance(): ContextContainer`
Get the singleton instance of the context container.

#### `setContext(ConnectionInterface $connection, object $context): void`
Store a context object for the specified connection.

#### `getContext(ConnectionInterface $connection, string $className): ?object`
Retrieve a context object of the specified type for the connection.

#### `clearContext(ConnectionInterface $connection, string $className): void`
Remove a specific context type for the connection.

#### `clearAllContexts(ConnectionInterface $connection): void`
Remove all context objects for the connection.

## Memory Management

This package uses PHP's WeakMap to automatically clean up context data when connection objects are garbage collected. This prevents memory leaks in long-running Workerman applications.

## License

MIT License