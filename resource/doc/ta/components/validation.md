# சரிப்பார்ப்புப் பரிசோதகம்
காம்போசரில் உள்ள பல சரிப்பார்ப்பக் கருவிகள் உபயோகிக்கும் போது, எடுத்துக்காட்டில் சிறிது சரிப்பார்ப்பக் கருவிகள் உள்ளது, உதாரணம்:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
# சரிப்பார்ப்பக் கருவி top-think/think-validate

## விளக்கம்
திங்க் பி.எச்.பி அதிகார சரிப்பார்ப்பக் கருவி

## திட்டம் முகவரி
https://github.com/top-think/think-validate

## நிறுவு
`composer require topthink/think-validate`
## விரைவுச் சொல்வோம்

**புதிய `app/index/validate/User.php` உருவாக்கு**

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
        'name.max'     => 'பெயர் 25 எழுத்துக்கும் அதிகமாக இருக்க கூடாது',
        'age.number'   => 'வயது இலக்கமாக இருக்க வேண்டும்',
        'age.between'  => 'வயது 1-120 இல் மட்டுமே இருக்க வேண்டும்',
        'email'        => 'மின்னஞ்சல் வடிவம் பிழை',    
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
# சரிப்பார்ப்புப் பொருத்தி workerman/validation
## விளக்கம்

இந்த திட்டத்துக்கு https://github.com/Respect/Validation இன் சொலப்பாக்கத்தின் தமிழ் மொழிப்பாட்டு பதிப்பு

## திட்ட முகவரி

https://github.com/walkor/validation
  
## நிறுவுதல்
 
```php
composer require workerman/validation
```
## விரைவான தொடக்கம்

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
            'nickname' => v::length(1, 64)->setName('குறிப்புப் பெயர்'),
            'username' => v::alnum()->length(5, 64)->setName('பயனர் பெயர்'),
            'password' => v::length(5, 64)->setName('கடவுச்சொல்')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**jQuery மூலம் அணுக**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'tom', username:'tom cat', password: '123456'}
  });
  ```

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class IndexController
{
    public function index(Request $request)
    {
        try {
            $data = v::input($request->post(), [
                'username' => v::alnum()->length(5, 64)->setName('பயனர்பெயர்'),
                'password' => v::length(5, 64)->setName('கடவுச்சொல்')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'சரி', 'data' => $data]);
    }
}
```
## சரிபார்ப்புப் பயன்பாட்டில் வழி

```php
use Respect\Validation\Validator as v;

// ஒற்றை விதி சரிசெய்யும்
$number = 123;
v::numericVal()->validate($number); // true

// பல விதிகளை கூடியதாக விரும்பேச் சரிசெய்யும்
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// முதல் தவறான காரணத்தை பெற
try {
    $usernameValidator->setName('பயனர்பெயர்')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // பயனர்பெயர் எண்ணிக்கை (a-z) மற்றும் எண்கள் (0-9) மட்டுமே கொண்டவை அவசியம்
}

// எல்லா தவறான பரிகாரங்களையும் பெற
try {
    $usernameValidator->setName('பயனர்பெயர்')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // அது அழுத்தப்படும்
    // -  பயனர்பெயர் கீழே குறிப்பிட்ட நெடுவரம் அருமைப்படுத்த வேண்டும்
    //     - பயனர்பெயர் எண்ணிக்கை (a-z) மற்றும் எண்கள் (0-9) மட்டுமே கொண்டவை அவசியம்
    //     - பயனர்பெயர் இடைக்காலத்தை கொண்டவை அவசியமல்ல
  
    var_export($exception->getMessages());
    // அது அழுத்தப்படும்
    // array (
    //   'alnum' => 'பயனர்பெயர் எண்ணிக்கை (a-z) மற்றும் எண்கள் (0-9) மட்டுமே கொண்டவை அவசியம்',
    //   'noWhitespace' => 'பயனர்பெயர் இடைக்காலத்தை கொண்டவை அவசியமல்ல',
    // )
}

// தனிவருக்கான பிழை செய்தி
try {
    $usernameValidator->setName('பயனர்பெயர்')->assert('alg anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'பயனர்பெயர் எழுத்துக்கள் மட்டும் கொள்கின்றது',
        'noWhitespace' => 'பயனர்பெயர் இடைக்கட்சி இருக்கக்காது',
        'length' => 'புள்ளி சேர்த்தலுக்கு பொருந்துவதில் பிழை இந்த கருவி காணப்படாது'
    ]);
    // முதலில் அல்லது பிழைகளை அச்சிடுகின்றது
    // array(
    //    'alnum' => 'பயனர்பெயர் எழுத்துக்கள் மட்டும் கொள்கின்றது',
    //    'noWhitespace' => 'பயனர்பெயர் இடைக்கட்சி இருக்கக்காது'
    // )
}

// சோதனை பொருட்கள்
$user = new stdClass;
$user->name = 'அலெக்சாண்டர்';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// வரைபடம் சோதனை
$data = [
    'parentKey' => [
        'field1' => 'value1',
        'field2' => 'value2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // சரியாக இருக்கலாம், check() அல்லது validate() என்று பயன்படுத்தலாம்

// விருப்ப சோதனை
v::alpha()->validate(''); // பொருத்தப்படுவதில் false 
v::alpha()->validate(null); // பொருத்தப்படுவதில் false 
v::optional(v::alpha())->validate(''); // பொருத்தப்படுவதில் true
v::optional(v::alpha())->validate(null); // பொருத்தப்படுவதில் true

// மறுபுறம் நெடுவரில்
v::not(v::intVal())->validate(10); // பொருத்தப்படுவதில் false
```

