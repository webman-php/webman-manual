## ThinkORM

### ThinkORM installieren

`composer require -W webman/think-orm`

Nach der Installation muss ein Neustart (reload funktioniert nicht) durchgeführt werden.

> **Hinweis**
> Wenn die Installation fehlschlägt, liegt es möglicherweise daran, dass Sie einen Composer-Proxy verwenden. Versuchen Sie, `composer config -g --unset repos.packagist` auszuführen, um den Composer-Proxy zu deaktivieren.

> [webman/think-orm](https://www.workerman.net/plugin/14) ist im Wesentlichen ein Plugin zur automatischen Installation von `toptink/think-orm`. Wenn Ihre webman-Version unter `1.2` liegt und das Plugin nicht verwendet werden kann, finden Sie im Artikel [Manuelle Installation und Konfiguration von Think-ORM](https://www.workerman.net/a/1289) weitere Informationen.

### Konfigurationsdatei
Passen Sie die Konfigurationsdatei `config/thinkorm.php` je nach Bedarf an.

### Verwendung

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### Modell erstellen

ThinkOrm-Modelle erben von `think\Model` und sehen ähnlich aus wie folgt:
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * Die mit dem Modell verknüpfte Tabelle.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Der Primärschlüssel, der mit der Tabelle verknüpft ist.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

Sie können auch mit folgendem Befehl ein Modell erstellen, das auf ThinkORM basiert
```
php webman make:model table_name
```

> **Hinweis**
> Dieser Befehl erfordert die Installation von `webman/console` mit dem Befehl `composer require webman/console ^1.2.13`

> **Hinweis**
> Wenn der Befehl `make:model` feststellt, dass das Hauptprojekt `illuminate/database` verwendet, wird anstelle von ThinkORM-Modellen Modelldateien basierend auf `illuminate/database` erstellt. In diesem Fall können Sie durch Hinzufügen eines zusätzlichen Parameters "tp" erzwingen, dass Think-ORM-Modelle erstellt werden, ähnlich wie folgt: `php webman make:model table_name tp` (Wenn dies nicht funktioniert, aktualisieren Sie `webman/console`)
