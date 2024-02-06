# சரிபார்க்கப்படுத்தி
காம்போஸர் உபயோகிக்கும் பல சரிபார்க்கி உள்ளுடன் பயன்படுத்த முடியும், உதா:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## சரிபார்க்கி top-think/think-validate

### விளக்கம்
தின்க் PHP இலாவான சரிபார்க்கி

### திரையரவு
https://github.com/top-think/think-validate

### நிறுவு
`composer require topthink/think-validate`

### விரைவு ஆரம்பி

**புதிய `app/index/validate/User.php`ஐ உருவாக்கவும்**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];

    protected $message  =   [
        'name.require' => 'பெயர் தேவை',
        'name.max'     => 'பெயர் 25 எழுத்துகளை அதிகரிக்க முடியாது',
        'age.number'   => 'வயது எண் வேண்டுகோள்',
        'age.between'  => 'வயது 1 முதல் 120 வரை மட்டுமே இருக்க வேண்டும்',
        'email'        => 'மின்னஞ்சல் வடிவம் தவறானது',    
    ];

}
```
  
**பயன்பாடு**
```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

<a name="respect-validation"></a>
# சரிபார்க்கி workerman/validation

### விளக்கம்

திறன் / கந்தகணக்கிய பதிப்பு https://github.com/Respect/Validation சந்தர்மாப்படி

### திரையரவு

https://github.com/walkor/validation
  
  
### நிறுவு
 
```php
composer require workerman/validation
```

### விரைவு ஆரம்பி

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        $data = v::input($request->post(), [
            'nickname' => v::length(1, 64)->setName('புனைந்தெழு'),
            'username' => v::alnum()->length(5, 64)->setName('பயனர்பெயர்'),
            'password' => v::length(5, 64)->setName('கடவுச்சொல்')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'நன்றி']);
    }
}  
```
  
**jquery மூலம் அணுகவும்**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'டாம்', username:'டாம் பூனை', password: '123456'}
  });
  ```
  
முடிவு பெற்றுக் கொண்டது:

`{"code":500,"msg":"பயனர்பெயர் அ஡்஡ாயமா(a-z) மற்றும் எண்கள்(0-9) பெட்டியிலில் மட்டும் இருக்க வேண்டும்"}`

விளக்கம்:

`v::input(array $input, array $rules)` தரவை சரிபார்க்கும் மற்றும் சேகரிப்புக் கொள்கிறது, தகவல் சரிபார்க்க தோல்விப்படுத்தில் `Respect\Validation\Exceptions\ValidationException` புனைவியை விட்டுவிடும், சர
