# Inicio rápido

El modelo de webman se basa en [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Cada tabla de la base de datos tiene un "modelo" correspondiente para interactuar con esa tabla. Puedes utilizar el modelo para consultar datos de la tabla, así como para insertar nuevos registros en la tabla.

Antes de comenzar, asegúrate de configurar la conexión de la base de datos en `config/database.php`.

> Nota: Para que Eloquent ORM admita observadores de modelos, se necesita importar adicionalmente `composer require "illuminate/events"` [Ejemplo](#Model-Observers)

## Ejemplo
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Nombre de la tabla asociada al modelo
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Redefine la clave primaria, que por defecto es id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indica si se mantiene automáticamente la marca de tiempo
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Nombre de la tabla
Puedes especificar una tabla personalizada al definir el atributo `table` en el modelo:
```php
class User extends Model
{
    /**
     * Nombre de la tabla asociada al modelo
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Clave primaria
Eloquent asume que cada tabla de datos tiene una columna de clave primaria llamada id. Puedes definir un atributo protegido $primaryKey para anular esta convención:
```php
class User extends Model
{
    /**
     * Redefine la clave primaria, que por defecto es id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent asume que la clave primaria es un valor entero incrementado, lo que significa que por defecto la clave primaria se convertirá automáticamente en un tipo int. Si deseas usar una clave primaria no incrementable o no numérica, debes establecer el atributo público $incrementing en false:
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

Si la clave primaria no es un entero, debes establecer el atributo protegido $keyType del modelo en string:
```php
class User extends Model
{
    /**
     * Tipo de "id" de incremento automático.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Marca de tiempo
Por defecto, Eloquent espera que tu tabla de datos tenga las columnas created_at y updated_at. Si no quieres que Eloquent gestione automáticamente estas dos columnas, debes establecer el atributo $timestamps del modelo en false:
```php
class User extends Model
{
    /**
     * Indica si se mantiene automáticamente la marca de tiempo
     *
     * @var bool
     */
    public $timestamps = false;
}
```

Si necesitas personalizar el formato de la marca de tiempo, establece el atributo $dateFormat en tu modelo. Este atributo determina el formato de almacenamiento de la fecha en la base de datos, así como el formato en el que se serializa el modelo a un array o a JSON:
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

Si necesitas personalizar los nombres de las columnas de la marca de tiempo, puedes establecer los valores de las constantes CREATED_AT y UPDATED_AT en el modelo:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Conexión a la base de datos
Por defecto, los modelos Eloquent utilizarán la conexión de base de datos predeterminada configurada en tu aplicación. Si quieres especificar una conexión diferente para el modelo, configura el atributo $connection:
```php
class User extends Model
{
    /**
     * Nombre de la conexión del modelo
     *
     * @var string
     */
    protected $connection = 'nombre-de-conexion';
}
```

## Valores predeterminados
Si necesitas definir valores predeterminados para algunas propiedades del modelo, puedes definir el atributo $attributes en el modelo:
```php
class User extends Model
{
    /**
     * Valores predeterminados del modelo
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Búsqueda de modelos
Una vez que hayas creado el modelo y la tabla de la base de datos asociada, puedes comenzar a consultar datos desde la base de datos. Imagina que cada modelo Eloquent es un potente constructor de consultas que te permite consultar más rápidamente la tabla de datos asociada. Por ejemplo:
```php
$usuarios = app\model\User::all();

foreach ($usuarios as $usuario) {
    echo $usuario->name;
}
```
> Nota: Dado que el modelo Eloquent también es un constructor de consultas, debes revisar todos los métodos disponibles en el [constructor de consultas](queries.md) que también puedes usar en las consultas Eloquent.

## Restricciones adicionales
El método all de Eloquent devolverá todos los resultados del modelo. Dado que cada modelo Eloquent actúa como un constructor de consultas, también puedes agregar condiciones de consulta y luego obtener los resultados usando el método get:
```php
$usuarios = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Recarga del modelo
Puedes utilizar los métodos fresh y refresh para recargar el modelo. El método fresh recuperará el modelo de la base de datos nuevamente, sin afectar la instancia del modelo existente:
```php
$usuario = app\model\User::where('name', 'tom')->first();

$usuarioActualizado = $usuario->fresh();
```

El método refresh utilizará los nuevos datos de la base de datos para actualizar el modelo existente. Además, las relaciones cargadas previamente se volverán a cargar:
```php
$usuario = app\model\User::where('name', 'tom')->first();

$usuario->name = 'jerry';

$usuario = $usuario->fresh();

$usuario->name; // "tom"
```

## Colecciones
Los métodos all y get de Eloquent pueden devolver varios resultados y retornar una instancia de `Illuminate\Database\Eloquent\Collection`. La clase `Collection` proporciona una gran cantidad de funciones auxiliares para manejar los resultados de Eloquent:
```php
$usuarios = $usuarios->reject(function ($usuario) {
    return $usuario->disabled;
});
```

## Uso de cursores
El método cursor te permite recorrer la base de datos utilizando cursores, lo que permite ejecutar la consulta solo una vez. Al manejar grandes cantidades de datos, el uso del método cursor puede reducir significativamente el uso de memoria:
```php
foreach (app\model\User::where('sex', 1)->cursor() as $usuario) {
    //
}
```

El método cursor devuelve una instancia de `Illuminate\Support\LazyCollection`. Las [colecciones perezosas](https://laravel.com/docs/7.x/collections#lazy-collections) te permiten utilizar la mayoría de los métodos de colección de Laravel, y solo cargar un único modelo en la memoria en cada iteración:
```php
$usuarios = app\model\User::cursor()->filter(function ($usuario) {
    return $usuario->id > 500;
});

foreach ($usuarios as $usuario) {
    echo $usuario->id;
}
```

## Subconsultas select
Eloquent proporciona soporte avanzado para subconsultas, lo que te permite extraer información de tablas relacionadas en una sola consulta. Por ejemplo, supongamos que tenemos una tabla de destinos destinations y una tabla de vuelos flights que llegan a los destinos. La tabla flights contiene un campo arrival_at que indica cuándo un vuelo llega a un destino.

Usando los métodos select y addSelect proporcionados por la funcionalidad de subconsulta, podemos recuperar todos los destinos destinations en una sola consulta, junto con el nombre del último vuelo que llegó a cada destino:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Ordenamiento por subconsulta
Además, la función orderBy del constructor de consultas también admite subconsultas. Podemos usar esta función para ordenar todos los destinos según la hora de llegada del último vuelo a cada destino. Del mismo modo, esto puede ejecutarse en una única consulta a la base de datos:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
## Consulta de modelo / colección
Además de recuperar todos los registros de una tabla de datos especificada, puedes usar los métodos find, first o firstWhere para recuperar un solo registro. Estos métodos devuelven una instancia de modelo única en lugar de devolver una colección de modelos:
```php
// Buscar un modelo por clave primaria...
$flight = app\model\Flight::find(1);

// Buscar el primer modelo que cumpla con la condición de la consulta...
$flight = app\model\Flight::where('activo', 1)->first();

// Búsqueda rápida del primer modelo que cumpla con la condición de la consulta...
$flight = app\model\Flight::firstWhere('activo', 1);
```

También puedes utilizar un array de claves primarias como argumento para el método find, el cual devolverá una colección de registros que coincidan:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

A veces, es posible que desees realizar otras acciones si no se encuentra el primer resultado al realizar una búsqueda. El método firstOr devolverá el primer resultado si se encuentra, de lo contrario, ejecutará el callback dado y devolverá el valor devuelto por el callback como resultado de firstOr:
```php
$model = app\model\Flight::where('patas', '>', 100)->firstOr(function () {
        // ...
});
```
El método firstOr también acepta un array de columnas para consultar:
```php
$model = app\model\Flight::where('patas', '>', 100)
            ->firstOr(['id', 'patas'], function () {
                // ...
            });
```

## Excepción "No encontrado"
A veces, es posible que desees lanzar una excepción si no se encuentra un modelo. Esto es muy útil en controladores y rutas. Los métodos findOrFail y firstOrFail recuperarán el primer resultado de la consulta y, si no se encuentra, lanzarán la excepción Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('patas', '>', 100)->firstOrFail();
```

## Recuperación de colecciones
También puedes utilizar los métodos count, sum y max proporcionados por el generador de consultas, así como otras funciones de colección para operar en colecciones. Estos métodos solo devolverán un valor escalar adecuado en lugar de una instancia de modelo:
```php
$count = app\model\Flight::where('activo', 1)->count();

$max = app\model\Flight::where('activo', 1)->max('precio');
```

## Inserción
Para insertar un nuevo registro en la base de datos, primero crea una nueva instancia del modelo, establece los atributos en la instancia y luego llama al método save:
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
Los timestamps created_at y updated_at se establecerán automáticamente (si la propiedad $timestamps del modelo es true) y no es necesario asignarlos manualmente.

## Actualización
El método save también se puede utilizar para actualizar un modelo que ya existe en la base de datos. Para actualizar un modelo, primero debes recuperarlo, establecer los atributos que deseas actualizar y luego llamar al método save. Del mismo modo, el timestamp updated_at se actualizará automáticamente, por lo que no es necesario asignarlo manualmente:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Actualización masiva
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Comprobar cambios de atributos
Eloquent proporciona los métodos isDirty, isClean y wasChanged para verificar el estado interno del modelo y determinar cómo han cambiado sus atributos desde la carga inicial. isDirty determina si se ha cambiado algún atributo desde la carga del modelo. Puedes pasar el nombre de un atributo específico para determinar si ese atributo específico ha cambiado. El método isClean es opuesto a isDirty y también acepta un parámetro de atributo opcional:
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
El método wasChanged determina si se han producido cambios en los atributos desde la última vez que se guardó el modelo en el ciclo de solicitud actual. También puedes pasar el nombre de un atributo para ver si ese atributo específico ha cambiado:
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

## Asignación masiva
También puedes usar el método create para guardar un nuevo modelo. Este método devolverá una instancia de modelo. Sin embargo, antes de usarlo, debes especificar las propiedades fillable o guarded en el modelo, ya que por defecto todos los modelos Eloquent no permiten la asignación masiva.

Si un usuario ingresa inesperadamente parámetros HTTP y esos parámetros modifican campos en la base de datos que no deseas modificar, se produce una vulnerabilidad de asignación masiva. Por ejemplo, un usuario malintencionado podría ingresar el parámetro is_admin a través de una solicitud HTTP y luego pasarlo al método create, lo que le permitiría a ese usuario promocionarse a administrador.

Por lo tanto, antes de comenzar, debes definir qué atributos del modelo pueden ser asignados de forma masiva. Puedes lograr esto utilizando la propiedad $fillable en el modelo. Por ejemplo, permitir que el atributo name del modelo Flight sea asignado de forma masiva:
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
Una vez que hayas configurado los atributos que se pueden asignar de forma masiva, puedes insertar nuevos datos en la base de datos utilizando el método create. Este método devolverá la instancia del modelo guardado:
```php
$flight = app\model\Flight::create(['name' => 'Vuelo 10']);
```
Si ya tienes una instancia de modelo, puedes pasar un array al método fill para asignar valores:
```php
$flight->fill(['name' => 'Vuelo 22']);
```

$fillable puede considerarse como una "lista blanca" de asignación masiva, y también puedes usar la propiedad $guarded para lograrlo. La propiedad $guarded contiene un array de atributos que no se permiten asignar de forma masiva. Es decir, $guarded funcionalmente sería más como una "lista negra". Nota: solo puedes usar uno de $fillable o $guarded, no ambos simultáneamente. En el siguiente ejemplo, todos los atributos excepto price pueden ser asignados de forma masiva:
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
Si deseas que todos los atributos puedan ser asignados de forma masiva, puedes definir $guarded como un array vacío:
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
Aquí hay dos métodos que podrías usar para asignación en masa: firstOrCreate y firstOrNew. El método firstOrCreate coincidirá los datos en la base de datos con pares clave/valor dados. Si no encuentra el modelo en la base de datos, insertará un registro que contiene los atributos del primer parámetro y opcionalmente los atributos del segundo parámetro.

El método firstOrNew intenta encontrar un registro en la base de datos utilizando los atributos dados, igual que el método firstOrCreate. Sin embargo, si firstOrNew no encuentra el modelo correspondiente, devolverá una nueva instancia del modelo. Es importante tener en cuenta que la instancia devuelta por firstOrNew aún no se ha guardado en la base de datos, por lo que tendrás que llamar al método save manualmente:

```php
// Buscar un vuelo por nombre, crear si no existe...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Vuelo 10']);

// Buscar un vuelo por nombre, o crear con atributos de retraso y hora de llegada
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Vuelo 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Buscar un vuelo por nombre, crear una nueva instancia si no existe
$flight = app\modle\Flight::firstOrNew(['name' => 'Vuelo 10']);

// Buscar un vuelo por nombre, o crear una nueva instancia con atributos de retraso y hora de llegada
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Vuelo 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

También podrías encontrarte en situaciones donde quieras actualizar un modelo existente o crear uno nuevo si no existe. En este caso, puedes usar el método updateOrCreate para lograrlo de manera sencilla. Al igual que firstOrCreate, updateOrCreate persiste el modelo, por lo que no es necesario llamar a save():

```php
// Si existe un vuelo de Oakland a San Diego, el precio será de 99 dólares.
// Si no hay un modelo coincidente, se creará uno.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## Eliminar modelos
Puedes llamar al método delete en una instancia de modelo para eliminarla:

```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Eliminar por clave primaria
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Eliminar por consulta
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Duplicar modelos
Puedes usar el método replicate para copiar una nueva instancia de un modelo que no se ha guardado en la base de datos. Este método es útil cuando las instancias de modelo comparten muchos atributos en común.
```php
$shipping = App\Address::create([
    'type' => 'envío',
    'line_1' => 'Calle Ejemplo 123',
    'ciudad' => 'Victorville',
    'estado' => 'CA',
    'código_postal' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'facturación'
]);

$billing->save();

```

## Comparación de modelos
A veces puede ser necesario verificar si dos modelos son "iguales". El método is se puede utilizar para verificar rápidamente si dos modelos tienen la misma clave primaria, tabla y conexión a la base de datos:

```php
if ($post->is($anotherPost)) {
    //
}
```

## Observadores de modelo
Consulta [Eventos de modelo y observadores en Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel) para obtener más información sobre cómo utilizar observadores de modelo en Laravel.

Nota: Para que Eloquent ORM admita observadores de modelo, se debe importar adicionalmente con composer require "illuminate/events"

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
