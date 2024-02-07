## ThinkORM

### ThinkORM installieren

`composer require -W webman/think-orm`

Nach der Installation ist ein Neustart erforderlich (reload ist nicht wirksam).

> **Hinweis**
> Wenn die Installation fehlschlägt, liegt dies möglicherweise daran, dass Sie einen Composer-Proxy verwenden. Versuchen Sie, `composer config -g --unset repos.packagist` auszuführen, um den Composer-Proxy zu deaktivieren.

> [webman/think-orm](https://www.workerman.net/plugin/14) ist tatsächlich ein Plugin zum automatischen Installieren von `toptink/think-orm`. Wenn Ihre webman-Version niedriger als `1.2` ist und das Plugin nicht verwendet werden kann, beachten Sie bitte den Artikel [Manuelle Installation und Konfiguration von Think-ORM](https://www.workerman.net/a/1289).

### Konfigurationsdatei
Ändern Sie die Konfigurationsdatei `config/thinkorm.php` entsprechend Ihren Anforderungen.

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

ThinkOrm-Modelle erben von `think\Model`, ähnlich wie folgt
```php
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

Sie können auch das folgende Befehl verwenden, um ein Modell basierend auf ThinkORM zu erstellen
```bash
php webman make:model table_name
```

> **Hinweis**
> Dieser Befehl erfordert die Installation von `webman/console`, der Installationsbefehl lautet `composer require webman/console ^1.2.13`.

> **Anmerkung**
> Wenn der Befehl `make:model` feststellt, dass das Hauptprojekt `illuminate/database` verwendet, wird eine Modelldatei auf Basis von `illuminate/database` erstellt und nicht von ThinkORM. In diesem Fall kann durch Hinzufügen eines Parameters tp erzwungen werden, ein Think-ORM-Modell zu generieren. Der Befehl lautet ähnlich wie `php webman make:model table_name tp` (Wenn dies nicht funktioniert, aktualisieren Sie bitte `webman/console`).
