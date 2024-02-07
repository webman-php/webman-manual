# Benutzerdefinierte Skripte

Manchmal müssen wir temporäre Skripte schreiben, die wie webman beliebige Klassen oder Schnittstellen aufrufen können, um Aufgaben wie Datenimport, Datenaktualisierung und -statistiken durchzuführen. In webman ist dies bereits sehr einfach, zum Beispiel:

**Neue `scripts/update.php` erstellen** (Erstellen Sie das Verzeichnis, wenn es nicht vorhanden ist)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Natürlich können wir auch benutzerdefinierte Befehle mit `webman/console` verwenden, um solche Operationen zu erledigen, siehe [Befehlszeile](../plugin/console.md)
