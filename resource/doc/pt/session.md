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
        return response('hello ' . $session->get('name'));
    }
}
```

Obtenha uma instância de `Workerman\Protocols\Http\Session` usando `$request->session();` e use os métodos da instância para adicionar, modificar ou excluir dados de sessão.

> Nota: Os dados da sessão são automaticamente salvos quando o objeto da sessão é destruído. Portanto, não mantenha o objeto retornado por `$request->session()` em uma matriz global ou membro da classe, pois isso pode impedir a salvamento dos dados de sessão.

## Obter todos os dados da sessão
```php
$session = $request->session();
$all = $session->all();
```
Isso retorna uma matriz. Se não houver dados de sessão, retorna uma matriz vazia.

## Obter um valor específico da sessão
```php
$session = $request->session();
$name = $session->get('name');
```
Se os dados não existirem, retorna `null`.

Também é possível passar um valor padrão como segundo argumento para o método `get`. Se o valor correspondente na matriz de sessão não for encontrado, o valor padrão será retornado. Por exemplo:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Armazenar sessão
Use o método `set` para armazenar um determinado dado.
```php
$session = $request->session();
$session->set('name', 'tom');
```
O método `set` não retorna nada; os dados da sessão são automaticamente salvos quando o objeto da sessão é destruído.

Para armazenar vários valores, use o método `put`.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Da mesma forma, o método `put` não retorna nada.

## Excluir dados de sessão
Use o método `forget` para excluir um ou vários dados da sessão.
```php
$session = $request->session();
// Excluir um item
$session->forget('name');
// Excluir vários itens
$session->forget(['name', 'age']);
```

Além disso, o sistema fornece o método `delete`, que só pode excluir um item.
```php
$session = $request->session();
// Equivale a $session->forget('name');
$session->delete('name');
```

## Obter e excluir um valor da sessão
```php
$session = $request->session();
$name = $session->pull('name');
```
Isso é equivalente ao código a seguir
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Se a sessão correspondente não existir, retornará `null`.

## Excluir todos os dados da sessão
```php
$request->session()->flush();
```
Este método não retorna nada; os dados da sessão são automaticamente removidos do armazenamento quando o objeto da sessão é destruído.

## Verificar se um determinado dado de sessão existe
```php
$session = $request->session();
$has = $session->has('name');
```
Se a sessão correspondente não existir ou o valor da sessão correspondente for `null`, isso retornará false, caso contrário, retornará true.

```
$session = $request->session();
$has = $session->exists('name');
```
Este código também é usado para verificar se os dados da sessão existem. A diferença é que quando o valor do item da sessão correspondente é `null`, ele também retornará true.

## Função auxiliar session()
> Adicionado em 09/12/2020

O webman fornece a função auxiliar `session()` para realizar as mesmas funcionalidades.
```php
// Obter uma instância de sessão
$session = session();
// Equivalente a
$session = $request->session();

// Obter um valor específico
$value = session('key', 'default');
// Equivalente a
$value = session()->get('key', 'default');
// Equivalente a
$value = $request->session()->get('key', 'default');

// Atribuir valores à sessão
session(['key1'=>'value1', 'key2' => 'value2']);
// Equivalente a
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Equivalente a
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## Arquivo de configuração
O arquivo de configuração da sessão está em `config/session.php` e tem um conteúdo semelhante ao seguinte:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class ou RedisSessionHandler::class ou RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Se o manipulador for FileSessionHandler::class, o valor é file,
    // Se o manipulador for RedisSessionHandler::class, o valor é redis
    // Se o manipulador for RedisClusterSessionHandler::class, o valor é redis_cluster, ou seja, cluster Redis
    'type'    => 'file',

    // Diferentes manipuladores usam configurações diferentes
    'config' => [
        // Configuração para o tipo file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Configuração para o tipo redis
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

    'session_name' => 'PHPSID', // Nome do cookie para armazenar o session_id
    
    // === As seguintes configurações precisam ser webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Atualizar automaticamente a sessão, padrão é false
    'lifetime' => 7*24*60*60,          // Tempo de expiração da sessão
    'cookie_lifetime' => 365*24*60*60, // Tempo de expiração do cookie que armazena o session_id
    'cookie_path' => '/',              // Caminho do cookie que armazena o session_id
    'domain' => '',                    // Domínio do cookie que armazena o session_id
    'http_only' => true,               // Ativar httpOnly, padrão é true
    'secure' => false,                 // Ativar apenas em sessões https, padrão é false
    'same_site' => '',                 // Usado para evitar ataques CSRF e rastreamento de usuário, valores disponíveis são strict/lax/none
    'gc_probability' => [1, 1000],     // Probabilidade de reciclagem da sessão
];
```

> **Nota** 
> A partir da versão 1.4.0 do webman, o namespace do SessionHandler foi alterado de
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> para
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  


## Configurações de tempo de expiração
Quando o webman-framework < 1.3.14, o tempo de expiração da sessão no webman precisa ser configurado no arquivo `php.ini`.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Supondo que o tempo de expiração seja de 1440 segundos, a configuração seria a seguinte:
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Dica**
> Você pode usar o comando `php --ini` para encontrar a localização do arquivo `php.ini`.
