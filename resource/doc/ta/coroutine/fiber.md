# கூடுறவை

>கூடுறவை தேவைகள்
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> வெப்மேன் மேம்படுத்தும் கோமான்ட் `composer require workerman/webman-framework ^1.5.0`
> பணியாளர் மேம்பாடு கோமான்ட் `composer require workerman/workerman ^5.0.0`
> நாங்கள்** சேர்த்தலுக்கானது் `composer require revolt/event-loop ^1.0.0` மூலம் பிபாரிக்க வேண்டும்

# மாதாநிலைகள்
### தள்ள பதில்

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // 1.5 நொடிக்கு தள்ளவும்
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()`  உள்ளீடு PHP ஐப் பயன்படுத்திய `sleep()` முறையைப் போதுமாகக் கொண்டுள்ளது, இவ்வுள்ளீடு  `Timer::sleep()` புரிந்திருக்காது என்று மாறிக்கொள்ளக்கூடாது. 


### HTTP கோரிக்கை தொடர்பு செய்யும்

> **குறிப்பு**
> `composer require workerman/http-client ^2.0.0` ஐப் பயன்படுத்தி பதிவிறக்க வேண்டும்

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
        $response = $client->get('http://example.com'); // தன்மையான முறையில் வேலையைத் தொடக்கும் பதிவைப் பயன்படுத்துக
        return $response->getBody()->getContents();
    }
}
```
அதனால் `$$client->get('http://example.com')` கோரிக்கை தள்ளும் பொழுது இது தடுப்பூட்டப்படாதது, இது வல்லுநிலையான முறையில் webman இல் தடுப்பூட்டப்படாது கூடியது.

மேலும் விவரங்களுக்கு [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) பார்க்க

### support\Context பாடத்தை சேர்க்கவும்

`support\Context` தரவை சேமிப்பது பயனர்கள் மேலும் விளைகின்ற தன் அணைக்கலனைப் போச்சி போலிருக்கும், மேலும் வருகின்ற கருத்து தரவு தானாக அழிக்கப்படும். அதுபோல, context தரவு உள்ளாட்சியானது வருகின்ற தரவுகள். `support\Context` பைபர், சுவோல் மற்றும் சுவோ கூடுதலும் அடுத்தவை போன்ற கூரியா சுவோல் சூழ்நிலைகளை ஆதரிக்கிறது.



### சுவோல் கூடுதல்
swoole நீட்டில் நிறுவப்பட்டது (தேவைப்பட்டால் உடனே பெரியது) உங்கள் தகவல்மை / மேம்படுத்தல் config/server.php இல் அமைப்புகளைத் திறந்துவைக்கவும்
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

மேலும் விவரங்களுக்கு [workerman நிகழ்நிலை இயங்கம்](https://www.workerman.net/doc/workerman/appendices/event.html) பார்க்கவும்

### புரைவரப்பு மட்டும் மடதில் சுவோல் ஐக்குரிக்கும் பொருட்பட்டது

கூடுறவை மாநிலையில் **வேறுபாடு** பதியும் பணியமாக்கப்படக்கூடிய நிலையினிடத்தில் **கூவுகப்படக்கூடாது** நிரல்பாகம் துண்டுமான இருப்பிடங்கள் அல்லது நிலைப்படுத்தப்படும், உதாரணமாக

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

முதன்முதலில் பகர்வதற்கான முறையில் **உரைப்படத்தை தந்துவிட்டோம் என்பது அவசியமன்று குணமத்துடன் பார்க்கவும்** 2 ஆம் நிலை கோரிக்கை 5 என்னும் கடகுரை முன் அரிதானது. எனவே, முதல் கோரிக்கை தூக்கப்பட்ட போது தருவதைக் கைவிட்டுப் பின்னர் கோரிக்கை முடிந்ததும் அதனை மிகுதியாக மட்டும் மாற்றம் செய்தது.

**சரியான முறை, கோண்டெக்ஸ்ட் மூலம் அணிக்கலைச் சேமித்து பயன்படுத்துக**
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

**உள்ளீடு சுருக்கப்படுவதால் புரைவரவர்க்கப்பட்ட மேலும் குரூபோல் பெயர்ப்புக்களை ஒரே ஆணைக்குட்பட்ட அணையல்புகளாக கொண்டு வைக்கும், அதுவும் கூவுவதற்கான வழிமுறையைக் குணமத்துடன் வழங்குகிறது.
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
அதனால்`$name`  இணைப்புக்கள் ஒத்தலாமைக்கூடியது, அப்போது அமையாது மாறிக் கொண்டுப்போகாது, அடுத்த கோரிக்கையால் நிரல் படைப்பது மிகுந்தே எண்களத்தில் நிரலைத் தாக்குவதற்குக்கும் ( சுவையும் கோரிக்கும் வழிஞ்சிலும் என கிடைக்க முடியவில்லை).

# கூடுறவை பற்றி
கூடுறவை ஒரு வருவாயம் அல்ல, கூடுறவை நுழைவு கொள்கையைப் புரிந்திருக்கும்  பின்னர் கூடுறவையை எட்டுவது ஒரு மீள்படி தேவை, நிலைப்படுத்த வேண்டும்191கூடுறவை மயங்கிய பிபாரிக்கையைப் பயன்படுத்தினால்.

webman மூலம் மேலுள்ள வேலைகள் தடுப்பூட்டப்பட்டிருக்கின்றன, [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) கடைசியில் அதைப் பார்க்கவும் அவற்றைப் படிக்கவும் இலவசமானதுலே.
மூலம் எளிதில் அடிப்படைப் பதிப்பினைப் பகுப்பதாக சொல்லப்படுகின்றது அதனால், நீங்கள் சவால்களைச் சூழ்நிலையில் பிரதிபலிக்க முடியும் ஆதாரமாக கூடுறவையை செயல்படுத்த முடியாது. 

webman முன்மாதிரியான பிழை கண்டுபிடிக்க சுருக்கமாக நிரல் படைப்பை குறிக்க மிகவும் சில விருப்பங்களைப் பயன்படுத்த வேண்டும். 
