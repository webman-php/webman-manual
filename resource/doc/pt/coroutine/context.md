# Contexto

A classe `support\Context` é usada para armazenar dados de contexto da solicitação. Quando a solicitação é concluída, os dados de contexto correspondentes serão automaticamente removidos. Ou seja, a vida útil dos dados de contexto segue a vida útil da solicitação. `support\Context` é compatível com ambientes de coroutine Fiber, Swoole e Swow.

Para mais informações consulte [webman coroutine](./fiber.md)

# Interface
O contexto fornece as seguintes interfaces

## Definir dados de contexto
```php
Context::set(string $name, mixed $value);
```

## Obter dados de contexto
```php
Context::get(string $name = null);
```

## Excluir dados de contexto
```php
Context::delete(string $name);
```

> **Observação**
> O framework chama automaticamente a interface Context::destroy() para destruir os dados de contexto após a conclusão da solicitação. As operações manuais não devem chamar Context::destroy().

# Exemplo
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# Atenção
**Ao usar coroutines**, não armazene **dados de estado relacionados à solicitação** em variáveis globais ou variáveis estáticas, pois isso pode causar poluição de dados globais. A prática correta é armazená-los e acessá-los usando o Context.
