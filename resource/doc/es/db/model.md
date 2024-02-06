# Inicio rápido

El modelo webman se basa en [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Cada tabla de la base de datos tiene un "modelo" correspondiente que se utiliza para interactuar con la tabla. Puedes utilizar el modelo para consultar datos en la tabla y para insertar nuevos registros en la misma.

Antes de comenzar, asegúrate de haber configurado la conexión a la base de datos en `config/database.php`.

> Nota: Para que Eloquent ORM admita observadores de modelo, debes importar adicionalmente `composer require "illuminate/events"`. [Ejemplo](#模型观察者)

## Ejemplo
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Nombre de la tabla asociada con el modelo
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Redefinir la clave primaria, por defecto es 'id'
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indica si se mantienen los sellos de tiempo automáticamente
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Nombre de la tabla
Puedes especificar una tabla personalizada para el modelo definiendo el atributo de tabla (table) en el modelo:
```php
class User extends Model
{
    /**
     * Nombre de la tabla asociada con el modelo
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Clave primaria
Eloquent asume que cada tabla tiene una columna de clave primaria llamada 'id'. Puedes definir un atributo protegido $primaryKey para cambiar esta convención:
```php
class User extends Model
{
    /**
     * Redefinir la clave primaria, por defecto es 'id'
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent asume que la clave primaria es un valor entero autoincremental, lo que significa que, por defecto, la clave primaria se convertirá automáticamente en un tipo int. Si deseas utilizar una clave primaria que no sea incremental o que no sea numérica, debes establecer el atributo público $incrementing como falso:
```php
class User extends Model
{
    /**
     * Indica si la clave primaria del modelo es incremental
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Si tu clave primaria no es un entero, debes establecer el atributo protegido $keyType en el modelo como string:
```php
class User extends Model
{
    /**
     * "Tipo" de ID autoincremental
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Sellos de tiempo
Por defecto, Eloquent espera que tu tabla tenga las columnas created_at y updated_at. Si no quieres que Eloquent gestione automáticamente estas dos columnas, establece el atributo $timestamps del modelo como false:
```php
class User extends Model
{
    /**
     * Indica si se mantienen los sellos de tiempo automáticamente
     *
     * @var bool
     */
    public $timestamps = false;
}
```

Si necesitas personalizar el formato de las marcas de tiempo, define el atributo de modelo $dateFormat. Este atributo determina cómo se almacenan las propiedades de fecha en la base de datos y el formato en el que se serializa el modelo a un array o JSON:
```php
class User extends Model
{
    /**
     * Formato de almacenamiento de la marca de tiempo
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Si necesitas personalizar los nombres de las columnas de marca de tiempo, puedes establecer los valores de las constantes CREATED_AT y UPDATED_AT en el modelo:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Conexión a la base de datos
Por defecto, los modelos Eloquent utilizarán la conexión a la base de datos predeterminada configurada en tu aplicación. Si deseas especificar una conexión diferente para el modelo, establece el atributo $connection del modelo:
```php
class User extends Model
{
    /**
     * Nombre de la conexión del modelo
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Valores predeterminados de atributos
Si deseas definir valores predeterminados para ciertos atributos del modelo, puedes hacerlo definiendo el atributo $attributes en el modelo:
```php
class User extends Model
{
    /**
     * Valores predeterminados de atributos del modelo
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Búsqueda de modelos
Una vez que has creado un modelo y su tabla asociada, puedes empezar a consultar datos desde la base de datos. Imagina que cada modelo Eloquent es un constructor de consulta potente que puedes utilizar para consultar rápidamente la tabla asociada. Por ejemplo:
```php
$usuarios = app\model\User::all();

foreach ($usuarios as $usuario) {
    echo $usuario->name;
}
```
> Nota: Dado que los modelos Eloquent también son constructores de consultas, deberías revisar todos los métodos disponibles en [constructor de consultas](queries.md) que puedes usar en las consultas Eloquent.

## Restricciones adicionales
El método all de Eloquent devolverá todos los resultados del modelo. Dado que cada modelo Eloquent actúa como un constructor de consultas, también puedes añadir condiciones de consulta y luego usar el método get para obtener los resultados de la consulta:
```php
$usuarios = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Recargar modelo
Puedes utilizar los métodos fresh y refresh para recargar el modelo. El método fresh recuperará el modelo de la base de datos nuevamente, sin afectar la instancia actual del modelo:
```php
$usuario = app\model\User::where('name', 'tom')->first();

$usuarioActualizado = $usuario->fresh();
```

El método refresh recalcará el modelo con los nuevos datos de la base de datos. Además, se recargarán las relaciones cargadas previamente:
```php
$usuario = app\model\User::where('name', 'tom')->first();

$usuario->name = 'jerry';

$usuario = $usuario->fresh();

$usuario->name; // "tom"
```

## Colecciones
Los métodos all y get de Eloquent pueden devolver varios resultados como una instancia de `Illuminate\Database\Eloquent\Collection`. La clase `Collection` proporciona muchos métodos auxiliares para manipular los resultados de Eloquent:
```php
$usuarios = $usuarios->reject(function ($usuario) {
    return $usuario->disabled;
});
```

## Uso de cursores
El método cursor te permite recorrer la base de datos utilizando cursores, realizando una sola consulta. Cuando se trabaja con una gran cantidad de datos, el método cursor puede reducir considerablemente el uso de memoria:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $usuario) {
    //
}
```

cursor devuelve una instancia de `Illuminate\Support\LazyCollection`. Las [colecciones perezosas (lazy collections)](https://laravel.com/docs/7.x/collections#lazy-collections) te permiten utilizar la mayoría de los métodos de colección de Laravel, cargando solo un modelo a la vez en la memoria:
```php
$usuarios = app\model\User::cursor()->filter(function ($usuario) {
    return $usuario->id > 500;
});

foreach ($usuarios as $usuario) {
    echo $usuario->id;
}
```

## Subconsultas Selects
Eloquent proporciona soporte avanzado para subconsultas, lo que te permite extraer información de tablas relacionadas en una sola consulta. Por ejemplo, supongamos que tenemos una tabla de destinos (destinations) y una tabla de vuelos (flights) que llegan a esos destinos. La tabla flights contiene un campo arrival_at que representa cuándo llega el vuelo a su destino.

Utilizando las funciones select y addSelect proporcionadas por la funcionalidad de subconsultas, podemos hacer una sola consulta para obtener todos los destinos y el nombre del último vuelo que llega a cada destino:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Ordenar según subconsulta
Además, el método orderBy del constructor de consultas también admite subconsultas. Podemos usar esta funcionalidad para ordenar todos los destinos según la hora de llegada del último vuelo a cada destino. De nuevo, esto se traduce en una sola consulta a la base de datos:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Búsqueda de un solo modelo / colección
Además de recuperar todos los registros de una tabla específica, puedes usar los métodos find, first o firstWhere para recuperar un solo registro. Estos métodos devuelven una instancia única del modelo en lugar de una colección de modelos:
```php
// Buscar un modelo por clave primaria...
$vuelo = app\model\Flight::find(1);

// Buscar el primer modelo que cumpla la condición de la consulta...
$vuelo = app\model\Flight::where('active', 1)->first();

// Búsqueda rápida del primer modelo que cumpla la condición de la consulta...
$vuelo = app\model\Flight::firstWhere('active', 1);
```

También puedes utilizar un array de claves primarias como argumento para el método find, el cual devolverá una colección de registros coincidentes:
```php
$vuelos = app\model\Flight::find([1, 2, 3]);
```

A veces, puede que desees realizar otras acciones si no se encuentra un resultado al buscar el primer resultado. El método firstOr devolverá el primer resultado si se encuentra, o ejecutará un callback si no se encuentra ningún resultado. El valor devuelto por el callback será el valor devuelto por el método firstOr:
```php
$modelo = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
El método firstOr también acepta un array de campos para la consulta:
```php
$modelo = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## Excepción de "No encontrado"
A veces, puede que desees lanzar una excepción si no se encuentra un modelo. Esto es útil en controladores y enrutadores. Los métodos findOrFail y firstOrFail recuperarán el primer resultado de la consulta y lanzarán una excepción `Illuminate\Database\Eloquent\ModelNotFoundException` si no se encuentra:
```php
$modelo = app\modle\Flight::findOrFail(1);
$modelo = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```
## Colección de búsqueda
También puede utilizar los métodos count, sum y max proporcionados por el constructor de consultas para operar en una colección. Estos métodos solo devolverán un valor escalar adecuado en lugar de una instancia del modelo:

```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## Inserción
Para agregar un nuevo registro a la base de datos, primero cree una nueva instancia del modelo, establezca sus atributos y luego llame al método save:

```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Agregar un nuevo registro a la tabla de usuarios
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validar la solicitud

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

Los sellos de tiempo created_at y updated_at se establecerán automáticamente (cuando la propiedad $timestamps del modelo es verdadera), por lo que no es necesario asignarlos manualmente.


## Actualización
El método save también se puede usar para actualizar un modelo que ya existe en la base de datos. Para actualizar un modelo, primero debe recuperarlo, establecer los atributos que desea actualizar y luego llamar al método save. Del mismo modo, el sello de tiempo updated_at se actualiza automáticamente, por lo que no es necesario asignarlo manualmente:

```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Actualización por lotes
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Verificar cambios de atributo
Eloquent proporciona los métodos isDirty, isClean y wasChanged para verificar el estado interno del modelo y determinar cómo han cambiado sus atributos desde la carga inicial. El método isDirty determina si se ha cambiado algún atributo desde que se cargó el modelo. Puede pasar un nombre específico de atributo para determinar si un atributo en particular está sucio. El método isClean es el opuesto a isDirty y también acepta un parámetro opcional de atributo:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
El método wasChanged determina si se han realizado cambios en algún atributo desde la última vez que se guardó el modelo en el ciclo de solicitud actual. También puede pasar el nombre del atributo para ver si un atributo específico ha cambiado:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```

## Asignación en masa
También puede usar el método create para guardar un nuevo modelo. Este método devolverá una instancia del modelo. Sin embargo, antes de usarlo, debe especificar los atributos fillable o guarded en el modelo, ya que por defecto, todos los modelos de Eloquent no son aptos para asignación en masa.

Cuando los usuarios ingresan parámetros HTTP inesperados que cambian campos que no desea cambiar en la base de datos, puede ocurrir una vulnerabilidad de asignación en masa. Por ejemplo, un usuario malintencionado podría pasar el parámetro is_admin a través de una solicitud HTTP y luego pasarlo al método create, lo que le permitiría al usuario ascender a administrador.

Por lo tanto, antes de comenzar, debe definir qué atributos del modelo son aptos para asignación en masa. Puede lograrlo con la propiedad $fillable del modelo. Por ejemplo, hagamos que el atributo name del modelo Flight sea apto para asignación en masa:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
Una vez que hayamos configurado los atributos que se pueden asignar de forma masiva, podemos usar el método create para insertar nuevos datos en la base de datos. El método create devolverá una instancia del modelo guardada:
```php
$flight = app\model\Flight::create(['name' => 'Flight 10']);
```
Si ya tiene una instancia del modelo, puede pasar una matriz al método fill para asignar valores:
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable se puede ver como una "lista blanca" para la asignación en masa. También puede usar la propiedad $guarded para lograr lo mismo. La propiedad $guarded contiene una matriz de atributos que no se pueden asignar de forma masiva. En otras palabras, $guarded funcionará más como una "lista negra". Tenga en cuenta que solo puede usar $fillable o $guarded, no ambos al mismo tiempo. En el siguiente ejemplo, todos los atributos excepto price pueden asignarse de forma masiva:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Atributos que no se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Si desea que todos los atributos puedan asignarse de forma masiva, puede definir $guarded como una matriz vacía:
```php
/**
 * Atributos que no se pueden asignar de forma masiva.
 *
 * @var array
 */
protected $guarded = [];
```

## Otros métodos de creación
firstOrCreate/firstOrNew
Aquí hay dos métodos que puede usar para asignación en masa: firstOrCreate y firstOrNew. El método firstOrCreate intentará buscar un registro en la base de datos mediante pares clave/valor dados. Si el modelo no se encuentra en la base de datos, se insertará un registro que incluirá los atributos del primer parámetro y, de forma opcional, los atributos del segundo parámetro.

El método firstOrNew intentará buscar un registro en la base de datos mediante los atributos dados, pero si no se encuentra el modelo, devolverá una nueva instancia del modelo. Tenga en cuenta que la instancia devuelta por firstOrNew no se ha guardado en la base de datos, por lo que deberá llamar al método save manualmente:
```php
// Buscar un vuelo por nombre, si no se encuentra, crear...
$flight = app\model\Flight::firstOrCreate(['name' => 'Flight 10']);

// Buscar un vuelo por nombre, o crear usando el nombre, la propiedad delayed y la propiedad arrival_time...
$flight = app\model\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Buscar un vuelo por nombre, si no se encuentra, crear una instancia...
$flight = app\model\Flight::firstOrNew(['name' => 'Flight 10']);

// Buscar un vuelo por nombre, o crear una instancia utilizando el nombre, la propiedad delayed y la propiedad arrival_time...
$flight = app\model\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```
Es posible que se encuentre en una situación en la que desee actualizar un modelo existente o crear uno nuevo si no existe. Puede lograr esto en un solo paso con el método updateOrCreate. Similar al método firstOrCreate, updateOrCreate persiste el modelo, por lo que no es necesario llamar a save():
```php
// Si hay un vuelo de Oakland a San Diego, establecer el precio en $99.
// Si no se encuentra un modelo coincidente, cree uno.
$flight = app\model\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Eliminar modelo
Puede llamar al método delete en una instancia del modelo para eliminarla:
```php
$flight = app\model\Flight::find(1);
$flight->delete();
```

## Eliminar por clave principal
```php
app\model\Flight::destroy(1);

app\model\Flight::destroy(1, 2, 3);

app\model\Flight::destroy([1, 2, 3]);

app\model\Flight::destroy(collect([1, 2, 3]));
```

## Eliminar mediante consulta
```php
$deletedRows = app\model\Flight::where('active', 0)->delete();
```

## Copiar modelo
Puede usar el método replicate para copiar una nueva instancia que no se haya guardado en la base de datos. Este método es útil cuando las instancias de modelo comparten muchos atributos en común.
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();
```

## Comparación de modelos
A veces, es posible que desee determinar si dos modelos son "iguales". El método is se puede utilizar para verificar rápidamente si dos modelos tienen el mismo id, tabla y conexión de base de datos:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Observadores de modelos
Consulte [Eventos de modelo y observador en Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel) para obtener más información sobre cómo usar eventos y observadores de modelos en Laravel.

Tenga en cuenta: para que Eloquent ORM admita observadores de modelos, debe importar adicionalmente "illuminate/events" mediante composer require "illuminate/events".

```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```
