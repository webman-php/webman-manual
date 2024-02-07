# ซอฟต์แวร์พัฒนา SDK (V3)

## ที่อยู่ของโปรเจ็กต์
https://github.com/yansongda/pay

## การติดตั้ง

```php
composer require yansongda/pay ^3.0.0
```

## การใช้

> หมายเหตุ: ข้างล่างนี้จะมีการระบุการเขียนเอกสารในระบบ sandbox ของ Alipay และหากมีปัญหา โปรดแจ้งให้ทราบ!

## ไฟล์การกำหนดค่า

ที่สมมติว่ามีไฟล์การกำหนดค่าต่อไปนี้: `config/payment.php`

```php
<?php
/**
 * @desc ไฟล์การกำหนดค่าการชำระเงิน
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // จำเป็นต้องระบุ- app_id ที่อัลิเปย์มอบหมาย
            'app_id' => '20160909004708941',
            // จำเป็นต้องระบุ- คีย์ส่วนตัวของแอป สตริงหรือเส้นทาง
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // จำเป็นต้องระบุ- เส้นทางของใบรับรองสาธารณะของแอป
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // จำเป็นต้องระบุ- เส้นทางของใบรับรองสาธารณะของอัลิเปย์
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // จำเป็นต้องระบุ- เส้นทางของใบรับรองสาธารณะของอัลิเปย์รูท
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // สามารถเลือกได้- ที่อยู่สำหรับการเรียกร้องสถานะการชำระเงิน
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // สามารถเลือกได้- ที่อยู่สำหรับการแจ้งเตือนแบบไม่ระบุเงิน
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // สามารถเลือกได้- ID ของผู้ให้บริการในโหมดผู้ให้บริการ ตอนที่โหมดเป็น Pay::MODE_SERVICE
            'service_provider_id' => '',
            // สามารถเลือกได้- เริ่มต้นโหมดเป็นปกติ สามารถเลือกได้: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // จำเป็นต้องระบุ- หมายเลขร้านค้า, ในโหมดผู้ให้บริการคือหมายเลขร้านค้าของผู้ให้บริการ
            'mch_id' => '',
            // จำเป็นต้องระบุ- คีย์ลับของร้านค้า
            'mch_secret_key' => '',
            // จำเป็นต้องระบุ- เส้นทางของคีย์ส่วนตัวของร้านค้า สตริงหรือเส้นทาง
            'mch_secret_cert' => '',
            // จำเป็นต้องระบุ- เส้นทางของใบรับรองสาธารณะของร้านค้า
            'mch_public_cert_path' => '',
            // จำเป็นต้องระบุ
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // สามารถเลือกได้- ของแอปพลิเคชันสาธารณะ app_id
            'mp_app_id' => '2016082000291234',
            // สามารถเลือกได้- ของแอปพลิเคชันขนาดเล็ก app_id
            'mini_app_id' => '',
            // สามารถเลือกได้- ของแอป app_id
            'app_id' => '',
            // สามารถเลือกได้- app_id ของการรวมระดับ
            'combine_app_id' => '',
            // สามารถเลือกได้- หมายเลขร้านค้ารวม
            'combine_mch_id' => '',
            // สามารถเลือกได้- ในโหมดผู้ให้บริการ, app_id ของส่วนย่อยของสาธารณะ
            'sub_mp_app_id' => '',
            // สามารถเลือกได้- ในโหมดผู้ให้บริการ, app_id ของแอป
            'sub_app_id' => '',
            // สามารถเลือกได้- ในโหมดผู้ให้บริการ, app_id ของแอปพลิเคชันขนาดเล็ก
            'sub_mini_app_id' => '',
            // สามารถเลือกได้- ในโหมดผู้ให้บริการ, หมายเลขร้านค้าย่อย
            'sub_mch_id' => '',
            // สามารถเลือกได้- เส้นทางของใบรับรองสาธารณะของวีชา, ไม่บังคับ แนะนำให้กำหนดพารามิเตอร์นี้ในโหมด php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // สามารถเลือกได้- เริ่มต้นโหมดเป็นปกติ สามารถเลือกได้: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // แนะนำให้ปรับระดับในสภาพแวดล้อมการผลิตเป็น info, สำหรับสภาพแวดล้อมการพัฒนาเป็น debug
        'type' => 'single', // สามารถเลือก daily.
        'max_file' => 30, // สามารถเลือกที่ปรับเมื่อประเภทเป็น daily. ค่าเริ่มต้น 30 วัน
    ],
    'http' => [ // สามารถเลือกได้
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // ค่าการตั้งค่าเพิ่มเติมโปรดดูที่ [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> ข้อควรระวัง: ไม่มีข้อกำหนดเกี่ยวกับไดเรกทอรีใบรับรอง ตัวอย่างของข้างต้นคือการจัดเก็บไว้ในไดเรกทอรี 'payment' ของโครงข่าย

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## การเตรียมความพร้อม

เรียกใช้เมธอด `config` เพื่อเตรียมความพร้อม
```php
// รับไฟล์การกำหนดค่า config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> ข้อควรระวัง: หากเป็นโหมด sandbox ของ Alipay จําเป็นต้องจดจำเปิดใช้ config ในไฟล์การกำหนดค่า `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` ทางเลือกนี้ได้รับค่าเริ่มต้นเป็นโหมดปกติ

