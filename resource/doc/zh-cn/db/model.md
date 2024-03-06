# 快速开始

webman模型 基于 [Eloquent ORM](https://laravel.com/docs/7.x/eloquent) 。每个数据库表都有一个对应的「模型」用来与该表交互。你可以通过模型查询数据表中的数据，以及在数据表中插入新记录。

在开始之前，请确保配置了 `config/database.php` 中配置数据库连接。

> 注意：Eloquent ORM 要支持模型观察者需要额外导入`composer require "illuminate/events"` [例子](#模型观察者)

## 示例
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 重定义主键，默认是id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## 表名
你可以通过在模型上定义 table 属性来指定自定义数据表：
```php
class User extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user';
}
```

## 主键
Eloquent 也会假设每个数据表都有一个名为 id 的主键列。你可以定义一个受保护的 $primaryKey 属性来重写约定。
```php
class User extends Model
{
    /**
     * 重定义主键，默认是id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent 假设主键是一个自增的整数值，这意味着默认情况下主键会自动转换为 int 类型。如果您希望使用非递增或非数字的主键则需要设置公共的 $incrementing 属性设置为 false
```php
class User extends Model
{
    /**
     * 指示模型主键是否递增
     *
     * @var bool
     */
    public $incrementing = false;
}
```

如果你的主键不是一个整数，你需要将模型上受保护的 $keyType 属性设置为 string：
```php
class User extends Model
{
    /**
     * 自动递增ID的“类型”。
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## 时间戳
默认情况下，Eloquent 预期你的数据表中存在 created_at 和 updated_at 。如果你不想让 Eloquent 自动管理这两个列， 请将模型中的 $timestamps 属性设置为 false：
```php
class User extends Model
{
    /**
     * 指示是否自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}
```
如果需要自定义时间戳的格式，在你的模型中设置 $dateFormat 属性。这个属性决定日期属性在数据库的存储方式，以及模型序列化为数组或者 JSON 的格式：
```php
class User extends Model
{
    /**
     * 时间戳存储格式
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

如果你需要自定义存储时间戳的字段名，可以在模型中设置 CREATED_AT 和 UPDATED_AT 常量的值来实现：
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## 数据库连接
认情况下，Eloquent 模型将使用你的应用程序配置的默认数据库连接。如果你想为模型指定一个不同的连接，设置 $connection 属性：
```php
class User extends Model
{
    /**
     * 模型的连接名称
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## 默认属性值
如果要为模型的某些属性定义默认值，可以在模型上定义 $attributes 属性：
```php
class User extends Model
{
    /**
     * 模型的默认属性值。
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## 模型检索
创建模型和 它关联的数据库表 后，你就可以从数据库中查询数据了。将每个 Eloquent 模型想象成一个强大的 查询构造器 ，你可以用它更快速的查询与其相关联的数据表。例如：
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> 提示：因为 Eloquent 模型也是查询构造器，所以你也应当阅读 [查询构造器](queries.md) 可用的所有方法。你可以在 Eloquent 查询中使用这些方法。

## 附加约束
Eloquent 的 all 方法会返回模型中所有的结果。由于每个 Eloquent 模型都充当一个 查询构造器，所以你也可以添加查询条件，然后使用 get 方法获取查询结果：
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## 重新加载模型
你可以使用 fresh 和 refresh 方法重新加载模型。 fresh 方法会重新从数据库中检索模型。现有的模型实例不受影响：
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh 方法使用数据库中的新数据重新赋值现有模型。此外，已经加载的关系会被重新加载：
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## 集合
Eloquent 的 all 和 get 方法可以查询到多个结果，返回一个 `Illuminate\Database\Eloquent\Collection `实例。`Collection` 类提供了大量的辅助函数来处理 Eloquent 结果：
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## 使用游标
cursor 方法允许你使用游标遍历数据库，它只执行一次查询。处理大量的数据时， cursor 方法可以大大减少内存的使用量：
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursor 返回 `Illuminate\Support\LazyCollection` 实例。 [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) 允许你使用 Laravel 集合中大多数集合方法，而且每次只会加载单个模型到内存中：
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Selects 子查询
Eloquent 提供了高级子查询支持，你可以用单条查询语句从相关表中提取信息。举个例子，假设我们有一个目的地表 destinations 和一个到目的地的航班表 flights。flights 表包含一个 arrival_at 字段，表示航班何时到达目的地。

使用子查询功能提供的 select 和 addSelect 方法，我们可以用单条语句查询全部目的地 destinations，以及抵达各目的地最后一班飞机的名称：
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```
## 根据子查询进行排序
此外，查询构建器的 orderBy 函数也支持子查询。我们可以使用此功能根据最后一班航班到达目的地的时间对所有目的地排序。 同样，这可以只对数据库执行单个查询：
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## 检索单个模型 / 集合
除了从指定的数据表检索所有记录外，你可以使用 find、 first 或 firstWhere 方法来检索单条记录。这些方法返回单个模型实例，而不是返回模型集合：
```php
// 通过主键查找一个模型...
$flight = app\model\Flight::find(1);

// 查找符合查询条件的首个模型...
$flight = app\model\Flight::where('active', 1)->first();

// 查找符合查询条件的首个模型的快速实现...
$flight = app\model\Flight::firstWhere('active', 1);
```

你也可以使用主键数组作为参数调用 find 方法，它将返回匹配记录的集合：
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

有时你可能希望在查找首个结果但找不到值时执行其他动作。firstOr 方法将会在查找到结果时返回首个结果，如果没有结果，将会执行给定的回调。回调的返回值将会作为 firstOr 方法的返回值：
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOr 方法同样接受栏位数组来查询：
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```
## 「未找到」异常
有时你希望在未找到模型时抛出异常。这在控制器和路由中非常有用。 findOrFail 和 firstOrFail 方法会检索查询的第一个结果，如果未找到，将抛出 Illuminate\Database\Eloquent\ModelNotFoundException 异常：
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## 检索集合
你还可以使用 查询构造器 提供的 count、 sum 和 max 方法，和其他的集合函数 来操作集合。这些方法只会返回适当的标量值而不是一个模型实例：
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## 插入
要往数据库新增一条记录，先创建新模型实例，给实例设置属性，然后调用 save 方法：
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * 在用户表中添加一条新的记录
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // 验证请求

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at 和 updated_at 时间戳将会自动设置(模型中$timestamps属性为true时)，不需要手动赋值。


## 更新
save 方法也可以用来更新数据库已经存在的模型。更新模型，你需要先检索出来，设置要更新的属性，然后调用 save 方法。同样， updated_at 时间戳会自动更新，所以也不需要手动赋值：
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## 批量更新
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom'']);
```

## 检查属性变化
Eloquent 提供了 isDirty, isClean 和 wasChanged 方法，以检查模型的内部状态并确定其属性从最初加载时如何变化。
isDirty 方法确定自加载模型以来是否已更改任何属性。 您可以传递特定的属性名称来确定特定的属性是否变脏。isClean 方法与 isDirty 相反，它也接受可选的属性参数：
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
wasChanged 方法确定在当前请求周期内最后一次保存模型时是否更改了任何属性。 你还可以传递属性名称以查看特定属性是否已更改：
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
## 批量赋值
你也可以使用 create 方法来保存新模型。 此方法会返回模型实例。 不过，在使用之前，你需要在模型上指定 fillable 或 guarded 属性，因为所有的 Eloquent 模型都默认不可进行批量赋值。

当用户通过请求传入意外的 HTTP 参数，并且该参数更改了数据库中你不需要更改的字段时，就会发生批量赋值漏洞。 比如：恶意用户可能会通过 HTTP 请求传入 is_admin 参数，然后将其传给 create 方法，此操作能让用户将自己升级成管理员。

所以，在开始之前，你应该定义好模型上的哪些属性是可以被批量赋值的。你可以通过模型上的 $fillable 属性来实现。 例如：让 Flight 模型的 name 属性可以被批量赋值：

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
一旦我们设置好了可以批量赋值的属性，就可以通过 create 方法插入新数据到数据库中了。 create 方法将返回保存的模型实例：
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
如果你已经有一个模型实例，你可以传递一个数组给 fill 方法来赋值：
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable 可以看作批量赋值的「白名单」, 你也可以使用 $guarded 属性来实现。 $guarded 属性包含的是不允许批量赋值的数组。也就是说， $guarded 从功能上将更像是一个「黑名单」。注意：你只能使用 $fillable 或 $guarded 二者中的一个，不可同时使用。下面这个例子中，price 属性之外的所有属性都可以进行批量赋值：
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 不可批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

如果你想让所有属性都可以批量赋值， 你可以将 $guarded 定义成一个空数组：
```php
/**
 * 不可批量赋值的属性。
 *
 * @var array
 */
protected $guarded = [];
```

## 其他创建方法
firstOrCreate/ firstOrNew
这里有两个你可能用来批量赋值的方法： firstOrCreate 和 firstOrNew。 firstOrCreate 方法会通过给定的键 / 值对来匹配数据库中的数据。如果在数据库中找不到模型，则将插入一条记录，其中包含第一个参数的属性以及可选的第二个参数的属性。

firstOrNew 方法像 firstOrCreate 方法一样尝试通过给定的属性查找数据库中的记录。不过，如果 firstOrNew 方法找不到对应的模型，会返回一个新的模型实例。注意 firstOrNew 返回的模型实例尚未保存到数据库中，你需要手动调用 save 方法来保存：

```php
// 通过 name 检索航班，不存在则创建...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// 通过 name 检索航班，或使用 name 和 delayed 属性和 arrival_time 属性创建...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// 通过 name 检索航班，不存在则创建一个实例...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// 通过 name 检索航班，或使用 name 和 delayed 属性和 arrival_time 属性创建一个模型实例...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

你还可能遇到希望更新现有模型或在不存在的情况下则创建新的模型的情景。 updateOrCreate 方法来一步实现。 类似于 firstOrCreate 方法，updateOrCreate 持久化模型，因此无需调用 save()：
```php
// 如果有从奥克兰到圣地亚哥的航班，则价格定为99美元。
// 如果没匹配到存在的模型，则创建一个。
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## 删除模型

可以在模型实例上调用 delete 方法来删除实例：
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## 通过主键删除模型
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## 通过查询删除模型
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## 复制模型
您可以使用 replicate 方法复制一个新的未保存到数据库的实例， 当模型实例共享许多相同的属性时，这个方法非常好用。
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

## 模型比较
有时可能需要判断两个模型是否「相同」。is 方法可以用来快速校验两个模型是否拥有相同的主键、表和数据库连接：
```php
if ($post->is($anotherPost)) {
    //
}
```


## 模型观察者
使用参考[Laravel 中的模型事件与 Observer
](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

注意：Eloquent ORM 要支持模型观察者需要额外导入composer require "illuminate/events"

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

## 事务
参见[数据库事务](../others/transaction.md)
