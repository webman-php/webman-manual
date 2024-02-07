# クエリビルダ
## すべての行を取得
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

## 指定した列を取得
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## 1行を取得
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## 1列を取得
```php
$titles = Db::table('roles')->pluck('title');
```
指定したidフィールドの値をインデックスとして使用
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## 値(フィールド)を1つ取得
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## 重複を排除
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## チャンク化した結果
数千件のデータベースレコードを処理する必要がある場合、これらのデータを一度に取得すると時間がかかり、メモリーを超過してしまう可能性があります。その場合は、`chunkById` メソッドを使用することが考えられます。このメソッドは結果セットを小さな塊にして、それをクロージャ関数に渡して処理します。例えば、全ての `users` テーブルのデータを、一度に100件のレコードを処理する小さな塊に分割することができます：
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
クロージャ内で false を返して分割処理を中断することができます。
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // レコードを処理...

    return false;
});
```

> 注：コールバック内でデータを削除しないでください。それにより一部のレコードが結果セットに含まれなくなる可能性があります。

## 集計
クエリビルダは、count、max、min、avg、sum 等の様々な集計メソッドを提供しています。
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## レコードの存在を確認
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## 生の式
原型
```php
selectRaw($expression, $bindings = [])
```
クエリ内で生の式を使用する必要がある場合があります。`selectRaw()` を使用して生の式を作成できます：
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

同様に、`whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` のような生の式メソッドも提供されています。

`Db::raw($value)` も生の式を作成するために使用されますが、それはバインド機能を持たず、使用する際にはSQLインジェクションに注意する必要があります。
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Join 文
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

## Union 文
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Where ステートメント
メソッド 
```php
where($column, $operator = null, $value = null)
```
最初の引数は列名であり、2 番目の引数はデータベースシステムでサポートされている任意の演算子であり、3 番目は比較する列の値です。
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// 演算子が「=」の場合、省略可能です。したがって、この文は前の文と同じ効果があります
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

また、where 関数に条件配列を渡すこともできます：
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

orWhere メソッドも where メソッドと同じように引数を受け取ります：
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

最初の引数としてクロージャを orWhere メソッドに渡すこともできます：
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

whereBetween / orWhereBetween メソッドは、フィールド値が指定された 2 つの値の間にあるかどうかを検証します：
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween メソッドは、フィールド値が指定された 2 つの値の外にあるかどうかを検証します：
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn メソッドは、フィールドの値が指定された配列内に存在する必要があります：
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull メソッドは、指定されたフィールドが NULL である必要があります：
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull メソッドは、指定されたフィールドが NULL でない必要があります：
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime メソッドは、フィールド値を指定された日付と比較するために使用されます：
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn メソッドは、2 つのフィールドの値が等しいかどうかを比較します：
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// 比較演算子を指定することもできます
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn メソッドには配列を渡すこともできます
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

パラメーターのグループ化
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

## ランダムな順序
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> ランダムな並び替えはサーバーのパフォーマンスに大きな影響を与えるため、使用は推奨されません

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// groupBy メソッドに複数の引数を渡すことができます
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

## 挿入
1 件の挿入
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
複数の挿入
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## 自動増分 ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> 注意：PostgreSQL を使用する場合、insertGetId メソッドはデフォルトで id を自動増分フィールドの名前とみなします。他の "シーケンス" から ID を取得する場合は、insertGetId メソッドの第二引数にフィールド名を渡すことができます。

## 更新
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## 更新または挿入
既存のレコードを更新したり、一致するレコードが存在しない場合は新規に作成したりする場合があります：
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert メソッドは、最初の引数のキーと値を使用してデータベースレコードを探します。 レコードが存在する場合は、2 番目の引数の値を使用してレコードを更新します。 レコードが見つからない場合、2 つの配列のコレクションで新しいレコードを挿入します。

## インクリメント & デクリメント
これらのメソッドは、少なくとも 1 つのパラメーター、つまり変更する列を受け取ります。2 番目のパラメーターは省略可能で、列の増分または減分量を制御します：
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
操作中に更新するフィールドも指定できます：
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```
## 削除
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
テーブルを空にする必要がある場合は、truncate メソッドを使用して、すべての行を削除し、自動増分IDを0にリセットすることができます：
```php
Db::table('users')->truncate();
```

## 悲観的ロック
クエリビルダには、「悲観的ロック」を実装するためのいくつかの機能も含まれています。クエリで「共有ロック」を実装したい場合は、sharedLock メソッドを使用できます。共有ロックは、トランザクションがコミットされるまで選択されたデータ行が変更されるのを防ぎます：
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
または、lockForUpdate メソッドを使用することもできます。更新ロックを使用すると、他の共有ロックによる行の変更や選択を防ぐことができます：
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## デバッグ
dd や dump メソッドを使用して、クエリ結果や SQL 文を出力することができます。dd メソッドを使用すると、デバッグ情報が表示され、リクエストの処理が停止します。一方、dump メソッドはデバッグ情報を表示しますが、リクエストの処理は停止しません：
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```
> **注意**
> デバッグを行うには、`symfony/var-dumper` をインストールする必要があります。インストールコマンドは `composer require symfony/var-dumper` です。
