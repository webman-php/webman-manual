## Giao diện
Mặc định, webman sử dụng cú pháp nguyên bản của PHP làm mẫu, và có hiệu suất tốt nhất khi kích hoạt `opcache`. Ngoài ra, nó còn cung cấp các công cụ cũng như Twig, Blade, think-template làm thư viện mẫu.

## Kích hoạt opcache
Khi sử dụng giao diện, đề nghị kích hoạt các tùy chọn `opcache.enable` và `opcache.enable_cli` trong tệp php.ini để giao diện mẫu đạt hiệu suất tốt nhất.

## Cài đặt Twig
1. Cài đặt thông qua Composer

   `composer require twig/twig`

2. Sửa đổi tệp cấu hình `config/view.php` như sau:

   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > **Lưu ý**
   > Các tùy chọn cấu hình khác có thể được truyền qua options, ví dụ

   ```php
   return [
       'handler' => Twig::class,
       'options' => [
           'debug' => false,
           'charset' => 'utf-8'
       ]
   ];
   ```

## Cài đặt Blade
1. Cài đặt thông qua Composer

   `composer require psr/container ^1.1.1 webman/blade`

2. Sửa đổi tệp cấu hình `config/view.php` như sau:

   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## Cài đặt think-template
1. Cài đặt thông qua Composer

   `composer require topthink/think-template`

2. Sửa đổi tệp cấu hình `config/view.php` như sau:

   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **Lưu ý**
   > Các tùy chọn cấu hình khác có thể được truyền qua options, ví dụ

   ```php
   return [
       'handler' => ThinkPHP::class,
       'options' => [
           'view_suffix' => 'html',
           'tpl_begin' => '{',
           'tpl_end' => '}'
       ]
   ];
   ```

## Ví dụ về giao diện mẫu PHP nguyên bản
Tạo tệp `app/controller/UserController.php` với nội dung như sau:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Tạo tệp `app/view/user/hello.html` với nội dung:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Ví dụ về giao diện mẫu Twig
Sửa đổi tệp cấu hình `config/view.php` như sau:

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

Tạo tệp `app/controller/UserController.php` với nội dung như sau:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Tạo tệp `app/view/user/hello.html` với nội dung:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

Xem thêm tài liệu tại [Twig](https://twig.symfony.com/doc/3.x/)

## Ví dụ về giao diện mẫu Blade
Sửa đổi tệp cấu hình `config/view.php` như sau:

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

Tạo tệp `app/controller/UserController.php` với nội dung như sau:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Tạo tệp `app/view/user/hello.blade.php` với nội dung sau:

> Lưu ý rằng tên mẫu Blade có đuôi là `.blade.php`

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

Xem thêm tài liệu tại [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## Ví dụ về giao diện mẫu ThinkPHP
Sửa đổi tệp cấu hình `config/view.php` như sau:

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

Tạo tệp `app/controller/UserController.php` với nội dung như sau:

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Tạo tệp `app/view/user/hello.html` với nội dung sau:

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

Xem thêm tài liệu tại [think-template](https://www.kancloud.cn/manual/think-template/content)

## Gán giá trị cho mẫu
Ngoài cách sử dụng `view(template, array_of_variables)` để gán giá trị cho mẫu, chúng ta cũng có thể gán giá trị cho mẫu bất kỳ bằng cách gọi `View::assign()`. Ví dụ:

```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` rất hữu ích trong một số tình huống, ví dụ như mỗi trang của một hệ thống đều cần hiển thị thông tin người đăng nhập hiện tại. Nếu mỗi trang đều phải gán giá trị thông tin này thông qua `view('template', ['user_info' => 'user information']);` thì sẽ rất phiền phức. Giải pháp cho vấn đề này là lấy thông tin người dùng trong middleware, và sau đó gán giá trị thông tin người dùng vào mẫu thông qua `View::assign()`.
## Về đường dẫn tệp mẫu

#### Bộ điều khiển
Khi bộ điều khiển gọi `view('tên mẫu',[]);`, tệp mẫu được tìm theo các quy tắc sau:

1. Khi không phải ứng dụng nhiều, sử dụng tệp mẫu tương ứng trong `app/view/`
2. [Khi có nhiều ứng dụng](multiapp.md), sử dụng tệp mẫu tương ứng trong `app/ tên ứng dụng /view/`

Tóm lại, nếu `$request->app` rỗng, sẽ sử dụng tệp mẫu trong `app/view/`, nếu không, sẽ sử dụng tệp mẫu trong `app/{$request->app}/view/`.

#### Hàm đóng
Hàm đóng `$request->app` rỗng, không thuộc về bất kỳ ứng dụng nào, vì vậy hàm đóng sử dụng tệp mẫu trong `app/view/`, ví dụ trong tệp cấu hình `config/route.php` định nghĩa tuyến đường:
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
Sẽ sử dụng `app/view/user.html` làm tệp mẫu (khi sử dụng mẫu lưỡi gươm thì tệp mẫu là `app/view/user.blade.php`).

#### Chỉ định ứng dụng
Để tệp mẫu có thể tái sử dụng trong chế độ nhiều ứng dụng, `view($template, $data, $app = null)` cung cấp tham số thứ ba `$app` để chỉ định việc sử dụng tệp mẫu của thư mục ứng dụng nào. Ví dụ `view('user', [], 'admin');` sẽ bắt buộc sử dụng tệp mẫu trong `app/admin/view/`.

## Mở rộng twig

> **Lưu ý**
> Tính năng này yêu cầu webman-framework>=1.4.8

Chúng ta có thể mở rộng phiên bản xem twig bằng cách cung cấp `view.extension` trong cấu hình, ví dụ `config/view.php` như sau:
```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Thêm Extension
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Thêm Bộ lọc
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Thêm hàm
    }
];
```

## Mở rộng blade
> **Lưu ý**
> Tính năng này yêu cầu webman-framework>=1.4.8
Tương tự, chúng ta có thể mở rộng phiên bản xem blade bằng cách cung cấp `view.extension` trong cấu hình, ví dụ `config/view.php` như sau

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Thêm chỉ thị cho blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Blade sử dụng thành phần

> **Lưu ý
> Yêu cầu webman/blade>=1.5.2**

Giả sử cần thêm một thành phần Alert

**Tạo mới `app/view/components/Alert.php`**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**Tạo mới `app/view/components/alert.blade.php`**
```php
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` tương tự như mã sau**
```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

Với việc sử dụng thành phần Blade Alert, trong mẫu sẽ có dạng như sau
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```


## Mở rộng think-template
think-template sử dụng `view.options.taglib_pre_load` để mở rộng thư viện thẻ, ví dụ
```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

Chi tiết về việc mở rộng thẻ xem tại [mở rộng thẻ think-template](https://www.kancloud.cn/manual/think-template/1286424)
