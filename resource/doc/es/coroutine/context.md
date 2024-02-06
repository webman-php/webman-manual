La clase `support\Context` se utiliza para almacenar datos de contexto de solicitud, y cuando la solicitud se completa, los datos de contexto correspondientes se eliminan automáticamente. Es decir, el ciclo de vida de los datos de contexto sigue el ciclo de vida de la solicitud. `support\Context` es compatible con el entorno de las corrutinas Fiber, Swoole y Swow.

Más información en [webman协程](./fiber.md)

# Interfaz
El contexto proporciona las siguientes interfaces

## Establecer datos de contexto
```php
Context::set(string $name, $mixed $value);
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
> El marco llamará automáticamente a la interfaz Context::destroy() para destruir los datos de contexto después de que se complete la solicitud. El negocio no puede llamar manualmente a Context::destroy()

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
**Al utilizar corrutinas**, no se deben almacenar **datos de estado relacionados con la solicitud** en variables globales o estáticas, ya que esto podría causar contaminación de datos globales. La forma correcta de hacerlo es utilizar Context para almacenar y recuperar estos datos.
