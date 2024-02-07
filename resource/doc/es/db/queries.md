# Constructor de consultas
## Obtener todas las filas
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function all(Request $request)
    {
        $users = Db::table('users')->get();
        return view('user/all', ['users' => $users]);
    }
}
```

## Obtener columnas especificas
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Obtener una fila
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Obtener una columna
```php
$titles = Db::table('roles')->pluck('title');
```
Especificar el valor del campo id como índice
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Obtener un único valor (campo)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Distinct
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Resultados en bloques
Si necesitas manejar miles de registros de la base de datos, cargarlos todos a la vez puede consumir mucho tiempo y llevar al límite de memoria. En este caso, considera usar el método `chunkById`. Este método obtiene un pequeño bloque del conjunto de resultados y lo envía a una función de cierre para su procesamiento. Por ejemplo, podemos dividir todos los datos de la tabla `users` en bloques de 100 registros para procesarlos de a trozos:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Puedes detener la obtención de resultados por bloques devolviendo `false` en el cierre.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Procesar los registros...

    return false;
});
```
> Ten en cuenta: No elimines datos dentro del cierre, ya que algunos registros podrían no estar incluidos en el conjunto de resultados.

## Agregación
El constructor de consultas también proporciona varios métodos de agregación, como count, max, min, avg, sum, entre otros.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Verificar si un registro existe
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Expresiones SQL nativas
Prototipo
```php
selectRaw($expression, $bindings = [])
```
En ocasiones, es posible que necesites usar expresiones SQL nativas en una consulta. Puedes utilizar `selectRaw()` para crear una expresión SQL nativa:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```
Del mismo modo, también se proporcionan los métodos de expresiones SQL nativas `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, y `groupByRaw()`.

`Db::raw($value)` también se utiliza para crear una expresión SQL nativa, pero no tiene la capacidad de vincular parámetros, así que ten cuidado con los problemas de inyección SQL al usarlo.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Declaración Join
```php
// join
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// leftJoin            
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// rightJoin
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// crossJoin    
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

## Declaración Union
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Sentencia Where
Firma
```php
where($column, $operator = null, $value = null)
```
El primer parámetro es el nombre de la columna, el segundo parámetro es un operador compatible con el sistema de base de datos, y el tercero es el valor con el que se comparará la columna.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Cuando el operador es igual, puede omitirse, por lo que esta expresión es equivalente a la anterior
$users = Db::table('users')->where('votes', 100)->get();

$users = Db::table('users')
                ->where('votes', '>=', 100)
                ->get();

$users = Db::table('users')
                ->where('votes', '<>', 100)
                ->get();

$users = Db::table('users')
                ->where('name', 'like', 'T%')
                ->get();
```

También puedes pasar una matriz de condiciones a la función where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

El método orWhere recibe los mismos parámetros que el método where:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Puedes pasar un cierre (closure) al método orWhere como primer parámetro:
```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$users = Db::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();
```

Los métodos whereBetween / orWhereBetween validan si el valor del campo está entre los dos valores dados:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Los métodos whereNotBetween / orWhereNotBetween validan si el valor del campo está fuera de los dos valores dados:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Los métodos whereIn / whereNotIn / orWhereIn / orWhereNotIn validan si el valor del campo debe estar en el arreglo especificado:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

Los métodos whereNull / whereNotNull / orWhereNull / orWhereNotNull validan si el campo especificado debe ser NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

El método whereNotNull valida si el campo especificado no es NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

Los métodos whereDate / whereMonth / whereDay / whereYear / whereTime se utilizan para comparar el valor del campo con la fecha dada:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

El método whereColumn / orWhereColumn se utiliza para comparar si los valores de dos campos son iguales:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// También se puede pasar un operador de comparación
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// El método whereColumn también puede recibir una matriz
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();
```

Agrupación de parámetros
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
               $query->where('votes', '>', 100)
                     ->orWhere('title', '=', 'Admin');
           })
           ->get();
```

whereExists
```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$users = Db::table('users')
           ->whereExists(function ($query) {
               $query->select(Db::raw(1))
                     ->from('orders')
                     ->whereRaw('orders.user_id = users.id');
           })
           ->get();
```

## OrderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Orden Aleatorio
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> La orden aleatoria afecta mucho el rendimiento del servidor, no se recomienda su uso

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Puedes pasar múltiples parámetros al método groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## offset / limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## Inserción
Insertar un registro
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Insertar múltiples registros
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## ID Autoincremental
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> Nota: Cuando se utiliza PostgreSQL, el método insertGetId asumirá que 'id' es el nombre del campo de incremento automático. Si deseas obtener el ID de otra "secuencia", puedes pasar el nombre del campo como segundo parámetro al método insertGetId.

## Actualización
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Actualizar o Insertar
A veces es posible que desees actualizar un registro existente en la base de datos, o crearlo si no existe:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
El método updateOrInsert intentará primero usar la clave y el valor del primer parámetro para encontrar un registro coincidente en la base de datos. Si existe el registro, lo actualiza con los valores del segundo parámetro. Si no se encuentra el registro, se inserta un nuevo registro con la combinación de datos de ambas matrices.

## Incrementar & Decrementar
Ambos métodos aceptan al menos un parámetro: la columna a modificar. El segundo parámetro es opcional, y es utilizado para controlar la cantidad de incremento o decremento en la columna:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
También puedes especificar el campo que se actualizará durante la operación:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Eliminación
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Si necesitas vaciar una tabla, puedes usar el método truncate, que eliminará todas las filas y reiniciará el ID autoincremental a cero:
```php
Db::table('users')->truncate();
```

## Bloqueo Pesimista
El constructor de consultas también incluye funciones que te permiten implementar "bloqueos pesimistas" en la sintaxis select. Si deseas implementar un "bloqueo compartido" en la consulta, puedes usar el método sharedLock. El bloqueo compartido evita que las columnas de datos seleccionadas sean modificadas hasta que la transacción se confirme:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
O, puedes usar el método lockForUpdate. El bloqueo de "para actualización" evita que las filas sean modificadas o seleccionadas por otros bloqueos compartidos:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```
## Depuración
Puedes utilizar los métodos `dd` o `dump` para imprimir los resultados de las consultas o las sentencias SQL. El método `dd` muestra la información de depuración y detiene la ejecución de la solicitud, mientras que el método `dump` también muestra la información de depuración, pero no detiene la ejecución de la solicitud:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Nota**
> La depuración requiere la instalación de `symfony/var-dumper` mediante el comando `composer require symfony/var-dumper`.
