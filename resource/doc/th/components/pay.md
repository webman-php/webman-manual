# ซีเอสเคเพย์เอสเคเดีซที่ 

### ที่อยู่โปรเจกต์

 https://github.com/yansongda/pay

### การติดตั้ง

```php
composer require yansongda/pay -vvv
```

### การใช้

**อัลิบาบัน**

```php
<?php
namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'app_id' => '2016082000295641',
        'notify_url' => 'http://yansongda.cn/notify.php',
        'return_url' => 'http://yansongda.cn/return.php',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuWJKrQ6SWvS6niI+4vEVZiYfjkCfLQfoFI2nCp9ZLDS42QtiL4Ccyx8scgc3nhVwmVRte8f57TFvGhvJD0upT4O5O/lRxmTjechXAorirVdAODpOu0mFfQV9y/T9o9hHnU+VmO5spoVb3umqpq6D/Pt8p25Yk852/w01VTIczrXC4QlrbOEe3sr1E9auoC7rgYjjCO6lZUIDjX/oBmNXZxhRDrYx4Yf5X7y8FRBFvygIE2FgxV4Yw+SL3QAa2m5MLcbusJpxOml9YVQfP8iSurx41PvvXUMo49JG3BDVernaCYXQCoUJv9fJwbnfZd7J5YByC+5KM4sblJTq7bXZWQIDAQAB',
        // วิธีการเข้ารหัส: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // ในกรณีที่ต้องการใช้งานในโหมดสาธารณะ โปรดกำหนดพารามิเตอร์สองตัวดังต่อไปนี้ และปรับแก้ไข ali_public_key เป็นเส้นทางของไฟล์กุญแจสาธารณะ alipay ที่มีนามสกุล .crt เช่น (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //เส้นทางของกุญแจสาธารณะแอปพลิเคชัน
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', //เส้นทางของไฟล์ราก alipay
        'log' => [ // ตัวเลือก
            'file' => './logs/alipay.log',
            'level' => 'info', // แนะนำให้ปรับระดับในสภาพแวดล้อมการผลิตเป็น info สำหรับสภาพแวดล้อมการพัฒนาเป็น debug
            'type' => 'single', // ตัวเลือก , เลือกล่วงหน้า
            'max_file' => 30, // ตัวเลือก ใช้ได้เมื่อ type เป็นรายวัน เริ่มต้น 30 วัน
        ],
        'http' => [ // ตัวเลือก
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // ตัวเลือกเพิ่มเติมกรุณาดู [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // ตัวเลือก, กำหนดพารามิเตอร์นี้ จะเข้าสู่โหมดหุ่นยนต์
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - ทดสอบ',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// ในกรณีของเฟรมเวิร์กลิ้ง โปรดใช้ `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // ใช่ เพียงแค่ตรวจสอบบัตร!

        // หมายเลขคำสั่งซื้อ：$data->out_trade_no
        // หมายเลขธุรกรรมของอาลีบาบา: $data->trade_no
        // ยอดรวมการสั่งซื้อ：$data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // ใช่ เพียงแค่ตรวจสอบบัตร!

            // กรุณาตรวจสอบ trade_status เป็นการตรวจสอบต่างๆ และตรรกตรวจสอบความสมบูรณ์ในการดำเนินการอื่นๆ ในการแจ้งการแจ้งเตือนธุรกรรม
            // 1. ผู้ขายต้องตรวจสอบว่า out_trade_no ในข้อมูลการแจ้งนี้เป็นหมายเลขออเดอร์ที่สร้างขึ้นในระบบผู้ขายหรือไม่
            // 2. ตรวจสอบว่า total_amount เป็นจริงสำหรับยอดเงินทั้งหมดของออเดอร์นี้ (คือจำนวนเงินที่สร้างขึ้นในขณะสร้างข้อมูลการสั่งซื้อของผู้ขาย)
            // 3. ตรวจสอบดูรหัสผู้ขาย (หรืออีเมลผู้ขาย) ในการแจ้งเตือนว่าตราตรภาพของออร์เดอร์นี้คือ emergency roomเดิมที่มี pingle publisher（บางครั้งผู้ประกอบการ profileิัดมีครบเครเมิมีหลายครั้ง）
            // 4. ตระสสเอื้ม app ได้บทีการิกหรือไม่
            // 5. สถานการณ์อื่นๆ ที่น่าสนใจในการดำเนินการในธุรกรรม商。

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// ในกรณีของเฟรมเวิร์กลิ้ง โปรดใช้ `return $alipay->success()`
    }
}
```

**เวอร์ชั่น**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // หมายเลข APP แอป
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // หลดของแอปพลิเคชัน APPID
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // ตัวเลือก หากต้องการคืนเงิน และการเพิมเติม
        'cert_key' => './cert/apiclient_key.pem',// ตัวเลือก หากต้องการคืนเงิน และการเพิมเติม
        'log' => [ // ตัวเลือก
            'file' => './logs/wechat.log',
            'level' => 'info', // แนะนำให้ปรับระดับในสภาพแวดล้อมการผลิตเป็น info สำหรับสภาพแวดล้อมการพัฒนาเป็น debug
            'type' => 'single', // optional, สามารถเลือกเป็นรายวันได้
            'max_file' => 30, // optional, เมื่อ type เป็นรายวัน มีผลเป็น 30 วัน เมื่อไม่ระบุรายวันไว้ล่วงหน้า
        ],
        'http' => [ // ตัวเลือก
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // ตัวเลือกเพิ่มเติมกรุณาดู [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // ตัวเลือก, dev/hk; เมื่อเป็น `hk` คือ จะเป็นโหมดเกตเวย์ฮ๊องกุง
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **หน่วย：สตางค์**
            'body' => 'test body - ทดสอบ',
            'openid' => 'onkVf1FjWS5SBIixxxxxxx',
        ];

        $pay = Pay::wechat($this->config)->mp($order);

        // $pay->appId
        // $pay->timeStamp
        // $pay->nonceStr
        // $pay->package
        // $pay->signType
    }

    public function notify()
    {
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify(); // ใช่ เพียงแค่ตรวจสอบบัตร!

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// ในกรณีของเฟรมเวิร์กลิ้ง โปรดใช้ `return $pay->success()`
    }
}
```

### เนื้อหาเพิ่มเติม

เข้าชม https://pay.yanda.net.cn/docs/2.x/overview
