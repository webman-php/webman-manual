# Ngữ cảnh

Lớp `support\Context` được sử dụng để lưu trữ dữ liệu ngữ cảnh của yêu cầu, khi yêu cầu hoàn thành, dữ liệu ngữ cảnh tương ứng sẽ tự động bị xóa. Điều này có nghĩa là chu kỳ sống của dữ liệu ngữ cảnh theo chu kỳ sống của yêu cầu. `support\Context` hỗ trợ môi trường coroutine của Fiber, Swoole và Swow.

Xem thêm tại [webman coroutine](./fiber.md).

# Giao diện
Ngữ cảnh cung cấp các giao diện sau

## Thiết lập dữ liệu ngữ cảnh
```php
Context::set(string $name, $mixed $value);
```

## Lấy dữ liệu ngữ cảnh
```php
Context::get(string $name = null);
```

## Xóa dữ liệu ngữ cảnh
```php
Context::delete(string $name);
```

> **Lưu ý**
> Framework sẽ tự động gọi giao diện Context::destroy() để hủy dữ liệu ngữ cảnh sau khi yêu cầu kết thúc, doanh nghiệp không thể gọi bằng tay giao diện Context::destroy()

# Ví dụ
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

# Chú ý
**Khi sử dụng coroutine**, không thể lưu trữ **dữ liệu trạng thái liên quan đến yêu cầu** trong biến toàn cục hoặc biến tĩnh, điều này có thể dẫn đến ô nhiễm dữ liệu toàn cục, cách sử dụng đúng là sử dụng Context để lưu trữ và lấy chúng.
