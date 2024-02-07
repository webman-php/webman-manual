# 쿼리 빌더
## 모든 행 가져 오기
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

## 특정 열 가져 오기
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## 한 행 가져 오기
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## 한 열 가져 오기
```php
$titles = Db::table('roles')->pluck('title');
```
지정된 id 필드 값을 인덱스로 사용
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## 개별 값(필드) 가져 오기
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## 중복 제거
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## 분할 된 결과
수천 개 이상의 데이터베이스 레코드를 처리해야하는 경우 이러한 데이터를 한 번에 로드하면 시간이 많이 걸리고 메모리 초과로 이어질 수 있습니다.이때 chunkById 메서드를 사용하여 결과 세트를 작은 조각으로 가져와 클로저 함수에 전달할 수 있습니다. 예를 들어 전체 users 테이블 데이터를 100 개의 레코드를 한 번에 처리하는 작은 조각으로 나눌 수 있습니다.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
콜백에서 false를 반환하여 분할 결과를 계속 가져 오지 못하도록 할 수 있습니다.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // 레코드 처리...

    return false;
});
```

> 참고: 콜백에서 데이터를 삭제하지 마십시오. 그러면 결과 집합에 포함되지 않을 수 있습니다.

## 집계

쿼리 빌더는 count, max, min, avg, sum 등과 같은 다양한 집계 메서드도 제공합니다.
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

## Native 표현식
원형
```php
selectRaw($expression, $bindings = [])
```
가끔 쿼리에서 Native 표현식을 사용해야 할 때가 있습니다. `selectRaw()`를 사용하여 Native 표현식을 생성할 수 있습니다.
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

동일하게, `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` 에 대한 Native 표현식 메서드도 제공됩니다.

`Db::raw($value)`도 Native 표현식을 생성하는 데 사용됩니다. 그러나 이것은 매개 변수를 바인딩하는 기능이 없으므로 SQL 인젝션에 주의해야 합니다.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```
## Join 문
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

## Union 문
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## WHERE 문

prototype
```php
where($column, $operator = null, $value = null)
```
첫 번째 매개변수는 열 이름이고, 두 번째 매개변수는 데이터베이스 시스템이 지원하는 연산자이며, 세 번째 매개변수는 해당 열의 비교값입니다.

```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// 연산자가 등호일 때는 생략 가능하므로, 이 문장은 위의 문장과 동일한 작용을 합니다
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

WHERE 함수에 조건 배열을 전달할 수도 있습니다.
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

orWhere 메서드와 where 메서드는 매개변수를 동일하게 받습니다.
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

첫 번째 매개변수로 클로저를 orWhere 메서드에 전달할 수 있습니다.
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

whereBetween / orWhereBetween 메서드는 필드 값이 주어진 두 값 사이에 있는지 확인합니다.
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween 메서드는 필드 값이 주어진 두 값 사이에 없는지 확인합니다.
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn 메서드는 필드 값이 지정된 배열에 존재해야 하는지 확인합니다.
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull 메서드는 지정된 필드가 NULL이어야 하는지 확인합니다.
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull 메서드는 지정된 필드가 NULL이 아니어야 하는지 확인합니다.
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime 메서드는 필드 값과 주어진 날짜를 비교하는 데 사용됩니다.
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn 메서드는 두 개의 필드 값이 동일한지 비교합니다.
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// 비교 연산자도 전달할 수 있습니다
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn 메서드에 배열도 전달할 수 있습니다
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();
```

매개변수 그룹화
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
> 무작위 정렬은 서버 성능에 매우 큰 영향을 미치므로 권장하지 않습니다

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// groupBy 메서드에 여러 매개변수를 전달할 수 있습니다
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
여러 개의 자료를 삽입
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## 자동 ID 증가
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> 주의: PostgreSQL을 사용하는 경우, insertGetId 메서드는 기본적으로 id를 자동 증가 필드의 이름으로 설정합니다. 다른 "시퀀스"에서 ID를 가져오려면 insertGetId 메서드에 두 번째 매개변수로 필드 이름을 전달할 수 있습니다.

## 업데이트
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## 업데이트 또는 삽입
때로는 기존 레코드를 업데이트하거나 일치하는 레코드가 없으면 생성하고 싶을 수 있습니다.
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert 메서드는 먼저 첫 번째 매개변수의 키와 값 쌍을 사용하여 일치하는 데이터베이스 레코드를 찾으려고 시도합니다. 레코드가 있으면 두 번째 매개변수의 값을 사용하여 레코드를 업데이트합니다. 레코드를 찾을 수 없으면 두 배열의 집합으로 새 레코드를 삽입합니다.

## 증가 및 감소
이 두 가지 방법은 모두 적어도 하나의 매개변수를 취합니다. 수정할 열입니다. 두 번째 매개변수는 선택 사항으로 열을 증가 또는 감소시킬 양을 제어합니다.
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
작업 중에 업데이트할 필드를 지정할 수도 있습니다.
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```
## 삭제
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
테이블을 비우려면 truncate 메서드를 사용하여 모든 행을 삭제하고 자동 증가 ID를 0으로 재설정할 수 있습니다:
```php
Db::table('users')->truncate();
```

## 비관적 잠금
쿼리 빌더에는 "비관적 잠금"을 구현하는 데 도움이 되는 몇 가지 기능도 포함되어 있습니다. "공유 잠금"을 쿼리에 적용하려면 sharedLock 메서드를 사용할 수 있습니다. 공유 잠금은 트랜잭션이 커밋될 때까지 선택된 데이터 행이 변경되지 않도록 합니다:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
또는 lockForUpdate 메서드를 사용할 수도 있습니다. "업데이트" 잠금을 사용하면 다른 공유 잠금으로 행을 수정하거나 선택하는 것을 방지할 수 있습니다:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## 디버깅
dd 또는 dump 메서드를 사용하여 쿼리 결과 또는 SQL 문을 출력할 수 있습니다. dd 메서드를 사용하면 디버그 정보를 표시하고 요청 실행을 중지합니다. dump 메서드도 디버그 정보를 표시하지만 요청 실행을 중지하지는 않습니다:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **참고**
> 디버깅을 위해서는 `symfony/var-dumper`를 설치해야 합니다. 설치 명령어는 `composer require symfony/var-dumper`입니다.
