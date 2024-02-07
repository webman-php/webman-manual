# webman நிகழ்வு தொகுப்பு webman-event

[![உரிமை](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

நிகழ்வு மதிப்புரை இடுகைகளும் நடைவேற்றி, மதிப்பு நிகழ்வுகளுக்கு மேலும் அளவு நீண்டவை (அல்லது அளவு சிறியவை) விளைவாக உள்ளது என்பது விண்ணப்பம்களுக்கு சிறப்புபடுவது மற்றும் செயல்பாட்டு உற்பத்திக்கு அதிக பொருட்களுக்கு அனுமதிப்பரை நைனமிக்கும். உதாரணமாக, நாங்கள் பொதுவாக பயனாளர் பதிவு அல்லாத உள்ளமைவுகளை செயல்படுத்த வேண்டுமென்று எந்த படித்தமுக குறித்து முட்டம்செய்வு வழக்குகளைச் செய்துகொள்ளுவது மற்றும் அதை உண்மைப்படுத்துவது மூலத உள் இதயமாக்களின் இயற்கை வளிப்பூர்வமாகக் குறைக்கிறது என்றும் கூறலாம்.

## திட்டம் இருப்பு

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## ஆதாரம்

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## நிறுவுக

```shell script
composer require tinywan/webman-event
```

## அமைப்பு

நிகழ்வு அமைப்பு கோப்பு `config/event.php` உள்ளடக்கம் கீழே உள்ளது

```php
return [
    // நிகழ்வு அனுப்பு
    'listener'    => [],

    // நிகழ்வு இயக்குபர்
    'subscriber' => [],
];
```

### நடைவூட்டி தொடக்கம்

`config/bootstrap.php` ஐ திறக்க, பின்னர் பிடிக்கும் அமைப்பினை சேர்க்கவும்:

```php
return [
    // இங்கு மற்றும் விவரங்களை விட மற்றைய அமைப்புகள் ...
    webman\event\EventManager::class,
];
```

## விரைவான தொடக்கம்

### நிகழ்வுகளை வொன்றின்

நிகழ்வு வகை `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // நிகழ்வு பெயர், நிகழ்வு ஒருமிதமான அடையாளம்

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

### நிகழ்வுகளை கவனித்தல்
```php
return [
    // நிகழ்வு கேள்வி
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### நிகழ்வு சந்தேக்கரவூட்டி

சந்தேக்குன்னர் `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: முறை விவரம்
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: நிகழ்வை இயக்கு
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // சில உச்சித்தன வாழ்வியலை
        var_dump($event->handle());
    }
}
```

நிகழ்வுகளை சந்தேக்குன்னர்
```php
return [
    // நிகழ்வு சந்தேக்குனர்
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### நிகழ்வு உயர்வெடுத்தருகுவான்

`LogErrorWriteEvent` நிகழ்வை உயர்வாக்கவும்.

```php
$error = [
    'errorMessage' => 'பிழை செய்தி',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

இயக்கம் முடிவு

![அச்சிடில் முடிவு](./trigger.png)

## உரிமை

இந்த திட்டம் [பாசிக் 2.0 உரிமை](LICENSE) க்கு அனுமதி உண்டு.
