## ThinkORM

### Instalação do ThinkORM

`composer require -W webman/think-orm`

Após a instalação, é necessário reiniciar (reload não funciona)

> **Dica**
> Se a instalação falhar, pode ser devido ao uso de um proxy do composer. Tente executar `composer config -g --unset repos.packagist` para desativar o proxy do composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) na verdade é um plugin de instalação automática do `toptink/think-orm`. Se a versão do seu webman for inferior a `1.2`, você não poderá utilizar o plugin. Por favor, consulte o artigo [Instalação manual e configuração do think-orm](https://www.workerman.net/a/1289).

### Arquivo de configuração

Modifique o arquivo de configuração de acordo com a situação real `config/thinkorm.php`.

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

### Criação de modelos

O modelo ThinkOrm herda de `think\Model`, como segue:
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

Você também pode usar o seguinte comando para criar um modelo baseado em thinkorm:
```sh
php webman make:model nome_da_tabela
```

> **Dica**
> Este comando requer a instalação do `webman/console`, o comando de instalação é `composer require webman/console ^1.2.13`.

> **Nota**
> Se o comando make:model detectar que o projeto principal está usando `illuminate/database`, ele criará um arquivo de modelo baseado em `illuminate/database` em vez de thinkorm. Nesse caso, você pode forçar a geração do modelo think-orm adicionando um parâmetro tp, o comando é semelhante a `php webman make:model nome_da_tabela tp` (se não funcionar, por favor, atualize o `webman/console`).
