## Stomp 대기열

Stomp은 간단한(스트림) 텍스트 지향 메시징 프로토콜로 상호 운용 가능한 연결 형식을 제공하여 STOMP 클라이언트가 임의의 STOMP 메시징 브로커(Broker)와 상호 작용할 수 있도록 합니다. [workerman/stomp](https://github.com/walkor/stomp)은 Stomp 클라이언트를 구현하여 주로 RabbitMQ, Apollo, ActiveMQ 등 메시지 대기열 시나리오에 사용됩니다.

## 설치
`composer require webman/stomp`

## 구성
구성 파일은 `config/plugin/webman/stomp`에 위치합니다.

## 메시지 전달
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // 대기열
        $queue = 'examples';
        // 데이터 (배열을 전달할 경우 직접 직렬화해야 합니다. 예: json_encode, serialize 등)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // 전달 수행
        Client::send($queue, $data);

        return response('redis queue test');
    }

}
```
> 다른 프로젝트와의 호환성을 위해 Stomp 구성 요소는 자동으로 직렬화 및 역직렬화 기능을 제공하지 않습니다. 배열 데이터를 전달하는 경우 직접 직렬화하고, 소비할 때 직접 역직렬화해야 합니다.

## 메시지 소비
`app/queue/stomp/MyMailSend.php`를 새로 만듭니다 (클래스 이름은 임의로 지정하고 PSR4 규칙을 준수하면 됩니다).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // 대기열 이름
    public $queue = 'examples';

    // 연결 이름, stomp.php에 있는 연결과 일치합니다
    public $connection = 'default';

    // 값이 client이면 $ack_resolver->ack()를 호출하여 서버에 소비를 성공했다고 알려야 합니다
    // 값이 auto이면 $ack_resolver->ack()를 호출할 필요가 없습니다
    public $ack = 'auto';

    // 소비
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // 데이터가 배열인 경우 직접 역직렬화해야 합니다
        var_export(json_decode($data, true)); // ['to' => 'tom@gmail.com', 'content' => 'hello'] 출력
        // 서버에 소비를 성공했다고 알림
        $ack_resolver->ack(); // ack가 auto일 때는 이 호출을 생략할 수 있습니다
    }
}
```

# rabbitmq에서 stomp 프로토콜 사용하기
rabbitmq는 기본적으로 stomp 프로토콜을 사용하지 않으므로 다음 명령을 실행하여 활성화해야 합니다.
```shell
rabbitmq-plugins enable rabbitmq_stomp
```
활성화한 후 stomp의 기본 포트는 61613입니다.
