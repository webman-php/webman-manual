# 빠른 시작

webman 모델은 [Eloquent ORM](https://laravel.com/docs/7.x/eloquent)에 기반을 두고 있습니다. 각 데이터베이스 테이블은 해당 테이블과 상호 작용하는 "모델"을 가지고 있습니다. 모델을 사용하여 데이터베이스 테이블에서 데이터를 조회하거나 새 레코드를 삽입할 수 있습니다.

시작하기 전에 `config/database.php` 파일에서 데이터베이스 연결을 구성했는지 확인하십시오.

> 참고: Eloquent ORM이 모델 옵저버를 지원하려면 `composer require "illuminate/events"`를 추가해야 합니다. [예제](#모델-옵저버)

## 예제
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
     * 기본 키를 재정의합니다. 기본값은 id입니다.
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * 자동으로 타임스탬프를 유지할지 여부를 나타냅니다.
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## 테이블 이름
모델에 table 속성을 정의하여 사용자 정의 데이터 테이블을 지정할 수 있습니다:
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
Eloquent는 각 데이터 테이블에 id라는 기본 키 열이 있는 것으로 가정합니다. 이 가정을 재정의하려면 protected $primaryKey 속성을 정의할 수 있습니다:
```php
class User extends Model
{
    /**
     * 기본 키를 재정의합니다. 기본값은 id입니다.
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent는 기본 키가 자동으로 증가하는 정수 값이라고 가정합니다. 이는 기본적으로 기본 키를 int 유형으로 자동 변환한다는 것을 의미합니다. 자동 증가되지 않거나 숫자가 아닌 기본 키를 사용하려면 공개 $incrementing 속성을 false로 설정해야 합니다:
```php
class User extends Model
{
    /**
     * 모델의 기본 키가 자동으로 증가하는지를 나타냅니다.
     *
     * @var bool
     */
    public $incrementing = false;
}
```
如果您的主键不是整数，则需要将模型上受保护的 `$keyType` 属性设置为字符串：
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

## 타임스탬프
기본적으로 Eloquent는 데이터 테이블에 created_at 및 updated_at이 있다고 가정합니다. Eloquent가 이러한 두 열을 자동으로 관리하지 않으려면 모델의 timestamps 속성을 false로 설정하십시오:
```php
class User extends Model
{
    /**
     * 타임스탬프를 자동으로 유지할지 여부를 나타냅니다.
     *
     * @var bool
     */
    public $timestamps = false;
}
```

타임스탬프 형식을 사용자 정의해야 하는 경우 모델에서 $dateFormat 속성을 설정하십시오. 이 속성은 날짜 속성의 데이터베이스 저장 방식과 모델이 배열 또는 JSON으로 직렬화하는 방식을 결정합니다:
```php
class User extends Model
{
    /**
     * 타임스탬프 저장 형식
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

시간스탬프를 사용자 정의하여 저장할 필드 이름을 지정해야 하는 경우 모델에서 CREATED_AT 및 UPDATED_AT 상수 값을 설정할 수 있습니다:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## 데이터베이스 연결
기본적으로 Eloquent 모델은 애플리케이션에서 구성한 기본 데이터베이스 연결을 사용합니다. 모델에 다른 연결을 지정하려면 $connection 속성을 설정하십시오:
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
모델의 일부 속성에 기본값을 정의하려면 모델에 $attributes 속성을 정의할 수 있습니다:
```php
class User extends Model
{
    /**
     * 모델의 기본 속성 값.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## 모델 검색
모델과 연결된 데이터베이스 테이블을 만든 후, 데이터베이스에서 데이터를 조회할 수 있습니다. 각 Eloquent 모델을 강력한 쿼리 빌더로 상상하고 연결된 데이터 테이블을 빠르게 조회할 수 있습니다. 예:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> 팁: Eloquent 모델은 쿼리 빌더이기 때문에 가능한 모든 방법을 사용할 수 있도록 [쿼리 빌더](queries.md)에서 모든 메서드를 읽어야 합니다. 이러한 메서드를 Eloquent 쿼리에서 사용할 수 있습니다.

## 추가적인 제약 조건
Eloquent의 all 메서드는 모델의 모든 결과를 반환합니다. 각각의 Eloquent 모델이 쿼리 빌더이기 때문에 쿼리 조건을 추가한 다음 get 메서드를 사용하여 조회 결과를 얻을 수도 있습니다:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## 모델 다시 로드
fresh 및 refresh 메서드를 사용하여 모델을 다시로드할 수 있습니다. fresh 메서드는 데이터베이스에서 모델을 다시 조회합니다. 기존 모델 인스턴스에는 영향을 주지 않습니다:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh 메서드는 데이터베이스에서 새 데이터로 기존 모델을 다시로드합니다. 또한 로드된 관계도 다시로드됩니다:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## 컬렉션
Eloquent의 all 및 get 메서드는 여러 결과를 조회하여 `Illuminate\Database\Eloquent\Collection` 인스턴스를 반환합니다. `Collection` 클래스는 Eloquent 결과를 처리하기 위한 많은 보조 함수를 제공합니다:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## 커서 사용
cursor 메서드를 사용하여 데이터베이스를 반복하면서 조회할 수 있습니다. 이 메서드는 한 번의 쿼리만 실행합니다. 대량의 데이터를 처리할 때 cursor 메서드는 메모리 사용량을 크게 줄일 수 있습니다:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursor는 `Illuminate\Support\LazyCollection` 인스턴스를 반환합니다. [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections)은 Laravel 컬렉션에서 대부분의 컬렉션 메서드를 사용할 수 있으며 각 시점에 하나의 모델만 메모리로 로드됩니다:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```
## Selects 서브쿼리
Eloquent는 고급 서브쿼리 지원을 제공하여 관련된 테이블에서 정보를 단일 쿼리로 추출할 수 있습니다. 예를 들어, 목적지 테이블 destinations과 목적지로 가는 항공편 테이블 flights가 있다고 가정해 봅시다. flights 테이블에는 목적지에 도착하는 시간을 나타내는 arrival_at 필드가 있습니다.

서브쿼리 기능을 제공하는 select 및 addSelect 메서드를 사용하여 단일 문으로 모든 목적지 destinations와 각 목적지에 마지막 비행기가 도착하는 시간을 쿼리할 수 있습니다.
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## 서브쿼리 기반으로 정렬하기
또한, 쿼리 빌더의 orderBy 함수도 서브쿼리를 지원합니다. 이 기능을 사용하여 각 목적지로 가는 마지막 비행기의 도착 시간에 따라 모든 목적지를 정렬할 수 있습니다. 마찬가지로, 이것은 데이터베이스에 대해 단일 쿼리를 실행할 수 있습니다.
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## 개별 모델 / 컬렉션 검색
지정된 데이터 테이블에서 모든 레코드를 검색하는 것 외에도, find, first 또는 firstWhere 메서드를 사용하여 단일 레코드를 검색할 수 있습니다. 이러한 메서드는 모델 컬렉션 대신 단일 모델 인스턴스를 반환합니다.
```php
// 주요 키를 사용하여 모델을 찾습니다...
$flight = app\model\Flight::find(1);

// 제공된 쿼리 조건과 일치하는 첫 번째 모델을 찾습니다...
$flight = app\model\Flight::where('active', 1)->first();

// 제공된 쿼리 조건과 일치하는 첫 번째 모델을 빠르게 찾습니다...
$flight = app\model\Flight::firstWhere('active', 1);
```

또한, find 메서드에 주요 키 배열을 사용하여 호출하여 일치하는 레코드 컬렉션을 반환할 수 있습니다.
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

가끔은 첫 번째 결과를 찾지 못할 때 다른 작업을 수행하고 싶을 수 있습니다. firstOr 메서드는 결과를 찾을 경우 첫 번째 결과를 반환하고, 결과를 찾지 못한 경우 주어진 콜백을 실행합니다. 콜백의 반환 값은 firstOr 메서드의 반환 값으로 사용됩니다.
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOr 메서드는 또한 필드 배열을 사용하여 쿼리할 수 있습니다.
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "찾을 수 없음" 예외
모델을 찾을 수 없을 때 예외를 throw하고 싶을 때가 있습니다. 이는 컨트롤러 및 라우터에서 매우 유용합니다. findOrFail 및 firstOrFail 메서드는 질의의 첫 번째 결과를 검색하고, 결과를 찾지 못할 경우 Illuminate\Database\Eloquent\ModelNotFoundException 예외를 throw합니다.
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## 컬렉션 검색
쿼리 빌더가 제공하는 count, sum 및 max 메서드와 같은 기능을 사용하여 컬렉션을 조작할 수도 있습니다. 이러한 메서드는 모델 인스턴스 대신 적절한 스칼라 값만 반환합니다.
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## 삽입
데이터베이스에 레코드를 삽입하려면 새 모델 인스턴스를 만들고, 속성을 설정한 후 save 메서드를 호출해야 합니다.
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * 새로운 레코드를 사용자 테이블에 추가합니다
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // 요청 유효성 검사

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at 및 updated_at 타임스탬프는 (모델의 $timestamps 속성이 true 인 경우) 자동으로 설정되기 때문에 수동으로 할당할 필요가 없습니다.

## 업데이트
save 메서드를 사용하여 데이터베이스에 이미 존재하는 모델을 업데이트할 수도 있습니다. 모델을 업데이트하려면 먼저 해당 모델을 검색하고, 업데이트할 속성을 설정한 후 save 메서드를 호출해야 합니다. 마찬가지로 updated_at 타임스탬프는 자동으로 업데이트되므로 수동으로 할당할 필요가 없습니다.
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
Eloquent는 isDirty, isClean 및 wasChanged 메서드를 제공하여 모델의 내부 상태를 확인하고 속성이 초기로드된 후 어떻게 변경되었는지 확인할 수 있습니다.
isDirty 메서드는 모델이로드된 후 속성이 변경되었는지를 확인합니다. 특정 속성이 변경되었는지 확인하려면 특정한 속성 이름을 전달할 수 있습니다. isClean 메서드는 isDirty와 반대로 작동하며 선택적인 속성 매개변수를 허용합니다.
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

wasChanged 메서드는 현재 요청 주기에서 모델을 마지막으로 저장할 때 속성이 변경되었는지 확인합니다. 특정 속성이 변경되었는지 확인하려면 속성 이름을 전달할 수 있습니다.
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
# 일괄 할당

새 모델을 저장하는 데 create 메서드를 사용할 수도 있습니다. 이 방법은 모델 인스턴스를 반환합니다. 그러나 사용하기 전에 모델에서는 기본적으로 일괄 할당이 불가능하기 때문에 모델에 대해 채우거나 보호된 속성을 지정해야 합니다.

사용자가 요청을 통해 의도하지 않은 HTTP 매개변수를 전달하고 그 매개변수가 데이터베이스의 변경에 영향을 미치지 않아야 할 필드를 변경하면 일괄 할당 취약점이 발생할 수 있습니다. 예를 들어, 악의적인 사용자는 is_admin 매개변수를 HTTP 요청으로 전달하고 그것을 create 메서드에 전달하여 자신을 관리자로 업그레이드시킬 수 있습니다.

따라서 시작하기 전에 모델에서 어떤 속성이 일괄 할당될 수 있는지 정의해야합니다. 이를 $fillable 속성을 사용하여 구현할 수 있습니다. 예를들어, Flight 모델의 name 속성을 일괄 할당 할 수 있도록 정의합니다.

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 일괄 할당할 수 있는 속성들.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```

일괄 할당할 수 있는 속성을 설정한 후에는 create 메서드를 사용하여 새로운 데이터를 데이터베이스에 삽입할 수 있습니다. create 메서드는 저장된 모델 인스턴스를 반환합니다.

```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```

이미 모델 인스턴스가 있는 경우에는 fill 메서드에 배열을 전달하여 값 할당이 가능합니다.

```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable은 일종의 일괄 할당 "화이트리스트"로 볼 수 있으며, $guarded 속성을 사용하여 구현할 수도 있습니다. $guarded 속성에는 일괄 할당을 허용하지 않는 속성들이 포함됩니다. 즉, $guarded는 "블랙리스트"와 같은 기능을 합니다. 주의할 점은 $fillable 또는 $guarded 중 하나만 사용할 수 있으며 둘을 동시에 사용할 수 없습니다. 아래 예시에서는 price 속성을 제외한 모든 속성이 일괄 할당될 수 있도록 설정합니다.

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * 일괄 할당할 수 없는 속성들.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

만약 모든 속성을 일괄 할당할 수 있도록 하려면 $guarded를 빈 배열로 정의할 수 있습니다.

```php
/**
 * 일괄 할당할 수 없는 속성들.
 *
 * @var array
 */
protected $guarded = [];
```

## 다른 생성 메서드

firstOrCreate/ firstOrNew

여기에는 일괄 할당에 사용할 수 있는 두 가지 메서드가 있습니다: firstOrCreate 및 firstOrNew. firstOrCreate 메서드는 주어진 키/값 쌍을 사용하여 데이터베이스에서 데이터를 찾습니다. 데이터베이스에서 모델을 찾을 수 없는 경우 첫 번째 매개변수의 속성 및 선택적 두 번째 매개변수의 속성을 포함하는 레코드를 삽입합니다.

firstOrNew 메서드도 firstOrCreate 메서드와 마찬가지로 데이터베이스에서 주어진 속성을 사용하여 레코드를 찾으려 시도합니다. 그러나 firstOrNew 메서드는 해당 모델을 찾을 수 없는 경우 새 모델 인스턴스를 반환합니다. 주의할 점은 firstOrNew가 반환한 모델 인스턴스는 아직 데이터베이스에 저장되지 않으므로 수동으로 save 메서드를 호출하여 저장해야 합니다.

```php
// name으로 항공편 검색, 존재하지 않으면 생성...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// name으로 항공편 검색하거나 name과 delayed 및 arrival_time 속성과 함께 생성...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// name으로 항공편 검색, 존재하지 않으면 인스턴스 생성...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// name으로 항공편 검색하거나 name과 delayed 및 arrival_time 속성과 함께 모델 인스턴스 생성...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

기존 모델을 업데이트하거나 존재하지 않는 경우 새 모델을 만들고 싶을 때도 있습니다. 이를 한번에 할 수 있는 updateOrCreate 메서드도 있습니다. firstOrCreate 메서드와 유사하게 updateOrCreate는 모델을 지속시키므로 save()를 호출할 필요가 없습니다.

```php
// 오크랜드에서 산디에이고로 가는 항공편이 있는 경우 가격을 99달러로 설정합니다.
// 일치하는 모델이 없는 경우에는 생성합니다.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## 모델 삭제

모델 인스턴스에서 delete 메서드를 사용하여 모델을 삭제할 수 있습니다.

```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## 기본 키를 사용하여 모델 삭제

```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## 쿼리를 사용하여 모델 삭제

```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## 모델 복사

복제되지 않은 새로운 인스턴스를 만들기 위해 replicate 메서드를 사용할 수 있습니다. 모델 인스턴스들이 많은 공통 속성을 공유할 때 이 메서드는 매우 유용합니다.

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

때로는 두 모델이 "동일한"지 확인해야 할 수도 있습니다. is 메서드를 사용하면 두 모델이 동일한 기본 키, 테이블 및 데이터베이스 연결을 가지고 있는 지 빠르게 검증할 수 있습니다.

```php
if ($post->is($anotherPost)) {
    //
}
```

## 모델 옵저버

참조: [라라벨의 모델 이벤트와 옵저버](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

주의: Eloquent ORM은 모델 옵저버를 지원하기 위해 별도로 "illuminate/events"를 가져와야 합니다.

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
