# 日誌
日誌類的使用方式與數據庫類似
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

如果想要重複使用主項目的日誌配置，可以直接使用
```php
use support\Log;
Log::info('日誌內容');
// 假設主項目有一個名為test的日誌配置
Log::channel('test')->info('日誌內容');
```