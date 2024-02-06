## ThinkORM

### Installation de ThinkORM

`composer require -W webman/think-orm`

Après l'installation, il est nécessaire de redémarrer (reload n'est pas valide)

> **Conseil**
> Si l'installation échoue, c'est peut-être parce que vous utilisez un proxy composer, essayez d'exécuter `composer config -g --unset repos.packagist` pour annuler le proxy composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) est en fait un plugin pour installer automatiquement `toptink/think-orm`. Si votre version de webman est inférieure à `1.2` et que vous ne pouvez pas utiliser le plugin, veuillez consulter l'article [Installation et configuration manuelles de think-orm](https://www.workerman.net/a/1289).

### Fichier de configuration
Modifiez le fichier de configuration selon les besoins réels `config/thinkorm.php`

### Utilisation

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

### Création de modèle

Le modèle ThinkOrm hérite de `think\Model`, similaire à ce qui suit
```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

Vous pouvez également utiliser la commande suivante pour créer un modèle basé sur thinkorm
```
php webman make:model nom_table
```

> **Conseil**
> Cette commande nécessite l'installation de `webman/console`, la commande d'installation est `composer require webman/console ^1.2.13`

> **Remarque**
> Si la commande make:model détecte que le projet principal utilise `illuminate/database`, elle créera des fichiers de modèle basés sur `illuminate/database` au lieu de think-orm. Dans ce cas, vous pouvez forcer la génération d'un modèle think-orm en ajoutant un paramètre tp, la commande est similaire à `php webman make:model nom_table tp` (si cela ne fonctionne pas, veuillez mettre à niveau `webman/console`)