## การชำระเงิน (ภายในเว็บ)

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
    // 1. รับไฟล์การกำหนดค่า config/payment.php
    $config = Config::get('payment');
    
    // 2. เริ่มต้นการกำหนดค่า
    Pay::config($config);

    // 3. การชำระเงินทางเว็บ
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'การชำระเงิน webman',
        '_method' => 'get' // ใช้วิธีการ get ในการเปลี่ยนเส้นทาง
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## การแจ้งเตือนแบบไม่เริ่งตัว

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: การแจ้งเตือนอัลิเปย์แบบไม่เริ่งตัว
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. รับไฟล์การกำหนดค่า config/payment.php
    $config = Config::get('payment');

    // 2. เริ่มต้นการกำหนดค่า
    Pay::config($config);

    // 3. การประมวลผลการส่งกลับของอัลิเปย์
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // กรุณาตรวจสอบ trade_status และตรวจสอบตรรกะอื่นๆ เมื่อมีความจำเป็น,
    // การแจ้งเตือนการซื้อขายจะถูกตั้งค่าเพียงแบบ TRADE_SUCCESS หรือ TRADE_FINISHED เท่านั้น อัลิเปย์จึงจะมอบหมายว่าผู้ซื้อชำระเงินเรียบร้อยแล้ว
    // 1. ค้าไข้ร้านค้าต้องยืนยันว่า out_trade_no ในข้อมูลการแจ้งเตือนนี้เป็นหมายเลขออเดอร์ที่สร้างขึ้นในระบบของร้านค้า;
    // 2. ตรวจสอบ total_amount ที่ถูกต้องตามจำนวนเงินจริงของรายการสั่งซื้อ (คือ จํานวนเงินที่สร้างเมื่อสร้างออเดอร์);
    // 3. ตรวจสอบโจทย์ของผู้ติกงข้าีผู้บริการ (หรืออีเมลล์ของผู้ขาย) ว่ามีค่าเท่ากับโจทย์ของบิลเงินนี้หรือไม่;
    // 4. ยืนยันว่า app_id คือ ร้านค้าเอง;
    // 5. ตรรงะอื่นๆ
    // ===================================================================================================

    // การประมวลผลการส่งกลับของอัลิเปย์
    return new Response(200, [], 'success');
}
```
> ข้อควรระวัง: ไม่สามารถใช้คำสั้ง `return Pay::alipay()->success();` ในการตอบกลับการแจ้งเตือนของอัลิเปย์ หากมีการใช้ middleware จะทำให้เกิดปัญหา ดังนั้นการตอบกลับต้องใช้คลาสการตอบกลับของ webman `support\Response`
## การโปรแกรมคอลแบ็กซิงค์

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: การแจ้งเตือนแบ็กซิงของ 『Alipay』
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('การแจ้งเตือนแบ็กซิงของ 『Alipay』' . json_encode($request->get()));
    return 'success';
}
```

## รหัสทั้งหมดของตัวอย่าง

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## เนื้อหาเพิ่มเติม

เข้าชมเอกสารทางการ https://pay.yansongda.cn/docs/v3/
