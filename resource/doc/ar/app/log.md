# 日志
日志类用法也与数据库用法类似
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

如果想复用主项目的日志配置，直接使用
```php
use support\Log;
Log::info('日志内容');
// 假设主项目有个test日志配置
Log::channel('test')->info('日志内容');
```
