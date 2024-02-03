# 查詢構造器
## 獲取所有行
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

## 獲取指定列
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## 獲取一行
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## 獲取一列
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

## 獲取單個值(字段)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## 去重
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## 分塊結果
如果你需要處理成千上萬條數據庫記錄，一次性讀取這些數據會很耗時，並且容易導致內存超限，這時你可以考慮使用 chunkById 方法。該方法一次獲取結果集的一小塊，並將其傳遞給 閉包 函數進行處理。例如，我們可以將全部 users 表數據切割成一次處理 100 條記錄的一小塊：
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
你可以通過在 闭包 中返回 false 來終止繼續獲取分塊結果。
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // 處理記錄...

    return false;
});
```

> 注意：不要在回調刪除數據，那樣可能會導致有些記錄沒有包含在結果集中

## 聚合

查詢構造器還提供了各種聚合方法，比如 count, max，min， avg，sum 等。
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## 判斷記錄是否存在
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## 原生表達式
原型
```php
selectRaw($expression, $bindings = [])
```
有時候你可能需要在查詢中使用原生表達式。你可以使用 `selectRaw()` 創建一個原生表達式：

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

同樣的，還提供了 `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` 原生表達式方法。


`Db::raw($value)`也用於創建一個原生表達式，但是它沒有綁定參數功能，使用時需要小心SQL注入問題。
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## Join 語句
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

## Union 語句
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Where 語句
原型 
```php
where($column, $operator = null, $value = null)
```
第一個參數是列名，第二個參數是任意一個數據庫系統支持的運算符，第三個是該列要比較的值
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// 當運算符為 等號 時可省略，所以此句表達式與上一個作用相同
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

你還可以傳遞條件數組到 where 函數中：
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

orWhere 方法和 where 方法接收的參數一樣：
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

你可以傳一個閉包給 orWhere 方法作為第一個參數：
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

whereBetween / orWhereBetween 方法驗證字段值是否在給定的兩個值之間：
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween 方法驗證字段值是否在給定的兩個值之外：
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn 方法驗證字段的值必須存在指定的數組裡：
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull 方法驗證指定的字段必須是 NULL：
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull 方法驗證指定的字段必須不是 NULL：
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime 方法用於比較字段值與給定的日期：
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn 方法用於比較兩個字段的值是否相等：
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// 你也可以傳入一個比較運算符
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn 方法也可以傳遞數組
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

參數分組
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

## 隨機排序
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> 隨機排序會對伺服器性能影響很大，不建議使用

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// 你可以向 groupBy 方法傳遞多個參數
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
插入單條
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
插入多條
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

> 注意：當使用 PostgreSQL 時，insertGetId 方法將默認把 id 作為自動遞增字段的名稱。如果你要從其他「序列」來獲取 ID ，則可以將字段名稱作為第二個參數傳遞給 insertGetId 方法。

## 更新
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## 更新或新增
有時您可能希望更新數據庫中的現有記錄，或者如果不存在匹配記錄則創建它：
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert 方法將首先嘗試使用第一個參數的鍵和值對來查找匹配的數據庫記錄。 如果記錄存在，則使用第二個參數中的值去更新記錄。 如果找不到記錄，將插入一個新記錄，新記錄的數據是兩個數組的集合。

## 自增 & 自減
這兩種方法都至少接收一個參數：需要修改的列。第二個參數是可選的，用於控制列遞增或遞減的量：
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
你也可以在操作過程中指定要更新的字段：
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## 刪除
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
如果你需要清空表，你可以使用 truncate 方法，它將刪除所有行，並重置自增 ID 為零：
```php
Db::table('users')->truncate();
```

## 悲觀鎖
查詢構造器也包含一些可以幫助你在 select 語法上實現「悲觀鎖定」的函數。若想在查詢中實現一個「共享鎖」， 你可以使用 sharedLock 方法。 共享鎖可防止選中的數據列被篡改，直到事務被提交為止:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
或者，你可以使用 lockForUpdate 方法。使用 「update」鎖可避免行被其它共享鎖修改或選取：
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## 調試
你可以使用 dd 或者 dump 方法輸出查詢結果或者 SQL 語句。 使用 dd 方法可以顯示調試信息，然後停止執行請求。 dump 方法同樣可以顯示調試信息，但是不會停止執行請求：
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **注意**
> 調試需要安裝`symfony/var-dumper`,命令為`composer require symfony/var-dumper`