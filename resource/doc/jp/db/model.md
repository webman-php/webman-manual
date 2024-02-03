# クイックスタート

webmanモデルは[Eloquent ORM](https://laravel.com/docs/7.x/eloquent)に基づいています。各データベーステーブルには、そのテーブルとやり取りするための対応する「モデル」があります。モデルを使用してデータベーステーブルからデータをクエリしたり、新しいレコードを挿入したりできます。

開始する前に、`config/database.php` でデータベース接続が設定されていることを確認してください。

>Eloquent ORMはモデルオブザーバをサポートするために`composer require "illuminate/events"`を追加する必要があります。[例](#モデルオブザーバ)

## 例
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * モデルと関連するテーブル名
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * プライマリキーを再定義します。デフォルトはidです。
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * タイムスタンプを自動的に維持するかどうかを示します。
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## テーブル名
モデルでカスタムデータテーブルを指定するには、table属性を定義します：
```php
class User extends Model
{
    /**
     * モデルと関連するテーブル名
     *
     * @var string
     */
    protected $table = 'user';
}
```

## プライマリキー
Eloquentは、データベーステーブルごとにidという名前のプライマリキー列があると仮定します。この規定を上書きするために、守られた$primaryKey属性を定義できます。
```php
class User extends Model
{
    /**
     * プライマリキーを再定義します。デフォルトはidです。
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquentは、プライマリキーが自動増分の整数値であると仮定します。これは、デフォルトでプライマリキーが自動的にint型に変換されることを意味します。非増分または数値でない主キーを使用したい場合は、パブリックの$incrementing属性をfalseに設定する必要があります。
```php
class User extends Model
{
    /**
     * モデルの主キーが増分かどうかを示します。
     *
     * @var bool
     */
    public $incrementing = false;
}
```

主キーが整数でない場合は、モデル上の守られた$keyType属性をstringに設定する必要があります：
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
デフォルトでは、Eloquentはデータテーブルにcreated_atとupdated_atがあることを想定しています。これらの2つの列をEloquentが自動的に管理したくない場合は、モデル内の$timestamps属性をfalseに設定してください：
```php
class User extends Model
{
    /**
     * タイムスタンプを自動的に維持するかどうかを示します。
     *
     * @var bool
     */
    public $timestamps = false;
}
```
タイムスタンプの書式をカスタマイズする必要がある場合は、モデルで$dateFormat属性を設定します。この属性は、データベースでの日付プロパティの格納方法、およびモデルが配列やJSONとしてシリアル化される形式を決定します：
```php
class User extends Model
{
    /**
     * タイムスタンプの格納形式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

タイムスタンプのフィールド名をカスタマイズする必要がある場合は、モデルでCREATED_ATとUPDATED_ATの定数の値を設定できます：
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## データベース接続
デフォルトでは、Eloquentモデルはアプリケーションが設定したデフォルトのデータベース接続を使用します。モデルに別の接続を指定する場合は、$connection属性を設定してください：
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

## デフォルトの属性値
モデルの特定の属性にデフォルト値を定義する場合は、モデルで$attributes属性を定義できます：
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

## モデルの検索
モデルとそれに関連するデータベーステーブルを作成したら、データベースからデータをクエリできます。各Eloquentモデルを強力なクエリビルダと考えることができます。これを使用して、関連するデータテーブルをより迅速にクエリできます。例えば：
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> ヒント：Eloquentモデルもクエリビルダですので、[クエリビルダ](queries.md)で使用できるすべてのメソッドを読んでおくことをお勧めします。Eloquentクエリでもこれらのメソッドを使用できます。

## 追加の制約
Eloquentのallメソッドはモデル内のすべての結果を返します。各Eloquentモデルがクエリビルダとして機能するため、クエリ条件を追加し、次にgetメソッドを使用してクエリ結果を取得することができます：
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## モデルの再読み込み
freshメソッドとrefreshメソッドを使用してモデルを再読み込みできます。freshメソッドはデータベースからモデルを再取得します。既存のモデルのインスタンスには影響しません：
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refreshメソッドを使用すると、データベースから新しいデータを使って既存のモデルを再アサインできます。また、ロードされたリレーションも再ロードされます：
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## コレクション
Eloquentのallメソッドとgetメソッドは複数の結果をクエリし、`Illuminate\Database\Eloquent\Collection`インスタンスを返します。`Collection`クラスには、Eloquent結果を処理するための多くの補助的な関数が用意されています：
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## カーソルの使用
cursorメソッドを使用すると、データベースをカーソルで反復処理できます。大量のデータを処理する場合、cursorメソッドを使用するとメモリの使用量を大幅に削減できます：
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursorは`Illuminate\Support\LazyCollection`インスタンスを返します。[遅延コレクション](https://laravel.com/docs/7.x/collections#lazy-collections)を使用すると、Laravelコレクションのほとんどのコレクションメソッドを使用でき、メモリに1つのモデルしかロードされません：
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## selectsサブクエリ
Eloquentは高度なサブクエリサポートを提供し、1つのクエリステートメントで関連テーブルから情報を取得できます。例えば、destinationテーブルと到着地点へのフライトテーブルflightsがあるとします。flightsテーブルには、到着地点への最後の飛行機の名前を示す到着時間が含まれています。

サブクエリ機能が提供するselectとaddSelectメソッドを使用すると、全目的地destinationsを1つのステートメントでクエリし、各目的地に到着した最後の飛行機の名前を取得できます：
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## サブクエリを使用したソート
さらに、クエリビルダのorderByメソッドもサブクエリをサポートしています。この機能を使用して、全目的地を最後の到着地点の時間に基づいてソートできます。同様に、これはデータベースに1回のクエリを実行します：
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## 個々のモデル/コレクションの検索
すべてのレコードを指定のデータテーブルから検索するだけでなく、find、first、またはfirstWhereメソッドを使用して単一のレコードを検索できます。これらのメソッドは単一のモデルインスタンスを返し、モデルコレクションを返しません：
```php
// 主キーでモデルを検索...
$flight = app\model\Flight::find(1);

// クエリ条件に一致する最初のモデルを検索...
$flight = app\model\Flight::where('active', 1)->first();

// クエリ条件に一致する最初のモデルを素早く検索...
$flight = app\model\Flight::firstWhere('active', 1);
```

また、主キー配列を引数としてfindメソッドを呼び出すことで、一致するレコードのコレクションを返すことができます：
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

時には最初の結果が見つからない場合に他のアクションを実行したいことがあります。firstOrメソッドは結果が見つかった場合に最初の結果を返し、結果がない場合は指定されたコールバックを実行します。コールバックの戻り値は、firstOrメソッドの戻り値になります：
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOrメソッドは、クエリビルダを使っても受け入れることができます：
```php
$model = app\model\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## “見つからない”例外
モデルが見つからない場合に例外をスローしたい場合があります。これはコントローラーやルートで非常に便利です。findOrFailとfirstOrFailメソッドは、クエリの最初の結果を取得し、見つからない場合はIlluminate\Database\Eloquent\ModelNotFoundException例外をスローします：
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```
## コレクションの取得
また、問い合わせビルダーの count、sum、max メソッドやその他のコレクション関数を使用して、コレクションを操作することもできます。これらのメソッドは、モデルのインスタンスではなく、適切なスカラー値を返します。

```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## 挿入
新しいレコードをデータベースに挿入するには、まず新しいモデルインスタンスを作成し、インスタンスに属性を設定してから、save メソッドを呼び出します。

```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * ユーザーテーブルに新しいレコードを追加します
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // リクエストを検証

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at と updated_at のタイムスタンプは自動的に設定されます（モデルの $timestamps プロパティが true の場合）、手動で値を設定する必要はありません。

## 更新
save メソッドは既存のモデルを更新するためにも使用できます。モデルを更新するには、まず取得し、更新したい属性を設定してから save メソッドを呼び出します。同様に、updated_at タイムスタンプも自動的に更新されるため、手動で値を設定する必要はありません。

```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## バッチ更新
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## 属性の変更を確認する
Eloquent は isDirty、isClean、wasChanged メソッドを提供し、モデルの内部状態を確認し、属性が初期読み込み時からどのように変化したかを確認できます。isDirty メソッドは、モデルが読み込まれてから属性が変更されたかどうかを判断します。特定の属性が変更されたかどうかを判断するには、特定の属性名を渡すことができます。isClean メソッドは isDirty とは逆で、オプションで属性を渡すことができます。

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

wasChanged メソッドは、現在のリクエストサイクルで最後にモデルを保存したときに属性が変更されたかどうかを確認します。特定の属性が変更されたかどうかを確認するには、属性名を渡すこともできます。

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

## バルク代入
新しいモデルを保存するには create メソッドを使用することもできます。このメソッドはモデルインスタンスを返します。ただし、使用する前にモデルで fillable または guarded プロパティを指定する必要があります。なぜなら、すべての Eloquent モデルはデフォルトでバルク代入を許可しないからです。

ユーザーが意図せずにHTTPパラメータを渡し、それによってデータベース内で変更したくないフィールドが変更される可能性があるバルク代入の脆弱性が発生することがあります。例えば、悪意のあるユーザーが is_admin パラメータをHTTPリクエストに渡し、それを create メソッドに渡した場合、それによってユーザーが自分自身を管理者に昇格させることができます。

したがって、始める前にモデル上でどの属性がバルク代入可能かを定義する必要があります。fillable プロパティを使用して、Flight モデルの name 属性がバルク代入可能になるようにします。

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * バルク代入可能な属性。
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```

バルク代入可能な属性を設定したら、create メソッドを使用して新しいデータをデータベースに挿入できます。create メソッドは保存されたモデルインスタンスを返します。

```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```

既にモデルインスタンスが存在する場合は、fill メソッドに配列を渡すことで値を代入できます。

```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable はバルク代入の「ホワイトリスト」と見なすことができ、$guarded プロパティを使用しても同様です。$guarded プロパティにはバルク代入を許可しない属性が含まれます。つまり $guarded は「ブラックリスト」のような機能を提供します。注意: $fillable または $guarded のどちらか一方のみを使用できますが、両方を同時に使用することはできません。次の例では、price 属性以外のすべての属性をバルク代入できるようにしています。

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * バルク代入不可の属性。
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

すべての属性をバルク代入できるようにする場合は、$guarded を空の配列に定義します。

```php
/**
 * バルク代入不可の属性。
 *
 * @var array
 */
protected $guarded = [];
```

## その他の作成方法
firstOrCreate/firstOrNew
ここで言及する2つのバルク代入可能なメソッドは、firstOrCreate と firstOrNew です。firstOrCreate メソッドは、指定されたキー/値のペアを使用してデータベース内のデータを照合し、モデルが見つからない場合には、最初のパラメータの属性とオプションの2番目のパラメータの属性を含むレコードを挿入します。firstOrNew メソッドは firstOrCreate メソッドと同じように指定された属性でデータベースレコードを検索しますが、見つからない場合に新しいモデルインスタンスを返します。注意: firstOrNew メソッドが返すモデルインスタンスはまだデータベースに保存されていないため、save メソッドを手動で呼び出す必要があります。

```php
// 名前でフライトを検索し、存在しない場合は作成する...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// 名前でフライトを検索し、名前と delayed 属性、arrival_time 属性で作成する...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// 名前でフライトを検索し、存在しない場合はインスタンスを作成...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// 名前でフライトを検索し、名前と delayed 属性、arrival_time 属性でインスタンスを作成...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

また、既存のモデルを更新したい場合や存在しない場合は新しいモデルを作成したい場合にも一括作成できる場合があります。それを一括で行うには updateOrCreate メソッドを使用します。 firstOrCreate メソッドと同様に、updateOrCreate はモデルを永続化するので、save() を呼び出す必要はありません。

```php
// オークランドからサンディエゴへのフライトが存在する場合、価格を99ドルに設定します。
// 一致するモデルがない場合は作成します。
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## モデルの削除
モデルインスタンスに delete メソッドを使用して削除できます。

```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## 主キーでモデルを削除する
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## クエリでモデルを削除
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## モデルの複製
replicate メソッドを使用すると、データベースに保存されていない新しいインスタンスを複製できます。モデルインスタンス間で多くの共通の属性がある場合に便利です。

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
時には2つのモデルが「同じ」かどうかを判断する必要があります。この場合、is メソッドを使用して2つのモデルが同じ主キー、テーブル、およびデータベース接続を持っているかを素早く確認することができます。

```php
if ($post->is($anotherPost)) {
    //
}
```

## モデルオブザーバ
[Laravel 中のモデルイベントとオブザーバ](https://learnku.com/articles/6657/model-events-and-observer-in-laravel) を参照してください。

注意: Eloquent ORM でモデルオブザーバをサポートするには、追加の composer require "illuminate/events" が必要です。

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
