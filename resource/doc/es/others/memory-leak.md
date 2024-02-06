# Sobre las fugas de memoria
webman es un marco de trabajo de memoria residente, por lo tanto, es importante prestar un poco de atención a las fugas de memoria. Sin embargo, los desarrolladores no necesitan preocuparse demasiado, ya que las fugas de memoria ocurren en condiciones extremas y son fáciles de evitar. El desarrollo con webman es básicamente igual que con el desarrollo de un marco de trabajo tradicional, por lo que no es necesario realizar operaciones adicionales de gestión de memoria.

> **Consejo**
> El proceso monitor integrado en webman supervisará el uso de memoria de todos los procesos. Si el proceso a punto de alcanzar el límite de memoria establecido en `memory_limit` en php.ini, reiniciará automáticamente de manera segura el proceso correspondiente para liberar memoria, sin causar impacto en el funcionamiento del negocio.

## Definición de fuga de memoria
A medida que aumentan las solicitudes, la memoria utilizada por webman también **aumenta infinitamente** (ten en cuenta que es **infinita**), llegando a varios cientos de megabytes o incluso más, esto se conoce como fuga de memoria. No se considera una fuga de memoria si la memoria aumenta pero luego deja de crecer.

Es normal que un proceso ocupe varias decenas de megabytes de memoria. Cuando un proceso maneja peticiones extremadamente grandes o mantiene una gran cantidad de conexiones, es común que el uso de memoria de un solo proceso alcance cientos de megabytes. Después de utilizar esta parte de la memoria, es posible que PHP no la devuelva por completo al sistema operativo, sino que la mantenga para su reutilización, lo que puede provocar un aumento de la memoria después de manejar una solicitud grande sin liberar la memoria. Esto es un fenómeno normal. (Llamando al método gc_mem_caches() se puede liberar parte de la memoria inactiva).

## Cómo ocurre una fuga de memoria
**Una fuga de memoria ocurre cuando se cumplen las siguientes dos condiciones:**
1. Existe un arreglo con **ciclo de vida largo** (ten en cuenta que es un arreglo con **ciclo de vida largo**, no un arreglo normal).
2. Y este arreglo con **ciclo de vida largo** se expande infinitamente (el negocio inserta datos ilimitadamente en él y nunca limpia los datos).

Si se **cumplen ambas condiciones** 1 y 2 (ten en cuenta que se deben cumplir simultáneamente), entonces ocurrirá una fuga de memoria. De lo contrario, si no se cumplen ambas condiciones o se cumple solo una de ellas, no habrá fuga de memoria.

## Arreglos con ciclo de vida largo
En webman, los arreglos con ciclo de vida largo incluyen:
1. Arreglos con la palabra clave `static`
2. Propiedades de arreglos singleton
3. Arreglos con la palabra clave `global`

> **Nota**
> En webman, se permite el uso de datos con ciclo de vida largo, pero se debe garantizar que los datos dentro de ellos sean finitos, es decir, que el número de elementos no se expanda infinitamente.

A continuación se presentan ejemplos explicativos.

#### Arreglo `static` que se expande infinitamente
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

El arreglo `$data` con la palabra clave `static` es un arreglo con ciclo de vida largo, y en este ejemplo, el arreglo `$data` se expande infinitamente con cada solicitud, lo que provoca una fuga de memoria.

#### Arreglo de propiedad singleton que se expande infinitamente
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

`Cache::instance()` devuelve un singleton de Cache, que es una instancia de ciclo de vida largo. Aunque su propiedad `$data` no utiliza la palabra clave `static`, debido a que la clase en sí tiene un ciclo de vida largo, `$data` también es un arreglo con ciclo de vida largo. Con la adición continua de datos con diferentes claves al arreglo `$data`, el uso de memoria del programa seguirá aumentando, lo que provocará una fuga de memoria.

> **Nota**
> Si las claves agregadas al método Cache::instance()->set(key, value) son de cantidad finita, no habrá fuga de memoria, porque el arreglo `$data` no se está expandiendo infinitamente.

#### Arreglo `global` que se expande infinitamente
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
El arreglo definido con la palabra clave `global` no se liberará después de que se complete una función o un método de clase, por lo que es un arreglo con ciclo de vida largo y el código anterior provocará una fuga de memoria a medida que aumentan las solicitudes. Del mismo modo, un arreglo con ciclo de vida largo también se producirá si se define un arreglo con la palabra clave `static` dentro de una función o método de clase, por ejemplo:
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
Se recomienda a los desarrolladores no prestar mucha atención a las fugas de memoria, ya que rara vez ocurren. En caso de que ocurran, podemos ubicar el código que produce la fuga a través de pruebas de presión, permitiéndonos identificar el problema. Incluso si los desarrolladores no logran encontrar el punto de fuga, el servicio monitor integrado en webman reiniciará de manera segura los procesos que sufren de fugas de memoria, liberando así la memoria.

Si deseas evitar las fugas de memoria en la medida de lo posible, puedes tener en cuenta las siguientes recomendaciones.
1. Evita el uso de arreglos con las palabras clave `global` y `static`. Si es necesario utilizarlos, asegúrate de que no se expandan infinitamente.
2. Evita el uso de singletons para clases desconocidas y, en su lugar, inicializa las instancias con la palabra clave `new`. Si es necesario utilizar singletons, revisa si tienen propiedades de arreglos que se expandan infinitamente.
