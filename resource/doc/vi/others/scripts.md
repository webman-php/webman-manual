# Tùy chỉnh Script

Đôi khi chúng ta cần viết một số script tạm thời, trong đó các script này có thể gọi bất kỳ lớp hoặc giao diện nào giống như webman, hoàn thành các hoạt động như nhập dữ liệu, cập nhật dữ liệu thống kê, v.v. Điều này rất dễ dàng trong webman, ví dụ:

**Tạo mới `scripts/update.php`** (Nếu thư mục không tồn tại hãy tự tạo)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Tất nhiên, chúng ta cũng có thể sử dụng `webman/console` để tạo lệnh tùy chỉnh như vậy, xem thêm [Dòng lệnh](../plugin/console.md)
