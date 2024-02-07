# Query Builder
## Get All Rows
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

## Get Specific Columns
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Get One Row
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Get One Column
```php
$titles = Db::table('roles')->pluck('title');
```
Specify the value of the id field as the index
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Get a Single Value (Field)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Distinct
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Chunk the Results
If you need to process thousands or even millions of database records, reading all the data at once can be time-consuming and may cause memory overflow. In such cases, you can consider using the `chunkById` method. This method retrieves a small chunk of the result set at a time and passes it to a closure function for processing. For example, we can chunk the entire "users" table data into small batches of 100 records, each batch being processed once:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
You can return false in the closure to stop further chunk retrieval.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Process the records...

    return false;
});
```
> Note: Do not delete data inside the closure, as it may result in some records not being included in the result set.

## Aggregates

The query builder also provides various aggregate methods such as count, max, min, avg, sum, etc.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Check for Record Existence
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Raw Expressions
Prototype
```php
selectRaw($expression, $bindings = [])
```
Sometimes, you may need to use raw expressions in your queries. You can use `selectRaw()` to create a raw expression:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

Similarly, there are also `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, and `groupByRaw()` methods for raw expressions.

`Db::raw($value)` is also used to create a raw expression, but it does not have the binding parameter feature, so be careful with SQL injection when using it.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Join Statements
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

## Union Statements
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Where Clause
Prototype
```php
where($column, $operator = null, $value = null)
```
The first argument is the column name, the second argument is any operator that the database system supports, and the third argument is the value to compare the column against.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// When the operator is '=', it can be omitted, so this statement is the same as the previous one
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

You can also pass an array of conditions to the where function:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

The orWhere method accepts the same parameters as the where method:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

You can pass a closure as the first argument to the orWhere method:
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

The whereBetween / orWhereBetween methods validate if a field's value is between two given values:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

The whereNotBetween / orWhereNotBetween methods validate if a field's value is not between two given values:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

The whereIn / whereNotIn / orWhereIn / orWhereNotIn methods validate if a field's value exists in a specified array:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

The whereNull / whereNotNull / orWhereNull / orWhereNotNull methods validate if a specified field is NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

The whereNotNull method validates if a specified field is not NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

The whereDate / whereMonth / whereDay / whereYear / whereTime methods compare a field's value with a given date:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

The whereColumn / orWhereColumn methods compare the values of two fields:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// You can also pass a comparison operator
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// The whereColumn method can also accept an array
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Parameter grouping
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

## Random Sorting
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Random sorting can have a significant impact on server performance and is not recommended

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// You can pass multiple arguments to the groupBy method
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

## Inserting
Inserting a single row
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Inserting multiple rows
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Auto-Incrementing IDs
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> Note: When using PostgreSQL, the insertGetId method will assume that 'id' is the name of the auto-incrementing field by default. If you want to get the ID from another "sequence", you can pass the field name as the second parameter to the insertGetId method.

## Updating
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Updating or Inserting
Sometimes you may want to update an existing record in the database, or create it if it doesn't exist:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
The updateOrInsert method will first try to find a matching database record using the keys and values in the first parameter. If a record is found, it will be updated with the values in the second parameter. If no record is found, a new record will be inserted with the combined data from both arrays.

## Incrementing & Decrementing
Both of these methods accept at least one parameter: the column to modify. The second parameter is optional and controls the amount by which the column should be incremented or decremented:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
You can also specify the fields to update during the operation:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Deleting
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
If you need to empty a table, you can use the truncate method, which will delete all rows and reset the auto-increment ID to zero:
```php
Db::table('users')->truncate();
```

## Pessimistic Lock
The query builder also includes some functions that can help you achieve "pessimistic locking" on select statements. To achieve a "shared lock" in your query, you can use the sharedLock method. A shared lock will prevent selected data columns from being tampered with until the transaction is committed:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Alternatively, you can use the lockForUpdate method. Using an "update" lock will prevent rows from being modified or selected by other shared locks:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```
## Debugging
You can use the `dd` or `dump` method to output query results or SQL statements. The `dd` method displays the debugging information and stops the execution of the request. The `dump` method also displays the debugging information, but it does not stop the execution of the request:

```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Note**
> Debugging requires the installation of `symfony/var-dumper` using the command `composer require symfony/var-dumper`.
