# Sobre las fugas de memoria
webman es un marco de residencia de memoria, por lo que necesitamos prestar un poco de atención a las fugas de memoria. Sin embargo, los desarrolladores no necesitan preocuparse demasiado, porque las fugas de memoria ocurren en condiciones muy extremas y son fáciles de evitar. El desarrollo con webman es casi igual que el desarrollo con marcos tradicionales, no es necesario realizar operaciones innecesarias para la gestión de la memoria.

> **Consejo**
> El proceso de monitoreo integrado de webman supervisará el uso de la memoria en todos los procesos. Si el uso de la memoria de un proceso está a punto de alcanzar el valor establecido en `memory_limit` en php.ini, se reiniciará automáticamente de forma segura el proceso correspondiente para liberar la memoria, sin afectar al negocio. 

## Definición de fuga de memoria
A medida que aumentan las solicitudes, la memoria utilizada por webman también **aumenta infinitamente** (ten en cuenta que es **aumento infinito**), llegando a varios cientos de megabytes o más, esto se llama fuga de memoria.
Si la memoria aumenta pero luego deja de crecer, no se considera una fuga de memoria.

Usualmente, que un proceso ocupe varias decenas de megabytes de memoria es una situación normal. Cuando un proceso maneja solicitudes muy grandes o mantiene una gran cantidad de conexiones, es común que el uso de memoria de un solo proceso pueda alcanzar cientos de megabytes. Después de usar esta parte de la memoria, php puede que no la devuelva completamente al sistema operativo, sino que la deja para reutilizarla. Por lo tanto, es posible que después de manejar una solicitud grande, el uso de memoria aumente sin liberarla, esto es un fenómeno normal. (Llamar al método gc_mem_caches() puede liberar parte de la memoria inactiva).


## Cómo ocurre la fuga de memoria
**La fuga de memoria ocurre cuando se cumplen las siguientes dos condiciones:**
1. Hay un array de **larga duración** (ten en cuenta que es un array de **larga duración**, no un array regular)
2. Y este array de **larga duración** se expande infinitamente (el negocio inserta datos ilimitadamente en él y nunca limpia los datos).

Si se cumplen **ambas** condiciones 1 y 2 (ten en cuenta que es **ambas**), ocurrirá una fuga de memoria. Si no se cumplen estas condiciones o si solo se cumple una de ellas, no se trata de una fuga de memoria.


## Arrays de larga duración

En webman, los arrays de larga duración incluyen:
1. Arrays con la palabra clave static
2. Propiedades de arrays singleton
3. Arrays con la palabra clave global

> **Nota**
> En webman se permite el uso de datos de larga duración, pero se debe garantizar que los datos dentro del array sean finitos, es decir, que el número de elementos no se expanda infinitamente.

A continuación se muestran ejemplos de cada uno

#### Array static con expansión infinita
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

El array `$data` definido con la palabra clave `static` es un array de larga duración, y el array `$data` en el ejemplo se expande continuamente con cada solicitud, lo que ocasiona una fuga de memoria.

#### Propiedad de array singleton con expansión infinita
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```

Código de llamada
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

`Cache::instance()` devuelve una instancia única de Cache, que es una instancia de larga duración. Aunque su propiedad `$data` no usa la palabra clave `static`, debido a que la clase en sí tiene una larga duración, `$data` también es un array de larga duración. Con cada adición de un key diferente al array `$data`, el programa utiliza cada vez más memoria, lo que resulta en una fuga de memoria.

> **Nota**
> Si las keys agregadas a través de Cache::instance()->set(key, value) son de cantidad finita, no habrá fuga de memoria, ya que el array `$data` no se expande infinitamente.

#### Array global con expansión infinita
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
El array definido con la palabra clave global no se libera después de que una función o un método está completo, por lo tanto, tiene una larga duración. El código anterior resulta en una fuga de memoria a medida que hay más solicitudes. Del mismo modo, un array definido con la palabra clave static dentro de un método o función también es un array de larga duración, y si el array se expande infinitamente, también resultará en una fuga de memoria, por ejemplo:
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```


## Recomendaciones
Se recomienda a los desarrolladores no prestar demasiada atención a las fugas de memoria, ya que rara vez ocurren. En caso de que ocurra un desafortunado incidente, podemos identificar el código que está causando la fuga de memoria a través de pruebas de estrés. Aunque si los desarrolladores no pueden encontrar el punto de fuga, el servicio de monitorización integrado de webman reiniciará oportunamente de forma segura el proceso que está experimentando la fuga de memoria para liberar la memoria.

Si sin embargo, deseas evitar las fugas de memoria tanto como sea posible, podrías seguir estas recomendaciones.
1. Trata de no utilizar arrays con las palabras clave `global` y `static`, y si las utilizas, asegúrate de que no se expandan infinitamente.
2. Evita usar singletons para clases desconocidas, inicialízalas con la palabra clave `new`. Si es necesario el singleton, verifica si tiene propiedades de arrays con expansión infinita.
