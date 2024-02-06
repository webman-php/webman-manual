# Script personnalisé

Parfois, nous avons besoin d'écrire des scripts temporaires, dans lesquels nous pouvons appeler n'importe quelle classe ou interface comme dans webman, afin d'effectuer des opérations telles que l'importation de données, la mise à jour des données et les statistiques. Cela est déjà très facile à faire dans webman, par exemple :

**Créez un nouveau fichier `scripts/update.php`** (si le répertoire n'existe pas, veuillez le créer vous-même)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Bien sûr, nous pouvons également utiliser la commande personnalisée `webman/console` pour effectuer ce type d'opérations, voir [ligne de commande](../plugin/console.md)
