# Validator (Doğrulayıcı)
composer'da kullanılabilen birçok doğrulayıcı bulunmaktadır, örneğin:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
# Validator top-think/think-validate

## Açıklama
ThinkPHP resmi doğrulayıcı

## Proje Adresi
https://github.com/top-think/think-validate

## Kurulum
`composer require topthink/think-validate`

## Hızlı Başlangıç

**Yeni bir `app/index/validate/User.php` dosyası oluşturun**

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
        'name.require' => 'Adı gereklidir',
        'name.max'     => 'Ad 25 karakterden uzun olmamalıdır',
        'age.number'   => 'Yaş sayı olmalıdır',
        'age.between'  => 'Yaş 1 ile 120 arasında olmalıdır',
        'email'        => 'E-posta formatı hatalıdır',    
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
# Validator workerman/validation

## Açıklama
Proje, https://github.com/Respect/Validation'in Çince sürümüdür

## Proje Adresi
https://github.com/walkor/validation

## Kurulum

```php
composer require workerman/validation
```

## Hızlı Başlangıç

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
            'nickname' => v::length(1, 64)->setName('Takma Ad'),
            'username' => v::alnum()->length(5, 64)->setName('Kullanıcı Adı'),
            'password' => v::length(5, 64)->setName('Şifre')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'tamam']);
    }
}  
```

**Jquery üzerinden erişme**

  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```

Sonuç alınır:

`{"code":500,"msg":"Kullanıcı Adı sadece harf (a-z) ve rakam (0-9) içerebilir"}`

Açıklama:

`v::input(array $input, array $rules)` verileri doğrulamak ve toplamak için kullanılır, veri doğrulaması başarısız olursa `Respect\Validation\Exceptions\ValidationException` istisnasını atar, doğrulama başarılıysa doğrulandıktan sonraki verileri (dizi) döndürür.

Eğer iş mantığı kodu doğrulama istisnasını yakalamamış ise, webman çerçevesi otomatik olarak yakalar ve HTTP istek başlığına göre json verileri (benzer şekilde `{"code":500, "msg":"xxx"}`) veya normal bir istisnanın sayfasını döndürür. Döndürülen biçim iş gereksinimlerine uymuyorsa, geliştirici istisna (`ValidationException`) yakalayabilir ve istenen veriyi döndürebilir, aşağıdaki örnekte olduğu gibi:

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
                'username' => v::alnum()->length(5, 64)->setName('Kullanıcı Adı'),
                'password' => v::length(5, 64)->setName('Şifre')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'tamam', 'data' => $data]);
    }
}
```
## Validator Kılavuzu

```php
use Respect\Validation\Validator as v;

// Tek bir kuralı doğrulama
$number = 123;
v::numericVal()->validate($number); // true

// Birden çok kuralı zincirleme doğrulama
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// İlk başarısız doğrulama nedenini almak
try {
    $usernameValidator->setName('Kullanıcı adı')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Kullanıcı adı sadece harfler (a-z) ve rakamlar (0-9) içerebilir
}

// Başarısız doğrulama nedenlerinin tümünü almak
try {
    $usernameValidator->setName('Kullanıcı adı')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Aşağıdakileri yazdıracak
    // - Kullanıcı adı aşağıdaki kurallara uymalıdır
    //     - Kullanıcı adı sadece harfler (a-z) ve rakamlar (0-9) içerebilir
    //     - Kullanıcı adı boşluk içeremez
  
    var_export($exception->getMessages());
    // Aşağıdakileri yazdıracak
    // array (
    //   'alnum' => 'Kullanıcı adı sadece harfler (a-z) ve rakamlar (0-9) içerebilir',
    //   'noWhitespace' => 'Kullanıcı adı boşluk içeremez',
    // )
}

// Özel hata mesajları belirleme
try {
    $usernameValidator->setName('Kullanıcı adı')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Kullanıcı adı sadece harf ve rakam içerebilir',
        'noWhitespace' => 'Kullanıcı adı boşluk içeremez',
        'length' => 'length kuralına uygundur, bu yüzden görüntülenmeyecek'
    ]));
    // Aşağıdakileri yazdıracak
    // array(
    //    'alnum' => 'Kullanıcı adı sadece harf ve rakam içerebilir',
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
    ->assert($data); // check() veya validate() kullanılabilir

// Opsiyonel doğrulama
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Ters kural
v::not(v::intVal())->validate(10); // false
```
  
## Validator'ın `validate()` `check()` `assert()` Üç Metodunun Farkları

`validate()` boolean değer döndürür, istisna fırlatmaz

`check()` doğrulama başarısız olduğunda istisna fırlatır, `$exception->getMessage()` ile ilk başarısız doğrulama nedeni alınabilir

`assert()` doğrulama başarısız olduğunda istisna fırlatır, `$exception->getFullMessage()` ile tüm doğrulama nedenlerine ulaşabilirsiniz
  

## Sık Kullanılan Doğrulama Kuralları Listesi

`Alnum()` sadece harf ve rakam içerir

`Alpha()` sadece harfler içerir

`ArrayType()` dizi türü

`Between(mixed $minimum, mixed $maximum)` girişin diğer iki değer arasında olup olmadığını doğrular.

`BoolType()` mantıksal türü doğrular

`Contains(mixed $expectedValue)` girişin belirli değerleri içerip içermediğini doğrular

`ContainsAny(array $needles)` girişin en az bir tanımlı değeri içerip içermediğini doğrular

`Digit()` girişin sadece rakamları içerip içermediğini doğrular

`Domain()` geçerli bir etki alanı olup olmadığını doğrular

`Email()` geçerli bir e-posta adresi olup olmadığını doğrular

`Extension(string $extension)` uzantıyı doğrular

`FloatType()` ondalık sayı türünü doğrular

`IntType()` tam sayı olup olmadığını doğrular

`Ip()` ip adresi olup olmadığını doğrular

`Json()` json verisi olup olmadığını doğrular

`Length(int $min, int $max)` uzunluğun verilen aralıkta olup olmadığını doğrular

`LessThan(mixed $compareTo)` uzunluğun belirtilen değerden küçük olup olmadığını doğrular

`Lowercase()` küçük harf olup olmadığını doğrular

`MacAddress()` mac adresi olup olmadığını doğrular

`NotEmpty()` boş olup olmadığını doğrular

`NullType()` null olup olmadığını doğrular

`Number()` sayı olup olmadığını doğrular

`ObjectType()` nesne olup olmadığını doğrular

`StringType()` dize türünü doğrular

`Url()` url olup olmadığını doğrular
  

Daha fazla doğrulama kuralı için https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ sayfasına bakınız.
  
## Daha Fazla Bilgi

https://respect-validation.readthedocs.io/en/2.0/ adresini ziyaret edebilirsiniz.

