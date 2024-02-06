# Валидатор
В Composer есть множество валидаторов, которые можно использовать напрямую, например:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Валидатор top-think/think-validate

### Описание
Официальный валидатор ThinkPHP

### Адрес проекта
https://github.com/top-think/think-validate

### Установка
`composer require topthink/think-validate`

### Быстрый старт

**Создайте файл `app/index/validate/User.php`**

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
        'name.require' => 'Необходимо указать имя',
        'name.max'     => 'Имя не должно превышать 25 символов',
        'age.number'   => 'Возраст должен быть числом',
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

<a name="respect-validation"></a>
# Валидатор workerman/validation

### Описание

Проект - это версия на китайском языке проекта https://github.com/Respect/Validation

### Адрес проекта

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
  
**Через jQuery запросить**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Том', username:'том кэт', password: '123456'}
  });
  ```
  
Получаем результат:

`{"code":500,"msg":"Имя пользователя может содержать только буквы a-z и цифры 0-9"}`

Описание:

`v::input(array $input, array $rules)` используется для проверки и сбора данных. Если данные не проходят проверку, то будет выброшено исключение `Respect\Validation\Exceptions\ValidationException`, в случае успешной проверки будет возвращен проверенный набор данных (массив).

Если бизнес-код не обрабатывает исключения проверки, то фреймворк webman автоматически обработает их и в зависимости от заголовка HTTP-запроса вернет данные в формате JSON (подобно `{"code":500, "msg":"xxx"}`) или обычную страницу с ошибкой. Если формат ответа не соответствует требованиям бизнеса, разработчик может самостоятельно обработать исключение `ValidationException` и вернуть необходимые данные, подобно следующему примеру:

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

### Руководство по функциям Validator

```php
use Respect\Validation\Validator as v;

// Проверка одного правила 
$number = 123;
v::numericVal()->validate($number); // true

// Цепочка нескольких правил
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Получение первой причины неудачной проверки
try {
    $usernameValidator->setName('имя пользователя')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Имя пользователя может содержать только буквы a-z и цифры 0-9
}

// Получение всех причин неудачной проверки
try {
    $usernameValidator->setName('имя пользователя')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Будет выведено
    // -  Имя пользователя должно соответствовать следующим правилам
    //     - Имя пользователя может содержать только буквы a-z и цифры 0-9
    //     - Имя пользователя не может содержать пробелы
 
    var_export($exception->getMessages());
    // Будет выведено
    // array (
    //   'alnum' => 'Имя пользователя может содержать только буквы a-z и цифры 0-9',
    //   'noWhitespace' => 'Имя пользователя не может содержать пробелы',
    // )
}

// Пользовательское сообщение об ошибке
try {
    $usernameValidator->setName('имя пользователя')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Имя пользователя может содержать только буквы и цифры',
        'noWhitespace' => 'Имя пользователя не может содержать пробелы',
        'length' => 'length соответствует правилам, поэтому этот пункт не будет отображаться'
    ]);
    // Будет выведено 
    // array(
    //    'alnum' => 'Имя пользователя может содержать только буквы и цифры',
    //    'noWhitespace' => 'Имя пользователя не может содержать пробелы'
    // )
}

// Проверка объекта
$user = new stdClass;
$user->name = 'Александр';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Проверка массива
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

// Отрицание правила
v::not(v::intVal())->validate(10); // false
```
  
### Различие между методами `validate()`, `check()`, `assert()` в Validator

`validate()` возвращает логическое значение, не выдает исключение

`check()` выдает исключение при неудачной проверке, с помощью `$exception->getMessage()` можно получить первую причину неудачной проверки

`assert()` выдает исключение при неудачной проверке, с помощью `$exception->getFullMessage()` можно получить все причины неудачной проверки
  
  
### Список часто используемых правил проверки

`Alnum()` только буквы и цифры

`Alpha()` только буквы

`ArrayType()` тип - массив

`Between(mixed $minimum, mixed $maximum)` проверка, входит ли введенное значение в интервал между двумя значениями.

`BoolType()` проверка на булев тип

`Contains(mixed $expectedValue)` проверка на наличие входного значения

`ContainsAny(array $needles)` проверка, входит ли введенное значение хотя бы в одно из заданных значений

`Digit()` проверка, входит ли введенное значение только цифры

`Domain()` проверка на корректный домен

`Email()` проверка на корректный электронный адрес

`Extension(string $extension)` проверка расширения

`FloatType()` проверка на тип - число с плавающей запятой

`IntType()` проверка на целочисленный тип

`Ip()` проверка на корректный IP-адрес

`Json()` проверка на JSON-данные

`Length(int $min, int $max)` проверка длины находится в указанном интервале

`LessThan(mixed $compareTo)` проверка, меньше ли длина указанного значения

`Lowercase()` проверка на строчные буквы

`MacAddress()` проверка на MAC-адрес

`NotEmpty()` проверка на непустоту

`NullType()` проверка на значение - null

`Number()` проверка на число

`ObjectType()` проверка на объект

`StringType()` проверка на тип - строка

`Url()` проверка на URL
  
Более детальный список правил проверки можно найти по адресу https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
### Дополнительная информация

Посетите https://respect-validation.readthedocs.io/en/2.0/
