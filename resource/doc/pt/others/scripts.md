# Script personalizado

Às vezes precisamos escrever alguns scripts temporários nos quais podemos chamar qualquer classe ou interface, assim como fazemos com o webman, para concluir operações como importação de dados, atualização de dados e estatísticas. Isso é muito fácil de fazer no webman, por exemplo:

**Crie um novo `scripts/update.php`** (se o diretório não existir, crie-o)

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Claro que também podemos usar o `webman/console` para criar comandos personalizados e realizar operações semelhantes, consulte [Linha de Comando](../plugin/console.md).
