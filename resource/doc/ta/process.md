# தனி போக்கு

வெப்மானில் வெப்மானை போக்கு அல்லது போக்குவரத்தை தனிப்பற்றியதுபோன்று வேலை செய்யலாம் ஊடகவாசங்களை உருவாக்க முடியும்.

> **குறிக்கோவாக்**
> பயனர்களுக்கு வெப்மானை துவக்க `php windows.php` ஐப் பயன்படுத்தி தனி போக்கைத் தொடங்க வேண்டும்.

## தனியுலா http சேவை
கொஞ்சம் விசேஷமான தேவைகளுக்கு, உங்கள் வெப்மான் http சேவையான உள்ளகார குறியீடுகள் மாற்ற வேண்டிய நேரியாகும், இது தனி போக்கைகளைக் கொண்டு அமலாமல்.

உதாரணைக்காக அப்பை app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // இங்கே ரிவைட் செய்க Webman\App இன் முறைகள்
}
```

`config/process.php` -க்கு பின்னால் பின்வருங்கள்

```php
use Workerman\Worker;

return [
    // ... மற்ற வரையாகப் படத்தை வாசிக்க ...

    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // பிரகியாக்க எண்ணிக்கை
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // கோரிக்கை வகை அமைக்க
            'logger' => \support\Log::channel('default'), // பதிச் தினம்
            'app_path' => app_path(), // app இடப்பெயரில் இருந்து
            'public_path' => public_path() // பொது இடப்பெயரிலுள்ள இடம்
        ]
    ]
];
```

> **குறிக்கோவாக்**
> வெப்மான் வெப்மான் உள்ள உள்ளாய்வியை மூட வேறு எதிர்காலத்தில்`config/server.php` இல் `listen=>''` ஐ அமைத்து வேண்டும்.

## தனியுலா வெப்சாகெட் கவர் உதாரணை

புது `app/Pusher.php` 
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> குறிக்கோவாக்: அனைத்து onXXX பணைகளும் பெரும்பாலும் பொது.

`config/process.php` -க்கு பின்னால் பின்வருங்கள்
```php
return [
    // ... மற்ற போக்கி உற்பத்தியாக இடையே ...
    
    // websocket_test  ஆதரிக்கு போக்க பெயர்
    'websocket_test' => [
        // இங்கே காலங்களாக போக் நீதியாக்கி, மேலுறையாக, முறையினார் உருவாக்க Pusher போக்கு வகை
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## தனி கைகளுக்கான போக்கு உதாரணை
புது `app/TaskTest.php` 
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // 10 வினாக்குறைகளில் தரும்பு மேம்பேர் பயிற்ச்சி செய் படம்
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
`config/process.php` -க்கு பின்னால் பின்வருங்கள்
```php
return [
    // ... மற்ற போக்கிகளை உருவாக்க புது ...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> குறிக்கோவாக்: listen உரியாட்டை மறேவழமைப்படுத்தி, count உரியாட்டை இயல்பாக 1 மட்டுமே உள்ளது.

## அமைப்பு கோரக்கொள்கைகள்

ஒரு போக்கு முழு அமைப்பு வரம்புகளின் குறிக்கோவாக் அமைக்கப்பட்டுக் கொள்ளும்
```php
return [
    // ... 
    
    // websocket_test ஆதரிக்கு போக்கு பெயர்
    'websocket_test' => [
        // இங்கே காலங்களாக போக்கு வகை
        'handler' => app\Pusher::class,
        // உழுப்பாகம் ஐப், ஐப் மற்றும் ஏதோ உள்ளடக்கத்துக்கு கென்கன் (தேவையானா)
        'listen'  => 'websocket://0.0.0.0:8888',
        // பெரும்பாலும் (தேவையானா) இயல்பாக 1
        'count'   => 2,
        // வெளியீடு செய்யும் பயனர் (தேவையானா) தற்கையார் பயனரை
        'user'    => '',
        // வெளியீடு செய்யும் பயனர் குழ (தேவையானா) தற்கையார் பயனருக்கு
        'group'   => '',
        // தற்போக்குவசதி உள்ளது அல்லது ஈற்றடி (தேவையானா, இயல்பாக true) (தேவையானா, php >= 7.0)
        'reloadable' => true,
        // reusePort (தேவையானா, இந்த விருப்பத்தை இயலும் போது, php >= 7.0 முதல், இயல்பாக true)
        'reusePort'  => true,
        // கவணம் (தேவையானா, பிரதிக்கை ssl செய்ய உள்ள போது சான்றிய பாதைகளை அமைத்த உள்ளடக்கம்)
        'transport'  => 'tcp',
        // உள்ளடக்கம் (தேவையானா, பஞ்சு இருக்கும்போது ssl இருமுறை இயலும் போது, முதன்மையாக tcp)
        'context'    => [], 
        // போக்கு வகை செயலியின் உறுப்பு கணக்கு, இங்கே உள்ளது போக்ஷார் Pusher வகையின் உறுப்பு கணக்கு (தேவையானா)
        'constructor' => [],
    ],
];
```

## சமைக்கக் கவாசங்கள்
வெப்மான் தனிப்பற்றிய போக்குகள் உண்மையில் ஒரு குறிப்போடிக்குச் செயல்முறை ஒரு சுலபமான அடிப்படைகாரியமாக உள்ளது, அது அமைப்பு மற்றும் வணிகம் கிருபைகளை பிரிக்கின்கள், மற்றும் வெப்மானின் விரும்பப்பெறத்தட்ட உடன் `onXXX` அழைவுகளை ஒரு வழியாக கொண்டு வருகின்றது. மற்ற பயன்பாடுகள் workerman இல்லை.
