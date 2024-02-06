## มุมมอง
webman มีการใช้ภาษา PHP ธรรมดาเป็นต้นฉบับเป็นรูปแบบของเทมเพลต ในสภาวะที่ opcache เปิดเมื่อทำการเปิดใช้งานจะมีประสิทธิภาพที่ดีที่สุด นอกจากนี้ยังมีเทมเพลตเอนจินอื่นๆ เช่น [Twig](https://twig.symfony.com/doc/3.x/)  [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)  [think-template](https://www.kancloud.cn/manual/think-template/content) ให้ใช้งาน

## เปิดใช้งาน opcache
อย่างมากแนะนำให้เปิดค่า `opcache.enable` และ `opcache.enable_cli` ใน php.ini เมื่อใช้งานเทมเพลตเพื่อให้เทมเพลตเอนจินทำงานได้ดีที่สุด

## การติดตั้ง Twig
1. ติดตั้งผ่านคำสั่ง composer

```composer require twig/twig```

2. แก้ไขการตั้งค่าใน `config/view.php` เป็น

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **คำแนะนำ**
> การตั้งค่าอื่นๆ สามารถส่งผ่าน options เช่น

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## การติดตั้ง Blade
1. ติดตั้งผ่านคำสั่ง composer

```composer require psr/container ^1.1.1 webman/blade```

2. แก้ไขการตั้งค่าใน `config/view.php` เป็น

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## การติดตั้ง think-template
1. ติดตั้งผ่านคำสั่ง composer

```composer require topthink/think-template```

2. แก้ไขการตั้งค่าใน `config/view.php` เป็น

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **คำแนะนำ**
> การตั้งค่าอื่นๆ สามารถส่งผ่าน options เช่น

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

## ตัวอย่างการใช้งานเทมเพลตภาษา PHP ธรรมดา
สร้างไฟล์ `app/controller/UserController.php` ดังนี้

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

สร้างไฟล์ `app/view/user/hello.html` ดังนี้

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

## ตัวอย่างการใช้งานเทมเพลตภาษา Twig
แก้ไขการตั้งค่าใน `config/view.php` เป็น

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` ดังนี้

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

ไฟล์ `app/view/user/hello.html` ดังนี้

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

สำหรับข้อมูลเพิ่มเติม ดูได้ใน [Twig](https://twig.symfony.com/doc/3.x/) 

## ตัวอย่างการใช้งานเทมเพลตภาษา Blade
แก้ไขการตั้งค่าใน `config/view.php` เป็น

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` ดังนี้

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

ไฟล์ `app/view/user/hello.blade.php` ดังนี้

> ข้อควรระวัง: ชื่อของไฟล์เทมเพลต blade ต้องเป็น `.blade.php`

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

สำหรับข้อมูลเพิ่มเติม ดูได้ใน [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## ตัวอย่างการใช้งานเทมเพลตภาษา think-template
แก้ไขการตั้งค่าใน `config/view.php` เป็น

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` ดังนี้

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

ไฟล์ `app/view/user/hello.html` ดังนี้


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

สำหรับข้อมูลเพิ่มเติม ดูได้ใน [think-template](https://www.kancloud.cn/manual/think-template/content)

## การ set ค่าให้เทมเพลต
นอกจากการใช้ `view(เทมเพลต, อาร์เรย์ของตัวแปร)` เพื่อ set ค่าให้เทมเพลต ยังสามารถทำได้จากตัวแปร ซึ่งจะ set ค่าให้เทมเพลตได้ในทุกๆที่ เช่น:

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

`View::assign()` มีประโยชน์มากในบางโอกาส เช่น ระบบใดๆ เมื่อทุกหน้าเพจจะต้องแสดงข้อมูลของผู้ใช้ที่ login ตัวแปรของผู้ใช้ที่ login จึงสามารถ set ค่าให้เทมเพลตผ่าน `View::assign()` ได้

## เกี่ยวกับการกำหนดเส้นทางของไฟล์เทมเพลต

#### ตัวควบคุม
เมื่อตัวควบคุมเรียกใช้ `view('ชื่อเทมเพลต', []);` อย่างต่อไปนี้คือตามกฏการค้นหา

1. เมื่อไม่มีการใช้แอปพลิเคชันซึ่งใช้ `app/view/` เป็นไฟล์เทมเพลตที่นำมาใช้
2. [ในกรณีไฟล์เทมเพลตมีมากกว่า 1 แอปพลิเคชัน](multiapp.md) ให้ใช้ไฟล์เทมเพลตจาก `app/ชื่อแอปพลิเคชัน/view/`

และในท้ายที่สุด การกำหนดว่าถ้า `$request->app` ว่างเปล่า จะใช้เทมเพลตจาก `app/view/` หรือจะใช้ `app/{$request->app}/view/`

#### ฟังก์ชันปิด
เพราะฟังก์ชันปิดใช้ `$request->app` ว่างเปล่า ซึ่งไม่ได้อยู่ภายใต้แอปพลิเคชันใดๆ ดังนั้นในกรณีนี้จะใช้ไฟล์เทมเพลตจาก `app/view/` เช่นเดียวกันกับการใช้เทมเพลต blade ที่ไฟล์เทมเพลตจะใช้ `app/view/user.blade.php`

#### การกำหนดแอปพลิเคชัน
เพื่อที่จะทำให้ไฟล์เทมเพลตสามารถใช้ซ้ำในโหมดมากขึ้น `view($template, $data, $app = null)` มีอาร์กิวเมนตที่สาม `$app` สามารถใช้สำหรับกำหนดให้มันใช้ไฟล์เทมเพลตภายใต้ไดเรกทอรี่ของแอปพลิเคชันใดๆ เช่น  `view('user', [], 'admin');` จะบังคับให้ใช้ไฟล์เทมเพลตจาก `app/admin/view/`


## การเพิ่มส่วนขยายใน twig

> **โปรดทราบ**
> คุณสามารถทำการเพิ่มส่วนขยายในตัวกำหนด `view.extension` โดยที่คุณสามารถเพิ่มเพื่อต่อขยายเทมเพลต twig ได้ เช่น `config/view.php` เพิ่มเป็นดังนี้

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // เพิ่มส่วนขยาย
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // เพิ่มตัวกรอง
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // เพิ่มฟังก์ชัน
    }
];
```


## การเพิ่มส่วนขยายใน Blade
> **โปรดทราบ**
> คุณสามารถทำการเพิ่มส่วนขยายในตัวกำหนด `view.extension` โดยที่คุณสามารถเพิ่มเพื่อต่อขยายเทมเพลต Blade ได้ เช่น `config/view.php` เพิ่มเป็นดังนี้

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // เพิ่มกรอบ blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## การใช้งานคอมโพแนนต์ใน Blade
> **โปรดทราบ
> จะต้องใช้งาน webman/blade>=1.5.2**

สมมติว่าต้องการเพิ่มคอมโพแนนต์เพิ่มเติม เช่น Alert

**สร้าง `app/view/components/Alert.php`**
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

**สร้าง `app/view/components/alert.blade.php`**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` การตั้งค่าผล้ายกับนี้**

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

นับจากนี้ ตัวคอมโพแนนต์ของ Blade คือ Alert มันจะเสร็จสมบูรณ์ และในเทมเพลตก็สามารถเรียกใช้ได้แบบนี้
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


## เพิ่มส่วนขยายใน think-template
think-template ใช้`view.options.taglib_pre_load` เพื่อสุ่มการเพิ่มป้าย <body> ลงในหน้า HTML

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

โปรดดูรายละเอียดเพิ่มเติมได้ที่ [think-template การเพิ่มป้าย](https://www.kancloud.cn/manual/think-template/1286424)
