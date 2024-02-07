# Bağımlılık Otomatik Enjeksiyonu

Webman'de bağımlılık otomatik enjeksiyonu isteğe bağlı bir özelliktir ve varsayılan olarak kapalıdır. Bağımlılık otomatik enjeksiyonu gerekiyorsa [php-di](https://php-di.org/doc/getting-started.html) önerilir. Webman'in `php-di` ile birleşmesi aşağıdaki gibi yapılır.

## Kurulum
```sh
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

`config/container.php` yapılandırmasını aşağıdaki gibi değiştirin:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` sonunda `PSR-11` kuralına uygun bir konteyner örneği döner. `php-di` kullanmak istemiyorsanız burada başka bir `PSR-11` kuralına uygun bir konteyner örneği oluşturup döndürebilirsiniz.

## Constructor Enjeksiyonu
Yeni bir `app/service/Mailer.php` dosyası oluşturun (dizin yoksa kendiniz oluşturun) ve aşağıdaki içeriği ekleyin:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Mail gönderme kodu buraya gelecek
    }
}
```

`app/controller/UserController.php` dosyasını aşağıdaki gibi güncelleyin:

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
        $this->mailer->mail('hello@webman.com', 'Merhaba ve hoş geldiniz!');
        return response('ok');
    }
}
```
Normalde, `app\controller\UserController` örneğini oluşturmak için aşağıdaki kodlar kullanılır:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Ancak `php-di` kullanıldığında, geliştirici `UserController` içindeki `Mailer` örneğini manuel olarak oluşturmak zorunda değildir, webman bunu otomatik olarak gerçekleştirir. `Mailer`'ın oluşturulmasında başka sınıflara bağımlılık varsa, webman bunları da otomatik olarak oluşturup enjekte eder. Geliştiricinin herhangi bir başlatma işlemi yapmasına gerek kalmaz.

> **Not**
> Bağımlılık otomatik enjeksiyonu için çerçeve veya `php-di` tarafından oluşturulan bir örnek olmalıdır. Manuel olarak oluşturulan `new` ifadesi kullanılarak örnekleme yapılan sınıfların bağımlılık otomatik enjeksiyonu gerçekleştiremez. Enjekte etmek için `new` ifadesinin yerine `support\Container` arabirimini kullanmanız gerekmektedir, örneğin:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// new ifadesiyle oluşturulan örnekler bağımlılık enjeksiyonunu gerçekleştiremez
$user_service = new UserService;
// new ifadesiyle oluşturulan örnekler bağımlılık enjeksiyonunu gerçekleştiremez
$log_service = new LogService($path, $name);

// Container ile oluşturulan örnekler bağımlılık enjeksiyonunu gerçekleştirebilir
$user_service = Container::get(UserService::class);
// Container ile oluşturulan örnekler bağımlılık enjeksiyonunu gerçekleştirebilir
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Anotasyon Enjeksiyonu
Constructor bağımlılığı otomatik enjeksiyonunun yanı sıra anotasyon enjeksiyonu da kullanılabilir. Yukarıdaki örneğe devam edersek, `app\controller\UserController` aşağıdaki gibi güncellenebilir:

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
        $this->mailer->mail('hello@webman.com', 'Merhaba ve hoş geldiniz!');
        return response('ok');
    }
}
```
Bu örnek, `@Inject` anotasyonunu kullanarak enjekte eder ve `@var` anotasyonunu kullanarak nesne türünü bildirir. Bu örnek, constructor enjeksiyonuyla aynı etkiyi gösterir, ancak kod daha kısa ve öz olarak yazılmıştır.

> **Not**
> 1.4.6 sürümünden önce webman, controller parametre enjeksiyonunu desteklemez. Yani aşağıdaki kod (webman<=1.4.6) desteklenmez:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6 sürümünden önce controller parametre enjeksiyonu desteklenmez
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Merhaba ve hoş geldiniz!');
        return response('ok');
    }
}
```

