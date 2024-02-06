# Validator
Composer cung cấp nhiều trình xác thực mà bạn có thể sử dụng trực tiếp, ví dụ:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validator top-think/think-validate

### Giải thích
Trình xác thực chính thức của ThinkPHP

### Địa chỉ dự án
https://github.com/top-think/think-validate

### Cài đặt
`composer require topthink/think-validate`

### Bắt đầu nhanh

**Tạo `app/index/validate/User.php` mới**

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
        'age.between'  => 'Tuổi phải từ 1 đến 120',
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
# Validator workerman/validation

### Giải thích

Dự án là phiên bản dành cho người sử dụng tiếng Trung của https://github.com/Respect/Validation

### Địa chỉ dự án

https://github.com/walkor/validation
  
  
### Cài đặt
 
```php
composer require workerman/validation
```

### Bắt đầu nhanh

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
            'nickname' => v::length(1, 64)->setName('tên gọi'),
            'username' => v::alnum()->length(5, 64)->setName('tên người dùng'),
            'password' => v::length(5, 64)->setName('mật khẩu')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Thực hiện thông qua jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Nhận kết quả:

`{"code":500,"msg":"tên người dùng chỉ có thể chứa các chữ cái (a-z) và số (0-9)"}`

Giải thích:

`v::input(array $input, array $rules)` được sử dụng để xác thực và thu thập dữ liệu. Nếu xác thực dữ liệu thất bại, nó sẽ ném ra ngoại lệ `Respect\Validation\Exceptions\ValidationException`. Nếu xác thực thành công, nó sẽ trả về dữ liệu đã được xác thực (dưới dạng mảng).

Nếu mã kinh doanh không bắt ngoại lệ xác thực, framework webman sẽ tự động bắt và trả về dữ liệu dựa trên tiêu đề yêu cầu HTTP (tương tự như`{"code":500, "msg":"xxx"}`) hoặc trang ngoại lệ thông thường. Nếu định dạng trả về không phù hợp với yêu cầu kinh doanh, nhà phát triển có thể tự mình bắt ngoại lệ `ValidationException` và trả về dữ liệu cần thiết, tương tự như ví dụ dưới đây:

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
                'username' => v::alnum()->length(5, 64)->setName('tên người dùng'),
                'password' => v::length(5, 64)->setName('mật khẩu')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Hướng dẫn chức năng của Validator

```php
use Respect\Validation\Validator as v;

// Xác thực quy tắc đơn
$number = 123;
v::numericVal()->validate($number); // true

// Chuỗi quy tắc nhiều liên kết
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Nhận lý do xác thực thất bại đầu tiên
try {
    $usernameValidator->setName('tên người dùng')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // tên người dùng chỉ có thể chứa các chữ cái (a-z) và số (0-9)
}

// Nhận tất cả lý do xác thực thất bại
try {
    $usernameValidator->setName('tên người dùng')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Sẽ in ra 
    // -  tên người dùng phải tuân theo quy tắc sau
    //     - tên người dùng chỉ có thể chứa các chữ cái (a-z) và số (0-9)
    //     - tên người dùng không được chứa khoảng trắng
  
    var_export($exception->getMessages());
    // Sẽ in ra
    // array (
    //   'alnum' => 'tên người dùng chỉ có thể chứa các chữ cái (a-z) và số (0-9)',
    //   'noWhitespace' => 'tên người dùng không được chứa khoảng trắng',
    // )
}

// Tùy chỉnh thông báo lỗi
try {
    $usernameValidator->setName('tên người dùng')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'tên người dùng chỉ có thể chứa chữ cái và số',
        'noWhitespace' => 'tên người dùng không được chứa khoảng trắng',
        'length' => 'độ dài phù hợp, do đó thông báo này sẽ không được hiển thị'
    ]);
    // Sẽ in ra 
    // array(
    //    'alnum' => 'tên người dùng chỉ có thể chứa chữ cái và số',
    //    'noWhitespace' => 'tên người dùng không được chứa khoảng trắng'
    // )
}

// Xác thực đối tượng
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Xác thực dữ liệu dạng mảng
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
  
// Xác thực lựa chọn
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Quy tắc phủ định
v::not(v::intVal())->validate(10); // false
```
  
### Các phương thức `validate()` `check()` `assert()` trong Validator khác nhau

`validate()` trả về kiểu boolean, không ném ra ngoại lệ

`check()` ném ra ngoại lệ khi xác thực thất bại, thông qua `$exception->getMessage()` để lấy lý do xác thực thất bại đầu tiên

`assert()` ném ra ngoại lệ khi xác thực thất bại, thông qua `$exception->getFullMessage()` có thể nhận được tất cả lý do xác thực thất bại
  
  
### Danh sách quy tắc xác thực phổ biến

`Alnum()` Chỉ chứa chữ cái và số

`Alpha()` Chỉ chứa chữ cái

`ArrayType()` Loại mảng

`Between(mixed $minimum, mixed $maximum)` Xác thực nhập liệu nằm giữa hai giá trị khác nhau.

`BoolType()` Xác thực liệu có phải là kiểu boolean

`Contains(mixed $expectedValue)` Xác thực liệu nhập liệu có chứa giá trị nào đó

`ContainsAny(array $needles)` Xác thực liệu nhập liệu ít nhất phải chứa một giá trị đã được xác định

`Digit()` Xác thực liệu nhập liệu chỉ chứa chữ số

`Domain()` Xác thực liệu nhập liệu có phải là tên miền hợp lệ

`Email()` Xác thực liệu nhập liệu có phải là địa chỉ email hợp lệ

`Extension(string $extension)` Xác thực phần mở rộng

`FloatType()` Xác thực liệu có phải là kiểu số thực

`IntType()` Xác thực liệu có phải là kiểu số nguyên

`Ip()` Xác thực liệu có phải là địa chỉ IP

`Json()` Xác thực liệu có phải là dữ liệu JSON

`Length(int $min, int $max)` Xác thực độ dài có nằm trong phạm vi cung cấp

`LessThan(mixed $compareTo)` Xác thực độ dài có nhỏ hơn giá trị cung cấp

`Lowercase()` Xác thực liệu có phải là chữ cái viết thường

`MacAddress()` Xác thực liệu có phải là địa chỉ MAC

`NotEmpty()` Xác thực liệu không rỗng

`NullType()` Xác thực liệu có phải là null

`Number()` Xác thực liệu có phải là số

`ObjectType()` Xác thực liệu có phải là đối tượng

`StringType()` Xác thực liệu có phải là kiểu chuỗi

`Url()` Xác thực liệu có phải là url
  
Xem thêm danh sách quy tắc xác thực tại https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### Nội dung khác

Truy cập https://respect-validation.readthedocs.io/en/2.0/
