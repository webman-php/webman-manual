# Limitador de taxa

Limitador de taxa webman com suporte a limitação por anotação.
Suporta drivers apcu, redis e memory.

## Repositório fonte

https://github.com/webman-php/limiter

## Instalação

```
composer require webman/limiter
```

## Uso

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // Padrão é limitação por IP, janela de tempo padrão 1 segundo
        return 'Máximo 10 requisições por IP por segundo';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, limitação por ID de usuário, requer session('user.id') não vazia
        return 'Máximo 100 buscas por usuário por 60 segundos';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Apenas 1 email por pessoa por minuto')]
    public function sendMail(): string
    {
        // key: Limit::SID, limitação por session_id
        return 'Email enviado com sucesso';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Cupons de hoje esgotados, tente amanhã')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Cada usuário pode resgatar apenas um cupom por dia')]
    public function coupon(): string
    {
        // key: 'coupon', chave personalizada para limitação global, máx 100 cupons por dia
        // Também limitação por ID de usuário, cada usuário um cupom por dia
        return 'Cupom enviado com sucesso';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Máximo 5 SMS por número por dia')]
    public function sendSms2(): string
    {
        // Quando key é variável: [classe, método_estático], ex. [UserController::class, 'getMobile'] usa valor retornado de UserController::getMobile() como chave
        return 'SMS enviado com sucesso';
    }

    /**
     * Chave personalizada, obter número móvel, deve ser método estático
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Taxa limitada', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Exceção padrão ao exceder: support\limiter\RateLimitException, modificável via parâmetro exception
        return 'ok';
    }

}
```

**Notas**

* Usa algoritmo de janela fixa
* Janela de tempo ttl padrão: 1 segundo
* Definir janela via ttl, ex. `ttl:60` para 60 segundos
* Dimensão de limitação padrão: IP (padrão `127.0.0.1` não limitado, ver configuração abaixo)
* Integrado: limitação IP, UID (requer `session('user.id')` não vazia), SID (por `session_id`)
* Com proxy nginx, passar header `X-Forwarded-For` para limitação IP, ver [proxy nginx](../others/nginx-proxy.md)
* Dispara `support\limiter\RateLimitException` ao exceder, classe de exceção personalizada via `exception:xx`
* Mensagem de erro padrão ao exceder: `Too Many Requests`, mensagem personalizada via `message:xx`
* Mensagem de erro padrão modificável via [tradução](translation.md), referência Linux:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

Às vezes desenvolvedores querem chamar o limitador diretamente no código, ver o seguinte exemplo:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile usado como chave aqui
        Limiter::check($mobile, 5, 24*60*60, 'Máximo 5 SMS por número por dia');
        return 'SMS enviado com sucesso';
    }
}
```

## Configuração

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Estes IPs não são limitados (efetivo apenas quando key é Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Ativar limitação de taxa
* **driver**: Um de `auto`, `apcu`, `memory`, `redis`; `auto` escolhe automaticamente entre `apcu` (prioritário) e `memory`
* **stores**: Configuração Redis, `connection` corresponde à chave em `config/redis.php`
* **ip_whitelist**: IPs na whitelist não são limitados (efetivo apenas quando key é `Limit::IP`)

## Escolha do driver

**memory**

* Introdução
  Nenhuma extensão necessária, melhor desempenho.

* Limitações
  Limitação apenas para processo atual, sem compartilhamento entre processos, limitação em cluster não suportada.

* Casos de uso
  Ambiente de desenvolvimento Windows; cenários sem limitação rigorosa; defesa contra ataques CC.

**apcu**

* Instalação da extensão
  Extensão apcu necessária, configurações php.ini:

```
apc.enabled=1
apc.enable_cli=1
```

Localização do php.ini com `php --ini`

* Introdução
  Excelente desempenho, suporta compartilhamento multi-processo.

* Limitações
  Cluster não suportado

* Casos de uso
  Qualquer ambiente de desenvolvimento; limitação servidor único em produção; cluster sem limitação rigorosa; defesa contra ataques CC.

**redis**

* Dependências
  Extensão redis e componente Redis necessários, instalação:

```
composer require -W webman/redis illuminate/events
```

* Introdução
  Desempenho inferior ao apcu, suporta limitação precisa servidor único e cluster

* Casos de uso
  Ambiente de desenvolvimento; servidor único em produção; ambiente cluster
