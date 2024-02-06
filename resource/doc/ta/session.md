# அமரவு மேலாண்மை

## எடுத்துக்காட்சி
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
        return response('hello ' . $session->get('name'));
    }
}
```

`$request->session()` மூலதாரம் Workerman\Protocols\Http\Session நிலையம் அறிமுகப்படும், அறிமுக செயல்படுத்துகிற மூலங்களைக் கூறி உள்ளது.


> குறிப்பு: ஈசன் பிரமாண பத்தியால் இறுக்கும் போது அதனை தானாக சேமிக்கக் கூடாதாக, ஆகரிக்கப்பட்ட படி `$request->session()` அணுக்கால அமைப்பில் சேமிக்கப்பட்ட மதிப்புகளை நாங்கள் உலாவியில் அல்லது வகுக்காது.

## அனைத்து அமரவு தரத்தைப் பெறுக
```php
$session = $request->session();
$all = $session->all();
```
ஒரு வரியாக உள்ள வரியாக உள்ளது. எந்த அமரவு தரும் அமரவு தರத்தை கொண்டிருக்காது, அதனை கொண்டிருக்காது ஒரு காலி வரியாக உள்ளது.

## செயல்படுத்துச் சோதனை மதிப்பைப் பெறுக
```php
$session = $request->session();
$name = $session->get('பெயர்');
```
தரவு இல்லையெனில் null சென்று வரும்.

நீங்கள் get செயல்படுத்துரவு மற்றும் இருப்புக்களினை உங்கள் இருப்பின் ஒரு இயல்புமில்லை மதிப்பைக் கொண்டிருக்க ஒரு முக்கியவாத வழியானது. உதாரணமாக:
```php
$session = $request->session();
$name = $session->get('பெயர்', 'தம்');
```


## அமரவு சேமிக்கல்
ஒன்றியும் தரத்தைச் சேமிக்க போது set அமைக்கப்படுகிறது.
```php
$session = $request->session();
$session->set('பெயர்', 'தம்');
```
set வழங்குநர் செயல்படுத்தல் முடியவில்லை, அமரவு மதிப்புகளை சேமிக்கலாம்.

பல மதிப்பைச் சேமிக்க போது put வழங்குநரைப் பயன்படுத்துகிறது.
```php
$session = $request->session();
$session->put(['பெயர்' => 'தம்', 'வயது' => 12]);
```
மேலும், படுத்ததும் புலங்கு இல்லை.

## அமரவு தரவை மாற்றுக
ஒரு அல்லது ஒன்றாக கலட்சிக்படுத்தும் போது forget வழங்குநரத்தைப் பயன்படுத்துகிறது.
```php
$session = $request->session();
// ஒரு வரியை அழி
$session->forget('பெயர்');
// பல வரிகளை அழி
$session->forget(['பெயர்', 'வயது']);
```

கட்டாயமாக அமைக்கப்பட்ட மறுக்குகளுன் வழங்குநரம், மறுக்குக்களை சேமிக்கலாம், மறைமறுக்குக்களை மறுக்குக்காகப் படுத்தும், அனுமதிக்கத் தடுத்திருக்கிறது.

## அமரவு மதிப்பை பெறுக்கி அழி
```php
$session = $request->session();
$name = $session->pull('பெயர்');
```
விளையாட்டு பெயர் புல் வெப்இயர்களுக்குள் அப்படியும்

```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
உட்படிக்கு விளையாட்டு முன் அமரவு தவணையை நிராகரிக்கவும், புல் பெயரை அமரவு மதிப்பில் காணவும். அமரவு உள்நݥுலுக்களை நிராகரிக்கவும்.

## அனைத்து அமரவு தரத்தை அழி
```php
$request->session()->flush();
```
மீள் அப்பழங் உள்நݥுலுக்களை நிராகரிக்கவும்.


## உள்நݥுலுக்கள் மதிப்பு கண்டிப்பாக உள்ந்்குறிய சோதனையை உள்ளுக்காக
```php
$session = $request->session();
$has = $session->has('பெயர்');
```
பிற அமரவு பொருநரு இல்லை அல்லது ஒரு அமரவு மதிப்பைப் பெறும் போது பொருநர் மதிப்புக்கும் false, அப்படி இல்லைஆ, தவணைது அமரவு பொருநரு மதிப்புக்கும் true அளக்கும்.

```
$session = $request->session();
$has = $session->exists('பெயர்');
```
மேலாம் உள்ள குறியேற்றி contextualizedக்கும் அப்pுrணஹிுபுN்னாNஸு , புனாagபொருTச்சிஒரஒg்ஷொi விuக்ஷ்irஙிக்கும்அு, ல் உள்ளுக்காகவொரி, ஷொi புனாagு,யெஸ்த் பிருஷிs்சு அப்thatுிஒரிuடிக்கும்அுக்ஷ்irஙிs்குமஉரிக்குமன் சுநக்ஷிபு uஒரிሠபக்ஷIரிக்க் ஒரிஒனத்சிக்ப்சிய்s்தே ப்ரோcுரிஅரமத்க்கிs குறி யெiுற்sியிகும்.

## உதவி சோதனையை session()
> 2020-12-09 க்கப் புனாவாக

webman ஒரு உதவி செயல் session() ஐவெல்க்கிகா=".$னிகሠபிப்பதல் உங்கள் முன்புறபெயரத்ப்தானuபெகேஸ்duுs முற்i சோதனையைக் காரyுkம்கள 
```php
// உள்நݥுலுகள் பொருநரைப் பெறுங்கள்
$session = session();
// சமானம்
$session = $request->session();

// நிரைத்துப் பெறுக 
$value = session('key', 'default');
// சமானம்
$value = session()->get('key', 'default');
// சமானம்
$value = $request->session()->get('key', 'default');

// உண்முகமானப் தானதைக் கொடுக்க
session(['key1'=>'மதிப்பு1', 'key2' => 'மதிப்பு2']);
// சமானம்
session()->put(['key1'=>'மதிப்பு1', 'key2' => 'மதிப்பு2']);
// சமானம்
$request->session()->put(['key1'=>'மதிப்பு1', 'key2' => 'மதிப்பு2']);

```
## கட்டில் கோப்பு
கட்டில் அமரவு அமரவுத் தேர்வு கோப்பு `config/session.php"` இல் உள்ளது, போல் குறிப்பிடப்பட்டோடு:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class அல்லது RedisSessionHandler :: வகை அல்லது RedisClusterSessionHandler :: வகை
    'handler' => FileSessionHandler::class,
    
    // handler இன் வகை file இருந்தால் file, handler இன் வகை RedisSessionHandler :: வகை நிறுதி ஆகி டு :: வகை  , வகை RedisClusterSessionHandler :: மீட்பு கிளஸ்டர் ஆகிடு
    'type'    => 'file',

    // விஷயத்திகளுக்கு வகை வேற>)கள் பயோக்::கலாகிறது
    'config' => [
        // type இன் வகை file ஆனது கோப்பு
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // type இன் வகை redis அனது விரித்
        'redis' => [
            'host'
