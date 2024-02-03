# 쿼리 빌더
## 모든 행 가져오기
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

## 특정 열 가져오기
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## 한 행 가져오기
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## 한 열 가져오기
```php
$titles = Db::table('roles')->pluck('title');
```
id 필드를 색인으로 지정
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## 개별 값(필드) 가져오기
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## 중복 제거
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## 결과 분할
수천 개 이상의 데이터베이스 레코드를 처리해야 하는 경우 해당 데이터를 한꺼번에 가져오는 것은 시간이 많이 걸리며 메모리 초과로 이어질 수 있습니다. 이러한 경우 `chunkById` 메소드를 사용해 일부 결과 세트를 일괄로 가져 와 클로저 함수에 전달할 수 있습니다. 예를 들어 전체 'users' 테이블 데이터를 100 개의 레코드로 자르고 처리 할 수 있습니다.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
`false`를 반환하여 클로저 내에서 결과 분할을 중단할 수 있습니다.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // 결과 처리...

    return false;
});
```

> 참고: 삭제 작업을 콜백 내에서 수행하지 마십시오. 그렇게 하면 결과 세트에 포함되지 않을 수 있습니다.

## 집합

쿼리 빌더는 count, max, min, avg, sum 등과 같은 다양한 집계 메서드를 제공합니다.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## 레코드 존재 여부 확인
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## 원시 표현식
프로토 타입
```php
selectRaw($expression, $bindings = [])
```
가끔 쿼리 내에서 원시 표현식을 사용해야 할 수 있습니다. `selectRaw()`를 사용하여 원시 표현식을 생성할 수 있습니다.
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

마찬가지로 `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()` 원시 표현식 메소드도 제공됩니다.


`Db::raw($value)`도 원시 표현식을 생성하는 데 사용되지만 바인딩 매개변수 기능이 없으며 사용할 때 SQL 인젝션에 주의해야 합니다.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## 조인 구문
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

## Union 구문
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Where 구문
프로토 타입
```php
where($column, $operator = null, $value = null)
```
첫 번째 매개변수는 열 이름이고, 두 번째 매개변수는 데이터베이스 시스템에서 지원하는 임의의 연산자이며, 세 번째 매개변수는 해당 열을 비교할 값입니다.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// 연산자가 동일할 때는 단축하여 사용할 수 있습니다. 이 경우 위와 동일한 효과를 내지만 다음과 같이 작성 가능합니다.
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

where 함수로 조건 배열을 전달할 수도 있습니다:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

orWhere 메소드는 where 메소드와 동일한 매개변수를 수신합니다:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

orWhere 메소드에 클로저를 첫 번째 매개변수로 전달할 수 있습니다:
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

whereBetween / orWhereBetween 메소드는 필드 값이 주어진 두 값 사이에 있는지 확인합니다:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween 메소드는 필드 값이 주어진 두 값 사이에 없는지 확인합니다:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn 메소드는 필드 값이 지정된 배열에 있어야 함을 확인합니다:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull 메소드는 지정된 필드가 NULL이어야 함을 확인합니다:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull 메소드는 지정된 필드가 NULL이 아니어야 함을 확인합니다:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime 메소드는 필드 값이 지정된 날짜와 비교됩니다:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn 메소드는 두 필드 값이 같은지 확인합니다:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// 비교 연산자를 전달할 수도 있습니다
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn 메소드에 배열을 전달할 수도 있습니다
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

파라미터 그룹
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

## 무작위 정렬
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> 무작위 정렬은 서버 성능에 큰 영향을 미치므로 권장하지 않습니다

## 그룹화 / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// groupBy 메소드에 여러 매개변수를 전달할 수 있습니다
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

## 삽입
단일 삽입
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
다중 삽입
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## 자동증가 ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> 참고: PostgreSQL을 사용할 때 insertGetId 메서드는 기본적으로 id를 자동 증가 필드로 취급합니다. 다른 "시퀀스"에서 ID를 가져 오려면 insertGetId 메서드의 두 번째 매개 변수로 필드 이름을 전달 할 수 있습니다.

## 갱신
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## 갱신 또는 삽입
가끔은 기존 레코드를 업데이트하거나 매칭 레코드가 없으면 새로 생성 할 수 있습니다:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert 메소드는 먼저 첫 번째 매개 변수의 키와 값 쌍을 사용하여 데이터베이스 레코드를 찾습니다. 레코드가 존재하는 경우 두 번째 매개 변수의 값으로 레코드를 업데이트하고, 찾을 수 없는 경우 두 배열의 결합으로 새 레코드를 삽입합니다.

## 자동증가 및 감소
이 두 가지 방법은 모두 변경할 열을 적어도 하나는 받습니다. 두 번째 매개 변수는 해당 열을 증가 또는 감소시킬 양을 제어하는 선택적 매개 변수입니다:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
작업 중에 갱신할 필드를 지정할 수도 있습니다:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## 삭제
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
테이블을 지우고 싶을 경우, 모든 행을 지우고 자동 증가 ID를 0으로 재설정하는 truncate 메서드를 사용할 수 있습니다:
```php
Db::table('users')->truncate();
```

## 비관적 잠금
쿼리 빌더에는 "비관적 잠금"을 구현할 수있는 일부 기능이 포함되어 있습니다. 조회 명령어에서 "공유 잠금"을 구현하려면 sharedLock 메서드를 사용할 수 있습니다. 공유 잠금은 트랜잭션이 커밋 될 때까지 선택한 데이터 행이 변경되지 않도록합니다:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
또는 lockForUpdate 메서드를 사용할 수도 있습니다. "갱신" 잠금을 사용하면 다른 공유 잠금에 의한 행의 수정 또는 선택이 방지됩니다:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## 디버깅
dd 또는 dump 메서드를 사용하여 쿼리 결과 또는 SQL 문을 출력할 수 있습니다. dd 메서드를 사용하면 디버그 정보를 표시하고 요청의 실행을 멈출 수 있습니다. dump 메서드도 디버그 정보를 표시하지만 요청을 중지하지는 않습니다:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **주의**
> 디버깅을 위해서는 `symfony/var-dumper`가 설치되어 있어야 합니다. 설치 명령은 `composer require symfony/var-dumper` 입니다.
