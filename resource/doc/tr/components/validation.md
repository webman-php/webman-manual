# Doğrulayıcı
composer kullanarak, doğrudan kullanabileceğiniz birçok doğrulayıcı var, örneğin:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Doğrulayıcı top-think/think-validate

### Açıklama
ThinkPHP resmi doğrulayıcısı

### Proje Bağlantısı
https://github.com/top-think/think-validate

### Kurulum
`composer require topthink/think-validate`

### Hızlı Başlangıç

**Yeni oluştur `app/index/validate/User.php`**

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
        'name.require' => 'İsim gereklidir',
        'name.max'     => 'İsim 25 karakterden fazla olmamalıdır',
        'age.number'   => 'Yaş sayı olmalıdır',
        'age.between'  => 'Yaş 1 ile 120 arasında olmalıdır',
        'email'        => 'E-posta formatı hatalı',    
    ];

}
```
  
**Kullanım**
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
# Doğrulayıcı workerman/validation

### Açıklama
Proje, https://github.com/Respect/Validation 'ın Çince versiyonudur

### Proje Bağlantısı

https://github.com/walkor/validation
  
  
### Kurulum
 
```php
composer require workerman/validation
```

### Hızlı Başlangıç

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
            'nickname' => v::length(1, 64)->setName('Takma ad'),
            'username' => v::alnum()->length(5, 64)->setName('Kullanıcı adı'),
            'password' => v::length(5, 64)->setName('Şifre')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'tamam']);
    }
}  
```
  
**Jquery ile erişme**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Sonuç alınır:

`{"code":500,"msg":"Kullanıcı adı sadece harf (a-z) ve rakam (0-9) içerebilir"}`

Açıklama:

`v::input(array $giriş, array $kurallar)` verileri doğrulamak ve toplamak için kullanılır, veri doğrulama başarısız olursa, `Respect\Validation\Exceptions\ValidationException` istisnası fırlatılır, başarılı doğrulama sonrasında doğrulanan verileri (diziler) döndürür.

İş kodu doğrulama istisnası yakalamazsa, webman çerçevesi otomatik olarak yakalar ve HTTP isteği başlığına göre json veri (benzer şekilde `{"code":500, "msg":"xxx"}`) veya normal istisna sayfası döndürür. Döndürülen format iş gereksinimlerine uymuyorsa, geliştirici kendisi `ValidationException` istisnasını yakalayabilir ve gereken veriyi döndürebilir, aşağıdaki örnekte olduğu gibi:

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
                'username' => v::alnum()->length(5, 64)->setName('Kullanıcı adı'),
                'password' => v::length(5, 64)->setName('Şifre')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'tamam', 'data' => $data]);
    }
}
```

### Validator Kılavuzu

```php
use Respect\Validation\Validator as v;

// Tek kural doğrulama
$number = 123;
v::numericVal()->validate($number); // true

// Birden çok kural zincirleme doğrulama
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// İlk başarısız olma nedenini almak
try {
    $usernameValidator->setName('Kullanıcı adı')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Kullanıcı adı sadece harf (a-z) ve rakam (0-9) içerebilir
}

// Tüm başarısız olma nedenlerini almak
try {
    $usernameValidator->setName('Kullanıcı adı')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Yazdırılacak
    // -  Kullanıcı adı aşağıdaki kurallara uymalıdır
    //     - Kullanıcı adı sadece harf (a-z) ve rakam (0-9) içerebilir
    //     - Kullanıcı adı boşluk içeremez
  
    var_export($exception->getMessages());
    // Yazdırılacak
    // array (
    //   'alnum' => 'Kullanıcı adı sadece harf (a-z) ve rakam (0-9) içerebilir',
    //   'noWhitespace' => 'Kullanıcı adı boşluk içeremez',
    // )
}

// Özel hata mesajları
try {
    $usernameValidator->setName('Kullanıcı adı')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Kullanıcı adı sadece harf (a-z) ve rakam (0-9) içerebilir',
        'noWhitespace' => 'Kullanıcı adı boşluk içeremez',
        'length' => 'length kuralla uyduğu için bu mesaj gösterilmeyecek'
    ]);
    // Yazdırılacak 
    // array(
    //    'alnum' => 'Kullanıcı adı sadece harf (a-z) ve rakam (0-9) içerebilir',
    //    'noWhitespace' => 'Kullanıcı adı boşluk içeremez'
    // )
}

// Nesne doğrulama
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Dizi doğrulama
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
    ->assert($data); // check() veya validate() da kullanılabilir
  
// Opsiyonel doğrulama
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Olumsuz kural
v::not(v::intVal())->validate(10); // false
```
  
### Validator'in `validate()` `check()` `assert()` olmak üzere üç methodu farkı

`validate()` boolean değer döndürür, başarısızlık durumunda istisna fırlatmaz

`check()` başarısızlık durumunda istisna fırlatır, `$exception->getMessage()` ile ilk başarısızlık nedeni alınır

`assert()` başarısızlık durumunda istisna fırlatır, `$exception->getFullMessage()` ile tüm başarısızlık nedenleri alınabilir
  
  
### Yaygın Doğrulama Kuralları Listesi

`Alnum()` sadece harf ve rakam içerir

`Alpha()` sadece harf içerir

`ArrayType()` dizi tipi

`Between(mixed $minimum, mixed $maximum)` girişin diğer iki değer arasında olup olmadığını doğrular.

`BoolType()` boolean tipi olduğunu doğrular

`Contains(mixed $expectedValue)` girişin belirli değerleri içerip içermediğini doğrular

`ContainsAny(array $needles)` girişin en az bir tanımlı değeri içerip içermediğini doğrular

`Digit()` girişin sadece rakam içerip içermediğini doğrular

`Domain()` geçerli bir alan adı olup olmadığını doğrular

`Email()` geçerli bir e-posta adresi olup olmadığını doğrular

`Extension(string $extension)` uzantı adını doğrular

`FloatType()` ondalık sayı tipini doğrular

`IntType()` tam sayı tipini doğrular

`Ip()` ip adresi olduğunu doğrular

`Json()` bir json verisi olup olmadığını doğrular

`Length(int $min, int $max)` uzunluğun belirtilen aralıkta olup olmadığını doğrular

`LessThan(mixed $compareTo)` uzunluğun belirtilen değerden küçük olup olmadığını doğrular

`Lowercase()` sadece küçük harf olduğunu doğrular

`MacAddress()` mac adresi olduğunu doğrular

`NotEmpty()` boş olup olmadığını doğrular

`NullType()` null olduğunu doğrular

`Number()` sayı olduğunu doğrular

`ObjectType()` nesne olduğunu doğrular

`StringType()` dize tipi olduğunu doğrular

`Url()` url olduğunu doğrular
  
Daha fazla doğrulama kuralı için https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ sayfasına bakabilirsiniz
  
### Daha Fazla Bilgi

Ziyaret et: https://respect-validation.readthedocs.io/en/2.0/
