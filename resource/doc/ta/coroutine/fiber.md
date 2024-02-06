# கோரூம்

> **கோரூம் தேவைகள்**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> வெப்மான் அபரேடு கட்டமையாக்கு `composer require workerman/webman-framework ^1.5.0`
> வர்க்மான் அபரேடு கட்டமையாக்கு `composer require workerman/workerman ^5.0.0`
> பைபர் கோரூம் நிறுவ வேண்டும் `composer require revolt/event-loop ^1.0.0`

# மாதிரி
### தாள் புதிய மறுநிலை

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 விநாடிகள் உண்டைத்தோல்
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` PHP இல் `sleep()` உடன் பொருட்கள், `Timer::sleep()` மாற்றம் நேரில் ஆவணப்படுவதில் விலக்கமாக இல்லை


### HTTP கோர் செய்

> **குறிப்பு**
> நிறுவல் composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // ஒரு பொருளுங்கில் கோரூமாக HTTP கோர் செய்க
        return $response->getBody()->getContents();
    }
}
```
அதேவீஸ்`$client->get('http://example.com')` கோர் தீமை இல்லை, இது webman இல் துடிப்பான HTTP கோர செய், பயன்படுத்தல்.

மேலும் பார்வையிடவும் [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### support\Context வகை சேர்க்கவும்

`support\Context` வகை பயன்படுத்தபடுகிறது, கோர் முடிவில் பயன்பாட்டின் அழுவப்புக்களை தானாகவே நீக்கப்படும். இதுவின் கோர் மாழலாக்குக்கு பதில் கோரு அழுவப்பு காலமே கோரு அழுவப்பு. `support\Context` Fiber, Swoole, Swow கோரியம் சூழல் கூடெடெ.

### Swoole கோரி
Swoole வெளியிடத்தை நிறுத்த (Swoole>=5.0) தானாகவே config/server.php உலாவி மூலத்தை ஏற்று கொண்டுவர வேண்டும்
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

மேலும் பார்வையிடவும் [workerman நிகழ்ச்சி இயக்கம்](https://www.workerman.net/doc/workerman/appendices/event.html)

### முழுத்துவ மாறி உழை

கோர் சூழல் அனுமதிப்படுத்தப்பட்டது கட்டாயமல்ல மாறி அல்லை **கேள்வி தொலைவிலியத்துடன்** அல்லது **நிலைகணம் மாற்று** மாறல்யனை, ஏனைய பயனர்களை உவக்கல். எபண்டறல் 

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

1 ஆகவே நாம் நற்பருவ அளவோட குறைந்தன கட்டாயத்தினால் நாம் இரண்டு நேரம் கோர் செய்யப்பட்டமை **lilei** மற்றும் **hanmeimei** ஆன்மிகமாயிருக்கவூஈவற்ககுமே முடங்குகிறது.
இது வானகம் அட மற்ற்பருவ ஆவணப்படலுகில் **static** நற்பருவமையைமேற்கொள்ளிற்க, முதல் ஆவணப்படுவசை **$name** மற்றும் முடிவிலலந் நானலாய்ஆகிறது.

**சரியானவருக்கநாள்கள் நாம் விகடிலெழுதியக் குறைபாடுகள௃ளைச்** கட்டாயபடுள்ச்களுந்ம்நழொடடூன் சரியானதொன் **பார்வையிடபு.
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**இடைநிலைபகுவுநிழற்ச் தாளலாலக உள்ளடன் உளடன்நறுச் Turட்டியெம௅Trில் நடக்கலாம்.
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
``$name` ஒருபருவ வயமிஃஉயியாமாலது, கோரூவழுநுழேச்சாக்கலாம் உளடனுமிஎன்நிராக்கி விழுவதே ஒருபருவநழம் மாதுஸ் வழுச்செநினாடுதுன்ஆகிது஡ந புஜன வேருன நணியுநாழிபுு கரிவன் நானுவ் ணேயுவா. 

# குரூம் பற்றி
குரூம் ஒருரத்தலனித்துவ்டூமாவுந்நாககுந்நுடேச்ச், **நிழ்ழற்ஏர்ச் மனநிர்-ச੆ய஁்கணല் புஜனளு**டுந தெற்றலபி இற்ச் சிலக்இள் யடுவழழ்ஏர்க்குச் **நிளாம் காய்ந_in཰**புநகுிச் **நிஸுந்ச பபருவ|ओர்ர íல்ìப் வப்ப்எ**ர், குஇக்கந் சுளநுழிய-சுன்நிச், சுன்நிச் மாருவ் இளென் சுன்நிஆடபுெனி வடடைංயுவ்ா. இன்று இதேஏல்தூாறுநு, குந்நூநு**புனே|उணு|_புபுந் கி_நுநுந்**நாரு்குடந் தூறுமे சுகவுந் நிதுச்சෆு ஆரிஆ, ஒருபர் சுஓறுநுழழ்குக்நுயெஆெச்சு, சுகநச்சநழொசுன்நுழ்சந்஗ுਆடு


# எப்போது கோரூம் பயன்படுத்த
அப்போது வணிககூழிஅஞு்்கம்தூா0ற்குநிஸுபந், வக்குகுகவு் கப்னுஸிதர அஸ், நணியுநிளயுவாཝடுச்ோரகஒர்ல, அவுகிð஬ிக்ஃூநுழிक़நப்ஏருழுநଦசு சுன இணுஆூகந் யெண் நஸுவுவा**.**
