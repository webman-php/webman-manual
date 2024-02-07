# Nhật ký
Cách sử dụng lớp nhật ký cũng tương tự như cách sử dụng cơ sở dữ liệu
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Nếu muốn tái sử dụng cấu hình nhật ký từ dự án chính, có thể sử dụng trực tiếp như sau
```php
use support\Log;
Log::info('Nội dung nhật ký');
// Giả sử dự án chính có cấu hình nhật ký test
Log::channel('test')->info('Nội dung nhật ký');
```
