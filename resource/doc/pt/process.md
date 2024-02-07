# Processos Personalizados

Em webman, você pode personalizar processos de escuta ou processos da mesma forma que em workerman.

> **Observação**
> Usuários do Windows precisam iniciar o webman usando `php windows.php` para iniciar processos personalizados.

## Serviço HTTP Personalizado
Às vezes, você pode ter uma necessidade especial para modificar o código do núcleo do serviço HTTP do webman. Nesse caso, você pode usar processos personalizados para alcançar isso.

Por exemplo, crie app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Aqui você substitui os métodos em Webman\App
}
```

Adicione a seguinte configuração em `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Outras configurações omitidas ...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Número de processos
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Configurar classe de requisição
            'logger' => \support\Log::channel('default'), // Instância de log
            'app_path' => app_path(), // Localização do diretório app
            'public_path' => public_path() // Localização do diretório public
        ]
    ]
];
```

> **Dica**
> Se desejar desativar o processo HTTP padrão do webman, basta configurar `listen=>''` em config/server.php

## Exemplo de Personalização da Escuta de WebSocket
Crie `app/Pusher.php`

```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> Observação: todas as propriedades onXXX são públicas

Adicione a seguinte configuração em `config/process.php`

```php
return [
    // ... Outras configurações de processo omitidas ...

    // websocket_test é o nome do processo
    'websocket_test' => [
        // Aqui você especifica a classe de processo, que é a classe Pusher definida acima
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Exemplo de Processo Personalizado Não ouvinte
Crie `app/TaskTest.php`

```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Verifica se há novos usuários registrados no banco de dados a cada 10 segundos
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Adicione a seguinte configuração em `config/process.php`

```php
return [
    // ... Outras configurações de processo omitidas ...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Observação: Se o atributo listen for omitido, nenhum porto será ouvido; se o atributo count for omitido, o número de processos será 1 por padrão.

## Explicação do Arquivo de Configuração

Uma configuração completa de um processo é definida da seguinte forma:
```php
return [
    // ... 

    // websocket_test é o nome do processo
    'websocket_test' => [
        // Aqui você especifica a classe de processo
        'handler' => app\Pusher::class,
        // Protocolo, IP e porta do servidor (opcional)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Número de processos (opcional, padrão 1)
        'count'   => 2,
        // Usuário que executará o processo (opcional, padrão: usuário atual)
        'user'    => '',
        // Grupo de usuário que executará o processo (opcional, padrão: grupo de usuário atual)
        'group'   => '',
        // Se o processo oferece suporte a recarregamento (opcional, padrão true)
        'reloadable' => true,
        // Ativar ou desativar o reusePort (opcional, requer PHP >= 7.0, padrão: true)
        'reusePort'  => true,
        // Transporte (opcional, defina como 'ssl' quando o SSL estiver ativado, padrão: tcp)
        'transport'  => 'tcp',
        // Contexto (opcional, necessário quando o transporte é ssl e um caminho para o certificado SSL é exigido)
        'context'    => [], 
        // Parâmetros do construtor da classe de processo, neste caso para a classe process\Pusher::class (opcional)
        'constructor' => [],
    ],
];
```

## Conclusão
A personalização de processos do webman é essencialmente um simples encapsulamento do workerman. Ele separa a configuração dos negócios e implementa os callbacks `onXXX` do workerman por meio de métodos de classe, com outros usos completamente idênticos ao workerman.