## Özel Constructor Enjeksiyonu
Bazı durumlarda constructor'a geçirilen parametreler sınıf örneği olmayabilir, bunun yerine string, sayı, dizi gibi veriler olabilir. Örneğin, Mailer constructor'ına smtp sunucusu IP'si ve portu geçilmesi gerekiyorsa:
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
        // Mail gönderme kodu buraya gelecek
    }
}
```
Bu durumda, önceki örnekteki gibi set edilemeyeceği için standart constructor enjeksiyonu kullanılamaz. Bu durumu özelleştirilmiş enjeksiyonla çözmek mümkündür.

`config/dependence.php` (dosya yoksa kendiniz oluşturun) içine aşağıdaki kodu ekleyin:
```php
return [
    // ... diğer konfigürasyonları burada atladık
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Bu sayede, bağımlılık enjeksiyonu `app\service\Mailer` örneği almak istediğinde, otomatik olarak burada oluşturulan `app\service\Mailer` örneği kullanılacaktır.

`config/dependence.php` dosyasında `new` ifadesi ile `Mailer` sınıfının örneklendiğini gözlemliyoruz, bu örnekte herhangi bir sorun olmasa da, düşünün ki `Mailer` sınıfı başka bir sınıfa bağımlıysa veya `Mailer` sınıfı içinde anotasyon enjeksiyonu kullanılıyorsa, `new` ile başlatma işlemi bağımlılık otomatik enjeksiyonu gerçekleştiremeyecektir. Bu durumu çözmek için özelleştirilmiş arabirim enjeksiyonunu kullanabilir, sınıfı başlatmak için `Container::get(ClassName)` veya `Container::make(ClassName, [constructorParam])` yöntemlerini kullanabilirsiniz.
## Özel Arayüz Enjeksiyonu
Gerçek projelerde, belirli sınıflar yerine arayüze yönelik programlama yapmayı tercih ederiz. Örneğin, `app\controller\UserController` sınıfında `app\service\Mailer` yerine `app\service\MailerInterface` kullanılmalıdır.

`MailerInterface` arayüzünü tanımlayın.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

`MailerInterface` arayüzünün gerçeklemesini tanımlayın.
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
        // Mail gönderme kodu burada olacak
    }
}
```

Spesifik gerçeklemeler yerine `MailerInterface` arayüzünü içeri alın.
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
        $this->mailer->mail('hello@webman.com', 'Merhaba ve hoş geldiniz!');
        return response('ok');
    }
}
```

`config/dependence.php` içerisinde `MailerInterface` arayüzünün aşağıdaki gibi gerçeklemlendiğini tanımlayın.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Bu şekilde, iş mantığı `MailerInterface` arayüzünü kullandığında otomatik olarak `Mailer` gerçekleme olacaktır.

> Arayüze yönelik programlamanın faydaları, bir bileşeni değiştirmemiz gerektiğinde, iş mantığını değiştirmemek, sadece `config/dependence.php` içerisindeki spesifik gerçeklemeyi değiştirmemiz gerekliliğidir. Bu ayrıca birim testler yaparken de çok faydalıdır.

## Diğer Özel Enjeksiyonlar
`config/dependence.php`, sadece sınıfların bağımlılıklarını tanımlamanın yanı sıra, dize, sayı, dizi gibi diğer değerleri de tanımlayabilir.

Örneğin, `config/dependence.php` aşağıdaki gibi tanımlanmıştır:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Bu durumda, `@Inject` kullanarak `smtp_host` ve `smtp_port`'u sınıf özelliklerine enjekte edebiliriz.
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
        // Mail gönderme kodu burada olacak
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 192.168.1.11:25 çıktısını verecek
    }
}
```

> Not: `@Inject("anahtar")` içerisinde çift tırnak kullanılmalıdır.

## Daha Fazla Bilgi
Lütfen [php-di dokümanı](https://php-di.org/doc/getting-started.html)na göz atın.
