# Log
Cách sử dụng lớp Log cũng tương tự như cách sử dụng cơ sở dữ liệu
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Nếu muốn tái sử dụng cấu hình log của dự án chính, hãy sử dụng trực tiếp
```php
use support\Log;
Log::info('Nội dung log');
// Giả sử dự án chính có cấu hình log 'test'
Log::channel('test')->info('Nội dung log');
```
