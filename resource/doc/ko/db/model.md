# 빠른 시작

webman 모델은 [Eloquent ORM](https://laravel.com/docs/7.x/eloquent)에 기반합니다. 각 데이터베이스 테이블에는 해당 테이블과 상호 작용하는 "모델"이 있습니다. 이 모델을 사용하여 데이터 테이블에서 데이터를 조회하고 새 레코드를 삽입할 수 있습니다.

시작하기 전에 `config/database.php`에서 데이터베이스 연결을 구성했는지 확인하십시오.

> 참고: Eloquent ORM은 모델 관찰자를 지원하기 위해 추가적으로 `composer require "illuminate/events"`를 가져와야 합니다. [예시](#모델-관찰자)

## 예시
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * 모델과 관련된 테이블 이름
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 기본 키를 재정의합니다. 기본적으로는 id입니다.
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * 자동으로 timestamp를 유지할 지 표시
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## 테이블 이름
모델에서 사용자 정의 데이터 테이블을 지정하려면 모델에서 `table` 속성을 정의할 수 있습니다:
```php
class User extends Model
{
    /**
     * 모델과 관련된 테이블 이름
     *
     * @var string
     */
    protected $table = 'user';
}
```

## 기본 키
Eloquent는 각 데이터 테이블에 id라는 기본 키 열이 있다고 가정합니다. 이 관습을 덮어쓰기 위해 보호된 `$primaryKey` 속성을 정의할 수 있습니다.
```php
class User extends Model
{
    /**
     * 기본 키 재정의, 기본값은 id입니다.
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent는 기본 키가 자동으로 증가하는 정수 값이라고 가정하므로 기본적으로 기본 키는 int 형으로 자동 변환됩니다. 증가하지 않거나 숫자가 아닌 기본 키를 사용하려면 공개된 `$incrementing` 속성을 `false`로 설정해야 합니다.
```php
class User extends Model
{
    /**
     * 모델 기본 키가 증가하는지 지시
     *
     * @var bool
     */
    public $incrementing = false;
}
```

기본 키가 정수가 아닌 경우 모델의 보호된 `$keyType` 속성을 `string`으로 설정해야 합니다:
```php
class User extends Model
{
    /**
     * 자동 증가 ID의 "유형".
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Timestamp
기본적으로 Eloquent는 데이터 테이블에 created_at 및 updated_at이 있는 것으로 예상합니다. Eloquent가 이 두 열을 자동으로 관리하지 않으려면 모델의 `$timestamps` 속성을 `false`로 설정하십시오:
```php
class User extends Model
{
    /**
     * timestamp를 자동으로 유지할 지 표시
     *
     * @var bool
     */
    public $timestamps = false;
}
```
원하는 경우 타임스탬프의 형식을 사용자 정의하려면 모델에 `$dateFormat` 속성을 설정하십시오. 이 속성은 데이터베이스에 저장되는 날짜 속성과 모델이 배열 또는 JSON으로 직렬화되는 형식을 결정합니다:
```php
class User extends Model
{
    /**
     * timestamp 저장 형식
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

타임스탬프를 사용자 정의 저장하려면 모델에 `CREATED_AT` 및 `UPDATED_AT` 상수의 값을 설정하십시오:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## 데이터베이스 연결
기본적으로 Eloquent 모델은 애플리케이션의 기본 데이터베이스 연결을 사용합니다. 모델에 다른 연결을 지정하려면 `$connection` 속성을 설정하십시오:
```php
class User extends Model
{
    /**
     * 모델의 연결 이름
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## 기본 속성 값
모델의 특정 속성에 기본값을 정의하려면 모델에 `$attributes` 속성을 정의하십시오:
```php
class User extends Model
{
    /**
     * 모델의 기본 속성 값
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## 모델 검색
모델과 관련된 데이터 테이블을 생성한 후 데이터베이스에서 데이터를 조회할 수 있습니다. 각 Eloquent 모델을 강력한 쿼리 빌더로 상상하여 연결된 데이터 테이블을 보다 빠르게 조회할 수 있습니다. 예를 들어:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> 팁: Eloquent 모델은 쿼리 빌더이므로 [쿼리 빌더](queries.md)에서 사용 가능한 모든 메서드를 확인하는 것이 좋습니다. Eloquent 쿼리에서 이러한 메서드를 사용할 수 있습니다.

## 추가 제약
Eloquent의 `all` 메서드는 모델의 모든 결과를 반환합니다. 각 Eloquent 모델이 쿼리 빌더 역할을 하므로 쿼리 조건을 추가한 다음 `get` 메서드로 쿼리 결과를 가져올 수도 있습니다:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## 모델 다시로드
`fresh` 및 `refresh` 메서드를 사용하여 모델을 다시로드할 수 있습니다. `fresh` 메서드는 데이터베이스에서 모델을 다시 가져옵니다. 기존 모델 인스턴스에는 영향을 미치지 않습니다:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

`refresh` 메서드는 데이터베이스에서 새 데이터로 기존 모델을 다시로드합니다. 또한로드된 관계도 다시로드됩니다:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## 컬렉션
Eloquent의 `all` 및 `get` 메서드는 여러 결과를 조회하여 `Illuminate\Database\Eloquent\Collection` 인스턴스를 반환합니다. `Collection` 클래스는 Eloquent 결과를 처리하기 위한 다양한 보조 함수를 제공합니다:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## 커서 사용
`cursor` 메서드를 사용하여 데이터베이스를 반복하는 커서를 사용할 수 있습니다. 대량의 데이터를 처리할 때 `cursor` 메서드는 메모리 사용량을 크게 줄일 수 있습니다:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

`cursor`는 `Illuminate\Support\LazyCollection` 인스턴스를 반환합니다. [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections)를 사용하면 Laravel 컬렉션 메서드를 사용할 수 있으며 각 시점마다 단일 모델만 메모리로 로드됩니다:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## 하위 쿼리
Eloquent는 고급 하위 쿼리 지원을 제공하며 관련 테이블에서 정보를 가져오는 단일 쿼리 문을 사용할 수 있습니다. 예를 들어, 목적지 테이블 destinations와 목적지로 가는 비행기 테이블 flights가 있다고 가정해보겠습니다. 비행기 테이블에는 도착 시간을 나타내는 arrival_at 필드가 있습니다.

하위 쿼리 기능을 사용하여 destinations 모두를 한 번의 문으로 조회할 수 있으며, 각 목적지에 대한 마지막 비행기의 이름을 포함합니다:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## 하위 쿼리를 기준으로 정렬
또한, 쿼리 빌더의 `orderBy` 함수는 하위 쿼리를 지원합니다. 이 기능을 사용하여 목적지에 대해 마지막 비행기 도착 시간에 따라 모든 목적지를 정렬할 수 있습니다. 마찬가지로 데이터베이스에 대한 단일 쿼리 만을 실행할 수 있습니다:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## 단일 모델 / 컬렉션 검색
모든 레코드를 조회하는 대신, `find`, `first`, 또는 `firstWhere` 메서드를 사용하여 단일 레코드를 조회할 수 있습니다. 이러한 메서드는 모델 모음이 아닌 단일 모델 인스턴스를 반환합니다:
```php
// 주요 키로 모델 찾기...
$flight = app\model\Flight::find(1);

// 쿼리 조건에 일치하는 첫 번째 모델 찾기...
$flight = app\model\Flight::where('active', 1)->first();

// 쿼리 조건에 일치하는 첫 번째 모델의 빠른 구현...
$flight = app\model\Flight::firstWhere('active', 1);
```

또한, `find` 메서드에 주요 키 배열을 매개변수로 전달하여 일치하는 레코드를 모델 모음 형태로 반환할 수 있습니다:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

가끔은 첫 번째 결과를 찾지 못할 때 대체 작업을 수행하길 원할 수 있습니다. `firstOr` 메서드는 결과가 있을 때 첫 번째 결과를 반환하고 결과가 없을 때 지정된 콜백을 실행합니다. 콜백의 반환 값은 `firstOr` 메서드의 반환 값으로 사용됩니다:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
`firstOr` 메서드는 또한 필드 배열에 대한 쿼리를 수행할 수 있습니다:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "찾을 수 없음" 예외
가끔은 모델을 찾을 수 없을 때 예외를 발생시키길 원할 수 있습니다. 이는 컨트롤러 및 라우트에서 유용합니다. `findOrFail` 및 `firstOrFail` 메서드는 쿼리의 첫 번째 결과를 검색하고, 결과를 찾을 수 없으면 `Illuminate\Database\Eloquent\ModelNotFoundException` 예외가 발생합니다:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```
## 컬렉션 검색
쿼리 빌더에서 제공하는 count, sum, max 등의 컬렉션 함수를 사용하여 컬렉션을 조작할 수 있습니다. 이러한 메소드는 모델 인스턴스를 반환하는 것이 아니라 적절한 스칼라 값만 반환합니다.
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## 삽입
레코드를 새로 추가하려면 먼저 새 모델 인스턴스를 만들고 해당 인스턴스에 속성을 설정한 뒤 save 메서드를 호출하면 됩니다.
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * 사용자 테이블에 새 레코드 추가
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // 요청 검증

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```
created_at 및 updated_at 타임스탬프는 (모델의 $timestamps 속성이 true일 때) 자동으로 설정되므로 수동으로 할당할 필요가 없습니다.


## 업데이트
save 메서드를 사용하여 데이터베이스에 이미 있는 모델을 업데이트할 수도 있습니다. 모델을 업데이트하려면 먼저 해당 모델을 검색하고 업데이트할 속성을 설정한 다음 save 메서드를 호출하면 됩니다. 마찬가지로 updated_at 타임스탬프는 자동으로 업데이트되므로 수동으로 할당할 필요가 없습니다.
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## 일괄 업데이트
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## 속성 변경 확인
Eloquent는 isDirty, isClean 및 wasChanged 메서드를 제공하여 모델의 내부 상태를 확인하고 속성이 초기로드 이후에 어떻게 변경되었는지 확인할 수 있습니다. isDirty 메서드는 모델을로드한 이후에 어떤 속성이 변경되었는지를 확인합니다. 특정 속성이 변경되었는지 확인하려면 특정한 속성 이름을 전달할 수 있습니다. isClean 메서드는 isDirty와 반대 기능을 합니다.
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
wasChanged 메서드는 현재 요청주기에서 모델을 마지막으로 저장할 때 어떤 속성이 변경되었는지 확인합니다. 특정 속성이 변경되었는지 확인하려면 해당 속성 이름을 전달할 수 있습니다.
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

## 일괄 할당
create 메서드를 사용하여 새 모델을 저장할 수도 있습니다.
그러나 이전에 사용하기 전에 모델에 대해 fillable 또는 guarded 속성을 지정해야 합니다. 모든 Eloquent 모델은 기본적으로 일괄할당을 허용하지 않기 때문입니다.

사용자가 의도하지 않은 HTTP 매개변수를 전달하고 데이터베이스에서 변경할 필요가 없는 필드가 변경되는 이러한 일괄 할당 취약점이 발생할 수 있습니다. 예를 들어 악의적인 사용자가 is_admin 매개변수를 HTTP 요청에 전달하고 이를 create 메서드에 전달하면 사용자는 관리자로 업그레이드될 수 있습니다.

따라서 시작하기 전에 일괄할당이 가능한 속성을 모델에 정의해야 합니다. $fillable 속성을 사용하여 Flight 모델의 name 속성이 일괄할당될 수 있도록 할 수 있습니다.
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 일괄할당할 수 있는 속성.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
일괄할당할 속성을 설정한 후 create 메서드를 사용하여 새로운 데이터를 데이터베이스에 추가할 수 있습니다. create 메서드는 저장된 모델 인스턴스를 반환합니다.
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
이미 모델 인스턴스가 있는 경우 fill 메서드에 배열을 전달하여 값을 할당할 수 있습니다.
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable은 일괄할당의 "화이트리스트"로 생각할 수 있으며 $guarded 속성을 사용할 수도 있습니다. $guarded 속성에는 일괄할당을 허용하지 않는 배열이 포함됩니다. 즉, 기능상 $guarded는 "블랙리스트"와 유사합니다. 참고: $fillable 또는 $guarded 둘 중 하나만 사용할 수 있으며 동시에 사용할 수 없습니다. 아래 예제에서 price 속성을 제외한 모든 속성에 대해 일괄할당이 가능합니다.
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 일괄할당할 수 없는 속성.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
모든 속성이 일괄할당되도록 하려면 $guarded를 빈 배열로 정의할 수 있습니다.
```php
/**
 * 일괄할당할 수 없는 속성.
 *
 * @var array
 */
protected $guarded = [];
```

## 다른 생성 메서드
firstOrCreate/firstOrNew
여기에는 일괄할당에 사용될 수 있는 두 가지 메서드, firstOrCreate 및 firstOrNew이 있습니다. firstOrCreate 메서드는 주어진 키/값 쌍으로 데이터베이스에서 모델을 찾습니다. 데이터베이스에서 모델을 찾을 수 없는 경우 첫 번째 인수의 속성과 선택적 두 번째 인수의 속성을 포함한 레코드를 삽입합니다.
firstOrNew 메서드는 firstOrCreate 메서드처럼 주어진 속성을 사용하여 데이터베이스에서 레코드를 찾으려고 시도합니다. 그러나 firstOrNew 메서드는 해당 모델을 찾을 수 없을 때 새로운 모델 인스턴스를 반환합니다. 주의: firstOrNew로 반환된 모델 인스턴스는 아직 데이터베이스에 저장되지 않으므로 수동으로 save 메서드를 호출해야 합니다.
```php
// name을 통해 비행기 검색하고 없으면 생성합니다.
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// name으로 비행기를 찾거나 name과 delayed 속성 및 arrival_time 속성을 사용하여 생성합니다.
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// name을 통해 비행기를 찾거나 name 및 delayed 속성과 arrival_time 속성을 사용하여 모델 인스턴스를 생성합니다.
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// name으로 비행기를 찾거나 name 및 delayed 속성과 arrival_time 속성을 사용하여 모델 인스턴스를 생성합니다.
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

기존 모델을 업데이트하거나 특정 모델이 없을 경우 새로운 모델을 생성하는 상황이 발생할 수도 있습니다. 이를 한 번에 수행하는 updateOrCreate 메서드도 있습니다. firstOrCreate 메서드와 유사하지만 updateOrCreate는 모델을 영구적으로 유지하므로 save()를 호출할 필요가 없습니다.
```php
// 오클랜드에서 샌디에고로 가는 비행기가 있으면 가격을 99달러로 설정합니다.
// 일치하는 모델을 찾지 못하면 새로 생성합니다.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## 모델 삭제
모델 인스턴스에서 delete 메서드를 호출하여 해당 인스턴스를 삭제할 수 있습니다.
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## 기본 키로 모델 삭제
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## 쿼리로 모델 삭제
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## 모델 복제
미디선트된 새 인스턴스를 복제하려면 replicate 메서드를 사용할 수 있습니다. 이 메서드는 많은 공통된 속성을 공유하는 모델 인스턴스를 생성할 때 유용합니다.
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

## 모델 비교
때때로 두 모델이 "동일"한지 확인해야 할 수 있습니다. is 메서드를 사용하여 빠르게 두 모델이 동일한 주요 키, 테이블 및 데이터베이스 포트를 가지고 있는지 확인할 수 있습니다.
```php
if ($post->is($anotherPost)) {
    //
}
```

## 모델 옵서버
[Laravel 모델 이벤트 및 옵서버](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)를 참조하십시오.

참고: Eloquent ORM은 모델 옵서버를 지원하려면 추가적으로 composer require "illuminate/events"를 가져와야 합니다.
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
