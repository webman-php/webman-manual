# 느린 비즈니스 처리

가끔은 느린 비즈니스를 처리해야 할 때가 있습니다. 웹맨의 다른 요청 처리에 영향을 미치지 않도록하기 위해 이러한 비즈니스는 상황에 맞게 다양한 처리 방법을 사용할 수 있습니다.

## 메시지 큐 사용
참조 : [레디스 대기열](../queue/redis.md) [stomp 대기열](../queue/stomp.md)

### 장점
급격한 대규모 비즈니스 처리 요청에 대응할 수 있습니다.

### 단점
클라이언트에게 직접 결과를 반환할 수 없습니다. 결과를 전달해야하는 경우 다른 서비스와 조합해야 합니다. 예를 들어 [webman/push](https://www.workerman.net/plugin/2)를 사용하여 처리 결과를 푸시해야 합니다.

## HTTP 포트 추가

> **주의**
> 이 기능은 webman-framework>=1.4 이상이 필요합니다.

둔한 요청을 처리하기 위해 HTTP 포트를 추가하여 이러한 둔한 요청은 특정 프로세스 그룹을 통해 결과를 처리한 후 직접 클라이언트에게 반환합니다.

### 장점
데이터를 직접 클라이언트에게 반환할 수 있습니다.

### 단점
급격한 대규모 요청에 대응할 수 없습니다.

### 구현 단계
`config/process.php`에 다음 구성을 추가합니다.
```php
return [
    // ... 다른 구성은 생략 ...

    'task' => [
        'handler' => \Webman\App::class,
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

이렇게 하면 느린 인터페이스는 `http://127.0.0.1:8686/`를 통해 이러한 프로세스 그룹을 통해 다른 프로세스의 비즈니스 처리에 영향을 주지 않습니다.

프론트엔드가 포트의 차이를 느끼지 못하게하기 위해 nginx에 8686 포트로 프록시를 추가할 수 있습니다. 느린 인터페이스 요청 경로가 모두 `/tast`로 시작한다고 가정하면 전체 nginx 구성은 다음과 유사합니다.
```nginx
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# 새로운 8686 업스트림
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # /tast로 시작하는 요청은 8686 포트로 이동하도록 합니다. 실제 상황에 맞게 /tast를 필요에 맞게 변경하세요.
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # 다른 요청은 기존 8787 포트로 이동합니다.
  location / {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      if (!-f $request_filename){
          proxy_pass http://webman;
      }
  }
}
```

이렇게 하면 클라이언트가 `도메인.com/tast/xxx`에 액세스할 때 별도의 8686 포트를 통해 처리되어 8787 포트의 요청 처리에 영향을 주지 않습니다.
