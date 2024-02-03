# 사용자 정의 프로세스

웹맨에서는 Workerman과 마찬가지로 사용자 정의 리스너 또는 프로세스를 만들 수 있습니다.

> **참고**
> Windows 사용자는 웹맨을 시작하려면 `php windows.php`를 사용하여 웹맨을 시작해야합니다.

## 사용자 정의 HTTP 서비스
가끔 특별한 요구 사항이 있어서 웹맨 HTTP 서비스의 내부 코드를 변경해야 하는 경우, 사용자 정의 프로세스를 사용하여 구현할 수 있습니다.

예를 들어, `app\Server.php`를 새로 만듭니다.

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // 여기에는 Webman\App에서 메서드를 재작성합니다.
}
```

`config/process.php`에 다음과 같은 구성을 추가합니다.

```php
use Workerman\Worker;

return [
    // ... 다른 구성 내용은 생략...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // 프로세스 수
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // 요청 클래스 설정
            'logger' => \support\Log::channel('default'), // 로그 인스턴스
            'app_path' => app_path(), // 앱 디렉토리 위치
            'public_path' => public_path() // 퍼블릭 디렉토리 위치
        ]
    ]
];
```

> **팁**
> 웹맨의 기본 HTTP 프로세스를 중지하려면 config/server.php에서 `listen=>''`로 설정합니다.

## 사용자 정의 웹소켓 리스너 예시

`app/Pusher.php`를 새로 만듭니다.

```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> 참고: 모든 onXXX 속성은 public으로 선언되어야 합니다.

`config/process.php`에 다음과 같은 구성을 추가합니다.

```php
return [
    // ... 다른 프로세스 구성은 생략...
    
    // websocket_test는 프로세스 이름입니다.
    'websocket_test' => [
        // 여기에 프로세스 클래스를 지정합니다. 위에서 정의한 Pusher 클래스입니다.
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## 사용자 정의 리스닝이 아닌 프로세스 예시

`app/TaskTest.php`를 새로 만듭니다.

```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // 10초마다 데이터베이스에서 새 사용자 등록을 확인합니다
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php`에 다음과 같은 구성을 추가합니다.

```php
return [
    // ... 다른 프로세스 구성은 생략...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> 참고: listen을 생략하면 어떤 포트도 리스닝하지 않습니다. count를 생략하면 기본 프로세스 수는 1입니다.

## 구성 파일 설명

프로세스의 완전한 구성은 다음과 같습니다.

```php
return [
    // ... 
    
    // websocket_test는 프로세스 이름입니다.
    'websocket_test' => [
        // 여기에 프로세스 클래스를 지정합니다
        'handler' => app\Pusher::class,
        // 리스닝 프로토콜, IP 및 포트 (선택 사항)
        'listen'  => 'websocket://0.0.0.0:8888',
        // 프로세스 수 (선택 사항, 기본값 1)
        'count'   => 2,
        // 프로세스를 실행하는 사용자 (선택 사항, 기본값 현재 사용자)
        'user'    => '',
        // 프로세스를 실행하는 사용자 그룹 (선택 사항, 기본값 현재 사용자 그룹)
        'group'   => '',
        // 현재 프로세스가 reload를 지원하는지 여부 (선택 사항, 기본값 true)
        'reloadable' => true,
        // reusePort 사용 여부 (선택 사항, PHP >= 7.0에서 기본값은 true입니다)
        'reusePort'  => true,
        // transport (선택 사항, SSL을 활성화해야 하는 경우 'ssl'로 설정, 기본값은 'tcp'입니다)
        'transport'  => 'tcp',
        // context (선택 사항, transport가 SSL인 경우 인증서 경로를 전달해야 합니다)
        'context'    => [], 
        // 프로세스 클래스의 생성자 매개변수, 여기서는 process\Pusher::class 클래스의 생성자 매개변수입니다 (선택 사항)
        'constructor' => [],
    ],
];
```

## 요약
웹맨의 사용자 정의 프로세스는 실제로 Workerman을 간단하게 캡슐화한 것으로 구성과 비즈니스를 분리하고, Workerman의 `onXXX` 콜백을 클래스 메소드로 구현하는 방식으로 동작합니다. 다른 사용법은 완전히 Workerman과 동일합니다.
