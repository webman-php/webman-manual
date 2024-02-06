# Contesto

La classe `support\Context` è utilizzata per memorizzare i dati di contesto della richiesta e verranno automaticamente eliminati una volta completata la richiesta. In altre parole, i dati di contesto hanno lo stesso ciclo di vita della richiesta. Il `support\Context` supporta l'ambiente a coroutine di Fiber, Swoole e Swow.

Per ulteriori informazioni, vedi [webman coroutine](./fiber.md).

# Interfacce
Il contesto fornisce le seguenti interfacce

## Impostare i dati di contesto
```php
Context::set(string $name, $mixed $value);
```

## Ottenere i dati di contesto
```php
Context::get(string $name = null);
```

## Eliminare i dati di contesto
```php
Context::delete(string $name);
```

> **Nota**
> Il framework chiamerà automaticamente l'interfaccia Context::destroy() per distruggere i dati di contesto dopo la richiesta. Il business non deve chiamare manualmente Context::destroy().

# Esempio
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

# Attenzione
**Quando si utilizzano le coroutine,** non è possibile memorizzare **i dati dello stato correlati alla richiesta** in variabili globali o variabili statiche, poiché ciò potrebbe causare la contaminazione dei dati globali. La prassi corretta è utilizzare Context per memorizzarli e recuperarli.
