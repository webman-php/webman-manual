## ThinkORM

### Instalação do ThinkORM

`composer require -W webman/think-orm`

Após a instalação, é necessário reiniciar (reload não é válido)

> **Nota**
> Se a instalação falhar, pode ser devido ao uso de um proxy do composer. Tente executar `composer config -g --unset repos.packagist` para desativar o proxy do composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) é na verdade um plugin de instalação automática do `toptink/think-orm`. Se a versão do seu webman for inferior a `1.2`, e você não puder usar o plugin, consulte o artigo [Instalando e configurando think-orm manualmente](https://www.workerman.net/a/1289).

### Arquivo de configuração
Modifique o arquivo de configuração de acordo com as circunstâncias reais em `config/thinkorm.php`.

### Utilização

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

### Criação de modelo

O modelo ThinkOrm herda de `think\Model`, similar ao exemplo abaixo
```
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

Você também pode usar o seguinte comando para criar um modelo baseado no thinkorm
```
php webman make:model nome_da_tabela
```

> **Nota**
> Este comando requer a instalação do `webman/console`, o comando de instalação é `composer require webman/console ^1.2.13`

> **Aviso**
> Se o comando make:model detectar que o projeto principal está usando `illuminate/database`, ele irá criar arquivos de modelo baseados em `illuminate/database`, em vez do thinkorm. Nesse caso, você pode forçar a geração do modelo think-orm adicionando o parâmetro 'tp', o comando seria semelhante a `php webman make:model nome_da_tabela tp` (se isso não funcionar, atualize o `webman/console`)
