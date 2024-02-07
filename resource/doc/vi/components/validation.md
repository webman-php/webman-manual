# Trình xác nhận
Có nhiều trình xác nhận có sẵn trực tiếp trong composer, ví dụ:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
# Trình xác nhận top-think/think-validate

## Giới thiệu
Trình xác nhận chính thức của ThinkPHP

## Địa chỉ dự án
https://github.com/top-think/think-validate

## Cài đặt
`composer require topthink/think-validate`

## Bắt đầu nhanh

**Tạo mới `app/index/validate/User.php`**

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
        'name.require' => 'Tên là bắt buộc',
        'name.max'     => 'Tên không được vượt quá 25 ký tự',
        'age.number'   => 'Tuổi phải là số',
        'age.between'  => 'Tuổi phải nằm trong khoảng từ 1 đến 120',
        'email'        => 'Định dạng email không đúng',    
    ];

}
```

**Sử dụng**
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
# Trình xác nhận workerman/validation

## Giới thiệu

Dự án này là bản dịch tiếng Trung của https://github.com/Respect/Validation

## Địa chỉ dự án

https://github.com/walkor/validation

## Cài đặt

```php
composer require workerman/validation
```

## Bắt đầu nhanh

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
            'nickname' => v::length(1, 64)->setName('Tên hiển thị'),
            'username' => v::alnum()->length(5, 64)->setName('Tên người dùng'),
            'password' => v::length(5, 64)->setName('Mật khẩu')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```

**Truy cập thông qua jquery**

```js
$.ajax({
    url : 'http://127.0.0.1:8787',
    type : "post",
    dataType:'json',
    data : {nickname:'Tom', username:'tom cat', password: '123456'}
});
```

Kết quả:

`{"code":500,"msg":"Tên người dùng chỉ có thể chứa chữ cái (a-z) và số (0-9)"}`

Giải thích:

`v::input(array $input, array $rules)` được sử dụng để xác nhận và thu thập dữ liệu. Nếu việc xác nhận dữ liệu thất bại, nó sẽ ném ra ngoại lệ `Respect\Validation\Exceptions\ValidationException`, nếu xác nhận thành công, dữ liệu đã được xác nhận (dạng mảng) sẽ được trả về.

Nếu mã doanh nghiệp không xử lý ngoại lệ xác nhận, framework webman sẽ tự động xử lý và dựa trên tiêu đề yêu cầu HTTP để chọn trả về dữ liệu json (giống như `{"code":500, "msg":"xxx"}`) hoặc trang ngoại lệ thông thường. Nếu định dạng trả về không phù hợp với yêu cầu của doanh nghiệp, người phát triển có thể tự mình xử lý ngoại lệ `ValidationException` và trả về dữ liệu cần thiết, tương tự như ví dụ dưới đây:

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
                'username' => v::alnum()->length(5, 64)->setName('Tên người dùng'),
                'password' => v::length(5, 64)->setName('Mật khẩu')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```
## Hướng dẫn sử dụng tính năng Validator

```php
use Respect\Validation\Validator as v;

// Validate the single rule
$number = 123;
v::numericVal()->validate($number); // true

// Validate multiple rules in chain
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Get the first validation failure reason
try {
    $usernameValidator->setName('Tên người dùng')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Tên người dùng chỉ có thể chứa chữ cái (a-z) và số (0-9)
}

// Get all validation failure reasons
try {
    $usernameValidator->setName('Tên người dùng')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Sẽ in ra
    // -  Tên người dùng phải tuân theo các quy tắc sau
    //     - Tên người dùng chỉ có thể chứa chữ cái (a-z) và số (0-9)
    //     - Tên người dùng không được chứa khoảng trắng
  
    var_export($exception->getMessages());
    // Sẽ in ra
    // array (
    //   'alnum' => 'Tên người dùng chỉ có thể chứa chữ cái (a-z) và số (0-9)',
    //   'noWhitespace' => 'Tên người dùng không được chứa khoảng trắng',
    // )
}

// Custom error message
try {
    $usernameValidator->setName('Tên người dùng')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Tên người dùng chỉ có thể chứa chữ cái và số',
        'noWhitespace' => 'Tên người dùng không được có khoảng trắng',
        'length' => 'length tuân theo quy tắc, nên thông báo này sẽ không hiển thị'
    ]);
    // Sẽ in ra 
    // array(
    //    'alnum' => 'Tên người dùng chỉ có thể chứa chữ cái và số',
    //    'noWhitespace' => 'Tên người dùng không được có khoảng trắng'
    // )
}

// Validate objects
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validate arrays
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
    ->assert($data); // Cũng có thể sử dụng check() hoặc validate()

// Optional validation
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negative rule
v::not(v::intVal())->validate(10); // false
```

## Sự khác biệt giữa 3 phương thức của Validator: `validate()`, `check()`, `assert()`

`validate()` trả về kiểu boolean, không ném ra ngoại lệ

`check()` ném ra ngoại lệ khi validation thất bại, thông qua `$exception->getMessage()` để lấy lý do thất bại đầu tiên

`assert()` ném ra ngoại lệ khi validation thất bại, thông qua `$exception->getFullMessage()` có thể lấy tất cả lý do thất bại

## Danh sách các quy tắc validation thường dùng

`Alnum()` chỉ chứa chữ cái và số

`Alpha()` chỉ chứa chữ cái

`ArrayType()` kiểu mảng

`Between(mixed $minimum, mixed $maximum)` kiểm tra giá trị có nằm giữa hai giá trị khác

`BoolType()` kiểm tra có phải là kiểu boolean hay không

`Contains(mixed $expectedValue)` kiểm tra có chứa giá trị mong muốn hay không

`ContainsAny(array $needles)` kiểm tra có chứa ít nhất một giá trị đã được xác định hay không

`Digit()` kiểm tra chỉ chứa chữ số

`Domain()` kiểm tra có phải là tên miền hợp lệ hay không

`Email()` kiểm tra có phải là địa chỉ email hợp lệ hay không

`Extension(string $extension)` kiểm tra phần mở rộng của tệp tin

`FloatType()` kiểm tra có phải là kiểu số thực hay không

`IntType()` kiểm tra có phải là số nguyên hay không

`Ip()` kiểm tra có phải là địa chỉ IP hay không

`Json()` kiểm tra có phải là dữ liệu JSON hay không

`Length(int $min, int $max)` kiểm tra chiều dài có nằm trong khoảng cho trước hay không

`LessThan(mixed $compareTo)` kiểm tra chiều dài có nhỏ hơn giá trị cho trước hay không

`Lowercase()` kiểm tra có phải là chữ thường hay không

`MacAddress()` kiểm tra có phải là địa chỉ MAC hay không

`NotEmpty()` kiểm tra có rỗng hay không

`NullType()` kiểm tra có phải là giá trị null hay không

`Number()` kiểm tra có phải là số hay không

`ObjectType()` kiểm tra có phải là đối tượng hay không

`StringType()` kiểm tra có phải là kiểu chuỗi hay không

`Url()` kiểm tra có phải là URL hay không

Xem thêm danh sách các quy tắc validation tại https://respect-validation.readthedocs.io/en/2.0/list-of-rules/

## Thêm nội dung

Truy cập https://respect-validation.readthedocs.io/en/2.0/
