# Contexto

La clase `support\Context` se utiliza para almacenar datos de contexto de solicitud, y estos datos de contexto se eliminan automáticamente cuando se completa la solicitud. Es decir, la vida útil de los datos de contexto sigue la vida útil de la solicitud. `support\Context` es compatible con el entorno de las corrutinas Fiber, Swoole y Swow.

Para obtener más información, consulte [webman corrutinas](./fiber.md).

# Interfaz

El contexto proporciona las siguientes interfaces:

## Establecer datos de contexto
```php
Context::set(string $name, mixed $value);
```

## Obtener datos de contexto
```php
Context::get(string $name = null);
```

## Eliminar datos de contexto
```php
Context::delete(string $name);
```

> **Nota**
> El framework llamará automáticamente a la interfaz Context::destroy() para destruir los datos de contexto después de que finaliza la solicitud. No se debe llamar manualmente a Context::destroy() desde el negocio.

# Ejemplo
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

# Atención
**Cuando se usan corrutinas**, no se deben almacenar los datos de estado **relacionados con la solicitud** en variables globales o estáticas, ya que esto podría causar contaminación de datos globales. La forma correcta de hacerlo es almacenar y restaurar estos datos usando el Context.
