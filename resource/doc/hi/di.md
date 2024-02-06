# डिपेंडेंस ऑटो इनजेक्शन

वेबमैन में डिपेंडेंस ऑटो इनजेक्शन एक वैकल्पिक सुविधा है, जो डिफ़ॉल्ट रूप से बंद है। यदि आप डिपेंडेंस ऑटो इनजेक्शन की आवश्यकता है, तो [php-di](https://php-di.org/doc/getting-started.html) का उपयोग करना अनुशंसित है। निम्नलिखित वेबमैन का उपयोग के साथ `php-di` का इस्तेमाल करने की प्रक्रिया है।

## स्थापना 
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

कॉन्फ़िगरेशन फ़ाइल `config/container.php` को संपादित करें, और निम्न अंतिम रूप में उपयोग करने के लिए:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> `config/container.php` में एक `PSR-11` मानक के अनुसार एक कंटेनर इंस्टेंस वापस करना होगा। यदि आप `php-di` का उपयोग नहीं करना चाहते ​​हैं, तो आपको एक अन्य `PSR-11` मानक के अनुसार एक कंटेनर इंस्टेंस बनाना और वापस करना होगा।

## निर्माण फ़ंक्शन इनजेक्शन
नया `app/service/Mailer.php`(यदि निर्दिष्ट फ़ोल्डर मौजूद न हो तो कृपया स्वयं बनाएं) का निर्माण निम्नलिखित है:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // ईमेल भेजने का कोड छोड़ दिया गया
    }
}
```

`app/controller/UserController.php` का निम्नलिखित है:

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
सामान्य रूप से, `app\controller\UserController` के इंस्टेंशियूशन को पूरा करने के लिए नीचे कार्य को करना होगा:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
`php-di` का उपयोग करने पर, डेवलपर को `Mailer` को मैन्युअली इंस्टेंशिएट करने की आवश्यकता नहीं है, वेबमैन आपका समर्थन करेगा। अगर `Mailer` को इंस्टेंशियेट करते समय किसी अन्य क्लास की ज़रूरत है, तो वेबमैन आपकी सहायता करेगा और सम्मिलित करेगा। डेवलपर को किसी भी प्रारंभिक काम की आवश्यकता नहीं होती।

> **नोट**
> डिपेंडेंस ऑटो इन्जेक्शन को पूरा करने के लिए केवल फ्रेमवर्क या `php-di` द्वारा बनाए गए इंस्टेंशियूशन हो सकती है, मैन्युअली बनाए गए इंस्टेंशियूशन से इसे पूरा नहीं किया जा सकता है। प्रवेश करने के लिए, `new` वयवस्था को हटाकर `support\Container` इंटरफ़ेस का उपयोग करना पड़ेगा। जैसे:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// new की साधना द्वारा बनाया गया इंस्टेंशियूशन डिपेंडेन्स इन्जेक्शन में पूरा नहीं हुई है
$user_service = new UserService;
// new की साधना द्वारा बनाया गया इंस्टेंशियूशन डिपेंडेन्स इन्जेक्शन में पूरा नहीं हुई है
$log_service = new LogService($path, $name);

// Container द्वारा बनाए गए इंस्टेंशियूशन में पूरा किया जा सकता है
$user_service = Container::get(UserService::class);
// Container द्वारा बनाए गए इंस्टेंशियूशन में पूरा किया जा सकता है
$log_service = Container::make(LogService::class, [$path, $name]);
```

## एनोटेशन इनजेक्शन
निर्माण फ़ंक्शन डिपेंडेंस के अलावा, हम एनोटेशन इन्जेक्शन का उपयोग कर सकते हैं। पहले के उदाहरण पर जारी रखते हुए, `app\controller\UserController` को निम्नलिखित रूप में बदला जा सकता है:
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
इस उदाहरण में `@Inject` एनोटेशन इनजेक्शन का उपयोग किया गया है, और `@var` से ऑब्जेक्ट टाइप की घोषणा की गई है। यह उदाहरण निर्माण फ़ंक्शन इनजेक्शन के प्रभाव को बताता है, लेकिन कोड अब संक्षेपित है।

> **नोट**
> webman 1.4.6 संस्करण से पहले कंट्रोलर पैरामीटर इनजेक्शन का समर्थन नहीं करता है, उदाहरण के लिए निम्नलिखित कोड समर्थित नहीं होगा जबतक webman<=1.4.6 नहीं है:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // 1.4.6 संस्करण से पहले कंट्रोलर पैरामीटर इनजेक्शन समर्थित नहीं होगा
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'Hello and welcome!');
        return response('ok');
    }
}
```

## कस्टम निर्माण फ़ंक्शन इनजेक्शन

कभी-कभी निर्माण फ़ंक्शन इनपुट को वास्तविक इंस्टेंस की बजाय स्ट्रिंग, संख्या, सरणी आदि के रूप में प्राप्त किया जा सकता है। उदाहरणकेतु, मेलर निर्माण फ़ंक्शन को सीपीटीपी सर्वर आईपी और पोर्ट को पास करने की आवश्यकता होगी।
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
        // ईमेल भेजने का कोड छोड़ दिया गया
    }
}
```

इस प्रकार के मामले में, उपरोक्त निर्माण फ़ंक्शन ऑटो इनजेक्शन का उपयोग सीधे किया नहीं जा सकता, क्योंकि `php-di` नहीं जानता है कि `$smtp_host` और `$smtp_port` की मूल्य क्या हो सकता है। इस प्रकार के मामले में कस्टम इनजेक्शन का प्रयास किया जा सकता है।

`config/dependence.php`(फ़ाइल मौजूद नहीं है तो कृपया खुद बनाएं) में निम्नलिखित कोड शामिल करें:
```php
return [
    // ... अन्य कॉन्फ़िगरेशन को यहां नजरअंदाज कर रहा है
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
इस तरह से जब डिपेंडेंस इनजेक्शन को `app\service\Mailer` इंस्टेंट की आवश्यकता होती है, तो यह कांफ़िगरेशन में बनाए गए `app\service\Mailer` इंस्टेंट का स्वचालित रूप से प्रयोग करेगा।

हम देखते हैं कि `config/dependence.php` में `new` का उपयोग `Mailer` क्लास का नमूना नहीं है, यह इस उदाहरण में कोई समस्या नहीं होती है, लेकिन सोचिए कि यदि `Mailer` क्लास किसी अन्य क्लास की आवश्यकता होती है या `Mailer` क्लास में एनोटेशन इनजेक्शन का उपयोग किया जाता है, तो `new` से आरंभिकीकरण `डिपेंडेंस` ऑटो इनजेक्शन को पूरा नहीं कर सकता। समाधान यह है कि हम अपनाए गए इंटरफ़ेस इनजेक्शन का उपयोग करके इसे आरंभिकीकरण करें, `Container::get(क्लास नाम)` या `Container::make(क्लास नाम, [निर्माण फ़ंक्शन पैरामीटर])` मेथड के माध्यम से।

## अन्य अपने संपादन का संदर्भ

