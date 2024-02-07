# Валидатор
В Composer есть множество валидаторов, которые можно использовать прямо сейчас, например:
#### <a href="#think-validate">top-think/think-validate</a>
#### <a href="#respect-validation">respect/validation</a>

<a name="think-validate"></a>
## Валидатор top-think/think-validate

### Описание
Официальный валидатор ThinkPHP

### Ссылка на проект
https://github.com/top-think/think-validate

### Установка
`composer require topthink/think-validate`

### Быстрый старт

**Создание `app/index/validate/User.php`**

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
        'name.require' => 'Имя обязательно',
        'name.max'     => 'Имя не должно содержать более 25 символов',
        'age.number'   => 'Возраст должен быть числовым',
        'age.between'  => 'Возраст должен быть от 1 до 120',
        'email'        => 'Неверный формат электронной почты',    
    ];

}
```  

**Использование**
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

# Валидатор workerman/validation

### Описание

Проект является переводом https://github.com/Respect/Validation

### Ссылка на проект
https://github.com/walkor/validation

### Установка

```php
composer require workerman/validation
```

### Быстрый старт

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
            'nickname' => v::length(1, 64)->setName('никнейм'),
            'username' => v::alnum()->length(5, 64)->setName('имя пользователя'),
            'password' => v::length(5, 64)->setName('пароль')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```  

**Через jquery обратитесь**
  
```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Том', username:'tom cat', password: '123456'}
  });
```  

Полученный результат:

`{"code":500,"msg":"имя пользователя может содержать только буквы (a-z) и цифры (0-9)"}`

Объяснение:

`v::input(array $input, array $rules)` используется для проверки и сбора данных. В случае неудачной проверки данных выбрасывается исключение `Respect\Validation\Exceptions\ValidationException`, в случае успеха возвращаются отфильтрованные данные (массивы).

Если бизнес-код не перехватывает исключение проверки, то фреймворк webman автоматически перехватит и, в зависимости от заголовка HTTP-запроса, вернет json-данные (подобные `{"code":500, "msg":"xxx"}`) или обычную страницу исключений. Если формат ответа не соответствует требованиям бизнеса, разработчики могут сами перехватывать исключение ValidationException и возвращать нужные данные, подобно следующему примеру:

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
                'username' => v::alnum()->length(5, 64)->setName('имя пользователя'),
                'password' => v::length(5, 64)->setName('пароль')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```  

### Руководство по функциям валидатора

```php
use Respect\Validation\Validator as v;

// Проверка по одному правилу
$number = 123;
v::numericVal()->validate($number); // true

// Цепочка проверок по нескольким правилам
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Получение первой причины неудачной проверки
try {
    $usernameValidator->setName('имя пользователя')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // имя пользователя может содержать только буквы (a-z) и цифры (0-9)
}

// Получение всех причин неудачной проверки
try {
    $usernameValidator->setName('имя пользователя')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // будет напечатано
    // - имя пользователя должно соответствовать следующим правилам
    //     - имя пользователя может содержать только буквы (a-z) и цифры (0-9)
    //     - имя пользователя не может содержать пробелы
  
    var_export($exception->getMessages());
    // будет напечатано
    // array (
    //   'alnum' => 'имя пользователя может содержать только буквы (a-z) и цифры (0-9)',
    //   'noWhitespace' => 'имя пользователя не может содержать пробелы',
    // )
}

// Пользовательские сообщения об ошибке
try {
    $usernameValidator->setName('имя пользователя')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'имя пользователя может содержать только буквы и цифры',
        'noWhitespace' => 'имя пользователя не может содержать пробелы',
        'length' => 'length подходит для правила, поэтому это сообщение не будет показано'
    ]);
    // будет напечатано
    // array(
    //    'alnum' => 'имя пользователя может содержать только буквы и цифры',
    //    'noWhitespace' => 'имя пользователя не может содержать пробелы'
    // )
}

// Проверка объекта
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Подтверждение массива
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
    ->assert($data); // также можно использовать check() или validate()
  
// Опциональная проверка
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Отрицательные правила
v::not(v::intVal())->validate(10); // false
```  

### Три метода валидатора `validate()` `check()` `assert()` и их различия

`validate()` возвращает логическое значение, исключение не выбрасывается

`check()` выбрасывает исключение при неудачной проверке, через `$exception->getMessage()` можно получить первую причину неудачной проверки

`assert()` при неудачной проверке выбрасывает исключение, через `$exception->getFullMessage()` можно получить все причины неудачной проверке
  
### Перечень часто используемых правил проверки

`Alnum()` только буквы и цифры

`Alpha()` только буквы

`ArrayType()` тип массива

`Between(mixed $minimum, mixed $maximum)` проверяет, находится ли ввод между указанными значениями.

`BoolType()` проверяет, является ли ввод логическим

`Contains(mixed $expectedValue)` проверяет, содержит ли ввод определенные значения

`ContainsAny(array $needles)` проверяет, содержит ли ввод хотя бы одно из определенных значений

`Digit()` проверяет, содержит ли ввод только цифры

`Domain()` проверяет, является ли ввод доменным именем

`Email()` проверяет, является ли ввод правильным имейлом

`Extension(string $extension)` проверяет расширение файла

`FloatType()` проверяет, является ли ввод числом с плавающей точкой

`IntType()` проверяет, является ли ввод целым числом

`Ip()` проверяет, является ли ввод IP-адресом

`Json()` проверяет, является ли ввод JSON-данными

`Length(int $min, int $max)` проверяет, находится ли длина ввода в заданном диапазоне

`LessThan(mixed $compareTo)` проверяет, меньше ли длина ввода заданного значения

`Lowercase()` проверяет, содержит ли ввод только строчные буквы

`MacAddress()` проверяет, является ли ввод MAC-адресом

`NotEmpty()` проверяет, не пустой ли ввод

`NullType()` проверяет, является ли ввод пустым

`Number()` проверяет, является ли ввод числом

`ObjectType()` проверяет, является ли ввод объектом

`StringType()` проверяет, является ли ввод строковым типом

`Url()` проверяет, является ли ввод URL-адресом
  
Более подробный список правил проверки смотрите по ссылке https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### Дополнительные ресурсы

Посетите https://respect-validation.readthedocs.io/en/2.0/
