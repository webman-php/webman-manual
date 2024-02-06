# Processo Personalizado

Em webman, você pode personalizar o processo de escuta ou o processo.

> **Note**
> Usuários do Windows precisam iniciar o webman usando `php windows.php` para iniciar os processos personalizados.

## Serviço HTTP Personalizado
Às vezes, você pode ter uma necessidade especial que requer a alteração do código principal do serviço HTTP do webman. Nesse caso, você pode usar um processo personalizado para realizar isso.

Por exemplo, crie app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Aqui, sobrescreva os métodos da classe Webman\App
}
```

Adicione a seguinte configuração em `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Outras configurações omitidas...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Número de processos
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Configuração da classe de solicitação
            'logger' => \support\Log::channel('default'), // Instância de log
            'app_path' => app_path(), // Localização do diretório app
            'public_path' => public_path() // Localização do diretório público
        ]
    ]
];
```

> **Dica**
> Para desativar o processo HTTP padrão do webman, basta definir `listen=>''` em config/server.php

## Exemplo de escuta WebSocket personalizada

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
> Observe: todos os métodos onXXX são públicos.

Adicione a seguinte configuração em `config/process.php`
```php
return [
    // ... Outras configurações de processo omitidas ...
    
    // websocket_test é o nome do processo
    'websocket_test' => [
        // Aqui você especifica a classe do processo, que é a classe Pusher definida acima
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Exemplo de Processo Customizado não de Escuta
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
        // Verifica o banco de dados a cada 10 segundos para ver se há novos registros de usuários
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Adicione a seguinte configuração em `config/process.php`
```php
return [
    // ... Outras configurações de processo omitidas...

    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Observe: se listen for omitido, nenhum porto será escutado. Se count for omitido, o número de processos padrão será 1.

## Explicação do Arquivo de Configuração

A definição completa de um processo é a seguinte:
```php
return [
    // ... 
    
    // websocket_test é o nome do processo
    'websocket_test' => [
        // Aqui você especifica a classe do processo
        'handler' => app\Pusher::class,
        // Protocolo, IP e porta para escutar (opcional)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Número de processos (opcional, padrão 1)
        'count'   => 2,
        // Usuário do processo (opcional, padrão é o usuário atual)
        'user'    => '',
        // Grupo do usuário do processo (opcional, padrão é o grupo do usuário atual)
        'group'   => '',
        // Se o processo atual suporta recarga (opcional, padrão verdadeiro)
        'reloadable' => true,
        // Se deve-se reutilizar a porta (opcional, este item requer php>=7.0, padrão é verdadeiro)
        'reusePort'  => true,
        // Transporte (opcional, defina como ssl quando é necessário habilitar SSL, padrão é tcp)
        'transport'  => 'tcp',
        // Contexto (opcional, quando o transporte é ssl, deve-se passar o caminho do certificado)
        'context'    => [], 
        // Parâmetros do construtor da classe do processo, aqui são os parâmetros do construtor da classe process\Pusher::class (opcional)
        'constructor' => [],
    ],
];
```

## Conclusão
A personalização de processos no webman é na verdade um simples encapsulamento do workerman. Ele separa a configuração dos negócios e implementa os retornos de chamada `onXXX` do workerman por meio de métodos de classe, sendo completamente semelhante ao uso do workerman.
