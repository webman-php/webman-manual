# Registro
L'uso della classe di registro è simile a quello del database
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Se si desidera riutilizzare la configurazione del registro del progetto principale, è sufficiente utilizzare
```php
use support\Log;
Log::info('Contenuto del registro');
// Supponendo che il progetto principale abbia una configurazione di registro chiamata "test"
Log::channel('test')->info('Contenuto del registro');
```
