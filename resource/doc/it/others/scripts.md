# Script personalizzato

A volte è necessario scrivere degli script temporanei, in cui è possibile chiamare classi o interfacce come in webman, per eseguire operazioni come l'importazione dei dati, l'aggiornamento dei dati e le statistiche. Questo è molto facile da fare in webman, ad esempio:

**Creare `scripts/update.php`** (creare la directory se non esiste)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Naturalmente, possiamo anche utilizzare il comando personalizzato `webman/console` per eseguire questo tipo di operazioni, vedere [linea di comando](../plugin/console.md).
