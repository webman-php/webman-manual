# Journal

La classe journal a une utilisation similaire à celle de la base de données.
```php
use support\Log;
Journal::channel('plugin.admin.default')->info('test');
```

Si vous souhaitez réutiliser la configuration du journal du projet principal, utilisez simplement
```php
use support\Log;
Journal::info('Contenu du journal');
// Supposons que le projet principal a une configuration de journal test
Journal::channel('test')->info('Contenu du journal');
```
