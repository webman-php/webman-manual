# Script personalizzati

A volte è necessario scrivere degli script temporanei, in cui è possibile chiamare qualsiasi classe o interfaccia come in webman, per eseguire operazioni come l'importazione di dati, l'aggiornamento dei dati e le statistiche. Questo è molto facile da fare in webman, ad esempio:

**Creare `scripts/update.php`** (se la cartella non esiste, creala)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Naturalmente, è anche possibile utilizzare il comando personalizzato `webman/console` per eseguire operazioni simili, come mostrato in [Linea di comando](../plugin/console.md)
