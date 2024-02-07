# 의존성 자동 주입

webman에서는 의존성 자동 주입이 선택 사항으로, 기본적으로 비활성화되어 있습니다. 의존성 자동 주입이 필요한 경우 [php-di](https://php-di.org/doc/getting-started.html)를 사용하는 것을 권장합니다. 아래는 `php-di`를 이용한 webman의 사용 예시입니다.

## 설치
```bash
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

`config/container.php` 파일을 수정하고 아래와 같이 설정합니다:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` 파일은 `PSR-11` 규격에 맞는 컨테이너 인스턴스를 반환해야 합니다. `php-di`를 사용하지 않는 경우, 여기에서 `PSR-11` 규격에 맞는 다른 컨테이너 인스턴스를 생성하고 반환할 수 있습니다.

## 생성자 주입
`app/service/Mailer.php` 파일을 생성하고(해당 디렉토리가 없는 경우 직접 생성) 아래와 같이 작성합니다:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // 이메일 보내기 코드는 생략되었습니다
    }
}
```

`app/controller/UserController.php` 파일은 다음과 같이 작성됩니다:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', '안녕하세요! 환영합니다!');
        return response('ok');
    }
}
```
일반적인 경우, `app\controller\UserController`의 인스턴스화에는 아래의 코드가 필요합니다:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
그러나 `php-di`를 사용하면, 개발자는 수동으로 `Mailer`를 인스턴스화할 필요가 없으며 webman이 자동으로 처리합니다. `Mailer` 인스턴스화 과정에 다른 클래스 의존성이 있는 경우에도 webman이 자동으로 인스턴스화하고 주입합니다. 개발자는 초기화 작업을 수행할 필요가 없습니다.

> **참고**
> 의존성 자동 주입을 완료하려면 프레임워크나 `php-di`에서 생성된 인스턴스여야 합니다. 수동으로 `new`로 생성된 인스턴스는 의존성 자동 주입을 완료할 수 없습니다. 주입이 필요한 경우에는 `support\Container` 인터페이스를 사용하여 `new` 문을 대체해야 합니다. 예를 들어:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// new 키워드로 생성된 인스턴스는 의존성 주입을 완료할 수 없음
$user_service = new UserService;
// new 키워드로 생성된 인스턴스는 의존성 주입을 완료할 수 없음
$log_service = new LogService($path, $name);

// Container로 생성된 인스턴스는 의존성 주입을 완료할 수 있음
$user_service = Container::get(UserService::class);
// Container로 생성된 인스턴스는 의존성 주입을 완료할 수 있음
$log_service = Container::make(LogService::class, [$path, $name]);
```

## 주석 주입
생성자 의존성 자동 주입 외에도 주석 주입을 사용할 수 있습니다. 위의 예시를 계속하여 `app\controller\UserController`를 아래와 같이 변경할 수 있습니다:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', '안녕하세요! 환영합니다!');
        return response('ok');
    }
}
```
이 예시는 `@Inject` 주석을 사용하여 주입하고 `@var` 주석으로 객체 유형을 선언합니다. 이 예시는 생성자 주입과 같은 효과를 가지지만 코드가 더 간결합니다.

> **참고**
> 1.4.6 버전 이전의 webman에서는 컨트롤러 인자 주입을 지원하지 않습니다. 예를 들면 아래 코드는 webman<=1.4.6일 때 지원되지 않습니다:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6 버전 이전은 컨트롤러 인자 주입을 지원하지 않습니다
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', '안녕하세요! 환영합니다!');
        return response('ok');
    }
}
```

## 사용자 정의 생성자 주입

가끔 생성자에 전달된 인수가 클래스의 인스턴스가 아니라 문자열, 숫자, 배열 등의 데이터인 경우가 있습니다. 예를 들어 Mailer 생성자에는 smtp 서버 IP와 포트를 전달해야 하는 경우:

```php
<?php
namespace app\service;

