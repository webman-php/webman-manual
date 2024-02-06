# คอมโพเนนต์ที่เกี่ยวข้องกับรหัสยืนยัน

## webman/captcha
ที่อยู่โปรเจกต์ https://github.com/webman-php/captcha

### การติดตั้ง
```
composer require webman/captcha
```

### การใช้งาน

**สร้างไฟล์ `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * หน้าทดสอบ
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * แสดงภาพรหัสยืนยัน
     */
    public function captcha(Request $request)
    {
        // สร้างคลาสรหัสยืนยัน
        $builder = new CaptchaBuilder;
        // สร้างรหัสยืนยัน
        $builder->build();
        // บันทึกค่ารหัสยืนยันไว้ในเซสชั่น
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // รับข้อมูลภาพรหัสยืนยันเป็นไบนารี
        $img_content = $builder->get();
        // แสดงข้อมูลภาพรหัสยืนยันเป็นไบนารี
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * ตรวจสอบรหัสยืนยัน
     */
    public function check(Request $request)
    {
        // รับฟิลด์รหัสยืนยันจากคำขอแบบ POST
        $captcha = $request->post('captcha');
        // เปรียบเทียบค่ารหัสยืนยันที่อยู่ในเซสชั่น
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'รหัสยืนยันที่ป้อนไม่ถูกต้อง']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**สร้างไฟล์เทมเพลต `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>การทดสอบรหัสยืนยัน</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="ส่ง" />
    </form>
</body>
</html>
```

เข้าสู่หน้าเว็บ `http://127.0.0.1:8787/login` จะมีหน้าต่างแสดงดังนี้:
  ![](../../assets/img/captcha.png)

### การตั้งค่าพารามิเตอร์ที่พบบ่อย
```php
    /**
     * แสดงภาพรหัสยืนยัน
     */
    public function captcha(Request $request)
    {
        // สร้างคลาสรหัสยืนยัน
        $builder = new CaptchaBuilder;
        // ความยาวของรหัสยืนยัน
        $length = 4;
        // รวมอักขระที่มีในรหัส
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // สร้างรหัสยืนยัน
        $builder->build();
        // บันทึกค่ารหัสยืนยันไว้ในเซสชั่น
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // รับข้อมูลภาพรหัสยืนยันเป็นไบนารี
        $img_content = $builder->get();
        // แสดงข้อมูลภาพรหัสยืนยันเป็นไบนารี
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

ดูเพิ่มเติมเกี่ยวกับอินเทอร์เฟซและพารามิเตอร์ที่ https://github.com/webman-php/captcha
