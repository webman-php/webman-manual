# 느린 비즈니스 처리

가끔 우리는 느린 비즈니스를 처리해야 할 때가 있습니다. 웹맨의 다른 요청 처리에 영향을 미치지 않도록하기 위해 이러한 비즈니스는 상황에 따라 다른 처리 방법을 적용할 수 있습니다.

## 메시지 대기열 사용
참조[레디스 대기열](https://www.workerman.net/plugin/12), [스톰프 대기열](https://www.workerman.net/plugin/13)

### 이점
급격한 대량의 비즈니스 처리 요청에 대응할 수 있습니다.

### 단점
직접적으로 결과를 클라이언트에 반환할 수 없습니다. 결과를 푸시하는 경우 [웹맨/푸시](https://www.workerman.net/plugin/2)를 사용하여 결과를 푸시해야합니다.

## 새 HTTP 포트 추가

> **주의**
> 이 기능은 webman-framework>=1.4이상에서 지원됩니다.

느린 요청을 처리하기 위해 새 HTTP 포트를 추가하여 이러한 느린 요청은 이 포트에 액세스하여 특정 프로세스 그룹에서 처리한 후 결과를 클라이언트에 직접 반환할 수 있습니다.

### 이점
데이터를 직접 클라이언트에 반환할 수 있습니다.

### 단점
급격한 대량의 요청에 대응할 수 없습니다.

### 구현 단계
`config/process.php`에 다음 구성을 추가하십시오.
```php
return [
    // ... 다른 구성은 생략됨 ...

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

이제 느린 인터페이스는 `http://127.0.0.1:8686/`을 통해 이 그룹의 프로세스를 따라 갈 수 있으며 다른 프로세스의 비즈니스 처리에 영향을 주지 않습니다.

프론트엔드가 포트의 차이를 인식하지 못하게하려면, 8686 포트로의 프록시를 nginx에 추가할 수 있습니다. 가정은 느린 인터페이스 요청 경로가 모두 `/tast`로 시작한다고 가정하고 전체 nginx 구성은 다음과 유사합니다.
```
upstream webman {
    server 127.0.0.1:8787;
    keepalive 10240;
}

# 새로운 8686 업스트림 추가
upstream task {
   server 127.0.0.1:8686;
   keepalive 10240;
}

server {
  server_name webman.com;
  listen 80;
  access_log off;
  root /path/webman/public;

  # /tast로 시작하는 요청은 8686 포트로 이동합니다. 실제 상황에 맞게 /tast를 필요에 맞게 변경하십시오.
  location /tast {
      proxy_set_header X-Real-IP $remote_addr;
      proxy_set_header Host $host;
      proxy_http_version 1.1;
      proxy_set_header Connection "";
      proxy_pass http://task;
  }

  # 다른 요청은 원래 8787 포트를 통해 처리됩니다.
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

이렇게 함으로써 클라이언트가 `도메인.com/tast/xxx`에 액세스 할 때 별도의 8686 포트를 통해 처리되어 8787 포트의 요청 처리에 영향을 주지 않습니다.

