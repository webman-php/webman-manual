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

## Obtener columnas específicas
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
Especificar el valor del campo de id como índice
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Obtener un solo valor (campo)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Distinct
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Resultados en bloques
Si necesitas procesar miles de registros de la base de datos, leer todos estos datos de una sola vez llevará mucho tiempo y puede causar que se exceda la memoria. En estos casos, puedes considerar usar el método `chunkById`. Este método obtiene un trozo pequeño del conjunto de resultados a la vez y lo pasa a una función de cierre para su procesamiento. Por ejemplo, podemos dividir todos los datos de la tabla de `users` en trozos de 100 registros para su procesamiento:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Puedes terminar la obtención de los resultados en bloques devolviendo false en el cierre.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Procesar los registros...

    return false;
});
```

> Nota: No elimines datos en el cierre, ya que podrías terminar con algunos registros que no están incluidos en el conjunto de resultados

## Agregar
El constructor de consultas también proporciona varios métodos de agregación, como count, max, min, avg, sum, etc.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Comprobar si existe un registro
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Expresión nativa
Prototipo
```php
selectRaw($expression, $bindings = [])
```
A veces es posible que necesites usar expresiones nativas en la consulta. Puedes usar `selectRaw()` para crear una expresión nativa:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

Del mismo modo, hay métodos de expresiones nativas como `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()`.

`Db::raw($value)` también se utiliza para crear una expresión nativa, pero no tiene la funcionalidad de enlazar parámetros, por lo que debes tener cuidado con los problemas de inyección de SQL al usarlo.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## Declaración Join
```php
// unir
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

## Declaración Where
Prototipo
```php
where($column, $operator = null, $value = null)
```
El primer parámetro es el nombre de la columna, el segundo parámetro es un operador compatible con el sistema de base de datos, y el tercero es el valor con el que se comparará la columna.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Cuando el operador es igual, se puede omitir, por lo que esta expresión realiza la misma acción que la anterior
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

También puedes pasar un arreglo de condiciones a la función where:
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

Puedes pasar un cierre al método orWhere como primer parámetro:
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

Los métodos whereBetween / orWhereBetween verifican si el valor del campo está entre los dos valores dados:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Los métodos whereNotBetween / orWhereNotBetween verifican si el valor del campo no está entre los dos valores dados:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Los métodos whereIn / whereNotIn / orWhereIn / orWhereNotIn verifican si el valor del campo debe estar presente en el array especificado:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

Los métodos whereNull / whereNotNull / orWhereNull / orWhereNotNull verifican si el campo especificado debe ser NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

El método whereNotNull verifica si el campo especificado no es NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

Los métodos whereDate / whereMonth / whereDay / whereYear / whereTime se utilizan para comparar el valor del campo con la fecha especificada:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

Los métodos whereColumn / orWhereColumn se utilizan para comparar si los valores de dos campos son iguales:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// También puedes pasar un operador de comparación
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// El método whereColumn también acepta un arreglo
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Agrupar parámetros
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

## orderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Orden aleatorio
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> El orden aleatorio tiene un gran impacto en el rendimiento del servidor, se recomienda no usarlo

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Puedes pasar varios parámetros al método groupBy
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
Inserción de un solo registro
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Inserción de varios registros
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## ID Incremental
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Nota: Cuando se utiliza PostgreSQL, el método insertGetId por defecto utilizará 'id' como el nombre del campo de incremento automático. Si deseas obtener el ID de otra "secuencia", puedes pasar el nombre del campo como segundo parámetro al método insertGetId.

## Actualización
```php
$affected = Db::table('users')
    ->where('id', 1)
    ->update(['votes' => 1]);
```

## Actualizar o Insertar
A veces es posible que desees actualizar un registro existente en la base de datos, o crearlo si no existe un registro correspondiente:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
El método updateOrInsert primero intentará buscar un registro en la base de datos utilizando la clave y el valor del primer parámetro. Si el registro existe, se actualizará con los valores del segundo parámetro. Si no se encuentra un registro, se insertará uno nuevo con los datos de ambas matrices.

## Incremento y Decremento
Ambos métodos aceptan al menos un parámetro: la columna que se va a modificar. El segundo parámetro es opcional y se utiliza para controlar la cantidad de incremento o decremento de la columna:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
También puedes especificar los campos que deseas actualizar durante la operación:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Eliminación
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Si necesitas vaciar una tabla, puedes utilizar el método truncate, que eliminará todas las filas y restablecerá el ID incremental a cero:
```php
Db::table('users')->truncate();
```

## Bloqueo Pessimista
El constructor de consultas también incluye algunas funciones que pueden ayudarte a implementar un "bloqueo pesimista" en la sintaxis select. Si deseas implementar un "bloqueo compartido" en la consulta, puedes utilizar el método sharedLock. El bloqueo compartido evita que las columnas de datos seleccionadas sean modificadas hasta que la transacción se complete:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
O bien, puedes usar el método lockForUpdate. El bloqueo "para actualización" evita que las filas sean modificadas o seleccionadas por otros bloqueos compartidos:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Depuración
Puedes utilizar los métodos dd o dump para mostrar los resultados de la consulta o la declaración SQL. El método dd muestra la información de depuración y luego detiene la ejecución de la solicitud. El método dump también muestra la información de depuración, pero no detiene la ejecución de la solicitud:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Nota**
> La depuración requiere la instalación de `symfony/var-dumper`, el comando es `composer require symfony/var-dumper`.
