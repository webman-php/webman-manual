# 의존성 자동 주입

웹맨에서는 의존성 자동 주입이 선택적인 기능이며, 기본적으로 비활성화되어 있습니다. 의존성 자동 주입이 필요한 경우 [php-di](https://php-di.org/doc/getting-started.html)를 추천합니다. 아래는 `php-di`를 통해 webman을 사용하는 방법입니다.

## 설치
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

`config/container.php` 설정을 수정하여 최종 내용은 다음과 같습니다:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php`은 최종적으로 `PSR-11` 규격을 준수하는 컨테이너 인스턴스를 반환해야 합니다. `php-di`를 사용하지 않으려면 여기에서 `PSR-11` 규격에 부합하는 다른 컨테이너 인스턴스를 생성하고 반환할 수 있습니다.

## 생성자 주입
`app/service/Mailer.php`를 생성하여(디렉토리가 없는 경우 직접 생성) 다음과 같은 내용을 작성합니다:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // 이메일 전송 코드 생략
    }
}
```

`app/controller/UserController.php` 내용은 다음과 같습니다:

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
일반적으로, `app\controller\UserController`를 인스턴스화하려면 다음 코드가 필요합니다:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
그러나 `php-di`를 사용하면, 개발자는 수동으로 `Mailer`를 인스턴스화할 필요가 없으며 webman이 자동으로 처리합니다. `Mailer`를 인스턴스화하는 중에 다른 클래스에 의존성이 있는 경우에도 webman이 자동으로 인스턴스화하고 주입합니다. 개발자는 초기화 작업을 수행할 필요가 없습니다.

> **주의**
> 의존성 자동 주입을 완료하려면 프레임워크나 `php-di`에 의해 생성된 인스턴스여아 합니다. 수동으로 `new`로 생성된 인스턴스는 의존성 자동 주입을 완료할 수 없으며, 주입이 필요한 경우 `support/Container` 인터페이스를 사용하여 `new` 문을 대체해야 합니다. 

## 주석 주입
생성자 의존성 자동 주입 외에도 주석 주입을 사용할 수 있습니다. 위의 예제를 계속 사용하여 `app\controller\UserController`를 다음과 같이 변경합니다:
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
이 예제는 `@Inject`로 주석을 추가하고 `@var`로 객체 유형을 선언하여 생성자 주입과 똑같은 효과를 냈지만 코드가 더 간결합니다.

> **주의**
> webman은 1.4.6 버전 이전에는 컨트롤러 매개변수 주입을 지원하지 않습니다. 따라서 아래 코드는 webman<=1.4.6에서 지원되지 않습니다.

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6 버전 이전은 컨트롤러 매개변수 주입을 지원하지 않습니다
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', '안녕하세요! 환영합니다!');
        return response('ok');
    }
}
```

## 사용자 정의 생성자 주입
가끔 생성자로 전달되는 매개변수가 클래스 인스턴스가 아니고 문자열, 숫자, 배열 등의 데이터일 수 있습니다. 예를 들어 Mailer 생성자는 smtp 서버 IP와 포트를 전달해야 합니다:
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
        // 이메일 전송 코드 생략
    }
}
```
이 경우 앞서 소개한 생성자 자동 주입은 사용할 수 없으며, `php-di`는 `$smtp_host` 및 `$smtp_port`의 값을 결정할 수 없습니다. 이때 사용자 정의 주입을 시도할 수 있습니다.

`config/dependence.php`(파일이 없는 경우 직접 생성)에 다음 코드를 추가합니다:
```php
return [
    // ... 다른 설정은 생략

    app\service\Mailer::class => new app\service\Mailer('192.168.1.11', 25);
];
```
이제 종속성 주입이 `app\service\Mailer` 인스턴스를 가져올 때 이 구성을 자동으로 사용합니다.

`config/dependence.php`에서 `new`를 사용하여 `Mailer` 클래스를 인스턴스화한다는 점에 유의해야 합니다. 이 예에서는 문제가 없지만, 만약 `Mailer` 클래스가 다른 클래스에 의존하거나 주석 주입을 사용하는 경우 `new`를 사용하여 초기화하면 의존성 자동 주입이 이루어지지 않습니다. 이 문제를 해결하기 위해 사용자 정의 인터페이스 주입을 사용하여 클래스를 초기화하는 `Container::get(클래스 이름)` 또는 `Container::make(클래스 이름, [생성자 매개변수])` 방법을 사용해야 합니다.

## 사용자 정의 인터페이스 주입
실제 프로젝트에서 우리는 구체적인 클래스가 아니라 인터페이스를 사용하는 것이 더 좋습니다. 예를 들어 `app\controller\UserController`에서 `app\service\Mailer` 대신 `app\service\MailerInterface`를 가져오는 것이 좋습니다.

`MailerInterface` 인터페이스를 정의합니다.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```
그 후, `MailerInterface` 인터페이스의 구현체를 작성합니다.
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
구체적인 구현 대신에 `MailerInterface` 인터페이스를 가져오도록 변경합니다.
```php
<?php
namespace app\controller;

use support/Request;
use app/service/MailerInterface;
use DI/Annotation/Inject;

class UserController
{
    /**
     * @Inject
     * @var MailerInterface
     */
    private $mailer;
    
    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', '안녕하세요! 환영합니다!');
        return response('ok');
    }
}
```
`config/dependence.php`에서 `MailerInterface` 인터페이스에 대한 구현을 다음과 같이 정의합니다.
```php
use Psr/Container/ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```
이제 `MailerInterface` 인터페이스를 사용해야 하는 경우 자동으로 `Mailer` 구현을 사용합니다.

> 인터페이스 중심 프로그래밍의 장점은 특정 구성 요소를 교체할 때 비즈니스 코드를 변경할 필요가 없다는 것입니다. 대신에 `config/dependence.php`의 구체적인 구현만 변경하면 됩니다. 이것은 단위 테스트에도 매우 유용합니다.

## 기타 사용자 정의 주입
`config/dependence.php`는 클래스 의존성 뿐만 아니라 문자열, 숫자, 배열 등 다른 값도 정의할 수 있습니다.

예를 들어, `config/dependence.php`에 다음과 같이 정의합니다:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```
이제 `@Inject`를 사용하여 `smtp_host`와 `smtp_port`를 클래스 속성에 주입할 수 있습니다.
```php
<?php
namespace app\service;

use DI/Annotation/Inject;

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
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 192.168.1.11:25를 출력합니다.
    }
}
```

> 주의 : `@Inject("key")` 안에는 쌍따옴표가 있어야 합니다.
## 추가 정보
[php-di 매뉴얼](https://php-di.org/doc/getting-started.html)을 참조하세요.
