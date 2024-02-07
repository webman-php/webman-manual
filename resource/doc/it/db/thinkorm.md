## ThinkORM

### Installazione di ThinkORM

`composer require -W webman/think-orm`

Dopo l'installazione è necessario riavviare (reload non funziona)

> **Suggerimento**
> Se l'installazione fallisce, potrebbe essere a causa dell'uso di un proxy di composer, prova a eseguire `composer config -g --unset repos.packagist` per disattivare il proxy di composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) è effettivamente un plugin per l'installazione automatica di `toptink/think-orm`. Se la tua versione di webman è inferiore a `1.2` e non riesci a utilizzare il plugin, fai riferimento all'articolo [Installazione e configurazione manuale di think-orm](https://www.workerman.net/a/1289).

### File di configurazione
Modificare il file di configurazione `config/thinkorm.php` secondo necessità.

### Utilizzo

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

### Creare un modello

Il modello ThinkOrm estende `think\Model`, simile al seguente:
```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * La tabella associata al modello.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * La chiave primaria associata alla tabella.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

Puoi anche utilizzare il seguente comando per creare un modello basato su thinkorm
```shell
php webman make:model nome_tabella
```

> **Suggerimento**
> Questo comando richiede l'installazione di `webman/console`, il comando di installazione è `composer require webman/console ^1.2.13`

> **Nota**
> Se il comando make:model rileva che il progetto principale utilizza `illuminate/database`, verrà creato un file modello basato su `illuminate/database` anziché su thinkorm. In questo caso, puoi forzare la generazione del modello di think-orm utilizzando un parametro aggiuntivo tp, il comando è simile a `php webman make:model nome_tabella tp` (se non funziona, aggiorna `webman/console`)
