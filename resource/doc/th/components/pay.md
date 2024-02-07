# ซอฟต์แวร์สำหรับการชำระเงิน

## ที่อยู่โปรเจค

 https://github.com/yansongda/pay

## การติดตั้ง

```php
composer require yansongda/pay -vvv
```
## อเปีโอ

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
        // ในกรณีที่ใช้โหมดใบรับรองสาธารณะ โปรดกำหนดพารามิเตอร์สองตัวด้านล่าง และเปลี่ยน ali_public_key เป็นเส้นทางของใบรับรองสาธารณะของ Alipay ที่ลงท้ายด้วย .crt เช่น (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // เส้นทางของใบรับรองสาธารณะของแอป
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // เส้นทางของใบรับรองรากของ Alipay
        'log' => [ // ทางเลือก
            'file' => './logs/alipay.log',
            'level' => 'info', // แนะนำในการปรับระดับในสภาพแวดล้อมการผลิตเป็น info และสภาพแวดล้อมการพัฒนาเป็น debug
            'type' => 'single', // ทางเลือก, สามารถเลือกได้วันละครั้ง
            'max_file' => 30, // ทางเลือก, เฉพาะเมื่อ type เป็น daily, เริ่มต้นที่ 30 วัน
        ],
        'http' => [ // ทางเลือก
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // คอนฟิกเพิ่มเติมโปรดดูที่ [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // ทางเลือก, กำหนดพารามิเตอร์นี้เพื่อเข้าสู่โหมดทดสอบ
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - ทดสอบ',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// ในกรณีของกรอบการพัฒนา Laravel โปรดใช้ `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // ใช่ การตรวจสอบลายเซ็นนั้นง่ายแค่นี้เอง!

        // หมายเลขคำสั่งซื้อ: $data->out_trade_no
        // หมายเลขธุรกรรม Alipay: $data->trade_no
        // จำนวนทั้งหมดของคำสั่งซื้อ: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // ใช่ การตรวจสอบลายเซ็นนั้นง่ายแค่นี้เอง!

            // กรุณาตรวจสอบ trade_status และตรรกะเพื่อตรวจสอบลอจิกอื่นๆ ในการได้รับการชำระเงินจากผู้ซื้อเมื่อดำเนินการบน Alipay การแจ้งเตือนธุรกรรมและการทำธุรกรรมจาก Alipay ภายในธุรกรรมร้ายกำหนดการ ขอให้ Alipay มองเห็นว่าการชำระเงินของผู้ซื้อเสร็จสิ้นเมื่อสถานการณ์การแลกเปลี่ยนเป็น TRADE_SUCCESS หรือ TRADE_FINISHED เท่านั้น
            // 1. ผู้ประกอบการต้องตรวจสอบว่า out_trade_no ในข้อมูลการแจ้งเตือนเป็นหมายเลขคำสั่งซื้อที่สร้างขึ้นในระบบของผู้ประกอบการ
            // 2. ตรวจสอบว่า total_amount เป็นจริงสำหรับยอดเงินทั้งหมดของคำสั่งซื้อ (หรือจำนวนเงินที่สร้างขึ้นเมื่อผู้ประกอบการทำคำสั่งซื้อ)
            // 3. ตรวจสอบ seller_id (หรือ seller_email) ในการแจ้งเตือนว่าจริงหรือไม่สำหรับการดำเนินการโดยสามารถได้เท่านั้น
            // 4. การตรวจสอบ app_id ว่ามันเป็นของผู้ประกอบการเอง
            // 5. และตรรกะธุรกรรมอื่นๆ

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// ในกรณีของกรอบการพัฒนา Laravel โปรดใช้ `return $alipay->success()`
    }
}
```
## WeChat

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // ไอดีแอพพลิเคชัน
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // ไอดีของแอพพลิเคชันรุ่นเล็ก
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // ทางเลือก ใช้เมื่อมีการคืนเงิน หรือกรณีอื่น ๆ
        'cert_key' => './cert/apiclient_key.pem',// ทางเลือก ใช้เมื่อมีการคืนเงิน หรือกรณีอื่น ๆ
        'log' => [ // ทางเลือก
            'file' => './logs/wechat.log',
            'level' => 'info', // แนะนำในสภาพแวดล้อมการผลิตเป็น info ส่วนการพัฒนาเป็น debug
            'type' => 'single', // ทางเลือก, แบบรายวัน
            'max_file' => 30, // ทางเลือก, มีผลกับแบบรายวัน ค่าเริ่มต้น 30 วัน
        ],
        'http' => [ // ทางเลือก
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // สามารถดูการตั้งค่าเพิ่มเติมได้ที่ [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // ทางเลือก, dev/hk; เมื่อเป็น `hk` จะเป็นเกตเวย์ในฮ่องกง
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **หน่วย: สตางค์**
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
            $data = $pay->verify(); // ใช่ครับ, การตรวจสอบลายเซ็นง่ายแบบนี้เอง!

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// สำหรับโครงสร้าง Laravel โปรดใช้ `return $pay->success()`
    }
}
```
## เนื้อหาเพิ่มเติม

เข้าชม https://pay.yanda.net.cn/docs/2.x/overview
