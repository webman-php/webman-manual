# கட்டுப்பாட்டாளர்

PSR4 கொள்கைகளுக்கு ஏற்ப கட்டுப்பாட்டாளர் வரைப் பேஸ்டின் தரவரிசையனுக்கு `plugin\{செயலி அடையாளம்}` முகவரியாக தொடக்கம் செய்யும், உதாரணமாக

கட்டுப்பாட்டாளர் கோப்பு `plugin/foo/app/controller/FooController.php` ஐ உருவாக்குக.
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

`http://127.0.0.1:8787/app/foo/foo` ஐ அணுவல்களின் போக்ஷிஸ் `hello index` ஐ எழுப்பும் பக்கம்

`http://127.0.0.1:8787/app/foo/foo/hello` ஐ அணுவல்களின் போக்ஷிஸ் `hello webman` ஐ எழுப்பும் பக்கம்

## URL அணுவல்
பயன்பாட்டு சேர்க்கை URL முகவரி பாதைகள் மாற்றில் "/app" இடமாகப் புதிய பிராப்பு, அப்படி நிரப்பப்பட்டுள்ளது, பின்னர் செயலி அடையாளம், பின்னர் கொரியக் கட்டுப்பாட்டாளர் மற்றும் முறை. உதாரணத்தால் `plugin\foo\app\controller\UserController` URL முகவரி இது `http://127.0.0.1:8787/app/foo/user` ஆகும்.
