# ログ
ログクラスの使用法はデータベースの使用法と似ています
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

メインプロジェクトのログ構成を再利用したい場合は、直接以下のように使用します。
```php
use support\Log;
Log::info('ログの内容');
// メインプロジェクトにtestというログ構成があると仮定する
Log::channel('test')->info('ログの内容');
```
