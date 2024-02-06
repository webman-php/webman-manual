# دليل تطوير SDK للدفع (V3)

## عنوان المشروع

 https://github.com/yansongda/pay

## التثبيت

```php
composer require yansongda/pay ^3.0.0
```

## الاستخدام

> ملاحظة: سيتم كتابة الوثائق باستخدام بيئة الوسادة الرملية للدفع على الأليات التالية، إذا كان هناك أي مشكلة ، يرجى تقديم التعليقات!

### ملف تكوين

لنفترض أن لدينا ملف تكوين التالي `config/payment.php`

```php
<?php
return [
    'alipay' => [
        'default' => [
            'app_id' => '20160909004708941',
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            'service_provider_id' => '',
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    // ... (بقية الإعدادات)
];
```
> ملاحظة: ليس هناك تنظيم محدد لدليل الشهادة. المثال أعلاه يميل إلى وضعه في دليل الإطار `(/payment`)

### التهيئة

استدعاء الطريقة `config` مباشرة
```php
$config = Config::get('payment');
Pay::config($config);
```
> ملاحظة: إذا كانت الوضعية في وضع الرملية البيئة ، فيجب فتح ملف التكوين  `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` ، يُفترض أن هذا الاختيار هو الوضع العادي افتراضيًا.

### الدفع (عبر الإنترنت)

```php
public function payment(Request $request)
{
    $config = Config::get('payment');
    Pay::config($config);

    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get'
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### الاستدعاء العكسي

#### الاستدعاء العكسي للغة

```php
public function alipayNotify(Request $request): Response
{
    $config = Config::get('payment');
    Pay::config($config);

    $result = Pay::alipay()->callback($request->post());

    return new Response(200, [], 'success');
}
```
> ملاحظة: يجب عدم استخدام `return Pay::alipay()->success();` للرد على دعوة الدفع . لأنه إذا كنت تستخدم middleware ، فسيحدث مشكلة في middleware. لذا ، يجب استخدام كائن الاستجابة `support\Response;` للرد على الدفع الذي يجب استخدامه.

#### الاستدعاء العكسي للغة

```php
public function alipayReturn(Request $request)
{
    Log::info('الاستدعاء العكسي لـ 』السداد』'.json_encode($request->get()));
    return 'success';
}
```

## الشفرة الكاملة لمثال

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## لمزيد من المعلومات

يُرجى زيارة الوثائق الرسمية https://pay.yansongda.cn/docs/v3/
