# 디렉토리 구조

```
플러그인/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

우리는 웹맨과 동일한 디렉토리 구조와 구성 파일을 가진 애플리케이션 플러그인을 볼 수 있습니다. 실제로 플러그인 개발 경험은 일반적인 웹맨 응용 프로그램 개발과 거의 차이가 없습니다.
플러그인 디렉토리와 이름은 PSR4 규약을 준수합니다. 플러그인은 plugin 디렉토리에 위치하므로 네임스페이스는 일반적으로 'plugin\'으로 시작하며, 예를 들어 `plugin\foo\app\controller\UserController`와 같습니다.

## api 디렉토리에 관하여
각 플러그인에는 api 디렉토리가 있으며, 애플리케이션이 다른 애플리케이션에서 호출할 수 있는 몇 가지 내부 인터페이스를 제공하는 경우 해당 인터페이스를 api 디렉토리에 두어야 합니다.
여기서 말하는 인터페이스는 네트워크 호출이 아닌 함수 호출을 의미합니다.
예를 들어 `이메일 플러그인`은 `plugin/email/api/Email.php`에 `Email::send()` 인터페이스를 제공하여 다른 응용 프로그램에서 이메일을 보낼 수 있습니다.
또한 `plugin/email/api/Install.php`은 webman-admin 플러그인 마켓에서 설치 또는 제거 작업을 수행하기 위해 자동으로 생성됩니다.
