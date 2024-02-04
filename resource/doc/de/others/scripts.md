# Benutzerdefinierte Skripte

Manchmal müssen wir temporäre Skripte schreiben, mit denen wir wie bei webman beliebige Klassen oder Schnittstellen aufrufen können, um Aufgaben wie Datenimporte, Datenaktualisierungen und Statistiken durchzuführen. Dies ist in webman bereits sehr einfach möglich, zum Beispiel:

**Erstellen Sie `scripts/update.php`** (Erstellen Sie das Verzeichnis, falls es nicht vorhanden ist)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Natürlich können wir auch benutzerdefinierte Befehle mit `webman/console` verwenden, um solche Vorgänge auszuführen. Siehe [Befehlszeile](../plugin/console.md)
