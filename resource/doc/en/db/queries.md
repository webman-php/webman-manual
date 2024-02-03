# Query Builder
## Get all rows
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

## Get specific columns
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Get one row
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Get one column
```php
$titles = Db::table('roles')->pluck('title');
```
Specifying the value of the id field as the index
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Get a single value (field)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Distinct values
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Chunking results
If you need to handle thousands of database records, fetching all data at once can be time consuming and may lead to memory limit issues. In such cases, you can consider using the `chunkById` method. This method retrieves a small chunk of the result set at a time and passes it to a closure function for processing. For example, we can split all records in the `users` table into small chunks of 100 records each for processing:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
You can stop further fetching of chunked results by returning false inside the closure.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Process the records...

    return false;
});
```

> Note: Do not delete data inside the callback, as it may result in some records not being included in the result set.

## Aggregates
The query builder also provides various aggregation methods such as count, max, min, avg, sum, etc.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Checking for record existence
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Raw expressions
Prototype
```php
selectRaw($expression, $bindings = [])
```
Sometimes, you may need to use raw expressions in queries. You can use `selectRaw()` to create a raw expression:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```
Similarly, there are `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, and `groupByRaw()` methods for raw expressions.

`Db::raw($value)` is also used to create a raw expression, but it does not have a binding parameter feature. Use it carefully to avoid SQL injection issues.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Join statements
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

## Union statements
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Where statements
Prototype
```php
where($column, $operator = null, $value = null)
```
The first parameter is the column name, the second parameter is any operator supported by the database system, and the third is the value to compare the column with.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// The operator can be omitted when it is an equal sign, so this expression has the same effect as the previous one
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

You can also pass a condition array to the where function:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

The `orWhere` method and the `where` method accept the same parameters:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

You can pass a closure to the `orWhere` method as the first parameter:
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

The `whereBetween` / `orWhereBetween` method verifies if the field value is between two given values:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

The `whereNotBetween` / `orWhereNotBetween` method verifies if the field value is not between two given values:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

The `whereIn` / `whereNotIn` / `orWhereIn` / `orWhereNotIn` method verifies that the field value must be in the specified array:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

The `whereNull` / `whereNotNull` / `orWhereNull` / `orWhereNotNull` method verifies that the specified field must be NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

The `whereNotNull` method verifies that the specified field must not be NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

The `whereDate` / `whereMonth` / `whereDay` / `whereYear` / `whereTime` method is used to compare the field value with the given date:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

The `whereColumn` / `orWhereColumn` method is used to compare if the values of two fields are equal:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// You can also pass a comparison operator
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// The whereColumn method can also take an array
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

## Random ordering
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Random ordering can have a significant impact on server performance; it is not recommended.

## GroupBy / Having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// You can pass multiple parameters to the groupBy method
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## Offset / Limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## Insert
Insert single
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Insert multiple
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Auto-increment ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> Note: When using PostgreSQL, the `insertGetId` method will by default treat `id` as the name of the auto-increment field. If you want to get the ID from another "sequence", you can pass the field name as the second parameter to the `insertGetId` method.

## Update
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Update or Insert
Sometimes, you may want to update an existing record in the database or create it if it does not exist:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
The `updateOrInsert` method will first attempt to find a matching database record using the key-value pair in the first parameter. If the record exists, it will update the record with the values in the second parameter. If no record is found, a new record with the data from both arrays will be inserted.

## Increment & Decrement
Both of these methods accept at least one parameter: the column to be modified. The second parameter is optional and controls the amount by which the column will be incremented or decremented:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
You can also specify the fields to be updated during the operation:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Delete
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
If you need to empty a table, you can use the truncate method, which will delete all rows and reset the auto-increment ID to zero:
```php
Db::table('users')->truncate();
```

## Pessimistic Locking
The query builder also includes some functions that can help you achieve "pessimistic locking" in the select syntax. If you want to implement a "shared lock" in a query, you can use the `sharedLock` method. A shared lock prevents selected data from being tampered with until the transaction is committed:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Alternatively, you can use the `lockForUpdate` method. The "update" lock prevents rows from being modified or selected by other shared locks:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Debugging
You can use the `dd` or `dump` methods to output query results or SQL statements. Using the `dd` method will display debugging information and then stop the request execution. The `dump` method also displays debugging information, but it does not stop the request execution:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Note**
> Debugging requires installation of `symfony/var-dumper` using the command: `composer require symfony/var-dumper`.
