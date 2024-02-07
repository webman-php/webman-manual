## ThinkORM

### Installation de ThinkORM

`composer require -W webman/think-orm`

Après l'installation, il est nécessaire de redémarrer (reload n'est pas valide).

> **Astuce**
> Si l'installation échoue, cela peut être dû à l'utilisation d'un proxy composer, essayez d'exécuter `composer config -g --unset repos.packagist` pour annuler le proxy de composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) est en fait un plugin qui installe automatiquement `toptink/think-orm`. Si votre version de webman est inférieure à `1.2` et ne peut pas utiliser le plugin, veuillez vous référer à l'article [Installation et configuration manuelles de think-orm](https://www.workerman.net/a/1289).

### Fichier de configuration
Modifiez le fichier de configuration `config/thinkorm.php` en fonction de la situation réelle.

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

### Créer un modèle

Le modèle ThinkOrm hérite de `think\Model`, similaire à ce qui suit
```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * La table associée au modèle.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * La clé primaire associée à la table.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

Vous pouvez également utiliser la commande suivante pour créer un modèle basé sur thinkorm
```shell
php webman make:model table_name
```

> **Astuce**
> Cette commande nécessite l'installation de `webman/console`, la commande d'installation est `composer require webman/console ^1.2.13`

> **Remarque**
> Si la commande make:model détecte que le projet principal utilise `illuminate/database`, elle créera des fichiers de modèle basés sur `illuminate/database`, plutôt que sur thinkorm. Dans ce cas, vous pouvez forcer la génération d'un modèle think-orm en ajoutant un paramètre tp, la commande est similaire à `php webman make:model table_name tp` (si cela ne fonctionne pas, veuillez mettre à niveau `webman/console`)
