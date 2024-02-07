# Contexto

A classe `support\Context` é usada para armazenar os dados do contexto da solicitação. Quando a solicitação é concluída, os dados contextuais correspondentes são excluídos automaticamente. Isso significa que a vida útil dos dados do contexto segue a vida útil da solicitação. O `support\Context` é compatível com os ambientes de coroutine Fiber, Swoole e Swow.

Para mais informações, consulte [webman coroutine](./fiber.md).

# Interface

O contexto fornece as seguintes interfaces:

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
> O framework chama automaticamente a interface Context::destroy() para destruir os dados contextuais após a conclusão da solicitação. Os negócios não podem chamar manualmente Context::destroy().

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
**Ao usar coroutines**, não armazene **dados de estado relacionados à solicitação** em variáveis globais ou variáveis estáticas, pois isso pode causar contaminação de dados globais. A prática correta é armazenar e acessar esses dados usando o Contexto.
