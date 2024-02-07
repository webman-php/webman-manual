# 이벤트 처리
`webman/event`는 코드 침범 없이 비즈니스 로직을 실행할 때 사용되는 정교한 이벤트 메커니즘을 제공하여 비즈니스 모듈 간의 결합을 구현할 수 있습니다. 전형적인 시나리오로, 새로운 사용자가 등록되었을 때 `user.register`와 같은 사용자 정의 이벤트를 발행하면 각 모듈은 해당 이벤트를 수신하여 해당 비즈니스 로직을 실행할 수 있습니다.

## 설치
`composer require webman/event`

## 이벤트 구독
구독 이벤트를 통일하여 파일 `config/event.php`를 통해 구성합니다.
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...다른 이벤트 처리 함수...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...다른 이벤트 처리 함수...
    ]
];
```
**설명:**
- `user.register`, `user.logout` 등은 이벤트 이름으로 문자열 형식이며, 소문자로 작성하고 점(`.`)으로 구분하는 것이 좋습니다.
- 하나의 이벤트에 여러 이벤트 처리 함수를 연결하며, 호출 순서는 구성된 순서대로입니다.

## 이벤트 처리 함수
이벤트 처리 함수는 임의의 클래스 메서드, 함수, 클로저 함수일 수 있습니다.
예를 들어, 이벤트 처리 클래스 `app/event/User.php`를 생성합니다(디렉토리가 없는 경우 직접 생성하세요).
```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## 이벤트 발행
`Event::emit($event_name, $data);`를 사용하여 이벤트를 발행합니다. 예를 들어,
```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```

> **팁:**
> `Event::emit($event_name, $data);`의 매개변수 `$data`는 배열, 클래스 인스턴스, 문자열 등 임의의 데이터일 수 있습니다.

## 와일드카드 이벤트 수신
와일드카드 등록 수신을 사용하여 동일한 리스너에서 여러 이벤트를 처리할 수 있습니다. 예를 들어, `config/event.php`에 아래와 같이 구성합니다.
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
이벤트 처리 함수의 두 번째 매개변수 `$event_data`를 통해 특정 이벤트 이름을 얻을 수 있습니다.
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // user.register, user.logout 등의 구체적인 이벤트 이름
        var_export($user);
    }
}
```

## 이벤트 브로드캐스트 중지
이벤트 처리 함수에서 `false`를 반환하면 해당 이벤트 브로드캐스트가 중지됩니다.

## 클로저 함수로 이벤트 처리
이벤트 처리 함수는 클래스 메서드일 수도 있고, 아래와 같이 클로저 함수일 수도 있습니다.
```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

## 이벤트 및 리스너 보기
명령어 `php webman event:list`를 사용하여 프로젝트에 구성된 모든 이벤트 및 리스너를 확인할 수 있습니다.

## 유의사항
이벤트 처리는 비동기가 아니며, 느린 비즈니스에는 사용되지 않습니다. 느린 비즈니스는 [webman/redis-queue](https://www.workerman.net/plugin/12)와 같은 메시지 대기열을 사용해야 합니다.
