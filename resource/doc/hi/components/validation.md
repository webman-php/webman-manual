# वैधानिककरणकर्ता
कॉम्पोज़र में कई वैधानिककर्ता होते हैं जिन्हें सीधे इस्तेमाल किया जा सकता है, जैसे:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## वैधीकरणकर्ता top-think/think-validate

### ब्यान
ThinkPHP आधिकारिक वैधीकरणकर्ता

### परियोजना पता
https://github.com/top-think/think-validate

### स्थापना
`composer require topthink/think-validate`

### त्वरित प्रारंभ

**नया `app/index/validate/User.php` बनाएं**
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
        'name.require' => 'नाम आवश्यक है',
        'name.max'     => 'नाम 25 अक्षरों से अधिक नहीं हो सकता',
        'age.number'   => 'आयु को नंबर होना चाहिए',
        'age.between'  => 'आयु केवल 1-120 के बीच हो सकती है',
        'email'        => 'ईमेल प्रारूप गलत है',    
    ];

}
```
  
**उपयोग**
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
# वैधीकरणकर्ता workerman/validation

### ब्यान
परियोजना: https://github.com/Respect/Validation का हिंदी उपनयस्त संस्करण

### परियोजना पता
https://github.com/walkor/validation
  
### स्थापना
 
```php
composer require workerman/validation
```

### त्वरित प्रारंभ

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
            'nickname' => v::length(1, 64)->setName('उपनाम'),
            'username' => v::alnum()->length(5, 64)->setName('उपयोक्ता नाम'),
            'password' => v::length(5, 64)->setName('पासवर्ड')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ठीक है']);
    }
}  
```  
**jquery के माध्यम से पहुंचें**
  
```js
$.ajax({
    url : 'http://127.0.0.1:8787',
    type : "post",
    dataType:'json',
    data : {nickname:'टॉम', username:'टॉम कैट', password: '123456'}
});
```  
परिणाम प्राप्त करें:

`{"code":500,"msg":"उपयोक्ता नाम में केवल अक्षर (a-z) और संख्या (0-9) हो सकती है"}`

ब्यान:
`v::input(array $input, array $rules)` डेटा को जांचने और संग्रह उद्देश्य से है, अगर डेटा की प्रमाणिकता विफल होती है, तो यह `Respect\Validation\Exceptions\ValidationException` को फेंकेगा, अगर वैधता सफल होती है तो विफलता के बाद के डेटा (एरे) को वापस करेगा।

यदि व्यावसायिक कोड वैधता की अप्राप्ति को नहीं पकड़ता है, तो वेबमैन की ढांचा स्वचालित रूप से वैधता अप्राप्ति को पकड़ लेगा और इसके अनुसार HTTP अनुरोध शीर्षक को चयन करेगा, जिससे जैसे json डेटा (समान `{"code":500, "msg":"xxx"}`) या सामान्य अप्राप्ति पृष्ठ। जैसे कि प्रतिस्थापन प्रारूप व्यावसायिक आवश्यकताओं को अनुसार नहीं है तो उपयोगकर्ता स्वयं `ValidationException` अप्राप्ति को पकड़ और उसे आवश्यिक डेटा को समर्थन करने के लिए वापस कर सकते हैं, नीचे के उदाहरण की तरह:

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
                'username' => v::alnum()->length(5, 64)->setName('उपयोक्ता नाम'),
                'password' => v::length(5, 64)->setName('पासवर्ड')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ठीक है', 'data' => $data]);
    }
}
```  

### वैदकर्ता सुविधा गाइड

```php
use Respect\Validation\Validator as v;

// एकल नियम की जांच
$number = 123;
v::numericVal()->validate($number); // true

// बहुत से नियमों की श्रृंखला जांच
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// पहले अप्राप्ति के कारण को प्राप्त करें
try {
    $usernameValidator->setName('उपयोक्ता नाम')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // उपयोक्ता नाम में केवल अक्षर (a-z) और संख्या (0-9) हो सकती है
}

// सभी अप्राप्ति के कारण प्राप्त करें
try {
    $usernameValidator->setName('उपयोक्ता नाम')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // यह प्रिंट करेगा
    // -  उपयोक्ता नाम को निम्नलिखित नियम को फिला होना चाहिए
    //     - उपयोक्ता नाम में केवल अक्षर (a-z) और संख्या (0-9) हो सकती है
    //     - उपयोक्ता नाम में कोई भी रिक्तियां नहीं होनी चाहिए
  
    var_export($exception->getMessages());
    // यह प्रिंट करेगा
    // array (
    //   'alnum' => 'उपयोक्ता नाम में केवल अक्षर (a-z) और संख्या (0-9) हो सकती है',
    //   'noWhitespace' => 'उपयोक्ता नाम नहीं हो सकती है संख्या (0-9)'
    // )
}

// व्यक्ति जांच
$user = new stdClass;
$user->name = 'अलेक्जांडर';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// सरणी जांच
$data = [
    'parentKey' => [
        'field1' => 'मान1',
        'field2' => 'मान2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // check() या validate() भी किया जा सकता है
  
// वैकल्पिक वैधन
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// नकारात्मक नियम
v::not(v::intVal())->validate(10); // false
```  
### वैदकर्ता के तीन तरीके `validate()` `check()` `assert()` का अंतर

`validate()` बूलीयन विश्वकोश वापस करता है, यह कोई अप्राप्ति नहीं फेंकेगा

`check()` अप्राप्ति होने पर अप्राप्ति फेंकेगा, `$exception->getMessage()` बताएगा पहली वैधता के अप्राप्ति का कारण

`assert()` अप्राप्ति होने पर अप्राप्ति फेंकेगा, `$exception->getFullMessage()` से हम सभी वैधता के अप्राप्ति के कारणों को प्राप्त कर सकते हैं

### सामान्य वैधन सूची

`Alnum()` केवल अक्षर और संख्या

`Alpha()` केवल अक्षर

`ArrayType()` सरणी प्रकार

`Between(mixed $minimum, mixed $maximum)` विचर के बीच मानों की जांच

`BoolType()` बूलियन प्रकार का वैधन

`Contains(mixed $expectedValue)` इनपुट में कुछ मान शामिल होने की जांच

`ContainsAny(array $needles)` इनपुट में कम से कम एक परिभाषित मान शामिल होने की जांच

`Digit()` इनपुट में केवल अंक होने की जांच

`Domain()` वैध डोमेन की जांच

`Email()` वैध ईमेल पता की जांच

`Extension(string $extension)` फ़ाइल एक्सटेंशन की जांच

`FloatType()` वैध संख्यात्मक प्रकार की जांच

`IntType()` पूर्णांक प्रकार की जांच

`Ip()` वैध ip पता की जांच

`Json()` वैध json डेटा की जांच

`Length(int $min, int $max)` दिए गए सीमा में लंबाई की जांच

`LessThan(mixed $compareTo)` दिए गए मान से छोटी लंबाई की जांच

`Lowercase()` छोटे अक्षर की जांच

`MacAddress()` वैध mac पता की जांच

`NotEmpty()` खाली नहीं है की जांच

`NullType()` null की जांच

`Number()` संख्या की जांच

`ObjectType()` वस्तु की जांच

`StringType()` स्ट्रिंग प्रकार की जांच

`Url()` url की जांच
  
अधिक वैधन नियम सूची के लिए देखें https://respect-validation.readthedocs.io/en/2.0/list-of-rules/

### अधिक सामग्री

https://respect-validation.readthedocs.io/en/2.0/ पर जाएं
