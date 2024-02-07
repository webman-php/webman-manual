# Manipulação de eventos

`webman/event` oferece um mecanismo de eventos sofisticado que pode executar lógica de negócios sem invadir o código, permitindo a desacoplamento entre os módulos de negócios. Um cenário típico inclui o registro bem-sucedido de um novo usuário, onde basta emitir um evento personalizado como `user.register` e todos os módulos podem receber o evento e executar a lógica de negócios correspondente.

## Instalação

`composer require webman/event`

## Inscrição de eventos
A inscrição de eventos é configurada de forma centralizada através do arquivo `config/event.php`
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ... outras funções de manipulação de eventos ...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ... outras funções de manipulação de eventos ...
    ]
];
```

**Observações:**
- `user.register`, `user.logout` e assim por diante são nomes de eventos, do tipo string, é recomendado usar letras minúsculas e separar com ponto (`.`)
- Um evento pode ter múltiplas funções de manipulação de eventos e serão chamadas na ordem em que foram configuradas.

## Funções de manipulação de eventos
As funções de manipulação de eventos podem ser qualquer método de classe, função ou função de fecho.
Por exemplo, crie uma classe de manipulação de eventos `app/event/User.php` (se o diretório não existir, por favor, crie-o)
```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## Emissão de eventos
Use `Event::emit($event_name, $data);` para emitir um evento, por exemplo
```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```

> **Dica**
> O parâmetro `$data` em `Event::emit($event_name, $data);` pode ser qualquer tipo de dado, como array, instância de classe, string, entre outros.

## Escuta de eventos com curinga
A inscrição com curinga permite lidar com vários eventos no mesmo ouvinte, por exemplo, configurando em `config/event.php`:
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
Podemos obter o nome específico do evento usando o segundo parâmetro `$event_data` na função de manipulação de eventos.
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // Nome específico do evento, como user.register, user.logout, etc.
        var_export($user);
    }
}
```

## Parar a transmissão de eventos
Ao retornar `false` na função de manipulação de eventos, a transmissão desse evento será interrompida.

## Funções de manipulação de eventos como funções de fecho
As funções de manipulação de eventos podem ser métodos de classe ou funções de fecho, por exemplo

```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

## Ver eventos e ouvintes
Use o comando `php webman event:list` para ver todos os eventos e ouvintes configurados no projeto.

## Nota
A manipulação de eventos não é assíncrona e não é adequada para lidar com operações lentas. Para operações lentas, deve-se utilizar uma fila de mensagens, como por exemplo [webman/redis-queue](https://www.workerman.net/plugin/12)
