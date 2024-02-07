# Script personalizado

Às vezes, precisamos escrever alguns scripts temporários, nos quais podemos chamar classes ou interfaces arbitrariamente, semelhante ao que é feito no webman, para realizar operações como importação de dados, atualizações e estatísticas. Isso é extremamente fácil de fazer no webman, como por exemplo:

**Criar `scripts/update.php`** (crie o diretório se ele não existir)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Claro, também podemos usar o comando personalizado `webman/console` para realizar esse tipo de operação, consulte [Linha de Comando](../plugin/console.md)
