# 上下文context

`support\Context`類別用於存儲請求上下文數據，當請求完成時，相應的context數據會自動刪除。也就是說context數據生命週期是跟隨請求生命週期的。`support\Context`支持Fiber、Swoole、Swow協程環境。

更多參考[webman協程](./fiber.md)

# 接口
上下文提供了以下接口

## 設置上下文數據
```php
Context::set(string $name, $mixed $value);
```

## 獲取上下文數據
```php
Context::get(string $name = null);
```

## 刪除上下文數據
```php
Context::delete(string $name);
```

> **注意**
> 框架會在請求結束後自動調用Context::destroy()接口銷毀上下文數據，業務不能手動調用Context::destroy()

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
**使用協程時**，不能將**請求相關的狀態數據**存儲在全局變量或者靜態變量中，這可能會引起全局數據污染，正確的用法是使用Context來存取它們。
