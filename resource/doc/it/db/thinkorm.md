## ThinkORM

### Installazione di ThinkORM

`composer require -W webman/think-orm`

Dopo l'installazione, è necessario riavviare (reload non è valido)

> **Suggerimento**
> Se l'installazione fallisce, potrebbe essere dovuto all'uso di un proxy composer, prova a eseguire `composer config -g --unset repos.packagist` per annullare il proxy di composer

> [webman/think-orm](https://www.workerman.net/plugin/14) in realtà è un plugin che installa in modo automatico `toptink/think-orm`. Se la versione del tuo webman è inferiore a `1.2` e non è possibile utilizzare il plugin, consulta l'articolo [Installazione e configurazione manuale di think-orm](https://www.workerman.net/a/1289).


### File di configurazione
Modifica il file di configurazione `config/thinkorm.php` in base alle tue esigenze effettive

### Uso

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

### Creazione di un modello

Il modello di ThinkOrm eredita da `think\Model`, simile a quanto segue
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
```
php webman make:model nome_tabella
```

> **Suggerimento**
> Questo comando richiede l'installazione di `webman/console`, il comando di installazione è `composer require webman/console ^1.2.13`

> **Nota**
> Se il comando make:model rileva che il progetto principale sta utilizzando `illuminate/database`, verrà creato un file modello basato su `illuminate/database`, anziché su thinkorm. In questo caso, è possibile forzare la generazione di un modello think-orm aggiungendo un parametro tp, il comando sarà simile a `php webman make:model nome_tabella tp` (se non funziona, si prega di aggiornare `webman/console`)
