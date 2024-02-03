# ログ
ログクラスの使用方法も、データベースの使用方法と似ています。
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

メインプロジェクトのログ構成を再利用したい場合は、直接以下のように使用してください。
```php
use support\Log;
Log::info('ログの内容');
// メインプロジェクトにtestというログ構成があると仮定します
Log::channel('test')->info('ログの内容');
```
