# Günlük
Günlük sınıfının kullanımı veritabanı kullanımıyla benzerdir.

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Ana projenin günlük yapılandırmasını yeniden kullanmak istiyorsanız, doğrudan şunu kullanın:

```php
use support\Log;
Log::info('Günlük içeriği');
// Varsayalım ki ana projenin 'test' adında bir günlük yapılandırması var
Log::channel('test')->info('Günlük içeriği');
```