## சரிபார்க்கையாளர்  `validate()`  `check()`  `assert()` மூன்று முறையாக வேண்டும்

`validate()` பூலியன் மாறியின் மூலம் திருப்பியலை உள்ளடக்குகிறது, எப்புயரில் அல்லது நிகழ்வை எடுத்துக்கொள்ளாது

`check()` சொல்வது தவறு ஆகில் விதிபார்க்கும், `$exception->getMessage()` மூலம் முதல் தவறு காரணம் கொண்ட தவறு துணைத்தனம் விடுவிக்கிறது

`assert()` சொல்வது தவறு ஆகிறால் மோசமாக அவற்றை எடுத்துக்கொள்வதன் மூலம்,  `$exception->getFullMessage()` யை மூலம் எல்லா திருப்பியலுக்கான தவறுகளை அறிய முடியும்
## பொது சோதனை விதிகள் பட்டியல்

`Alnum()` எழுத்துகளையும் எண்களையும் மட்டுமே கொண்டிருக்கிறது

`Alpha()` எழுத்துகளைமட்டுமே கொண்டிருக்கிறது

`ArrayType()` வருமான வகை

`Between(mixed $minimum, mixed $maximum)` உள்ளிடுதல் மற்றும் அதிகமாக இருந்தது என்று சோதிக்கின்றது

`BoolType()` பூலியன் வகை ஆகும் என்று சோதிக்கின்றது

`Contains(mixed $expectedValue)` உள்ளிடுதல் மற்றும் கொண்டிருக்கக் கூடிய மதிப்பைச் சோதிக்கின்றது

`ContainsAny(array $needles)` ஒரு வரிசையில் ஒன்றும் அல்லது அந்தரப்பட்ட மதிப்புகளைக் கொண்டிருக்கின்றது என்று சோதிக்கின்றது

`Digit()` இலக்கங்களைமட்டும் கொண்டிருக்கிறது

`Domain()` சரியான தளம் ஆகும் என்று சோதிக்கின்றது

`Email()` சரியான மின்னஞ்சல் முகவரி ஆகும் என்று சோதிக்கின்றது

`Extension(string $extension)` விலையுருக்கு சோதிக்கின்றது

`FloatType()` மிதியாய் இருந்ததாக சோதிக்கின்றது

`IntType()` முழங்கான தர ஆகும் என்று சோதிக்கின்றது

`Ip()` IP முகவரி ஆகும் என்று சோதிக்கின்றது

`Json()` JSON தரவு ஆகும் என்று சோதிக்கின்றது

`Length(int $min, int $max)` கொடுக்கப்பட்ட வரம்பில் நீளம் உள்ளது என்று சோதிக்கின்றது

`LessThan(mixed $compareTo)` கொடுக்கப்பட்ட மதிப்புக்கு குறைவானது உள்ளது என்று சோதிக்கின்றது

`Lowercase()` சிறுபெரிய கொண்டிருக்கிறது என்று சோதிக்கின்றது

`MacAddress()` MAC முகவரி ஆகும் என்று சோதிக்கின்றது

`NotEmpty()` காலில் இல்லை என்று சோதிக்கின்றது

`NullType()` பூஜியமான என்று சோதிக்கின்றது

`Number()` எண் ஆகும் என்று சோதிக்கின்றது

`ObjectType()` பெருமை ஆகும் என்று சோதிக்கின்றது

`StringType()` சரியான வகை ஆகும் என்று சோதிக்கின்றது

`Url()` URL ஆகும் என்று சோதிக்கின்றது

மேலும் சோதனை விதிகளுக்கு பட்டியலைப் பார்க்க https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
## மேலும் உள்ளடக்கம்

https://respect-validation.readthedocs.io/en/2.0/ ஐ அணுகவும்.
