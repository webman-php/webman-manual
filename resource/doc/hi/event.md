# वेबमैन इवेंट लाइब्रेरी webman-event

[![लाइसेंस](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

इवेंट मध्यवर्तिन तुलना में इवेंट का फायदा यह है कि इवेंट मध्यवर्ती से अधिक सटीक स्थानांतरण (या स्थानांतरण फाइनग्रैन्ड पार्टिलस) कर सकता है, और कुछ व्यावसायिक स्थिति के विस्तार के लिए अधिक उपयुक्त है। उदाहरण के लिए, हमारे पास उपयोगकर्ता पंजीकरण या प्रवेश के बाद कुछ कार्य करने की आवश्यकता होती है, जिसमें इवेंट सिस्टम के माध्यम से प्रवेश के कार्य का विस्तार किया जा सकता है, मूल कोड को भिन्न किया जा सकता है, जिससे सिस्टम की मोस्ती कम होती है, साथ ही साथ बग की संभावनाएँ कम हो जाती हैं।

## प्रोजेक्ट पता

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## निर्भरता

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## स्थापना

```शैल स्क्रिप्ट
कॉम्पोजर आवश्यक tinywan/webman-event
```
## सेटिंग्स 

इवेंट सेटिंग्स फ़ाइल `config/event.php` की सामग्री निम्नलिखित है

```php
return [
    // इवेंट सुनने वाला
    'listener'    => [],

    // इवेंट सब्सक्राइबर
    'subscriber' => [],
];
```
### प्रक्रिया प्रारंभ करने की सेटिंग

`config/bootstrap.php` खोलें, और निम्न को जोड़ें:

```php
return [
    // यहाँ अन्य सेटिंग छोड़ दी गई है ...
    webman\event\EventManager::class,
];
```
## त्वरित आरंभ

### इवेंट परिभाषित

इवेंट क्लास `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // इवेंट नाम, इवेंट की यूनिक पहचान

    /** @var array */
    public array $log;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        return $this->log;
    }
}
```

### इवेंट सुनना

```php
return [
    // इवेंट सुनने वाला
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### इवेंट सब्सक्राइब

सब्सक्राइबर क्लास `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: विधि विवरण
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: इवेंट ट्रिगर
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // कुछ व्यावसायिक तार्किक
        var_dump($event->handle());
    }
}
```

इवेंट सब्सक्राइब
```php
return [
    // इवेंट सब्सक्राइब
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### इवेंट ट्रिगर

`LogErrorWriteEvent` इवेंट को ट्रिगर करें।

```php
$error = [
    'errorMessage' => 'त्रुटि संदेश',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

नतीजे

![प्रिंट आउट नतीजा](./trigger.png)

## लाइसेंस

इस प्रोजेक्ट का लाइसेंस [Apache 2.0 license](LICENSE) के तहत है।
