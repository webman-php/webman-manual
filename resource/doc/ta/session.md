# செஷன் மேலாண்மை

## உதாரணம்
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('வணக்கம் ' . $session->get('name'));
    }
}
```

`$request->session();` படிப்பான் மூலம் `Workerman\Protocols\Http\Session` உத்தரவின் பிணைய பெட்டி பெறலாம், பொருந்தக் கூறுகள் மூலம் பெட்டியில் துணைத்துள்ள துவக் கூற்றுகளையும் மாற்றுகளையும் சேமித்துக்கொள்ளலாம்.

ஐயராவது: செஷன்களுக்கு உடனாக `session` பொறியியல் சேமித்து வைக்கப்படும், அதனால் `Workerman\Protocols\Http\Session` அகரித்தலானதுக்குச் செல்கிறபோது குழப்பமாகவில்லை.

## அனைத்து செஷன் தரவுகளைப் பெறுவது
```php
$session = $request->session();
$all = $session->all();
```
அது ஒரு வரம்பரப்பட்ட அணிகளை பின்பற்றுகின்றது. ஒவ்வொன்றும் செஷன் தரவுகள் எதுவும் இல்லையெனில், ஒரு இலேங்களான அணி பின்கொடுக்கப்படும்.



## செஷன்கள் ஒரு மதிப்பைப் பெறுகின்றது
```php
$session = $request->session();
$name = $session->get('name');
```
கருவிகள் ஏதாவது இல்லையெனில் `null` பின்கொடுக்கப்படும்.

நீங்கள் இரண்டாவது பதிப்பிற்கு இரண்டாம் அளவிற்கு ஒரு முன்மதிப்பை வழங்கி அளவில்லாத முன்மதிப்பை உள்ளிடலாம். உதாரணமாக:
```php
$session = $request->session();
$name = $session->get('name', 'முன்னதாவது');
```


## செம்மை சேமிக்குகின்றது
ஒரு மதிப்பைச் சேமிக்க பொதுவாக பயன்படுத்தப்படுகிறது.

```php
$session = $request->session();
$session->set('name', 'அண்ணன்');
```
செம்மை திறக்குலத்தலைப் பெறுகின்றது, செஷன் பொறியின் அளவும் ஐயப்படுத்தும்.

பல மதிப்புகளை சேமிக்க பெறுகின்றதும், புறம் பொதுவாக பயன்படுத்தப்படுகின்றது.

```php
$session = $request->session();
$session->put(['name' => 'அண்ணன்', 'age' => 12]);
```
## செஷன் தரவை நீக்குவதற்கு
`forget` முறையைப் பயன்படுத்தி ஒரு அல்லது பல செஷன் தரவை நீக்கலாம்.
```php
$session = $request->session();
// ஒரு உரையை நீக்குக
$session->forget('name');
// பல உரையை நீக்குக
$session->forget(['name', 'age']);
```

மற்ற நிரல் இலவச delete முறையைக் கொண்டுள்ளது, forget முறையின் வேறுபாடு என்னவென்றால், அது ஒரு உரையை மட்டும் நீக்க முடியும்.
```php
$session = $request->session();
// சென்று சென்றவரை அடிப்படையாக உள்ளது $session->forget('name');
$session->delete('name');
```

## செஷன் மொத்த பகுதியைப் பெற்று அழிக்க
```php
$session = $request->session();
$name = $session->pull('name');
```
மேலும் குறிப்பிட்ட குறிச்சொல்லுடன் பயன்படுத்துவது
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
கொண்ட செஷன் அமைப்பு இல்லையானால், முடிவுக்கு null ஆகும்.


## அனைத்து செஷன் தரவையும் நீக்கு
```php
$request->session()->flush();
```
எந்த மதிப்பும் திரும்பவில்லையெனில், செஷன் மற்றும் மேலதிக மற்றும் பின்வரும் செயல்திறன்களிலிருந்து தானாகவே அழிக்கப்படும்.


## கொண்ட செஷன் தரவு உள்ளதா என்பதை சோதிக்கவும்.
```php
$session = $request->session();
$has = $session->has('name');
```
மேலும், கொண்ட செஷன் அமைப்பு இலவச அல்லது அது சீரமைந்து மதிப்பு அணுகக்கூடியதானாகின்றன, தவிர்க்கப்பட்டால் false பின் வரும், இல்லையெனில் true பின் வரும்.
```
$session = $request->session();
$has = $session->exists('name');
```
மேலும், வழிமுறையாக exists தொகுப்பும் செஷன் தரவு உள்ளதா என்பதைப் பரிசோதிக்க பயன்படுகிறது, பின்வரும் வேறுபாடு என்னவென்றால், அது null ஆனாலும் true பின் வரும்.

## உதவி செயலி செஷன்()
> 2020-12-09 இல் இணைக்கப்பட்டது

எஸ்டலிஷ், இரண்டு செயல்முறைகள் `session()` ஒருநாள் செயல்முறையைப் பயன்படுத்தி அதே செயல்முறைகளை முடியும்.
```php
// செஷன் முதன்மை பெண்களைப் பெறுங்கள்
$session = session();
// அதுவே என்றால்
$session = $request->session();

