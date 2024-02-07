# Contesto

La classe `support\Context` è utilizzata per memorizzare i dati di contesto della richiesta, e quando la richiesta è completata, i dati di contesto corrispondenti verranno automaticamente eliminati. In altre parole, il ciclo di vita dei dati di contesto segue il ciclo di vita della richiesta. `support\Context` supporta l'ambiente di co-routine Fiber, Swoole e Swow.

Riferimenti aggiuntivi [webman coroutine](./fiber.md)

# Interfacce
Il contesto fornisce le seguenti interfacce

## Impostare i dati di contesto
```php
Context::set(string $name, mixed $value);
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
> Il framework chiamerà automaticamente l'interfaccia Context::destroy() per distruggere i dati di contesto dopo la richiesta, non è possibile chiamare manualmente Context::destroy().

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
**Quando si utilizzano le co-routine**, non è possibile memorizzare i **dati di stato correlati alla richiesta** in variabili globali o variabili statiche, questo potrebbe causare una contaminazione dei dati globali. Il modo corretto è utilizzare Context per accedervi.
