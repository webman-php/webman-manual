# கையக நிருக்கம் அதிவாரம்

## வரைபடம்
`config/exception.php`
```php
return [
    //இங்கு கையக நிருக்க நிராகம் அமைக்கவும்
    '' => support\exception\Handler::class,
];
```
பல பயன்பாடுகளில், நீங்கள் ஒவ்வொன்றுக்கும் கையக நிருக்க நிராகமை அமைக்கலாம், ஆனால் [பல பயன்பாடு](multiapp.md) பார்க்கலாம்.


## இயல்பு கையக நிருக்க நிராகம்
webman-இல் இயலும் கையக நிருக்க நிராகம் `support\exception\Handler` நிருப்பத்தால் இயலும். இயலுமான இயல் உருவாக்கலாம் `config/exception.php` கோப்புக்கட்டியில் உள்ள வரைபடத்தை மாற்றி வரைபடங்களைப் பார்க்க முடியும். கையக நிருக்கத்தான், `Webman\Exception\ExceptionHandlerInterface` இயல்மலையில் இயல்மலை செயலப்படுத்துவதற்கான மொழிபேசி, பணியை மிகுந்த விரும்பப்படும்.

```php
interface ExceptionHandlerInterface
{
    /**
     * அறிக்கை செய்
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * வருவாய்த் தயார்
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e): Response;
}
```
## வருமானம் மற்றும் பதில்
வேலை செய்யும் பணியில் இருக்கும் `வருவாய்` மொழிகள் சுத்து ஒரு `வருவாய்` மதிப்பை உருவாக்குகிறது. இத்தனை `%` கெடுக்கலாம் என்று கூறுகள் எளிதாக்கப்படும்படி உலாவிகளும் அங்கு உலாவிகளும் வேலை செய்து கொள்ளும். உதாரணமாக `% app.debug = true` என்று அழைக்கலாம். அழைக்கலாம்.

JSON உருவாக்கம் மற்றும் அடைப்புக்களை மின்னமும் கொண்டு அனுப்புகின்றது. உதாரணமாக

```json
{
    "code": "500",
    "msg": "பிழை விவரம்"
}
```
`app.debug=true`, JSON வில் உள்ள தனிப்பட்ட இரகசிய தகவல்களையும் பிரத்தியேக சூழலில் உள்ள மெட்டுபாதையையும் பார்க்கும்.

உங்கள் பணிக்கு முதன்முதலில் உங்கள் நிர்வாக பூர்வமாகச் சேமித்து தொடங்க வேண்டும்.

# வெளியேற்று விழிப்புணவு முறைப்படியாக்குதல்
வழக்கத்தை மூடு நகர்த்தி மறுழுதுபோக்கும் போது நாம் ஒரு தரவு அமைத்துக் கொண்டுள்ளோம், மேலும் அதை மீண்டும் மதிப்பிடும், பிணைப்பு பூர்வமாக மீண்டும் அழைக்கின்றோம். பார்க்க உதவும் மாற்றத்தை அதிகப்படுத்தி, 

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->checkInput($request->post());
        return response('hello index');
    }
    
    protected function checkInput($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('தரவு பிழை', 3000);
        }
    }
}
```

மேலும் பின்வருவன, JSON அமைப்பை மறுழுதுபோக்கும் மேலும் JSON மதிப்புகளை அடைப்பதன் மூலம் அழைக்கும். உதாவி உள்ளது.

நீங்கள் உங்கள் வேலை விழிப்புணவை பிளஜின்க் வழங்க முடியும்.
## தனித்தனிய பணி எச்.பி. பைரமிட்டி

மேலே உள்ள பதிலில் உங்கள் கேள்விக்கு பொருந்துமான படிமங்கள், எடுத்துக்காட்ட முடியும் என்றால் உங்கள் மூலம் உரிமைப்படுத்தப்பட்ட, உதர
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        return json(['msg' => 'தானியங்கி விஷமிசை வெளியின்றி JSON  வடிவத்துடன் மாற்றிய கோப்பு  '], 400]);
    }
}
```
இந்தப் பாதிப்பில் நீங்கள் உங்கள் கேள்விக்கு பொருந்துமான படிமங்களைப் பெறலாம்.
