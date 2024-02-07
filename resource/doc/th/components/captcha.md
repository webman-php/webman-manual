# คอมโพเนนต์ที่เกี่ยวกับรหัสยืนยัน

## webman/captcha
ที่อยู่ของโปรเจค https://github.com/webman-php/captcha

### การติดตั้ง
```composer require webman/captcha```

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
     * แสดงรูปภาพยืนยันตัวตน
     */
    public function captcha(Request $request)
    {
        // เริ่มต้นคลาสรหัสยืนยันตัวตน
        $builder = new CaptchaBuilder;
        // สร้างรหัสยืนยันตัวตน
        $builder->build();
        // จัดเก็บค่ารหัสยืนยันตัวตนในเซสชัน
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // รับข้อมูลไบนารีของรูปภาพยืนยันตัวตน
        $img_content = $builder->get();
        // แสดงข้อมูลไบนารีของรูปภาพยืนยันตัวตน
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * ตรวจสอบรหัสยืนยันตัวตน
     */
    public function check(Request $request)
    {
        // รับค่าฟิลด์รหัสยืนยันตัวตนจากคำขอ post
        $captcha = $request->post('captcha');
        // เปรียบเทียบค่ารหัสยืนยันตัวตนในเซสชัน
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'รหัสยืนยันตัวตนที่ป้อนไม่ถูกต้อง']);
        }
        return json(['code' => 0, 'msg' => 'ตกลง']);
    }

}
```

**สร้างไฟล์เทมเพลต `app/view/login/index.html`**
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>ทดสอบรหัสยืนยันตัวตน</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="ยื่นยัน" />
    </form>
</body>
</html>
```

เข้าสู่หน้าเว็บ `http://127.0.0.1:8787/login` จะมีหน้าต่างในรูปแบบดังนี้:
  ![](../../assets/img/captcha.png)

### การตั้งค่าพารามิเตอร์ที่ใช้บ่อย
```php
    /**
     * แสดงรูปภาพยืนยันตัวตน
     */
    public function captcha(Request $request)
    {
        // เริ่มต้นคลาสรหัสยืนยันตัวตน
        $builder = new CaptchaBuilder;
        // ความยาวของรหัสยืนยันตัวตน
        $length = 4;
        // รวมตัวอักษรใดใดบ้าง
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // สร้างรหัสยืนยันตัวตน
        $builder->build();
        // จัดเก็บค่ารหัสยืนยันตัวตนในเซสชัน
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // รับข้อมูลไบนารีของรูปภาพยืนยันตัวตน
        $img_content = $builder->get();
        // แสดงข้อมูลไบนารีของรูปภาพยืนยันตัวตน
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

ดูข้อมูลเพิ่มเติมเกี่ยวกับอินเตอร์เฟซและพารามิเตอร์ได้ที่ https://github.com/webman-php/captcha
