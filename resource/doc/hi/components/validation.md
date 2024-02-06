# वैधानिकरण
कंपोजर में बहुत सारे वैधाताओं को सीधे उपयोग की जा सकती है, उदाहरण के लिए:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## वैधाता top-think/think-validate

### विवरण
थिंकPHP अधिकारी वैधाता

### परियोजना पता
https://github.com/top-think/think-validate

### स्थापना
`कंपोजर डिमांड topthink/think-validate`

### त्वरित प्रारंभ

**नया `app/index/validate/User.php` बनाएं**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'आवश्यक|अधिकतम:25',
        'age'   => 'आंकड़ा|1,120 के बीच',
        'email' => 'ईमेल',    
    ];

    protected $message  =   [
        'name.require' => 'नाम आवश्यक है',
        'name.max'     => 'नाम 25 अक्षरों से अधिक नहीं हो सकता',
        'age.number'   => 'आयु कोई आंकड़ा होना चाहिए',
        'age.between'  => 'आयु केवल 1-120 के बीच हो सकती है',
        'email'        => 'ईमेल प्रारूप गलत है',    
    ];

}
```
  
**उपयोग**
```php
$data = [
    'name'  => 'थिंकपीएचपी',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

<a name="respect-validation"></a>
# वैधाता workerman/validation

### विवरण

परियोजना https://github.com/Respect/Validation का हिंदी अनुवाद संस्करण

### परियोजना पता

https://github.com/walkor/validation
  
  
### स्थापना
 
```php
कंपोजर डिमांड workerman/validation
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
            'nickname' => v::length(1, 64)->setName('नामक'),
            'username' => v::alnum()->length(5, 64)->setName('उपयोक्ता नाम'),
            'password' => v::length(5, 64)->setName('पासवर्ड')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ठीक है']);
    }
}  
```
  
**जावास्क्रिप्ट के माध्यम से पहुँचें**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'टॉम', username:'टॉम कैट', password: '123456'}
  });
  ```
  
परिणाम प्राप्त करें:

`{"code":500,"msg":"उपयोक्ता नाम ज्ञातियों में केवल अक्षर (a-z) और संख्या (0-9) हो सकता है"}`

व्याख्या:

`v::input(array $input, array $rules)` डेटा का जांच और संग्रहण के लिए है, यदि डेटा की जांच विफल होती है, तो `Respect\Validation\Exceptions\ValidationException` अनुप्रयोग फेंकेगा, जांच सफल होने पर जांच के बाद का डेटा (एरे) लौटा जाएगा।

यदि व्यावसायिक कोड ने वैधाता अनुप्रयोग को नहीं पकड़ा है, तो webman फ़्रेमवर्क स्वचालित रूप से वैधाता अनुप्रयोग को पकड़ेगा और HTTP अनुरोध शीर्ष द्वारा जेसन डेटा (जैसे `{"कोड":500, "संदेश":"xxx"}`) या साधारण अनुप्रयोग पृष्ठ। यदि प्रत्यावश्य की आवश्यकता के अनुसार चित्र नहीं है, तो डेवलपर विचार वह अपने आप `ValidationException` अनुप्रयोग पकड़ सकता है और आवश्यक डेटा लौटा सकता है, जैसे नीचे के उदाहरण की तरह:

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

### वैधाता फ़़ंक्शन गाइड

```php
use Respect\Validation\Validator as v;

// एकल नियम वैधाता
$number = 123;
v::numericVal()->validate($number); // सत्य

// कई नियमों का चेन वैधाता
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // सत्य

// पहली वैधाता विफलता की वजह प्राप्त करें
try {
    $usernameValidator->setName('उपयोक्ता नाम')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // उपयोक्ता नाम में केवल अक्षर (a-z) और संख्या (0-9) हो सकता है
}

```
   ### वैधाता 3 विधियां `जांच()` `ऑसर्ट()` विभिन्नता `जांच()` मान लौटाती है, अनुप्रयोग को नहीं फेंकती

`validate()` बूलियन मान लौटाती है, अनुप्रयोग नहीं फेंकती

`check()` वैधाता विफलता पर अनुप्रयोग फेंकती है, `$exception->getMessage()` पहली वैधाता विफलता की वजह

`assert()` वैधाता विफलता पर अनुप्रयोग फेंकती है, `$exception->getFullMessage()` सभी वैधाता विफलता की वजह प्राप्त कर सकते हैं

### सामान्य वैधाता नियमों की सूची

`Alnum()` केवल वर्ण और संख्याएँ प्रमाणित करती है

`Alpha()` केवल वर्ण प्रमाणित करती है

`ArrayType()` सरणी प्रकार

`Between(mixed $minimum, mixed $maximum)` विभिन्न दो मानों के बीच इनपुट की जाँच करती है।

`BoolType()` बूलियन प्रकार प्रमाणित करती है

`Contains(mixed $expectedValue)` इनपुट में कुछ मानों की जाँच करती है

`ContainsAny(array $needles)` इनपुट में कम से कम एक परिभाषित मान को प्रमाणित करती है

`Digit()` इनपुट में केवल अंक होने की जाँच करती है

`Domain()` क्या वैध डोमेन है या नहीं की जाँच करती है

`Email()` क्या इसका वैध ईमेल पता है या नहीं की जाँच करती है

`Extension(string $extension)` विस्तार की जाँच करती है

`FloatType()` क्या यह फ़्लोट है या नहीं की जाँच करती है

`IntType()` क्या यह पूर्णांक है या नहीं की जाँच करती है

`Ip()` क्या यह आईपी पता है या नहीं की जाँच करती है

`Json()` क्या यह जेसन डेटा है या नहीं की जाँच करती है

`Length(int $min, int $max)` इनवेक्शन के बीच लंबाई की जाँच करती है

`LessThan(mixed $compareTo)` इनवेक्शन का मान न्यूनतम बीतते है की जाँच करती है

`Lowercase()` क्या यह छोटा वर्ण है की जाँच करती है

`MacAddress()` क्या यह मैक पता है या नहीं की जाँच करती है

`NotEmpty()` क्या खाली है या नहीं की जाँच करती है

`NullType()` क्या यह नल मान है या नहीं की जाँच करती है

`Number()` क्या यह संख्या है या नहीं की जाँच करती है

`ObjectType()` क्या यह ऑब्जेक्ट है या नहीं की जाँच करती है

`StringType()` क्या यह स्ट्रिंग प्रकार है या नहीं की जाँच करती है

`Url()` क्या यह यूआरएल है या नहीं की जाँच करती है

अधिक वैधाता नियमों के लिए देखें https://respect-validation.readthedocs.io/en/2.0/list-of-rules/

### और अधिक सामग्री

https://respect-validation.readthedocs.io/en/2.0/ पर जाएं
