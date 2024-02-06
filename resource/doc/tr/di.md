# Bağımlılık Otomatik Enjeksiyonu

Webman'de bağımlılık otomatik enjeksiyonu isteğe bağlı bir özelliktir ve varsayılan olarak devre dışı bırakılmıştır. Bağımlılık otomatik enjeksiyonu gerekiyorsa, [php-di](https://php-di.org/doc/getting-started.html) kullanmanızı öneririz. Webman'in `php-di` ile nasıl entegre edileceğine aşağıda yer verilmiştir.

## Kurulum
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

Aşağıdaki gibi `config/container.php` yapılandırmasını değiştirin:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` dosyası, `PSR-11` standartlarına uygun bir konteyner örneği döndürmelidir. Eğer `php-di` kullanmak istemiyorsanız, başka bir `PSR-11` standartlarına uygun konteyner örneği yaratıp döndürebilirsiniz.

## Constructor Enjeksiyonu
Yeni bir `app/service/Mailer.php` (dizin yoksa kendiniz oluşturun) dosyası aşağıdaki içeriğe sahip olmalıdır:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // Mail gönderme kodları buraya gelecek
    }
}
```

`app/controller/UserController.php` dosyasının içeriği şu şekilde olmalıdır:

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
        return response('tamam');
    }
}
```
Normalde, `app\controller\UserController` örneğini oluşturmak için aşağıdaki kod gereklidir:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
Ancak `php-di` kullanıldığında geliştiricinin `Mailer` sınıfını manuel olarak örneklemesine gerek kalmaz, webman bunu otomatik olarak halleder. `Mailer` sınıfının örneklenmesi sırasında başka sınıflara bağımlılık varsa, webman bu sınıfların da otomatik olarak örneklenmesini ve enjekte edilmesini sağlar. Geliştiricinin herhangi bir başlatma çalışması yapmasına gerek yoktur.

> **Not**
> Bağımlılık otomatik enjeksiyonunu tamamlamak için, çerçeve veya `php-di` tarafından oluşturulan örnekler kullanılmalıdır. Manuel olarak oluşturulmuş `new` örnekleri bağımlılık otomatik enjeksiyonunu tamamlayamaz. Enjeksiyon yapmak isteniyorsa, `new` ifadesinin yerine `support\Container` arayüzünün kullanılması gerekir. Örnek olarak:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// new anahtar kelimesi ile oluşturulan örnek bağımlılık enjekte edilemez
$user_service = new UserService;
// new anahtar kelimesi ile oluşturulan örnek bağımlılık enjekte edilemez
$log_service = new LogService($path, $name);

// Container ile oluşturulan örnek bağımlılık enjekte edilebilir
$user_service = Container::get(UserService::class);
// Container ile oluşturulan örnek bağımlılık enjekte edilebilir
$log_service = Container::make(LogService::class, [$path, $name]);
```

## Anotasyon Enjeksiyonu
Constructor'a bağımlılık otomatik enjeksiyonunun yanı sıra anotasyon enjeksiyonunu da kullanabiliriz. Yukarıdaki örneğe devam ederek, `app\controller\UserController` aşağıdaki gibi değiştirilebilir:
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
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
Bu örnek, `@Inject` anotasyonunu kullanarak enjeksiyon yapar ve `@var` anotasyonunu kullanarak nesne türünü belirtir. Bu örnek, constructor enjeksiyonuyla aynı etkiye sahiptir, ancak daha kısa bir kod yapısı sunar.

> **Not**
> webman, 1.4.6 sürümünden önce controller parametre enjeksiyonunu desteklemez. Örneğin, aşağıdaki kodun webman<=1.4.6 sürümünde desteklenmediği unutulmamalıdır:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6 sürümünden önce controller parametre enjeksiyonunu desteklemez
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## Özel Constructor Enjeksiyonu
Bazı durumlarda, constructor'a geçirilen parametreler bir sınıf örneği olmayabilir ve string, integer, dizi gibi veriler olabilir. Örneğin, Mailer constructor'ına smtp sunucusunun IP'si ve portu geçirilmesi gerekebilir:
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
        // Mail gönderme kodları buraya gelecek
    }
}
```
Bu durumda, önceki constructor enjeksiyonunu doğrudan kullanamayız, çünkü `php-di`, `$smtp_host` ve `$smtp_port`'un değerini belirleyemez. Bu durumda, özel enjeksiyon denenebilir.

`config/dependence.php` dosyasına şu kodu ekleyin (dosya yoksa kendiniz oluşturun):
```php
return [
    // ... diğer yapılandırmalar buraya eklenecek
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
Bu şekilde, bağımlılık enjeksiyonu `app\service\Mailer` örneği alındığında, otomatik olarak bu yapılandırmadaki `app\service\Mailer` örneği kullanılacaktır.

Görebildiğimiz gibi, `config/dependence.php` dosyasında, `Mailer` sınıfını `new` ile örneklendiriyoruz. Bu örnekte bu bir sorun oluşturmaz, ancak `Mailer` sınıfı başka sınıflara veya anotasyon enjeksiyonuna bağlıysa, `new` başlatma işlemi bağımlılık otomatik enjeksiyonunu sağlamaz. Çözüm, özel arayüz enjeksiyonunu kullanmaktır; yani `Container::get(class_name)` ya da `Container::make(class_name, [constructor_parameters])` yöntemleriyle sınıfı başlatmaktır.

## Özel Arayüz Enjeksiyonu
Gerçek projelerde, belirli bir sınıf değil, genellikle bir arayüze yönelik olarak programlama yapmak istiyoruz. Örneğin, `app\controller\UserController`'da `app\service\MailerInterface`'in, `app\service\Mailer`'dan ve onun yerine geçen bir sınıftan alınmasını istiyoruz.

`MailerInterface` arayüzünü tanımlayın.
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

`MailerInterface` arayüzünün uygulamasını tanımlayın.
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
        // Mail gönderme kodları buraya gelecek
    }
}
```

Spesifik uygulama yerine `MailerInterface` arayüzünü içeri alın.
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

`config/dependence.php` dosyasında, `MailerInterface` arayüzü şu şekilde tanımlanmalıdır.
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

Bu şekilde, işimiz `MailerInterface` arayüzünü kullanması gerektiğinde otomatik olarak `Mailer` uygulamasını kullanacaktır.

> Arayüze yönelik programlamanın faydası, bir bileşeni değiştirmemiz gerektiğinde iş kodunu değiştirmemize gerek kalmaz, sadece `config/dependence.php` dosyasındaki spesifik uygulamayı değiştirmemiz yeterli olur. Ayrıca bu yapı, birim testleri yaparken de oldukça kullanışlıdır.

## Diğer Özel Enjeksiyonlar
`config/dependence.php` dosyası, sadece sınıfların bağımlılıklarını değil, aynı zamanda string, integer, dizi gibi diğer değerleri de tanımlayabilir.

Örneğin, `config/dependence.php` dosyasında şu tanımlamaları yapabiliriz:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

Bu durumda, `@Inject` kullanarak `smtp_host` ve `smtp_port` değerlerini sınıfın özelliklerine enjekte edebiliriz.
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
        // Mail gönderme kodları buraya gelecek
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // Çıktı olarak 192.168.1.11:25 alınacaktır
    }
}
```

> Not: `@Inject("key")` içinde çift tırnak kullanılır

## Daha Fazlası
Lütfen [php-di dokümantasyonu](https://php-di.org/doc/getting-started.html)'na başvurun.