class Mailer
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // 이메일 보내기 코드는 생략되었습니다
    }
}
```

이러한 경우 생성자 자동 주입을 직접 사용할 수 없으며, `php-di`는 `$smtp_host` 및 `$smtp_port`의 값이 무엇인지 알 수 없습니다. 이때 사용자 정의 주입을 시도할 수 있습니다.

`config/dependence.php` 파일에 아래와 같이 추가합니다:
```php
return [
    // ... 다른 설정은 여기서 생략함

    app\service\Mailer::class => new app\service\Mailer('192.168.1.11', 25)
];
```
이렇게 하면 의존성 주입이 `app\service\Mailer` 인스턴스가 필요할 때 `config/dependence.php`에 설정된 `app\service\Mailer` 인스턴스가 자동으로 사용됩니다.

`config/dependence.php`에서 `Mailer` 클래스를 사용하여 `new`로 인스턴스화했음을 알 수 있습니다. 이 예시에서는 문제가 되지 않지만, Mailer 클래스가 다른 클래스에 의존하거나 Mailer 클래스 내부에서 주석 주입을 사용하는 경우 `new`로 초기화하면 의존성 자동 주입이 이루어지지 않습니다. 해결 방법은 사용자 정의 인터페이스 주입을 사용하여 `Container::get(클래스 이름)` 또는 `Container::make(클래스 이름, [생성자 매개변수])` 메소드를 사용하여 클래스를 초기화하는 것입니다.
## 사용자 정의 인터페이스 주입
실제 프로젝트에서는 특정한 클래스가 아닌 인터페이스에 대한 프로그래밍을 선호합니다. 예를 들어 `app\controller\UserController`는 `app\service\Mailer` 대신 `app\service\MailerInterface`를 가져와야 합니다.

`MailerInterface` 인터페이스 정의.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

`MailerInterface` 인터페이스의 구현 정의.
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    private $smtpHost;
    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // 이메일 전송 코드 생략
    }
}
```

구체적인 구현이 아닌 `MailerInterface` 인터페이스를 가져옵니다.
```php
<?php
namespace app\controller;

use support\Request;
use app\service\MailerInterface;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var MailerInterface
     */
    private $mailer;
    
    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', '안녕하세요. 환영합니다!');
        return response('ok');
    }
}
```

`config/dependence.php`에서 `MailerInterface` 인터페이스를 다음과 같이 정의합니다.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

이렇게 하면 비즈니스가 `MailerInterface` 인터페이스를 사용해야 할 때 자동으로 `Mailer` 구현이 사용됩니다.

> 인터페이스 지향 프로그래밍의 장점은 특정 구성요소를 교체할 필요가 있을 때 비즈니스 코드를 변경할 필요가 없고 구체적인 구현만 `config/dependence.php`에서 변경하면 된다는 것입니다. 이는 단위 테스트에도 매우 유용합니다.

## 기타 사용자 정의 주입
`config/dependence.php`는 클래스 의존성 뿐만 아니라 문자열, 숫자, 배열 등의 다른 값들을 정의할 수도 있습니다.

예를들어 `config/dependence.php`는 다음과 같이 정의됩니다.
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

이제 `@Inject`를 사용하여 `smtp_host` 및 `smtp_port`를 클래스 속성으로 주입할 수 있습니다.
```php
<?php
namespace app\service;

use DI\Annotation\Inject;

class Mailer
{
    /**
     * @Inject("smtp_host")
     */
    private $smtpHost;

    /**
     * @Inject("smtp_port")
     */
    private $smtpPort;

    public function mail($email, $content)
    {
        // 이메일 전송 코드 생략
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 출력: 192.168.1.11:25
    }
}
```

> 참고: `@Inject("key")`에서는 쌍따옴표를 사용합니다.

## 더 알아보기
[php-di 매뉴얼](https://php-di.org/doc/getting-started.html)을 참조하세요.
