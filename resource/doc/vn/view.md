## Giao diện
webman mặc định sử dụng cú pháp nguyên thủy của PHP làm mẫu. Khi bật `opcache`, webman sẽ có hiệu suất tốt nhất. Ngoài cú pháp mẫu gốc của PHP, webman còn cung cấp các công cụ mẫu như [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377), [think-template](https://www.kancloud.cn/manual/think-template/content).

## Bật opcache
Khi sử dụng giao diện, nên kích hoạt cài đặt `opcache.enable` và `opcache.enable_cli` trong tệp php.ini để đạt hiệu suất tốt nhất cho máy chủ mẫu.

## Cài đặt Twig
1. Cài đặt qua composer

   `composer require twig/twig`

2. Sửa đổi cấu hình `config/view.php` thành

   ```php
   <?php
   use support\view\Twig;

   return [
       'handler' => Twig::class
   ];
   ```
   > **Tip**
   > Các tùy chọn cấu hình khác có thể được truyền vào thông qua options, ví dụ

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
1. Cài đặt qua composer

   `composer require psr/container ^1.1.1 webman/blade`

2. Sửa đổi cấu hình `config/view.php` thành

   ```php
   <?php
   use support\view\Blade;

   return [
       'handler' => Blade::class
   ];
   ```

## Cài đặt think-template
1. Cài đặt qua composer

   `composer require topthink/think-template`

2. Sửa đổi cấu hình `config/view.php` thành

   ```php
   <?php
   use support\view\ThinkPHP;

   return [
       'handler' => ThinkPHP::class,
   ];
   ```
   > **Tip**
   > Các tùy chọn cấu hình khác có thể được truyền vào thông qua options, ví dụ

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

## Ví dụ về giao diện mẫu nguyên thủy của PHP
Tạo tệp `app/controller/UserController.php` như sau

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

Tạo tệp `app/view/user/hello.html` như sau

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

## Ví dụ về giao diện Twig
Sửa đổi cấu hình `config/view.php` thành
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` như sau

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

Tệp `app/view/user/hello.html` như sau

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

Xem thêm tài liệu [Twig](https://twig.symfony.com/doc/3.x/)

## Ví dụ về giao diện Blade
Sửa đổi cấu hình `config/view.php` thành
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` như sau

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

Tệp `app/view/user/hello.blade.php` như sau

> Lưu ý: tên mẫu blade có phần mở rộng là `.blade.php`

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

Xem thêm tài liệu [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## Ví dụ về giao diện ThinkPHP
Sửa đổi cấu hình `config/view.php` thành
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` như sau

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

Tệp `app/view/user/hello.html` như sau

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

Xem thêm tài liệu [think-template](https://www.kancloud.cn/manual/think-template/content)

## Gán giá trị cho mẫu
Ngoài việc sử dụng `view(template, array_variable)` để gán giá trị cho mẫu, chúng ta cũng có thể gán giá trị cho mẫu bất kỳ bằng cách gọi `View::assign()`. Ví dụ:

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

`View::assign()` rất hữu ích trong một số tình huống, ví dụ như mỗi trang của hệ thống đều cần hiển thị thông tin người đăng nhập hiện tại. Nếu mỗi trang đều phải gán thông tin này bằng `view('template', ['user_info' => 'user information'])` sẽ rất phiền. Giải pháp là lấy thông tin người dùng thông qua middleware, sau đó gán thông tin người dùng cho mẫu bằng cách sử dụng `View::assign()`.

## Về đường dẫn tệp giao diện
#### Bộ điều khiển
Khi bộ điều khiển gọi `view('tên_mẫu',[])`, tệp giao diện sẽ được tìm theo các quy tắc sau:

1. Khi không phải là ứng dụng nhiều, sử dụng tệp giao diện tương ứng trong `app/view/`
2. Khi là ứng dụng nhiều, sử dụng tệp giao diện tương ứng trong `app/tên_ứng_dụng/view/`

Tóm lại, nếu `$request->app` rỗng, sử dụng tệp giao diện trong `app/view/`, ngược lại sử dụng tệp giao diện trong `app/{$request->app}/view/`.

#### Hàm đóng gói
Hàm đóng gói `$request->app` rỗng, không thuộc bất kỳ ứng dụng nào, nên hàm đóng gói sử dụng tệp giao diện trong `app/view/`. Ví dụ, định nghĩa tuyến đường trong tệp `config/route.php` như sau

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```

sẽ sử dụng tệp mẫu `app/view/user.html` (khi sử dụng mẫu blade, tệp mẫu là `app/view/user.blade.php`).

#### Xác định ứng dụng
Để có thể tái sử dụng tệp mẫu trong chế độ nhiều ứng dụng, `view($template, $data, $app = null)` cung cấp thông số thứ ba `$app` để xác định việc sử dụng tệp mẫu trong thư mục ứng dụng nào. Ví dụ `view('user', [], 'admin')` sẽ bắt buộc sử dụng tệp mẫu trong `app/admin/view/`.

## Mở rộng twig

> **Lưu ý**
> Tính năng này yêu cầu webman-framework >= 1.4.8

Chúng ta có thể mở rộng phiên bản mẫu twig bằng cách gán `view.extension` trong cấu hình cho các hàm mở rộng twig, ví dụ như `config/view.php` dưới đây

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
> Tính năng này yêu cầu webman-framework >= 1.4.8

Tương tự, chúng ta có thể mở rộng phiên bản mẫu blade bằng cách gán `view.extension` trong cấu hình cho các hàm mở rộng blade, ví dụ như `config/view.php` dưới đây

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

## Sử dụng thành phần của blade

> **Lưu ý
> Yêu cầu webman/blade >= 1.5.2**

Giả sử cần thêm một thành phần cảnh báo

**Tạo `app/view/components/Alert.php` mới**
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

**Tạo `app/view/components/alert.blade.php` mới**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` tương tự như dưới đây**

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

Bây giờ, cảnh báo blade đã được thiết lập xong. Khi sử dụng trong mẫu, nó sẽ giống như sau
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

Xem chi tiết [mở rộng thẻ think-template](https://www.kancloud.cn/manual/think-template/1286424)

