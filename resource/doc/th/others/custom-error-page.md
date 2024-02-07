## กำหนด404เอง
เมื่อเกิด404ใน webman จะถูกตั้งค่าให้คืนค่าเนื้อหาจาก `public/404.html` ดังนั้นนักพัฒนาสามารถแก้ไขไฟล์ `public/404.html` ได้โดยตรง

หากคุณต้องการควบคุมเนื้อหา404แบบไดนามิก เช่น เมื่อร้องขอ ajax จะคืนค่าjson `{"code:"404", "msg":"404 not found"}` และเมื่อร้องขอหน้าจะคืนค่าแม่แบบ `app/view/404.html` โปรดดูตัวอย่างด้านล่าง

> ตัวอย่างด้านล่างใช้แม่แบบ php และแม่แบบอื่นๆ เช่น `twig` `blade` `think-tmplate` จะเป็นการใช้หลักการเดิมกัน

**สร้างไฟล์ `app/view/404.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 not found</title>
</head>
<body>
<?=htmlspecialchars($error)?>
</body>
</html>
```

**ใน`config/route.php` เพิ่มโค้ดดังนี้:**
```php
use support\Request;
use Webman\Route;

Route::fallback(function(Request $request){
    // คืนค่าjson เมื่อร้องขอ ajax
    if ($request->expectsJson()) {
        return json(['code' => 404, 'msg' => '404 not found']);
    }
    // คืนค่าแม่แบบ404.html เมื่อร้องขอหน้า
    return view('404', ['error' => 'some error'])->withStatus(404);
});
```

## กำหนด500เอง
**สร้าง `app/view/500.html` ใหม่**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>500 Internal Server Error</title>
</head>
<body>
กำหนดแม่แบบข้อผิดพลาดเอง:
<?=htmlspecialchars($exception)?>
</body>
</html>
```

**สร้างไฟล์** `app/exception/Handler.php` **(หากไม่มีไดเรกทอรีโปรดสร้างขึ้นเอง)**

```php
<?php

namespace app\exception;

use Throwable;
use Webman\Http\Request;
use Webman\Http\Response;

class Handler extends \support\exception\Handler
{
    /**
     * แสดงผล
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     */
    public function render(Request $request, Throwable $exception) : Response
    {
        $code = $exception->getCode();
        // คืนค่าjson เมื่อร้องขอ ajax
        if ($request->expectsJson()) {
            return json(['code' => $code ? $code : 500, 'msg' => $exception->getMessage()]);
        }
        // คืนค่าแม่แบบ500.html เมื่อร้องขอหน้า
        return view('500', ['exception' => $exception], '')->withStatus(500);
    }
}
```

**กำหนดค่า`config/exception.php`**
```php
return [
    '' => \app\exception\Handler::class,
];
```
