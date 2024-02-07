## Fila Redis

Uma fila de mensagens baseada em Redis que suporta o processamento de mensagens com atraso.

## Instalação
`composer require webman/redis-queue`

## Arquivo de Configuração
O arquivo de configuração do Redis é gerado automaticamente em `config/plugin/webman/redis-queue/redis.php`, com conteúdo semelhante ao seguinte:
```php
<?php
return [
    'default' => [
        'host' => 'redis://127.0.0.1:6379',
        'options' => [
            'auth' => '',         // Senha, parâmetro opcional
            'db' => 0,            // Banco de dados
            'max_attempts' => 5,  // Número de tentativas após falha no consumo
            'retry_seconds' => 5, // Intervalo de tentativa, em segundos
        ]
    ],
];
```

### Tentativas de Consumo Após Falha
Se houver uma falha no consumo (por exemplo, uma exceção), a mensagem será colocada em uma fila de atraso e aguardará uma nova tentativa. O número de tentativas é controlado pelo parâmetro `max_attempts`, e o intervalo de tentativas é determinado por `retry_seconds` e `max_attempts`. Por exemplo, se `max_attempts` for 5 e `retry_seconds` for 10, o intervalo para a primeira tentativa será de `1 * 10` segundos, para a segunda tentativa será de `2 * 10` segundos, e assim por diante até a quinta tentativa. Se o número de tentativas exceder o valor definido em `max_attempts`, a mensagem será colocada na fila de falhas com a chave `{redis-queue}-failed`.

## Envio de Mensagem (Síncrono)
> **Observação**
> É necessário ter a versão `webman/redis` >= 1.2.0, que depende da extensão redis.

```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Redis;

class Index
{
    public function queue(Request $request)
    {
        // Nome da fila
        $queue = 'send-mail';
        // Dados, podem ser enviados diretamente como um array, sem a necessidade de serialização
        $data = ['para' => 'tom@gmail.com', 'conteúdo' => 'olá'];
        // Envio da mensagem
        Redis::send($queue, $data);
        // Envio de mensagem atrasada, que será processada em 60 segundos
        Redis::send($queue, $data, 60);

        return response('Teste de fila Redis');
    }

}
```
Se o envio for bem-sucedido, `Redis::send()` irá retornar true, caso contrário, retornará false ou lançará uma exceção.

> **Dica**
> Pode haver uma discrepância no tempo de consumo da fila atrasada, por exemplo, se a velocidade de consumo for menor que a velocidade de produção, a fila pode ficar congestionada, resultando em atrasos no consumo. Uma maneira de aliviar isso é abrir mais processos de consumo.

## Envio de Mensagem (Assíncrono)
```php
<?php
namespace app\controller;

use support\Request;
use Webman\RedisQueue\Client;

class Index
{
    public function queue(Request $request)
    {
        // Nome da fila
        $queue = 'send-mail';
        // Dados, podem ser enviados diretamente como um array, sem a necessidade de serialização
        $data = ['para' => 'tom@gmail.com', 'conteúdo' => 'olá'];
        // Envio da mensagem
        Client::send($queue, $data);
        // Envio de mensagem atrasada, que será processada em 60 segundos
        Client::send($queue, $data, 60);

        return response('Teste de fila Redis');
    }

}
```

`Client::send()` não retorna nenhum valor, pois se trata de um envio assíncrono, o qual não garante a entrega de 100% das mensagens no Redis.

> **Dica**
> O princípio do `Client::send()` é estabelecer uma fila de memória local e, de forma assíncrona, enviar as mensagens para o Redis (a sincronização é muito rápida, aproximadamente 10 mil mensagens por segundo). Se o processo for reiniciado e a fila de memória local ainda não tiver sido sincronizada, isso pode resultar em perda de mensagens. O `Client::send()` é mais adequado para o envio de mensagens não críticas.

> **Dica**
> O `Client::send()` é assíncrono e só pode ser usado no ambiente de execução do workerman, para scripts de linha de comando, use a interface síncrona `Redis::send()`

## Envio de Mensagem a partir de Outro Projeto
Às vezes, é necessário enviar mensagens de outra aplicação e não é possível usar o `webman\redis-queue`. Nesse caso, é possível usar a seguinte função para enviar mensagens para a fila:

```php
function redis_queue_send($redis, $queue, $data, $delay = 0) {
    $queue_waiting = '{redis-queue}-waiting';
    $queue_delay = '{redis-queue}-delayed';
    $now = time();
    $package_str = json_encode([
        'id'       => rand(),
        'time'     => $now,
        'delay'    => $delay,
        'attempts' => 0,
        'queue'    => $queue,
        'data'     => $data
    ]);
    if ($delay) {
        return $redis->zAdd($queue_delay, $now + $delay, $package_str);
    }
    return $redis->lPush($queue_waiting.$queue, $package_str);
}
```

O parâmetro `$redis` é uma instância do Redis. Por exemplo, o uso da extensão Redis seria semelhante ao exemplo a seguir:
```php
$redis = new Redis;
$redis->connect('127.0.0.1', 6379);
$queue = 'user-1';
$data = ['alguns', 'dados'];
redis_queue_send($redis, $queue, $data);
````

## Consumo
O arquivo de configuração do processo de consumo está em `config/plugin/webman/redis-queue/process.php`.
Os consumidores estão localizados no diretório `app/queue/redis/`.

Ao executar o comando `php webman redis-queue:consumer my-send-mail`, será gerado o arquivo `app/queue/redis/MyMailSend.php`.

> **Dica**
> Se o comando não existir, você também pode criar manualmente.

```php
<?php

namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class MyMailSend implements Consumer
{
    // Nome da fila a ser consumida
    public $queue = 'send-mail';

    // Nome da conexão, correspondente à conexão em `plugin/webman/redis-queue/redis.php`
    public $connection = 'default';

    // Consumo
    public function consume($data)
    {
        // Não é necessário desserializar
        var_export($data); // Saída ['para' => 'tom@gmail.com', 'conteúdo' => 'olá']
    }
}
```

> **Observação**
> Durante o processo de consumo, se nenhuma exceção ou erro for lançado, considera-se que o consumo foi bem-sucedido. Caso contrário, será considerado como falha no consumo e a mensagem será encaminhada para a fila de tentativas.

> **Dica**
> A fila de mensagens suporta múltiplos servidores e processos, e a mesma mensagem **não** será consumida mais de uma vez. As mensagens consumidas são automaticamente removidas da fila, não sendo necessário fazer isso manualmente.

> **Dica**
> Os processos de consumo podem consumir várias filas diferentes e a adição de uma nova fila não requer a modificação da configuração no arquivo `process.php`. Basta adicionar uma classe de `Consumer` correspondente no diretório `app/queue/redis` e definir a propriedade da classe `$queue` para a fila desejada.

> **Dica**
> Os usuários do Windows precisam executar `php windows.php` para iniciar o webman; caso contrário, os processos de consumo não serão iniciados.
## Definir processos de consumo diferentes para filas diferentes
Por padrão, todos os consumidores compartilham o mesmo processo de consumo. No entanto, às vezes precisamos separar o consumo de algumas filas, por exemplo, colocar negócios com consumo lento em um grupo de processos e consumir negócios rápidos em outro grupo. Para isso, podemos dividir os consumidores em dois diretórios, por exemplo `app_path() . '/queue/redis/fast'` e `app_path() . '/queue/redis/slow'` (observe que o namespace da classe do consumidor precisa ser alterado adequadamente). Então a configuração seria a seguinte:
```php
return [
    ... Outras configurações aqui ...
    
    'redis_consumer_fast'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Diretório da classe consumidora
            'consumer_dir' => app_path() . '/queue/redis/fast'
        ]
    ],
    'redis_consumer_slow'  => [
        'handler'     => Webman\RedisQueue\Process\Consumer::class,
        'count'       => 8,
        'constructor' => [
            // Diretório da classe consumidora
            'consumer_dir' => app_path() . '/queue/redis/slow'
        ]
    ]
];
```

Com a classificação de diretórios e a configuração correspondente, podemos facilmente configurar processos de consumo diferentes para diferentes consumidores.

## Multiple Redis Configuration
#### Configuração
`config/plugin/webman/redis-queue/redis.php`
```php
<?php
return [
    'default' => [
        'host' => 'redis://192.168.0.1:6379',
        'options' => [
            'auth' => null,       // Senha, tipo de string, parâmetro opcional
            'db' => 0,            // Banco de dados
            'max_attempts'  => 5, // Número de tentativas após falha de consumo
            'retry_seconds' => 5, // Intervalo de tentativa, em segundos
        ]
    ],
    'other' => [
        'host' => 'redis://192.168.0.2:6379',
        'options' => [
            'auth' => null,       // Senha, tipo de string, parâmetro opcional
            'db' => 0,             // Banco de dados
            'max_attempts'  => 5, // Número de tentativas após falha de consumo
            'retry_seconds' => 5, // Intervalo de tentativa, em segundos
        ]
    ],
];
```

Observe que foi adicionada uma configuração 'other' como chave para a configuração do Redis.

#### Envio de Mensagens para Múltiplos Redis
```php
// Enviar mensagem para a fila com a chave 'default'
Client::connection('default')->send($queue, $data);
Redis::connection('default')->send($queue, $data);
// Equivalente a
Client::send($queue, $data);
Redis::send($queue, $data);

// Enviar mensagem para a fila com a chave 'other'
Client::connection('other')->send($queue, $data);
Redis::connection('other')->send($queue, $data);
```

#### Consumo de Múltiplos Redis
Configurar o consumo na fila com a chave 'other'
```php
namespace app\queue\redis;

use Webman\RedisQueue\Consumer;

class SendMail implements Consumer
{
    // Nome da fila a ser consumida
    public $queue = 'send-mail';

    // === Definir como 'other', para consumir a fila onde a chave é 'other' ===
    public $connection = 'other';

    // Consumir
    public function consume($data)
    {
        // Sem necessidade de desserialização
        var_export($data);
    }
}
```

## Perguntas Frequentes

**Por que ocorre o erro `Workerman\Redis\Exception: Workerman Redis Wait Timeout (600 seconds)`**

Esse erro ocorre apenas na interface de envio assíncrono `Client::send()`. O envio assíncrono inicialmente armazena a mensagem na memória local e, quando o processo está ocioso, envia a mensagem para o Redis. Se a velocidade de recebimento do Redis for mais lenta do que a velocidade de produção das mensagens, ou se o processo estiver ocupado com outras atividades e não tiver tempo suficiente para sincronizar as mensagens na memória com o Redis, isso pode causar congestionamento de mensagens. Se o congestionamento de mensagens persistir por mais de 600 segundos, esse erro será acionado.

Solução: Use a interface de envio síncrono `Redis::send()` para enviar mensagens.
