# クイックスタート

webmanモデルは[Eloquent ORM](https://laravel.com/docs/7.x/eloquent)に基づいています。各データベーステーブルには、テーブルとのやり取りに使用する「モデル」があります。モデルを使用してデータテーブルからデータをクエリしたり、新しいレコードをデータテーブルに挿入したりできます。

開始する前に、`config/database.php`を設定してデータベース接続が行われていることを確認してください。

> 注意：Eloquent ORMがモデルオブザーバをサポートするには、`composer require "illuminate/events"`を追加する必要があります。[例](#モデルオブザーバ)

## 例
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * 関連するテーブル名
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * デフォルトの主キーを再定義
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * タイムスタンプを自動的に維持するかどうかを示す
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## テーブル名
カスタムデータテーブルを指定するには、モデルでtable属性を定義できます：
```php
class User extends Model
{
    /**
     * 関連するテーブル名
     *
     * @var string
     */
    protected $table = 'user';
}
```

## 主キー
Eloquentは、データテーブルごとにidという名前の主キー列があると想定します。この想定を上書きするために、protectedの$primaryKey属性を定義できます。
```php
class User extends Model
{
    /**
     * デフォルトの主キーを再定義
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquentは主キーを自動的に増分する整数値と想定します。これは、デフォルトで主キーがint型に自動変換されることを意味します。非増分または非数値の主キーを使用したい場合は、publicの$incrementing属性をfalseに設定する必要があります。
```php
class User extends Model
{
    /**
     * モデルの主キーが増分するかどうかを示す
     *
     * @var bool
     */
    public $incrementing = false;
}
```

主キーが整数でない場合は、モデル上で保護された$keyType属性をstringに設定する必要があります：
```php
class User extends Model
{
    /**
     * 自動増分IDの「タイプ」。
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## タイムスタンプ
デフォルトでは、Eloquentはデータテーブルにcreated_atとupdated_atが存在することを想定しています。Eloquentにこれらの2つの列を自動的に管理させたくない場合は、モデルで$timestamps属性をfalseに設定してください：
```php
class User extends Model
{
    /**
     * タイムスタンプを自動的に維持するかどうかを示す
     *
     * @var bool
     */
    public $timestamps = false;
}
```

タイムスタンプの形式をカスタマイズする必要がある場合は、モデルで$dateFormat属性を設定できます。この属性は、データベースへの日付属性の保存方法、モデルの配列やJSONへのシリアル化形式を決定します：
```php
class User extends Model
{
    /**
     * タイムスタンプの保存形式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

保存するタイムスタンプのフィールド名をカスタマイズする必要がある場合は、モデルでCREATED_ATとUPDATED_AT定数の値を設定できます：
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## データベース接続
デフォルトでは、Eloquentモデルはアプリケーション構成でデフォルトのデータベース接続を使用します。モデルに異なる接続を指定したい場合は、$connection属性を設定してください：
```php
class User extends Model
{
    /**
     * モデルの接続名
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## デフォルト値
モデルの特定の属性にデフォルト値を定義したい場合は、モデルで$attributes属性を定義できます：
```php
class User extends Model
{
    /**
     * モデルのデフォルト属性値。
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## モデルの取得
モデルとそれに関連するデータテーブルを作成したら、データベースからデータをクエリできます。各Eloquentモデルを強力なクエリビルダーと考え、関連するデータテーブルを素早くクエリできます。例：
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> ヒント：Eloquentモデルはクエリビルダーでもあるため、利用可能なすべてのメソッドについては[クエリビルダー](queries.md)を読んでください。Eloquentクエリでこれらのメソッドを使用できます。

## 追加の制約条件
Eloquentのallメソッドはモデル内のすべての結果を返します。Eloquentモデルがクエリビルダーとして機能するため、クエリ条件を追加し、getメソッドでクエリ結果を取得できます：
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## モデルの再読み込み
freshメソッドとrefreshメソッドを使用してモデルを再読み込みできます。freshメソッドはデータベースからモデルを再取得します。既存のモデルインスタンスには影響しません：
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refreshメソッドは、データベースからの新しいデータを使って既存のモデルを更新します。さらに、読み込まれている関連が再度読み込まれます：
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## コレクション
Eloquentのallやgetメソッドは複数の結果を取得し、`Illuminate\Database\Eloquent\Collection`インスタンスを返します。`Collection`クラスには、Eloquentの結果を処理するための多くの補助関数が提供されています：
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## カーソルを使用
cursorメソッドを使用すると、データベースをカーソルで走査できます。大量のデータを処理する際には、cursorメソッドを使用することでメモリの使用量を大幅に削減できます：
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursorは`Illuminate\Support\LazyCollection`インスタンスを返します。[Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections)では、Laravelコレクションのほとんどのメソッドを使用できますが、一度に1つのモデルのみがメモリにロードされます：
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```
## サブクエリの選択
Eloquentは高度なサブクエリのサポートを提供し、関連するテーブルから情報を単一のクエリステートメントで取得することができます。たとえば、目的地テーブルdestinationsと目的地へのフライトテーブルflightsがあるとします。flightsテーブルには到着時刻を示すarrival_atフィールドが含まれています。

selectメソッドとaddSelectメソッドを使用して、サブクエリ機能を使って全目的地destinationsとそれぞれの目的地への最後のフライトの名前を単一のステートメントで取得することができます：
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## サブクエリによる並べ替え
また、クエリビルダのorderByメソッドもサブクエリをサポートしています。この機能を使用して、全ての目的地を最後のフライトが到着した時間でソートすることができます。同様に、これはデータベースへの単一のクエリの実行だけで実行できます：
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## 単一のモデル/コレクションの取得
指定されたデータテーブルから全てのレコードを取得するだけでなく、find、first、またはfirstWhereメソッドを使用して単一のレコードを取得することもできます。これらのメソッドは、モデルのコレクションではなく単一のモデルインスタンスを返します：
```php
// 主キーを使用して単一のモデルを検索する...
$flight = app\model\Flight::find(1);

// クエリ条件に一致する最初のモデルを検索する...
$flight = app\model\Flight::where('active', 1)->first();

// クエリ条件に一致する最初のモデルを高速で実装する...
$flight = app\model\Flight::firstWhere('active', 1);
```

また、主キーの配列をfindメソッドの引数として使用することで、一致するレコードのコレクションを返すことができます：
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

また、firstOrメソッドを使用して最初の結果を検索し、値が見つからない場合に他のアクションを実行することができます。firstOrメソッドの戻り値は、結果が見つかった場合には最初の結果が返され、結果がない場合には指定されたコールバックが実行された戻り値となります：
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOrメソッドはクエリフィールドの配列も受け入れることができます：
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "Not Found"例外
モデルが見つからない場合に例外をスローしたい場合があります。これはコントローラーやルートで特に便利です。findOrFailメソッドとfirstOrFailメソッドは、クエリの最初の結果を取得し、見つからない場合にはIlluminate\Database\Eloquent\ModelNotFoundException例外をスローします：
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## コレクションの取得
また、クエリビルダのcount、sum、maxメソッドやその他のコレクション関数を使用して、コレクションを操作することができます。これらのメソッドは、モデルインスタンスではなく、適切なスカラー値を返します：
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## 挿入
新しいレコードをデータベースに挿入するには、まず新しいモデルインスタンスを作成し、そのインスタンスにプロパティを設定し、saveメソッドを呼び出します：
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * ユーザーテーブルに新しいレコードを追加する
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // リクエストの検証

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```
created_atおよびupdated_atのタイムスタンプは自動的に設定されます($timestampsプロパティがtrueの場合)ので、手動で値を設定する必要はありません。

## 更新
saveメソッドは既存のモデルを更新するためにも使用できます。モデルを更新するには、まずモデルを取得し、更新するプロパティを設定してからsaveメソッドを呼び出す必要があります。同様に、updated_atのタイムスタンプも自動的に更新されますので、手動で値を設定する必要はありません：
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## バルク更新
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## プロパティの変更を確認
Eloquentは、モデルの内部状態を確認し、そのプロパティが最初にロードされてからどのように変更されたかを確認するisDirty、isClean、wasChangedメソッドを提供しています。
isDirtyメソッドは、モデルがロードされてからプロパティが変更されたかを確認します。特定のプロパティ名を渡すことで、特定のプロパティがdirtyであるかどうかを確認することができます。isCleanメソッドはisDirtyとは逆で、オプションでプロパティを受け取ることもできます：
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

wasChangedメソッドは、現在のリクエストサイクルでモデルが最後に保存されたときにプロパティが変更されたかどうかを確認します。また、特定のプロパティが変更されたかどうかを確認するためにプロパティ名を渡すこともできます：
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
## 一括代入
新しいモデルを保存するには、createメソッドを使用することもできます。このメソッドはモデルのインスタンスを返しますが、使用する前にモデルでfillableまたはguardedプロパティを指定する必要があります。なぜなら、Eloquentモデルはデフォルトで一括代入を許可しないからです。

ユーザーが意図しないHTTPパラメーターをリクエストで受け取り、それによってデータベース内の変更すべきでないフィールドが変更されると、一括代入の脆弱性が発生します。例えば、悪意のあるユーザーがHTTPリクエストでis_adminパラメータを渡し、それをcreateメソッドに渡すことで自分自身を管理者に昇格させることが可能です。

したがって、始める前にモデル上でどのプロパティが一括代入できるかを定義する必要があります。これはモデルの$fillableプロパティを使用して行うことができます。例えば、Flightモデルのname属性を一括代入可能にするには：

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 一括代入可能な属性。
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```

一括代入可能な属性を設定したら、createメソッドを使用して新しいデータをデータベースに挿入することができます。createメソッドは保存されたモデルのインスタンスを返します：

```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```

モデルインスタンスがすでにある場合は、fillメソッドに配列を渡すことで値を代入できます：

```php
$flight->fill(['name' => 'Flight 22']);
```

$fillableは一括代入の"ホワイトリスト"と見なすことができ、これに替えて$guardedプロパティを使用することもできます。$guardedプロパティには一括代入を許可しない属性が含まれます。つまり、機能としては$guardedが"ブラックリスト"に近いです。注意：$fillableと$guardedは同時に使用できないため、どちらか一つしか使用できません。以下の例では、price属性以外のすべての属性が一括代入可能です：

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 一括代入不可な属性。
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

すべての属性を一括代入可能にしたい場合は、$guardedを空の配列として定義できます：

```php
/**
 * 一括代入不可な属性。
 *
 * @var array
 */
protected $guarded = [];
```

## その他の作成方法
firstOrCreate/firstOrNew
ここには、一括代入に使用できるかもしれない2つのメソッドがあります：firstOrCreateとfirstOrNew。firstOrCreateメソッドは与えられたキー/値ペアでデータベース内のデータをマッチングし、モデルが見つからない場合は、最初の引数の属性とオプションの2番目の引数の属性を含むレコードを挿入します。

firstOrNewメソッドは、firstOrCreateメソッドと同じように属性でデータベース内のレコードを検索しようとします。ただし、firstOrNewメソッドは対応するモデルが見つからない場合に新しいモデルインスタンスを返します。注意：firstOrNewメソッドで返されるモデルインスタンスはまだデータベースに保存されていないため、保存するためには手動でsaveメソッドを呼び出す必要があります：

```php
// nameでフライトを検索し、存在しない場合は作成する…
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// nameでフライトを検索し、nameとdelayed属性およびarrival_time属性を使用して作成…
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// nameでフライトを検索し、存在しない場合はインスタンスを作成…
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// nameでフライトを検索し、nameとdelayed属性およびarrival_time属性を使用してモデルインスタンスを作成…
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

また、既存のモデルを更新したい場合や存在しない場合に新しいモデルを作成したい場合には、updateOrCreateメソッドを使用できます。firstOrCreateメソッドと同様に、updateOrCreateはモデルを永続化するためsave()を呼び出す必要はありません：

```php
// オークランドからサンディエゴへのフライトが存在する場合、価格を99ドルに設定する。
// 存在しないモデルが一致しない場合は作成する。
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## モデルの削除

モデルインスタンスに対してdeleteメソッドを呼び出すことで、インスタンスを削除できます：

```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## 主キーでモデルを削除
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## クエリによるモデルの削除
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## モデルの複製
replicateメソッドを使用して、まだデータベースに保存されていない新しいインスタンスを複製できます。モデルインスタンスが多くの同じ属性を共有している場合に、このメソッドは非常に便利です。

```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();
```

## モデルの比較
時には、2つのモデルが「同じ」かどうかを判断する必要があります。isメソッドは、2つのモデルが同じ主キー、テーブル、およびデータベース接続を持っているかどうかを素早くチェックするのに使用できます：

```php
if ($post->is($anotherPost)) {
    //
}
```

## モデルオブザーバ
[Laravel 中のモデルイベントとオブザーバー]（https://learnku.com/articles/6657/model-events-and-observer-in-laravel）を参照してください。

注意：Eloquent ORMでモデルオブザーバをサポートするには、追加でcomposer require "illuminate/events"をインポートする必要があります。

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
