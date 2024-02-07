# Gestão de sessão

## Exemplo

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('olá ' . $session->get('name'));
    }
}
```

Obtenha uma instância de `Workerman\Protocols\Http\Session` usando `$request->session();` e use os métodos dessa instância para adicionar, alterar ou excluir dados da sessão.

> Observação: Quando o objeto da sessão é destruído, os dados da sessão são salvos automaticamente. Portanto, não armazene o objeto retornado por `$request->session()` em um array global ou membro da classe, pois isso pode impedir o salvamento da sessão.

## Obter todos os dados da sessão

```php
$session = $request->session();
$all = $session->all();
```

Retorna um array. Se não houver nenhum dado na sessão, um array vazio será retornado.

## Obter um valor da sessão

```php
$session = $request->session();
$name = $session->get('name');
```

Se os dados não existirem, null será retornado.

Você também pode passar um valor padrão como segundo argumento para o método `get`, que será retornado se o valor correspondente não for encontrado na sessão. Por exemplo:

```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Armazenar na sessão

Use o método `set` para armazenar um dado na sessão.

```php
$session = $request->session();
$session->set('name', 'tom');
```

O método `set` não retorna nada, e os dados da sessão são salvos automaticamente quando o objeto da sessão é destruído.

Ao armazenar vários valores, use o método `put`.

```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```

Novamente, o método `put` não retorna nada.

## Excluir dados da sessão

Use o método `forget` para excluir um ou vários dados da sessão.

```php
$session = $request->session();
// Excluir um item
$session->forget('name');
// Excluir vários itens
$session->forget(['name', 'age']);
```

Além disso, o sistema fornece o método `delete`, que difere do método `forget` apenas em relação à capacidade de excluir apenas um item.

```php
$session = $request->session();
// Equivalente a $session->forget('name');
$session->delete('name');
```

## Obter e excluir um valor da sessão

```php
$session = $request->session();
$name = $session->pull('name');
```

O efeito acima é o mesmo que:

```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```

Se a sessão correspondente não existir, null será retornado.

## Excluir todos os dados da sessão

```php
$request->session()->flush();
```

Não há retorno, e os dados da sessão são removidos automaticamente do armazenamento quando o objeto da sessão é destruído.

## Verificar se um dado da sessão existe

```php
$session = $request->session();
$has = $session->has('name');
```

Quando a sessão correspondente não existe ou o valor da sessão correspondente é null, false é retornado; caso contrário, true é retornado.

```php
$session = $request->session();
$has = $session->exists('name');
```

O código acima também é usado para verificar se um dado da sessão existe, mas difere no sentido de que, quando o valor associado à sessão é null, ele também retorna true.

## Função auxiliar session()

> Adicionado em 09/12/2020

O webman oferece a função auxiliar `session()` para realizar as mesmas funcionalidades.

```php
// Obter a instância da sessão
$session = session();
// Equivalente a
$session = $request->session();

// Obter um valor
$value = session('chave', 'padrão');
// Equivalente a
$value = session()->get('chave', 'padrão');
// Equivalente a
$value = $request->session()->get('chave', 'padrão');

// Definir valores na sessão
session(['chave1'=>'valor1', 'chave2' => 'valor2']);
// Equivalente a
session()->put(['chave1'=>'valor1', 'chave2' => 'valor2']);
// Equivalente a
$request->session()->put(['chave1'=>'value1', 'chave2' => 'valor2']);
```

## Arquivo de configuração

O arquivo de configuração da sessão está em `config/session.php` e é semelhante ao exemplo a seguir:

```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class ou RedisSessionHandler::class ou RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Quando o manipulador é FileSessionHandler::class, o valor é file
    // Quando o manipulador é RedisSessionHandler::class, o valor é redis
    // Quando o manipulador é RedisClusterSessionHandler::class, o valor é redis_cluster (ou seja, cluster Redis)
    'type'    => 'file',

    // Diferentes manipuladores de sessão usam diferentes configurações
    'config' => [
        // Configuração quando o tipo é file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configuração quando o tipo é redis
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSESSID', // Nome do cookie para armazenar o session_id
    
    // === As configurações a seguir exigem webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Atualizar automaticamente a sessão, padrão é desligado
    'lifetime' => 7*24*60*60,          // Tempo de expiração da sessão
    'cookie_lifetime' => 365*24*60*60, // Tempo de expiração do cookie para armazenar o session_id
    'cookie_path' => '/',              // Caminho do cookie para armazenar o session_id
    'domain' => '',                    // Domínio do cookie que armazena o session_id
    'http_only' => true,               // Ativar o httpOnly, padrão é ativar
    'secure' => false,                 // Ativar a sessão apenas em https, padrão é desativar
    'same_site' => '',                 // Usado para evitar ataques CSRF e rastreamento de usuários, valores possíveis: strict/lax/none
    'gc_probability' => [1, 1000],     // Probabilidade de recolha da sessão
];
```

> **Observação** 
> A partir de webman 1.4.0, o namespace do SessionHandler foi alterado de:
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> Para:
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  



## Configuração de tempo de vida

Quando o webman-framework < 1.3.14, o tempo de expiração da sessão no webman precisa ser configurado no `php.ini`.

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Por exemplo, se o tempo de expiração for definido como 1440 segundos, a configuração ficará assim:

```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Nota**
> Você pode usar o comando `php --ini` para localizar o arquivo `php.ini`.
