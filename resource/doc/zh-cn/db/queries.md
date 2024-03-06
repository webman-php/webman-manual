# 查询构造器
## 获取所有行
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

## 获取指定列
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## 获取一行
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## 获取一列
```php
$titles = Db::table('roles')->pluck('title');
```
指定id字段的值作为索引
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## 获取单个值(字段)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## 去重
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## 分块结果
如果你需要处理成千上万条数据库记录，一次性读取这些数据会很耗时，并且容易导致内存超限，这时你可以考虑使用 chunkById 方法。该方法一次获取结果集的一小块，并将其传递给 闭包 函数进行处理。例如，我们可以将全部 users 表数据切割成一次处理 100 条记录的一小块：
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
你可以通过在 闭包 中返回 false 来终止继续获取分块结果。
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Process the records...

    return false;
});
```

> 注意：不要在回调里删除数据，那样可能会导致有些记录没有包含在结果集中

## 聚合

查询构造器还提供了各种聚合方法，比如 count, max，min， avg，sum 等。
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## 判断记录是否存在
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## 原生表达式
原型
```php
selectRaw($expression, $bindings = [])
```
有时候你可能需要在查询中使用原生表达式。你可以使用 `selectRaw()` 创建一个原生表达式：

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

同样的，还提供了 `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` 原生表达式方法。


`Db::raw($value)`也用于创建一个原生表达式，但是它没有绑定参数功能，使用时需要小心SQL注入问题。
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## Join 语句
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

## Union 语句
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Where 语句
原型 
```php
where($column, $operator = null, $value = null)
```
第一个参数是列名，第二个参数是任意一个数据库系统支持的运算符，第三个是该列要比较的值
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// 当运算符为 等号 时可省略，所以此句表达式与上一个作用相同
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

你还可以传递条件数组到 where 函数中：
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

orWhere 方法和 where 方法接收的参数一样：
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

你可以传一个闭包给 orWhere 方法作为第一个参数：
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

whereBetween / orWhereBetween 方法验证字段值是否在给定的两个值之间：
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween 方法验证字段值是否在给定的两个值之外：
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn 方法验证字段的值必须存在指定的数组里：
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull 方法验证指定的字段必须是 NULL：
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull 方法验证指定的字段必须不是 NULL：
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime 方法用于比较字段值与给定的日期：
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn 方法用于比较两个字段的值是否相等：
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// 你也可以传入一个比较运算符
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn 方法也可以传递数组
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

参数分组
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

## 随机排序
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> 随机排序会对服务器性能影响很大，不建议使用

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// 你可以向 groupBy 方法传递多个参数
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

## 插入
插入单条
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
插入多条
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## 自增 ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> 注意：当使用 PostgreSQL 时，insertGetId 方法将默认把 id 作为自动递增字段的名称。如果你要从其他「序列」来获取 ID ，则可以将字段名称作为第二个参数传递给 insertGetId 方法。

## 更新
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## 更新或新增
有时您可能希望更新数据库中的现有记录，或者如果不存在匹配记录则创建它：
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert 方法将首先尝试使用第一个参数的键和值对来查找匹配的数据库记录。 如果记录存在，则使用第二个参数中的值去更新记录。 如果找不到记录，将插入一个新记录，新记录的数据是两个数组的集合。

## 自增 & 自减
这两种方法都至少接收一个参数：需要修改的列。第二个参数是可选的，用于控制列递增或递减的量：
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
你也可以在操作过程中指定要更新的字段：
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## 删除
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
如果你需要清空表，你可以使用 truncate 方法，它将删除所有行，并重置自增 ID 为零：
```php
Db::table('users')->truncate();
```

## 事务
参见[数据库事务](../others/transaction.md)

## 悲观锁
查询构造器也包含一些可以帮助你在 select 语法上实现「悲观锁定」的函数。若想在查询中实现一个「共享锁」， 你可以使用 sharedLock 方法。 共享锁可防止选中的数据列被篡改，直到事务被提交为止:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
或者，你可以使用 lockForUpdate 方法。使用 「update」锁可避免行被其它共享锁修改或选取：
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## 调试
你可以使用 dd 或者 dump 方法输出查询结果或者 SQL 语句。 使用 dd 方法可以显示调试信息，然后停止执行请求。 dump 方法同样可以显示调试信息，但是不会停止执行请求：
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **注意**
> 调试需要安装`symfony/var-dumper`,命令为`composer require symfony/var-dumper`