// ஒரு மதிப்பைப் பெறுக
$value = session('key', 'default');
// அதன் ஒழுங்கு
$value = session()->get('key', 'default');
// அதைப் பெறுக
$value = $request->session()->get('key', 'default');

// செஷன் முதலில் மதிப்பை அளிக்குக
session(['key1'=>'value1', 'key2' => 'value2']);
// ஒரேபிரதியில்
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// ஒரேபிரதியில்
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);

```
## கட்டமைப்பு கோப்பு
செஷன் கட்டமைப்பு கோப்பு `config/session.php` ஜெயில் உள்ளது, இதன் உருவாக்கம் பின்கொடுக்கப்பட்டுள்ளது:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class அல்லது RedisSessionHandler::class அல்லது RedisClusterSessionHandler::class
    'handler' => FileSessionHandler::class,
    
    // handler என்பது FileSessionHandler::class உருவாக்கப்பட்டால் மட்டுமே 'file' ஆகும்,
    // handler என்பது RedisSessionHandler::class உருவாக்கப்பட்டால் மட்டுமே 'redis' ஆகும்
    // handler என்பது RedisClusterSessionHandler::class உருவாக்கப்பட்டால் மட்டுமே 'redis_cluster' ஆகும், அது பழையவகை உருப்படிகளால் இணைக்கப்பட்டது
    'type'    => 'file',

    // handler வேண்டிய சரக்குகள் வேறுபாடு பயன்படுத்தும்
    'config' => [
        // type என்பது file ஆனால் உள்ள சரத்து
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type என்பது redis ஆனால் உள்ள சரத்து
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // சேமிப்பு ஐடி சேமித்துக்கப்பட்டிருத்தம் சொல்

    // === 1.3.14வரை மேல்படியுள்ள கட்டமைப்புகளுக்கு webman-framework >= 1.3.14 workerman >= 4.0.37 ===
    'auto_update_timestamp' => false,  // செஷன் யானை தானாகவே புதுப்பிக்க அல்லது மீண்டும் தகவல் கலாய்த்தல், இயல்பானது மூடப்பட்டுவிடுவது
    'lifetime' => 7 * 24 * 60 * 60,          // செஷன் காலாவதிக்குள் நேரம்
    'cookie_lifetime' => 365 * 24 * 60 * 60, // சேமிப்பு ஐடி டிக்கவும் குக்கியின் நேரம்
    'cookie_path' => '/',              // சேமிப்பு ஐடி டின் பாதை
    'domain' => '',                    // சேமிப்பு ஐடி டின் டெய்மைன்
    'http_only' => true,               // httpOnly ஐ இயல்பானது, இயல்பானதாகும்
    'secure' => false,                 // மடல் இணைக்க மீது செய்யப்படும் சேஷன், இயல்பானது மூடப்பட்டுவிடுவது
    'same_site' => '',                 // CSRF தாக்குமானத்தையும் பயனர் ட்ரேட்டிங் ஐயும் முடக்குகிறதாக மேற்கொண்டு பின்பற்றி வேண்டும், முறையைத் தேர்ந்தெடுக்கவும்/வேலை செய்யவும்
    'gc_probability' => [1, 1000],     // செருகும் அவற்றின் வேலைச்செயல்முறை
];
```

> **குறிப்பு** 
> `வடிகட்டி ஒவ்வொன்றும் செஸ்ஷன்ஹேன்ட்லரின் நேயபகுதியின் பதிப்பாகவும், முன்பு வடிகட்டி:
> use Webman\FileSessionHandler;
> use Webman\RedisSessionHandler;
> use Webman\RedisClusterSessionHandler;
> மாற்றப்பட்டது:
> use Webman\Session\FileSessionHandler;
> use Webman\Session\RedisSessionHandler;
> use Webman\Session\RedisClusterSessionHandler;
## செல்லுபடியாகும் கடமைகள் அமைப்பு
webman-framework < 1.3.14 இல், webman இல் சேஷன் காலாவதியை `php.ini` உள்ளிட்ட அமைப்பில் வேண்டுகிறது.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

1440 விநாடிகளாக தெரிந்து கொள்ள உருவாக்கும் முன்பு, அமைப்புகள் பீட்டும்
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **குறிப்பு**
> `php.ini` ஐ கண்டறிய வேலையை செய்ய அர்டேருக்கு `php --ini` கட்டளையைப் பயன்படுத்தலாம்.
