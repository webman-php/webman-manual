# 검증기
Composer에는 사용 가능한 많은 검증기가 있습니다. 예를 들어:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## 검증기 top-think/think-validate

### 설명
ThinkPHP 공식 검증기

### 프로젝트 주소
https://github.com/top-think/think-validate

### 설치
`composer require topthink/think-validate`

### 빠른 시작

**새로 만들기 `app/index/validate/User.php`**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];

    protected $message  =   [
        'name.require' => '이름은 필수입니다',
        'name.max'     => '이름은 25자를 초과할 수 없습니다',
        'age.number'   => '나이는 숫자여야 합니다',
        'age.between'  => '나이는 1에서 120 사이여야 합니다',
        'email'        => '이메일 형식이 올바르지 않습니다',    
    ];

}
```
  
**사용**
```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

<a name="respect-validation"></a>
# 검증기 workerman/validation

### 설명

https://github.com/Respect/Validation의 한국어 버전 프로젝트입니다.

### 프로젝트 주소

https://github.com/walkor/validation

### 설치

```php
composer require workerman/validation
```

### 빠른 시작

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        $data = v::input($request->post(), [
            'nickname' => v::length(1, 64)->setName('별명'),
            'username' => v::alnum()->length(5, 64)->setName('사용자 이름'),
            'password' => v::length(5, 64)->setName('암호')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
**JQuery를 통해 접근**

```js
$.ajax({
    url : 'http://127.0.0.1:8787',
    type : "post",
    dataType:'json',
    data : {nickname:'톰', username:'tom cat', password: '123456'}
});
```

결과:

`{"code":500,"msg":"사용자 이름은 영문자(a-z)와 숫자(0-9)만을 포함해야 합니다"}`

설명:

`v::input(array $input, array $rules)`는 데이터를 검증하고 수집하는 데 사용되며, 데이터가 유효하지 않은 경우 `Respect\Validation\Exceptions\ValidationException` 예외가 발생하고, 성공적으로 검증되면 유효성이 검증된 데이터(배열)가 반환됩니다.

업무 코드가 유효성 예외를 처리하지 않으면 webman 프레임워크가 자동으로 검증 예외를 처리하여 HTTP 요청 헤더에 따라 JSON 데이터(예: `{"code":500, "msg":"xxx"}`) 또는 일반 예외 페이지를 반환합니다. 반환 형식이 비즈니스 요구에 부합하지 않는 경우 개발자는 직접 `ValidationException` 예외를 처리하고 필요한 데이터를 반환할 수 있습니다. 다음 예와 유사합니다:

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class IndexController
{
    public function index(Request $request)
    {
        try {
            $data = v::input($request->post(), [
                'username' => v::alnum()->length(5, 64)->setName('사용자 이름'),
                'password' => v::length(5, 64)->setName('암호')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Validator 기능 가이드

```php
use Respect\Validation\Validator as v;

// 단일 규칙 검증
$number = 123;
v::numericVal()->validate($number); // true

// 여러 규칙 체인식 검증
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 첫 번째 검증 실패 이유 가져오기
try {
    $usernameValidator->setName('사용자 이름')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // 사용자 이름은 영문자(a-z)와 숫자(0-9)만을 포함해야 합니다
}

// 모든 검증 실패 이유 가져오기
try {
    $usernameValidator->setName('사용자 이름')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 다음과 같이 출력됩니다
    // -  사용자 이름은 영문자(a-z)와 숫자(0-9)만을 포함해야 합니다
    //     - 사용자 이름은 공백을 포함해서는 안됩니다

    var_export($exception->getMessages());
    // 다음과 같이 출력됩니다
    // array (
    //   'alnum' => '사용자 이름은 영문자(a-z)와 숫자(0-9)만을 포함해야 합니다',
    //   'noWhitespace' => '사용자 이름은 공백을 포함해서는 안됩니다',
    // )
}

// 사용자 정의 오류 메시지
try {
    $usernameValidator->setName('사용자 이름')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => '사용자 이름은 영문자와 숫자만을 포함해야 합니다',
        'noWhitespace' => '사용자 이름에 공백을 포함해서는 안됩니다',
        'length' => '이 옵션은 규칙이 일치하므로 표시되지 않습니다'
    ]);
    // 다음과 같이 출력됩니다
    // array(
    //    'alnum' => '사용자 이름은 영문자와 숫자만을 포함해야 합니다',
    //    'noWhitespace' => '사용자 이름에 공백을 포함해서는 안됩니다'
    // )
}

// 객체 검증
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// 배열 검증
$data = [
    'parentKey' => [
        'field1' => 'value1',
        'field2' => 'value2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // check() 나 validate() 또는 사용할 수 있습니다.

// 선택적 검증
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 부정적인 규칙
v::not(v::intVal())->validate(10); // false
```

### Validator 메소드 `validate()` `check()` `assert()` 차이점

`validate()`는 부울 값을 반환하며 예외를 발생시키지 않습니다.

`check()`는 검증 실패시 예외를 발생시키며 `$exception->getMessage()`를 통해 첫 번째 검증 실패 이유를 얻을 수 있습니다.

`assert()`는 검증 실패시 예외를 발생시키며 `$exception->getFullMessage()`를 통해 모든 검증 실패 이유를 얻을 수 있습니다.


### 일반적인 검증 규칙 목록

`Alnum()` 영문자와 숫자만 포함하도록 검증

`Alpha()` 영문자만 포함하도록 검증

`ArrayType()` 배열 타입 검증

`Between(mixed $minimum, mixed $maximum)` 입력이 다른 두 값 사이에 있는지 확인합니다.

`BoolType()` 불리언 타입인지 확인합니다

`Contains(mixed $expectedValue)` 입력이 특정값을 포함하는지 확인합니다

`ContainsAny(array $needles)` 입력이 하나 이상의 지정된 값 중 하나를 포함하는지 확인합니다

`Digit()` 입력이 숫자만을 포함하는지 확인합니다

`Domain()` 유효한 도메인인지 확인합니다

`Email()` 유효한 이메일 주소인지 확인합니다

`Extension(string $extension)` 파일 확장자를 검증합니다

`FloatType()` 부동 소수점 숫자인지 확인합니다

`IntType()` 정수인지 확인합니다

`Ip()` IP 주소인지 확인합니다

`Json()` JSON 데이터인지 확인합니다

`Length(int $min, int $max)` 길이가 주어진 범위에 있는지 확인합니다

`LessThan(mixed $compareTo)` 길이가 주어진 값보다 작은지 확인합니다

`Lowercase()` 소문자 인지 확인합니다

`MacAddress()` MAC 주소인지 확인합니다

`NotEmpty()` 비어있지 않은지 확인합니다

`NullType()` null 값인지 확인합니다

`Number()` 숫자인지 확인합니다

`ObjectType()` 객체인지 확인합니다

`StringType()` 문자열 타입인지 확인합니다

`Url()` URL인지 확인합니다

더 많은 검증 규칙은 https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 를 참조하십시오.

### 더 많은 내용 

https://respect-validation.readthedocs.io/en/2.0/ 를 방문하세요.
