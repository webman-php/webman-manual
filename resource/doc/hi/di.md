# डिपेंडेंसी ऑटो इन्जेक्शन
वेबमैन में डिपेंडेंसी ऑटो इन्जेक्शन एक ऐच्छिक सुविधा है, यह फ़ंक्शन डिफ़ॉल्ट रूप में बंद है। यदि आपको डिपेंडेंसी आपने इन्जेक्शन की आवश्यकता है, तो हमारी सिफारिश है कि आप [php-di](https://php-di.org/doc/getting-started.html) का उपयोग करें, निम्नलिखित वेबमैन से `php-di` का उपयोग करने का तरीका है।

## स्थापना
```शीर्षक
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
``` 

कॉन्फ़िगरेशन `config/container.php` को संशोधित करें, और इसे अंतिम रूप में निम्नलिखित रूप में जाएगा:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
``` 

> `config/container.php` में एक `PSR-11` मानक को मान्यता देने वाला एक कंटेनर इंस्टेंस को अंत में लौटाया जाता है। यदि आप `php-di` का उपयोग नहीं करना चाहते हैं, तो आपको यहाँ पर एक अन्य `PSR-11` मानक को मान्यता देने वाला कंटेनर इंस्टेंस बनाना और लौटाना होगा।

## कंस्ट्रक्टर इन्जेक्शन
नया `app/service/Mailer.php` बनाएं (यदि निर्देशिका मौजूद नहीं है तो स्वयं निर्मित करें) उसकी सामग्री निम्नलिखित होगी:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // ईमेल भेजने का कोड छोड़ दिया गया है
    }
}
``` 

`app/controller/UserController.php` की सामग्री निम्नलिखित है:
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
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
``` 
सामान्य स्थितियों में, `app\controller\UserController` का नमूना उन्नयन करने के लिए निम्नलिखित कोड की आवश्यकता होती है:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
``` 
लेकिन `php-di` का उपयोग करते समय, डेवलपर को `UserContoller` में `Mailer` को मैन्युअल रूप से नमूना निकालने की आवश्यकता नहीं होती है, वेबमेन आपकी सहायता करेगा। यदि `Mailer` को नमूना निकलने के दौरान किसी अन्य क्लास की आवश्यकता है, तो वेबमेन आपको स्वतः ही नमूना निकालने और इंजेक्शन करने में सहायता करेगा। डेवलपर को किसी भी प्रारंभिक काम की आवश्यकता नहीं होती है।

> **नोट**
> डिपेंडेंसी ऑटो इन्जेक्शन को पूरा करने के लिए डिवेलपर द्वारा नए किए गए नमूने को फ्रेमवर्क या `php-di` द्वारा बनाए गए नमूने होना चाहिए, हाथ से `new` की गई नमूने को डिपेंडेंसी ऑटो इन्जेक्शन को पूरा नहीं कर सकती है, अगर इन्जेक्शन की आवश्यकता है तो `support\Container` इंटरफेस का इस्तेमाल करने के लिए `new` स्टेटमेंट को बदलना होगा, जैसे:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// new की गई नमूने को डिपेंडेंसी इन्जेक्शन करने में समर्थ नहीं होती है
$user_service = new UserService;
// new की गई नमूने को डिपेंडेंसी इन्जेक्शन करने में समर्थ नहीं होती है
$log_service = new LogService($path, $name);

// कंटेनर द्वारा बनी गई नमूने को डिपेंडेंसी इन्जेक्शन करने में समर्थ होती है
$user_service = Container::get(UserService::class);
// कंटेनर द्वारा बनी गई नमूने को डिपेंडेंसी इन्जेक्शन करने में समर्थ होती है
$log_service = Container::make(LogService::class, [$path, $name]);
``` 

## एनोटेशन इन्जेक्शन
कंस्ट्रक्टर डिपेंडेंसी ऑटो इन्जेक्शन के अलावा, हम एनोटेशन इन्जेक्शन का भी उपयोग कर सकते हैं। पिछले उदाहरण को आगे बढ़ाते हुए, `app\controller\UserController` को निम्नलिखित रूप में संशोधित किया गया है:
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
इस नमूने में `@Inject` एनोटेशन का उपयोग करके इन्जेक्शन किया गया है, और `@var` एनोटेशन द्वारा ऑब्जेक्ट का प्रकार घोषित किया गया है। यह उदाहरण कन्स्ट्रक्टर इन्जेक्शन के अंदाज़ से एकसा है, लेकिन कोड संक्षेपित है।

> **नोट**
> पहले से से 1.4.6 संस्करण से पहले वेबमेन को कंट्रोलर पैरामीटर इन्जेक्शन का समर्थन नहीं करता है, उदाहरण के लिए निम्नलिखित कोड की 1.4.6 संस्करण से पहले समर्थन नहीं करेगा:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6 संस्करण से पहले कंट्रोलर पैरामीटर इन्जेक्शन का समर्थन नहीं है
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```
## कस्टम कंस्ट्रक्टर इनजेक्शन

कभी-कभी कंस्ट्रक्टर में पास किए गए पैरामीटर किसी क्लास की इंस्टेंस नहीं होते हैं, बल्कि स्ट्रिंग, नंबर, एरे आदि डेटा होता है। उदाहरण के लिए, Mailer कंस्ट्रक्टर को SMTP सर्वर IP और पोर्ट पास करने की आवश्यकता होती है:
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
        // ईमेल भेजने का कोड छोड़ दिया गया है
    }
}
```
इस प्रकार की स्थिति में, कस्टम इंजेक्शन का प्रयास किया जा सकता है।

`config/dependence.php` में निम्नलिखित कोड जोड़ें:
```php
return [
    // ... यहां अन्य कॉन्फ़िगरेशन को नज़रअंदाज़ कर दिया गया
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
इस तरह, जब डिपेंडेंसी इनजेक्शन को चाहिए होगा तो इस कॉन्फ़िगरेशन के बनाए गए `app\service\Mailer` की इंस्टेंस आपातका उपयोग किया जाएगा।

हम देखते हैं, कि `config/dependence.php` में `new` का उपयोग करके `Mailer` क्लास को इंस्टेंस करने के लिए किया गया है, इस उदाहरण में कोई समस्या नहीं है, लेकिन सोचिए अगर `Mailer` क्लास दूसरी क्लास पर निर्भर होती है या `Mailer` क्लास में एंनोटेशन इंजेक्शन का उपयोग किया जाता है, तो `new` का उपयोग करके इंस्टेंस करने से डिपेंडेंस ऑटोइंजेक्शन नहीं होगा। इस समस्या का समाधान कस्टम इंटरफेस इंजेक्शन का उपयोग करके किया जा सकता है, `Container::get(क्लास का नाम)` या `Container::make(क्लास का नाम, [कंस्ट्रक्टर पैरामीटर])` मेथड का उपयोग करके क्लास को इंस्टेंस किया जाता है।


## कस्टम इंटरफेस इंजेक्शन
वास्तविक परियोजनाओं में, हमें अभिवादन इंटरफेस के ऊपर प्रोग्राम करना अधिक पसंद होता है, न कि विशिष्ट क्लास। उदाहरण के लिए, `app\controller\UserController` में `app\service\MailerInterface` को शामिल करना चाहिए न कि `app\service\Mailer`।

`MailerInterface` इंटरफेस को परिभाषित करें।
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

`MailerInterface` इंटरफेस का वास्तविकीकरण करें।
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
        // ईमेल भेजने का कोड छोड़ दिया गया है
    }
}
```

`MailerInterface` इंटरफेस को शामिल करें, थोड़ी नहीं भावनात्मक करना अनुभव।
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
        $this->mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

`config/dependence.php` में `MailerInterface` इंटरफेस का विशिष्टीकरण निम्नलिखित होगा।
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

इस प्रकार, जब व्यावसायिकता `MailerInterface` इंटरफेस का उपयोग करना चाहेगी, तो स्वचालित रूप से `Mailer` का विशिष्टीकरण होगा।

> इंटरफेस प्रोग्राम करने के लाभ यह है कि जब हमें किसी कंपोनेन्ट को बदलने की जरूरत पड़ती है, तो हमें व्यावसायिकता वाले कोड को बदलने की आवश्यकता नहीं होती है, केवल `config/dependence.php` में विशिष्ट विशिष्टीकरण को बदलने की आवश्यकता होती है। यह यूनिट टेस्टिंग के लिए भी बहुत उपयोगी है।


## अन्य कस्टम इंजेक्शन
`config/dependence.php` के अलावा, क्लास की डिपेंडेंस को विशिष्ट कर सकते हैं, स्ट्रिंग, नंबर, एरे आदि वैसे भी।

उदाहरण के लिए `config/dependence.php` निम्नलिखित तरह से परिभाषित करता है:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

इस समय हम `@Inject` का उपयोग करके `smtp_host` और `smtp_port` को क्लास के विशिष्टी कन्स्ट्रक्टर में इंजेक्ट कर सकते हैं।
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
        // ईमेल भेजने का कोड छोड़ दिया गया है
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // 192.168.1.11:25 को प्रिंट करेगा
    }
}
```

> ध्यान दें: `@Inject("key")` में डबल कोटेशन होती है।


## अधिक सामग्री
[php-di मैनुअल](https://php-di.org/doc/getting-started.html)रेफ़र करें।
