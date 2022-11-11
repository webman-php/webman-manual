# Query Constructor
## Get all lines
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

## Get specified columns
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Get a row
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Get a column
```php
$titles = Db::table('roles')->pluck('title');
```
Specify the value of the id field as an index
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Get a single value (field))
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## de-duplicate
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Chunking results
If you need to process thousands of database records, reading them all at once can be time consuming and can easily lead to memory overruns, then you might consider using the chunkById method. This method takes a small chunk of the result set at a time and passes it to a closure function for processing. For example, we can cut up the entire users table data into small chunks of 100 records at a time：
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
You can stop the chunking process by returning false in the closure。
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Process the records...

    return false;
});
```

> Note: Do not delete data in the callback, that may result in some records not included in the result set

## Aggregate

The query constructor also provides various aggregate methods such as count, max, min, avg, sum, etc.。
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Determine if a record exists
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Native Expression
Prototype
```php
selectRaw($expression, $bindings = [])
```
Sometimes you may need to use native expressions in your queries. You can use `selectRaw()` to create a native expression：

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

same，database `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` Native ExpressionMethod。


`Db::raw($value)`Also used to create a native expression, but it does not have the ability to bind parameters, so you need to be careful about SQL injection issues when using it。
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

## Where Statements
Prototype 
```php
where($column, $operator = null, $value = null)
```
The first argument is the column name, the second argument is any operator supported by the database system, and the third is the value to be compared for that column
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// This expression has the same effect as the previous one since it can be omitted when the operator is equal
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

You can also pass an array of conditions to the where function：
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

orWhere The method receives the same parameters as the where method：
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

You can pass a closure to the orWhere method as the first parameter：
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

whereBetween / orWhereBetween The method verifies that the field value is between the given two values：
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween whether the method validation field value is outside the given two values：
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn The method validation field value must exist in the specified array：
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull The field specified for method validation must be NULL：
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull The method validates that the specified field must not be NULL：
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime Method to compare field values with a given date：
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn method for comparing the value of two fields to be equal：
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// You can also pass in a comparison operator
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn Methods can also pass arrays
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Parameter Grouping
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
// You can pass multiple parameters to the groupBy method
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

## Insert
Insert Single Article
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Insert multiple entries
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Self-increment ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Note：when using PostgreSQL 时，insertGetId If your interface id as the name of the auto-increment field。Suitable for message pushing「Sequence」to get ID ，then you can pass the field name as a second parameter insertGetId Method。

## Update
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Update or Add
Sometimes you may want to update an existing record in the database, or create it if no matching record exists：
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert The method will first try to find the matching database record using the key and value pair of the first parameter。 Thank you very much here，then use the value in the second parameter to goUpdateRecord。 The number can be returned in，将InsertAll requests will be，The newly recorded data is a collection of two arrays。

## Self-incrementing Self-increment & Self-decreasing self-decrementing
Both methods take at least one parameter: the column to be modified. The second parameter is optional and is used to control the amount of column incrementing or decrementing：
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
You can also specify the fields to be updated during the operation：
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## delete
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
If you need to empty the table, you can use the truncate method, which will delete all rows and reset the self-incrementing ID to zero：
```php
Db::table('users')->truncate();
```

## Pessimistic Lock
Query ConstructorAlso include something that can help you in select You can use「Pessimistic Lock定」function。If you want to implement one in a query「Shared lock」， Classes must be implemented sharedLock Method。 A shared lock prevents selected data columns from being tampered with，until the transaction is committed:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Alternatively, you can use the lockForUpdate method. Use the update lock to prevent rows from being modified or selected by other shared locks：
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## debug
Classes must be implemented dd or dump Method to output query results or SQL Statements。 Usage dd The request is customizeddebugmessage，then stop executing the request。 dump The method can also be displayeddebugmessage，but does not stop executing the request：
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Note**
> Debugging requires the installation of `symfony/var-dumper`, the command is`composer require symfony/var-dumper`




