# கட்டுப்பாட்டாளர்

PSR4 கொள்கைக்கு உரிமப்பெயர் பக்கப்பாலை `plugin\{செருகு அடையாளம்}` ஆரம்பிக்கும், எக்கியமாக

புதிய கட்டுப்பாட்டாளர் கோப்பு `plugin/foo/app/controller/FooController.php` உருவாக்கவும்.

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

`http://127.0.0.1:8787/app/foo/foo` ஐ வரும் பேஜ் `வணக்கம் சுருக்கியவர்` என்று முடிவு செய்கின்றது.

`http://127.0.0.1:8787/app/foo/foo/hello` ஐ வரும் பேஜ் `வணக்கம் webman` என்று முடிவு செய்கின்றது.

## URL அணுகல்
பயன்பாட்டு செருகு URL முகவரி பாதைகள் உமிக்கு /app ஆரம்பிக்கும், பிறகு செருகு அடையாளம், பின்வரும் கட்டுப்பாட்டாளர் மற்றும் முறையீடு உள்ளவாறு உள்ளது.
அதாவது `plugin\foo\app\controller\UserController` url முகவரி `http://127.0.0.1:8787/app/foo/user` ஆகும்.
