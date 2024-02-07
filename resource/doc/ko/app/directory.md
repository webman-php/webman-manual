```
# 디렉토리 구조

```
plugin/
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

우리는 애플리케이션 플러그인이 webman과 동일한 디렉토리 구조와 설정 파일을 가지고 있다는 것을 볼 수 있습니다. 사실 플러그인을 개발하는 경험은 webman을 개발하는 것과 거의 차이가 없습니다.
플러그인 디렉토리 및 명명 규칙은 PSR4 규격을 따르며, 플러그인은 plugin 디렉토리에 있으므로 네임스페이스는 plugin으로 시작합니다. 예를 들어 `plugin\foo\app\controller\UserController`와 같이 사용됩니다.

## api 디렉토리에 관하여
각 플러그인에는 api 디렉토리가 있으며, 애플리케이션이 다른 애플리케이션에서 호출할 수 있는 일부 내부 인터페이스를 제공하는 경우 해당 인터페이스를 api 디렉토리에 넣어야 합니다.
여기서 말하는 인터페이스는 네트워크 호출이 아닌 함수 호출 인터페이스입니다.
예를 들어 `이메일 플러그인`은 `plugin/email/api/Email.php`에 있는 `Email::send()` 인터페이스를 제공하여 다른 애플리케이션이 이메일을 보낼 수 있도록 합니다.
또한, `plugin/email/api/Install.php`은 자동으로 생성되며, webman-admin 플러그인 시장에서 설치 또는 제거 작업을 수행하도록 합니다.
```
