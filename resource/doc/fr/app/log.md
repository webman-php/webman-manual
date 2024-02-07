# Journal

L'utilisation de la classe de journal est similaire à celle de la base de données.

```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Si vous souhaitez réutiliser la configuration du journal du projet principal, vous pouvez simplement utiliser :

```php
use support\Log;
Log::info('Contenu du journal');
// Supposons que le projet principal ait une configuration de journal nommée test
Log::channel('test')->info('Contenu du journal');
```
