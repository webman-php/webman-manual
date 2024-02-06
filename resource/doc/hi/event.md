# Webman इवेंट लाइब्रेरी webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

इवेंट मध्यवर्तीकरण की तुलना में इवेंट का फायदा इस से होता है कि इवेंट मध्यवर्तीकरण से बेहतर निश्चित स्थान (या बोल सकते हैं कि अधिक सूक्ष्म) होता है, और कुछ व्यावसायिक परिस्थितियों के विस्तार के लिए अधिक उपयुक्त होता है। उदाहरण के लिए, हमें आमतौर पर उपयोगकर्ता दर्ज करने या लॉगिन करने के बाद कुछ कार्य करने की जरूरत होती है, इवेंट सिस्टम के माध्यम से प्रारंभिक कोड में अतिक्रमण किए बिना लॉगिन के कार्य के विस्तार को संपन्न कर सकते हैं, जो सिस्टम की संलग्नता को कम करता है, साथ ही साथ बग की संभावनाओं को भी कम करता है।

## परियोजना पता

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## आवश्यकताएँ

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## स्थापना

```shell script
composer require tinywan/webman-event
```

## कॉन्फ़िगरेशन

इवेंट कॉन्फ़िगरेशन फ़ाइल `config/event.php` की सामग्री निम्नलिखित है

```php
return [
    // इवेंट सुनना
    'listener'    => [],

    // इवेंट सदस्य
    'subscriber' => [],
];
```

### प्रक्रिया प्रारंभ करने का विन्यास

`config/bootstrap.php` खोलें, और निम्न विन्यास जोड़ें:

```php
return [
    // यहां अन्य विन्यास छोड़ दिए गए हैं ...
    webman\event\EventManager::class,
];
```

## तेज़ आरंभ

### इवेंट परिभाषित करें

इवेंट क्लास `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // इवेंट नाम, इवेंट का अद्वितीय पहचान है

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
    // इवेंट सुनना
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### इवेंट सदस्य

सदस्य क्लास `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: मेथड विवरण
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: इवेंट ट्रिगर करना
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // कुछ विशिष्ट व्यावसायिक तार्किक
        var_dump($event->handle());
    }
}
```

इवेंट सदस्य
```php
return [
    // इवेंट सदस्य
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

कार्य परिणाम

![प्रिंट आउट](./trigger.png)

## लाइसेंस

इस परियोजना का लाइसेंस [एपाची 2.0 लाइसेंस](LICENSE) के तहत है।
