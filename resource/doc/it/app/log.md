# Log

L'uso della classe di log è simile a quello del database.

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Se si desidera riutilizzare la configurazione di log del progetto principale, è sufficiente utilizzare:

```php
use support\Log;
Log::info('Contenuto del log');
// Supponiamo che il progetto principale abbia una configurazione di log chiamata test
Log::channel('test')->info('Contenuto del log');
```
