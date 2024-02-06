# ซีเอสเคดี (V3)

## ที่อยู่ของโปรเจค

https://github.com/yansongda/pay

## การติดตั้ง

```php
composer require yansongda/pay ^3.0.0
```

## การใช้

> หมายเหตุ: คำสั่งด้านล่างจะใช้สภาพแวดล้อมของ Sandbox ของ Alipay ในการเขียนเอกสาร หากมีปัญหาโปรดแจ้งให้ทราบ!

### ไฟล์การตั้งค่า

ถ้ามีไฟล์การตั้งค่าดังต่อไปนี้ `config/payment.php`

```php
<?php
/**
 * @desc ไฟล์การตั้งค่าการชำระเงิน
 * @by Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
         'default' => [
            // จำเป็น- app_id ที่ Alipay มอบหมาย
            'app_id' => '20160909004708941',
            // จำเป็น- สายมือ app ทางธุรกิจ ตัวหรือเส้นทาง
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // จำเป็น- เส้นทางการรับรองสาธารณะของแอป
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // จำเป็น- เส้นทางรับรองสาธารณะของ Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // จำเป็น- เส้นทางราก Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // เลือกได้- เส้นทางเรียกคืน
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // เลือกได้- เส้นทางเรียกคืนแบบไม่ตรงคลิก
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            //  เลือกได้- บริการ id ในโหมดผู้ให้บริการในกรณีที่โหมดคือ Pay::MODE_SERVICE
            'service_provider_id' => '',
            //  เลือกได้- ค่าเริ่มต้นคือโหมดปกติ สามารถเลือก https://ได้: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // จำเป็น- โรงรหัสความมั่นใจ โหมดหรือ Profile ร่มสมุด
            'mch_id' => '',
            // จำเป็น- หมดความลับของกิจการ
            'mch_secret_key' => '',
            // จำเป็น- เส้นทางรับรองสาธารณะของร้านค้า
            'mch_secret_cert' => '',
            // จำเป็น- เส้นทางจุดรับรองสาธารณะของร้านค้า
            'mch_public_cert_path' => '',
            // จำเป็น
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // เลือกได้- app_id ของทะเบียนสาธารณะ
            'mp_app_id' => '2016082000291234',
            // เลือกได้- app_id ของแอปน้อย
            'mini_app_id' => '',
            // เลือกได้- app_id ของแอป
            'app_id' => '',
            // เลือกได้- app_id ที่รวมเข้าไป
            'combine_app_id' => '',
            // เลือกได้- หมดการผสมผสาสมื่อแอป
            'combine_mch_id' => '',
            // เลือกได้- โหมดผู้ให้บริการย่อยของ app_id ที่รวมเข้ามา
            'sub_mp_app_id' => '',
            // เลือกได้- โหมดผู้ให้บริการย่อยของ app_id
            'sub_app_id' => '',
            // เลือกได้- โหมดผู้ให้บริการย่อยของ app_id ที่เล็ก
            'sub_mini_app_id' => '',
            // เลือกได้- โหมดผู้ให้บริการย่อยของ รหัสผู้ให้บริการ
            'sub_mch_id' => '',
            // เลือกได้- เส้นทางจุดรับรองสาธารณะของวีชา, optional, ขอแนะนำ php-fpm โหมดบนของการกำหนดพารามิเตอร์นี้
            'wechat_public_cert_path' => [
               '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // เลือกได้- ค่าเริ่มต้นคือโหมดปกติ สามารถเลือก https://ได้: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // แนะนำให้ปรับระดับสำหรับโดยอจาก info, โหมดการพัฒนาเป็น debug
        'type' => 'single', // เลือกได้, ตั้งค่าวันต่อนไป
        'max_file' => 30, // เลือกได้, เมื่อ ตั้งค่าเป็นประเภท เป็ดลี้ เมื่อค่าเริ่มต้นเป็นน้อยกว่า 30 วัน
    ],
    'http' => [ // เลือกได้
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // ดูการตั้งค่าเพิ่มเติมที่[Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> หมายเหตุ: ไม่มีมาตรฐานของไดเรกทอรี่สำหรับการรับรองสาธารณะ เป็นตัวอย่างข้างต้นที่อยู่ในไดเรกทอรี่ 'การชำระเงิน'

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### เริ่มต้น

ใช้เมธอด 'config' เพื่อเริ่มต้น
```php
// ดูไฟล์การตั้งค่าของการชำระเงิน config/payment.php
$config = Config::get('payment');
Pay::config($config);
```

> หมายเหตุ: ถ้าเป็นโหมดการเป่าที่ Alipay จะต้องจำไว้ที่จะเปิดไฟล์การตั้งค่า `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`  ตัวเลือกนี้เป็นค่าตั้งต้น

### การชำระเงิน (เว็บ)

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @param Request $request
 * @return string
 */
public function payment(Request $request)
{
    // 1. ดูไฟล์การตั้งค่าของการชำระเงิน config/payment.php
    $config = Config::get('payment');

    // 2. เริ่มต้นการตั้งค่า
    Pay::config($config);

    // 3. การชำระเงิน ผ่านเว็บ
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'การชำระเงินของ Webman',
        '_method' => 'get' // ใช้วิธืเป็น get
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### การถอนเงิน

#### การแจ้งเตือนสัญญาณเคลื่อนไหว

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: การแจ้งลับนของ Alipay
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. ดูไฟล์การตั้งค่าของการชำระเงิน config/payment.php
    $config = Config::get('payment');

    // 2. เริ่มต้นการตั้งค่า
    Pay::config($config);

    // 3. การดำเนินการคืนของ Alipay
    $result = Pay::alipay()->callback($request->post());

    // กรุณาตรวจสอบ trade_status และตรรกตรวจสอบและการดำเนินการอื่นๆ เมื่อสถานะการแจ้งเตือนคือ TRADE_SUCCESS หรือ TRADE_FINISHED แล้ว Alipay จึงจะพิจารณาว่าผู้ซื้อชำระเงินสำเร็จเท่านั้น
    // 1. ร้านค้าจำเป็นต้องตรวจสอบ out_trade_no ในข้อมูลการแจ้งลับว่าเป็นหมายเลขสั่งซื้อที่ร้านค้าสร้างขึ้น;
    // 2. ตรวจสอบว่า total_amount เป็นยอมรับที่เป็นจริงของจำนวนเงินในสั่งซื้อ (กล่าวคือจำนวนเงินที่ร้านค้าสร้างเมื่อสร้างสั่งซื้อ);
    // 3. ตรวจสอบ seller_id (หรือ seller_email) ให้นี้ out_trade_no ในส่วนของผู้ปฏิบัติในตัวกระวบกระวนของใบสั่งซื้อนี้;
    // 4. ตรวจสอบ app_id ว่าเป็นตัวเองของร้านค้า;
    // 5. ค่าอื่นๆ ของการดำเนินการทางธุรกิจ
    // ===================================================================================================

    // 5. การดำเนินการคืนของ Alipay
    return new Response(200, [], 'success');
}
```
> หมายเหตุ: ไม่สามารถใช้การส่งคืน Pay::alipay()->success(); ของปลั๊กอินตัวเองสำหรับการแจ้งเตือนของ Alipay หากเพื่อใช้ก๊าสกลางจะมีปัญหาด้านกลาง ดังนั้นการส่งคืนของ Alipay จำเป็นต้องใช้เคล็ดลับของ webmanและใช้วัสดุการตอบกลับของ webman `support\Response;`

#### การแจ้งลับนเชจซิ้ม

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: การแจ้งลับนของ Alipay 
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('การแจ้งลับนของ Alipay'.json_encode($request->get()));
    return 'success';
}
```

## รหัสตัวอย่างเต็ม

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## เนื้อหาอื่น ๆ

เยี่ยมชมเอกสารทางการเงิน https://pay.yansongda.cn/docs/v3/
