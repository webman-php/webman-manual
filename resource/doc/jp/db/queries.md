# クエリビルダー
## すべての行を取得する
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

## 特定の列を取得する
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## 1行を取得する
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## 1列を取得する
```php
$titles = Db::table('roles')->pluck('title');
```
指定したidフィールドの値をインデックスとして使用する
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## 単一の値(フィールド)を取得する
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## 重複を削除する
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## チャンクごとに結果を取得する
数千件のデータベースレコードを処理する必要がある場合、これらのデータを一度に取得すると時間がかかり、メモリの限界を超える可能性があります。このような場合は、 `chunkById` メソッドを使用することを検討することができます。このメソッドは、結果セットを小さなチャンクに切り分け、それをクロージャ関数に渡して処理します。たとえば、すべての `users` テーブルデータを、1回につき100件のレコードを処理するように切り分けることができます：
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
ブロック内で `false` を返すことで、チャンクの取得を中止できます。
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // レコードを処理...

    return false;
});
```

> 注意：コールバック内でデータを削除しないでください。そうすると、一部のレコードが結果セットに含まれなくなる可能性があります。

## 集合

クエリビルダーは、count、max、min、avg、sum など、さまざまな集計メソッドを提供しています。
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## レコードの存在を確認する
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## 原生表現

プロトタイプ
```php
selectRaw($expression, $bindings = [])
```
時には、クエリ内で原生の式を使用する必要がある場合があります。`selectRaw()` を使用して、原生の式を作成できます：
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

同様に、`whereRaw()`、`orWhereRaw()`、`havingRaw()`、`orHavingRaw()`、`orderByRaw()`、`groupByRaw()` の原生式メソッドも提供されています。


`Db::raw($value)` も原生の式を作成するために使用できますが、こちらにはバインドパラメータの機能はありません。使用する際はSQLインジェクションに注意してください。
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Join ステートメント
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

## Union ステートメント
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Where ステートメント
プロトタイプ
```php
where($column, $operator = null, $value = null)
```
最初のパラメータは列の名前、2番目のパラメータはデータベースシステムでサポートされている任意の演算子、3番目はその列の比較値です。
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// 演算子が 等しい場合は省略することが可能なため、この式は前のものと同じです
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

また、`where` 関数に条件配列を渡すこともできます：
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

`orWhere` メソッドも `where` メソッドと同じパラメータを受け取ります：
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

`orWhere` メソッドには最初のパラメータとしてクロージャを渡すことができます：
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

`whereBetween` / `orWhereBetween` メソッドは、フィールドの値が指定された2つの値の間にあるかどうかを検証します：
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

`whereNotBetween` / `orWhereNotBetween` メソッドは、フィールドの値が指定された2つの値の外にあるかどうかを検証します：
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

`whereIn` / `whereNotIn` / `orWhereIn` / `orWhereNotIn` メソッドは、フィールドの値が指定された配列内に存在するかどうかを検証します：
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

`whereNull` / `whereNotNull` / `orWhereNull` / `orWhereNotNull` メソッドは、指定されたフィールドが NULL であるかどうかを検証します：
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

`whereNotNull` メソッドは、指定されたフィールドが NULL でないかどうかを検証します：
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

`whereDate` / `whereMonth` / `whereDay` / `whereYear` / `whereTime` メソッドは、フィールドの値を指定された日付と比較します：
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

`whereColumn` / `orWhereColumn` メソッドは、2つのフィールドの値が等しいかどうかを検証します：
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// 比較演算子を渡すこともできます
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn メソッドには配列も渡すことができます
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

パラメータのグループ化
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

## ランダムな順序で並べ替える
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> ランダムな並べ替えはサーバーのパフォーマンスに大きな影響を与える可能性がありますので、使用は推奨されません。

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// groupBy メソッドに複数のパラメータを渡すことができます
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
単一の挿入
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

## 自動増分ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> 注意：PostgreSQLを使用している場合、insertGetIdメソッドはデフォルトでIDを自動増分フィールドとして扱います。他の「シーケンス」からIDを取得する場合は、insertGetIdメソッドの第二引数にフィールド名を渡すことができます。

## 更新
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## 更新または挿入
既存のレコードを更新したい場合や、一致するレコードがない場合は新規作成したい場合があります：
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsertメソッドは、まず最初の引数のキーと値を使用してデータベースレコードを検索しようとします。レコードが存在する場合は、2番目の引数の値を使用してレコードを更新します。レコードが見つからない場合は、新しいレコードを挿入し、新しいレコードのデータは2つの配列のコレクションです。

## 自動増減
これらのメソッドは少なくとも1つのパラメータ、つまり変更する列を受け取ります。第二のパラメータはオプションで、列の増分または減分量を制御します：
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
操作中に更新するフィールドを指定することもできます：
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## 削除
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
テーブルをクリアする必要がある場合は、すべての行を削除し、自動増分IDをゼロにリセットするtruncateメソッドを使用できます：
```php
Db::table('users')->truncate();
```

## 悲観的ロック
クエリビルダには「悲観的ロック」を実装するのに役立ついくつかのメソッドも含まれています。「共有ロック」をクエリに実装したい場合は、sharedLockメソッドを使用できます。共有ロックは、選択されたデータ列がコミットされるまで変更されないようにします：
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
また、lockForUpdateメソッドを使用することもできます。「update」ロックを使用すると、他の共有ロックによる変更または選択が避けられます：
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## デバッグ
クエリの結果またはSQL文を出力するには、ddまたはdumpメソッドを使用できます。ddメソッドを使用すると、デバッグ情報が表示され、リクエストの実行が停止します。dumpメソッドもデバッグ情報を表示しますが、リクエストの実行は停止しません：
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **注意**
> デバッグには`symfony/var-dumper`をインストールする必要があります。インストールコマンドは`composer require symfony/var-dumper`です。
