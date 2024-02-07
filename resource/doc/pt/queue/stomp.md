## Fila Stomp

Stomp é um protocolo simples de mensagens baseado em texto, que fornece um formato de conexão interoperável, permitindo que clientes STOMP interajam com qualquer agente de mensagens STOMP (Broker). O [workerman/stomp](https://github.com/walkor/stomp) implementa um cliente Stomp, principalmente usado para cenários de filas de mensagens como RabbitMQ, Apollo, ActiveMQ, etc.

## Instalação
`composer require webman/stomp`

## Configuração
O arquivo de configuração está localizado em `config/plugin/webman/stomp`.

## Envio de Mensagens
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Fila
        $queue = 'exemplos';
        // Dados (ao enviar um array, é necessário serializá-lo manualmente, por exemplo usando json_encode, serialize, etc.)
        $data = json_encode(['para' => 'tom@gmail.com', 'conteúdo' => 'olá']);
        // Executa o envio
        Client::send($queue, $data);

        return response('teste de fila redis');
    }

}
```
> Para compatibilidade com outros projetos, o componente Stomp não fornece funcionalidade automática de serialização e desserialização. Se os dados enviados forem um array, é necessário serializá-los manualmente e deserializá-los ao consumi-los.

## Consumo de Mensagens
Crie um novo arquivo `app/queue/stomp/MyMailSend.php` (o nome da classe é arbitrário, desde que siga o padrão PSR-4).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Nome da fila
    public $queue = 'exemplos';

    // Nome da conexão, correspondente à conexão em stomp.php
    public $connection = 'padrão';

    // Quando o valor for 'client', é necessário chamar $ack_resolver->ack() para informar ao servidor que a mensagem foi consumida com sucesso
    // Quando o valor for 'auto', não é necessário chamar $ack_resolver->ack()
    public $ack = 'auto';

    // Consumo
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Se os dados forem um array, é necessário deserializá-los manualmente
        var_export(json_decode($data, true)); // Saída ['para' => 'tom@gmail.com', 'conteúdo' => 'olá']
        // Informa ao servidor que a mensagem foi consumida com sucesso
        $ack_resolver->ack(); // Quando ack é 'auto', este método pode ser omitido
    }
}
```

# Habilitando o protocolo stomp para o rabbitmq
Por padrão, o rabbitmq não tem o protocolo stomp habilitado e é necessário executar o seguinte comando para habilitá-lo.
```bash
rabbitmq-plugins enable rabbitmq_stomp
```
Após a habilitação, a porta padrão para stomp será 61613.
