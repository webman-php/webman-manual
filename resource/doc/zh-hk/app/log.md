# 日誌
日誌類的用法與數據庫用法類似
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

如果想複用主項目的日誌配置，直接使用
```php
use support\Log;
Log::info('日誌內容');
// 假設主項目有個test日誌配置
Log::channel('test')->info('日誌內容');
```
