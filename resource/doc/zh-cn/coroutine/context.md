# 上下文context

`support\Context`类用于存储请求上下文数据，当请求完成时，相应的context数据会自动删除。也就是说context数据生命周期是跟随请求生命周期的。`support\Context`支持Fiber、Swoole、Swow协程环境。

更多参考[webman协程](./fiber.md)

# 接口
上下文提供了以下接口

## 设置上下文数据
```php
Context::set(string $name, $mixed $value);
```

## 获取上下文数据
```php
Context::get(string $name = null);
```

## 删除上下文数据
```php
Context::delete(string $name);
```

> **注意**
> 框架会在请求结束后自动调用Context::destroy()接口销毁上下文数据，业务不能手动调用Context::destroy()

# 示例
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# 注意
**使用协程时**，不能将**请求相关的状态数据**存储在全局变量或者静态变量中，这可能会引起全局数据污染，正确的用法是使用Context来存取它们。
