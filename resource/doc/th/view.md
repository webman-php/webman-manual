## มุมมอง
webman ใช้ไวยากรณ์เดิมของ PHP เป็นมุมมองเริ่มต้น และมีประสิทธิภาพที่ดีที่สุดเมื่อเปิด `opcache` นอกจากมุมมองเริ่มต้นของ PHP webman ยังมีเครื่องมือบังคับอื่น ๆ เช่น [Twig](https://twig.symfony.com/doc/3.x/) 、 [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) และ [think-template](https://www.kancloud.cn/manual/think-template/content) 

## เปิดใช้งาน opcache
เมื่อใช้มุมมอง แนะนำให้เปิดตัวเลือก `opcache.enable` และ `opcache.enable_cli` ใน php.ini เพื่อให้เครื่องมงนมุมมองทำงานด้วยประสิทธิภาพที่ดีที่สุด

## ติดตั้ง Twig
1. ติดตั้งผ่านคำสั่ง composer

   `composer require twig/twig`

2. แก้ไขการตั้งค่า `config/view.php` เป็น
    ```php
    <?php
    use support\view\Twig;

    return [
        'handler' => Twig::class
    ];
    ```
    > **เทรียบกันด้วยการตั้งค่า**
    > ตัวเลือกการตั้งค่าอื่น ๆ สามารถส่งผ่าน options เช่น  

    ```php
    return [
        'handler' => Twig::class,
        'options' => [
            'debug' => false,
            'charset' => 'utf-8'
        ]
    ];
    ```
    
## ติดตั้ง Blade
1. ติดตั้งผ่าน composer 

   `composer require psr/container ^1.1.1 webman/blade`

2. แก้ไขการตั้งค่า `config/view.php` เป็น
    ```php
    <?php
    use support\view\Blade;

    return [
        'handler' => Blade::class
    ];
    ```

## ติดตั้ง think-template
1. ติดตั้งผ่าน composer

  `composer require topthink/think-template`

2. แก้ไขการตั้งค่า `config/view.php` เป็น
    ```php
    <?php
    use support\view\ThinkPHP;

    return [
        'handler' => ThinkPHP::class,
    ];
    ```
    > **เทรียบกันด้วยการตั้งค่า**
    > ตัวเลือกการตั้งค่าอื่น ๆ สามารถส่งผ่าน options เช่น

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

## ตัวอย่างของเครื่องมือมุมมอง PHP ธรรมดาร
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

## ตัวอย่างของเครื่องมือมุมมอง Twig
แก้ไขการตั้งค่า `config/view.php` เป็น
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

สามารถดูข้อมูลเพิ่มเติมได้ที่ [Twig](https://twig.symfony.com/doc/3.x/)

## ตัวอย่างของเครื่องมือมุมมอง Blade
แก้ไขการตั้งค่า `config/view.php` เป็น
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

> โปรดทราบว่า หลังส่วนต่อท้ายของไฟล์ blade เป็น `.blade.php`

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

สามารถดูข้อมูลเพิ่มเติมได้ที่ [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## ตัวอย่างของเครื่องมือมุมมองของ ThinkPHP
แก้ไขการตั้งค่า `config/view.php` เป็น
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

สามารถดูข้อมูลเพิ่มเติมได้ที่ [think-template](https://www.kancloud.cn/manual/think-template/content)

## การกำหนดค่าให้กับมุมมอง
นอกจากการกำหนดค่าด้วย `view(มุมมอง, อาร์เรย์ตัวแปร)` เรายังสามารถกำหนดค่าให้กับมุมมองที่ตำแหน่งใด ๆ โดยการเรียกใช้ `View::assign()` ตัวอย่างเช่น:
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
`View::assign()` มีประโยชน์มากในบางสถานการณ์ เช่น ในระบบบางระบบทุกหน้าต้องแสดงข้อมูลของผู้ใช้ที่ล็อกอิน หากต้องการกำหนดค่าข้อมูลผู้ใช้ทั้งหมดผ่าน `View::assign()` จะลดความยุ่งยากในการกำหนดค่าทุกหน้าเพจ. 

## เกี่ยวกับเส้นทางไฟล์มุมมอง
#### ควบคุม
เมื่อควบคุมเรียกใช้ `view('ชื่อมุมมอง',[]);` ไฟล์มุมมองจะถูกค้นหาตามกฎดังนี้:

1. ในกรณีที่ไม่ใช่แอพหลายตัว ใช้ไฟล์มุมมองที่อยู่ใต้ `app/view/`
2. ในกรณีที่เป็น [แอพหลายตัว](multiapp.md) ใช้ไฟล์มุมมองที่อยู่ใต้ `app/ชื่อแอพ/view/`

งานจริงทั้งหมดคือ หาก `$request->app` ว่างเปล่า ใช้ไฟล์มุมมองที่อยู่ใต้ `app/view/` แต่ถ้าไม่ก็ใช้ไฟล์มุมมองที่อยู่ใต้ `app/{$request->app}/view/`

#### ฟังก์ชันปิด
ฟังก์ชันปิด `$request->app` ว่างเปล่า ไม่ได้อยู่ในแอพใด ดังนั้นฟังก์ชันปิดนั้นใช้ไฟล์มุมมองที่อยู่ใต้ `app/view/` ตัวอย่างเช่น การกำหนดเส้นทางในไฟล์ `config/route.php` ดังนี้
```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```
จะใช้ `app/view/user.html` เป็นไฟล์มุมมอง (ในกรณีที่ใช้มุมมอง blade ไฟล์มุมมองจะเป็น `app/view/user.blade.php`)

#### การกำหนดแอพ
เพื่อให้ไฟล์มุมมองในโหมดแอพหลายตัวสามารถสามารถใช้ซ้ำได้ `view($template, $data, $app = null)` มีพารามิเตอร์ที่สาม `$app` ที่สามารถใช้เพื่อกำหนดให้มุมมองใช้อยู่ใต้ไดเรคทอรีของแอพใด ๆ เช่น `view('user', [], 'admin');` จะบังคับให้ใช้ไฟล์มุมมองที่อยู่ใต้ `app/admin/view/`

## ขยายtwig
> **โปรดทราบ**
> ฟีเจอร์นี้ต้องการ webman-framework>=1.4.8

เราสามารถขยายอินสแทนมุมมอง twig โดยการใช้การตั้งค่า `view.extension` callback ในตัวอย่างของ `config/view.php` ดังนี้
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // เพิ่ม Extension
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // เพิ่ม Filter
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // เพิ่มฟังก์ชัน
    }
];
```
## ขยาย blade
> **โปรดทราบ**
> คุณต้องการ webman-framework>=1.4.8 เพื่อใช้คุณลักษณะนี้
เช่นกัน เราสามารถขยายการมองเห็น blade โดยการให้คำร้องกลับไปที่คอนฟิก `view.extension` ตัวอย่างเช่น `config/view.php` ดังต่อไปนี้

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // เพิ่มคำสั่งให้กับ blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## ใช้ component ในการใช้ blade

> **โปรดทราบ**
> ต้องการ webman/blade>=1.5.2

สมมติว่าคุณต้องการเพิ่ม Alert ใน component

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

**`/config/view.php` คล้ายกับรหัสต่อไปนี้**
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

ดังนั้น Blade component Alert ได้ตั้งค่าเรียบร้อยแล้ว การใช้ในแม่แบบจะเป็นดังตัวอย่างต่อไปนี้
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


## ขยาย think-template
think-template ใช้ `view.options.taglib_pre_load` เพื่อขยายตัวช่วยคำสั่ง ตัวอย่างเช่น
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

รายละเอียดเพิ่มเติมอ่าน [think-template"การขยายแท็ก"](https://www.kancloud.cn/manual/think-template/1286424)
