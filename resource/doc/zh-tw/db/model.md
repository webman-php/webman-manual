# 快速開始

webman模型基於[Eloquent ORM](https://laravel.com/docs/7.x/eloquent) 。每個資料庫表都有一個對應的「模型」用來與該表互動。你可以通過模型查詢資料表中的數據，以及在資料表中插入新記錄。

在開始之前，請確保配置了 `config/database.php` 中的資料庫連接。

> 注意：Eloquent ORM 要支持模型觀察者需要額外導入`composer require "illuminate/events"` [例子](#模型觀察者)

## 示例
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * 與模型關聯的表名
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 重定義主鍵，默認是id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * 指示是否自動維護時間戳
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## 表名
你可以通過在模型上定義 table 屬性來指定自定義資料表：
```php
class User extends Model
{
    /**
     * 與模型關聯的表名
     *
     * @var string
     */
    protected $table = 'user';
}
```

## 主鍵
Eloquent 也會假設每個資料表都有一個名為 id 的主鍵列。你可以定義一個受保護的 $primaryKey 屬性來重寫約定。
```php
class User extends Model
{
    /**
     * 重定義主鍵，默認是id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent 假設主鍵是一個自增的整數值，這意味着默認情況下主鍵會自動轉換為 int 類型。如果您希望使用非遞增或非數字的主鍵則需要設置公共的 $incrementing 屬性設置為 false
```php
class User extends Model
{
    /**
     * 指示模型主鍵是否遞增
     *
     * @var bool
     */
    public $incrementing = false;
}
```

如果你的主鍵不是一個整數，你需要將模型上受保護的 $keyType 屬性設置為 string：
```php
class User extends Model
{
    /**
     * 自動遞增ID的“類型”。
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## 時間戳
默認情況下，Eloquent 預期你的資料表中存在 created_at 和 updated_at 。如果你不想讓 Eloquent 自動管理這兩個列，請將模型中的 $timestamps 屬性設置為 false：
```php
class User extends Model
{
    /**
     * 指示是否自動維護時間戳
     *
     * @var bool
     */
    public $timestamps = false;
}
```

如果需要自定義時間戳的格式，在你的模型中設置 $dateFormat 屬性。這個屬性決定日期屬性在資料庫的存儲方式，以及模型序列化為數組或者 JSON 的格式：
```php
class User extends Model
{
    /**
     * 時間戳存儲格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

如果你需要自定義存儲時間戳的字段名，可以在模型中設置 CREATED_AT 和 UPDATED_AT 常量的值來實現：
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## 數據庫連接
認情況下，Eloquent 模型將使用你的應用程序配置的默認數據庫連接。如果你想為模型指定一個不同的連接，設置 $connection 屬性：
```php
class User extends Model
{
    /**
     * 模型的連接名稱
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## 默認屬性值
如果要為模型的某些屬性定義默認值，可以在模型上定義 $attributes 屬性：
```php
class User extends Model
{
    /**
     * 模型的默認屬性值。
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## 模型檢索
創建模型和 它關聯的資料表 後，你就可以從資料庫中查詢數據了。將每個 Eloquent 模型想像成一個強大的查詢構建器，你可以用它更快速的查詢與其相關聯的資料表。例如：
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> 提示：因為 Eloquent 模型也是查詢構建器，所以你也應當閱讀[查詢構建器](queries.md) 可用的所有方法。你可以在 Eloquent 查詢中使用這些方法。

## 附加約束
Eloquent 的 all 方法會返回模型中所有的結果。由於每個 Eloquent 模型都充當一個 查詢構建器，所以你也可以添加查詢條件，然後使用 get 方法獲取查詢結果：
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## 重新加載模型
你可以使用 fresh 和 refresh 方法重新加載模型。 fresh 方法會重新從資料庫中檢索模型。現有的模型實例不受影響：
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh 方法使用數據庫中的新數據重新賦值現有模型。此外，已經加載的關係會被重新加載：
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## 集合
Eloquent 的 all 和 get 方法可以查詢到多個結果，返回一個 `Illuminate\Database\Eloquent\Collection `實例。`Collection` 類提供了大量的輔助函數來處理 Eloquent 結果：
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```
## 使用游標
`cursor` 方法讓你能夠使用游標遍歷資料庫，它僅執行一次查詢。當處理大量數據時，`cursor` 方法可以大大減少內存使用量：
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

`cursor` 回傳 `Illuminate\Support\LazyCollection` 實例。[惰性集合](https://laravel.com/docs/7.x/collections#lazy-collections)讓你能夠使用 Laravel 集合中的大部分方法，並且每次只會載入單個模型到內存中：
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Selects 子查詢
Eloquent 提供了進階子查詢支援，你可以使用單一查詢語句從相關表格中提取資訊。舉個例子，假設我們有一個目的地表格 destinations 和一個到達目的地的航班表格 flights。flights 表格包含一個 arrival_at 字段，表示航班何時到達目的地。

使用子查詢功能提供的 `select` 和 `addSelect` 方法，我們可以使用單一語句查詢全部目的地 destinations，以及抵達各目的地最後一班飛機的名稱：
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## 根據子查詢進行排序
此外，查詢構建器的 `orderBy` 函數也支援子查詢。我們可以使用此功能根據最後一班航班抵達目的地的時間對所有目的地進行排序。同樣的，這可以只對資料庫執行單個查詢：
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## 檢索單個模型 / 集合
除了從指定的資料表檢索所有記錄外，你可以使用 `find`、`first` 或 `firstWhere` 方法來檢索單條記錄。這些方法返回單個模型實例，而不是返回模型集合：
```php
// 通過主鍵查找一個模型...
$flight = app\model\Flight::find(1);

// 查找符合查詢條件的第一個模型...
$flight = app\model\Flight::where('active', 1)->first();

// 查找符合查詢條件的第一個模型的快速實作...
$flight = app\model\Flight::firstWhere('active', 1);
```

你也可以使用主鍵陣列作為參數調用 `find` 方法，它將返回匹配記錄的集合：
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

有時你可能希望在查找到第一個結果但找不到值時執行其他動作。`firstOr` 方法將會在查找到結果時返回第一個結果，如果沒有結果，將會執行給定的回調。回調的返回值將會作為 `firstOr` 方法的返回值：
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```

`firstOr` 方法同樣接受欄位陣列來查詢：
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## 「未找到」異常
有時你希望在未找到模型時拋出異常。這在控制器和路由中非常有用。`findOrFail` 和 `firstOrFail` 方法會檢索查詢的第一個結果，如果未找到，將拋出 `Illuminate\Database\Eloquent\ModelNotFoundException` 異常：
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## 檢索集合
你還可以使用查詢構建器提供的 `count`、 `sum` 和 `max` 方法，和其他的集合函數來操作集合。這些方法只會返回適當的純量值而不是一個模型實例：
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## 插入
要往資料庫新增一條記錄，先創建新模型實例，給實例設置屬性，然後調用 `save` 方法：
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * 在使用者表格中新增一條新的記錄
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // 驗證請求

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

`created_at` 和 `updated_at` 時間戳將會自動設置(模型中 `$timestamps` 屬性為 `true` 時)，不需要手動賦值。

## 更新
`save` 方法也可以用來更新資料庫已經存在的模型。更新模型，你需要先檢索出來，設置要更新的屬性，然後調用 `save` 方法。同樣，`updated_at` 時間戳會自動更新，所以也不需要手動賦值：
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## 批量更新
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```
## 檢查屬性變化
Eloquent 提供了 isDirty, isClean 和 wasChanged 方法，以檢查模型的內部狀態並確定其屬性從最初加載時如何變化。
isDirty 方法確定自加載模型以來是否已更改任何屬性。 您可以傳遞特定的屬性名稱來確定特定的屬性是否變髒。isClean 方法與 isDirty 相反，它也接受可選的屬性參數：
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
wasChanged 方法確定在當前請求周期內最後一次保存模型時是否更改了任何屬性。 你還可以傳遞屬性名稱以查看特定屬性是否已更改：
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

## 批量賦值
你也可以使用 create 方法來保存新模型。 此方法會返回模型實例。 不過，在使用之前，你需要在模型上指定 fillable 或 guarded 屬性，因為所有的 Eloquent 模型都默認不可進行批量賦值。

當用戶通過請求傳入意外的 HTTP 參數，並且該參數更改了數據庫中你不需要更改的字段時，就會發生批量賦值漏洞。 比如：惡意用戶可能會通過 HTTP 請求傳入 is_admin 參數，然後將其傳給 create 方法，此操作能讓用戶將自己升級成管理員。

所以，在開始之前，你應該定義好模型上的哪些屬性是可以被批量賦值的。你可以通過模型上的 $fillable 屬性來實現。 例如：讓 Flight 模型的 name 屬性可以被批量賦值：
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 可以被批量賦值的屬性。
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
一旦我們設置好了可以批量賦值的屬性，就可以通過 create 方法插入新數據到數據庫中了。 create 方法將返回保存的模型實例：
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
如果你已經有一個模型實例，你可以傳遞一個數組給 fill 方法來賦值：
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable 可以看作批量賦值的「白名單」, 你也可以使用 $guarded 屬性來實現。 $guarded 屬性包含的是不允許批量賦值的數組。也就是說， $guarded 從功能上將更像是一個「黑名單」。注意：你只能使用 $fillable 或 $guarded 二者中的一個，不可同時使用。下面這個例子中，price 屬性之外的所有屬性都可以進行批量賦值：
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 不可批量賦值的屬性。
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

如果你想讓所有屬性都可以批量賦值， 你可以將 $guarded 定義成一個空數組：
```php
/**
 * 不可批量賦值的屬性。
 *
 * @var array
 */
protected $guarded = [];
```

## 其他創建方法
firstOrCreate/ firstOrNew
這裡有兩個你可能用來批量賦值的方法： firstOrCreate 和 firstOrNew。 firstOrCreate 方法會通過給定的鍵 / 值對來匹配數據庫中的數據。如果在數據庫中找不到模型，則將插入一條記錄，其中包含第一個參數的屬性以及可選的第二個參數的屬性。

firstOrNew 方法像 firstOrCreate 方法一樣嘗試通過給定的屬性查找數據庫中的記錄。不過，如果 firstOrNew 方法找不到對應的模型，會返回一個新的模型實例。注意 firstOrNew 返回的模型實例尚未保存到數據庫中，你需要手動調用 save 方法來保存：
```php
// 通過 name 檢索航班，不存在則創建...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// 通過 name 檢索航班，或使用 name 和 delayed 屬性和 arrival_time 屬性創建...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// 通過 name 檢索航班，不存在則創建一個實例...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// 通過 name 檢索航班，或使用 name 和 delayed 屬性和 arrival_time 屬性創建一個模型實例...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

你還可能遇到希望更新現有模型或在不存在的情況下則創建新的模型的情景。 updateOrCreate 方法來一步實現。 類似於 firstOrCreate 方法，updateOrCreate 持久化模型，因此無需調用 save()：
```php
// 如果有從奧克蘭到聖地亞哥的航班，則價格定為99美元。
// 如果沒匹配到存在的模型，則創建一個。
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## 刪除模型

可以在模型實例上調用 delete 方法來刪除實例：
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## 通過主鍵刪除模型
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```
## 透過查詢刪除模型
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## 複製模型
您可以使用 replicate 方法複製一個新的尚未保存到資料庫的實例，當模型實例共享許多相同的屬性時，這個方法非常好用。
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

## 模型比較
有時可能需要判斷兩個模型是否「相同」。is 方法可以用來快速校驗兩個模型是否擁有相同的主鍵、表和資料庫連接：
```php
if ($post->is($anotherPost)) {
    //
}
```

## 模型觀察者
使用參考[Laravel 中的模型事件與 Observer
](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

注意：Eloquent ORM 要支持模型觀察者需要額外導入composer require "illuminate/events"
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
