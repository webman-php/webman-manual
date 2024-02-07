# পেমেন্ট এসডিকে (ভার্সন ৩)

## প্রকল্প ঠিকানা

https://github.com/yansongda/pay

## ইনস্টলেশন

```php
composer require yansongda/pay ^3.0.0
```

## ব্যবহার

> নোট: এই ডকুমেন্টেশনে আমরা পেমেন্ট সিস্টেমে প্রেরণ করা অ্যালিপে স্যান্ডবক্স এনভায়রনমেন্টের জন্য ডকুমেন্ট লেখা হয়েছে। সমস্যা থাকলে অবশ্যই ফিডব্যাক দিন।
```
<?php
/**
 * @desc পেমেন্ট কনফিগারেশন ফাইল
 * @লেখক Tinywan(ShaoBo ওয়ান)
 * @ তারিখ 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // প্রয়োজনীয় - এপিপাই অনুদেশিত app_id
            'app_id' => '20160909004708941',
            // প্রয়োজনীয়-অ্যাপ্লিকেশন প্রাইভেট কী স্ট্রিং বা পথ
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // প্রয়োজনীয় - অ্যাপ জন্য পাবলিক সার্টিফিকেট পথ
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // প্রয়োজনীয় - পেমেন্ট ঠিকানাে পাবলিক সার্টিফিকেট পথ
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // প্রয়োজনীয় - আলিপে প্রাথমিক সার্টিফিকেট পথ
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // ঐচ্ছিক - সিংক্রোনাইজড কলব্যক ঠিকানা
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // ঐচ্ছিক - অ্যাসিঙ্ক্রোনাস কলব্যক ঠিকানা
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // ঐচ্ছিক - পরিষেবা প্রদানকারী মোডেল এর মেরামত আইডি, যখন মোড হলো Pay::MODE_SERVICE তখন এই প্যারামিটার ব্যবহার করা হয়
            'service_provider_id' => '',
            // ঐচ্ছিক - ডিফল্ট পরিষেবার জন্য সাধারণ মোড যোগ্য। MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // প্রয়োজনীয় - বণিক নম্বর, পরিষেবা প্রদানকারী মোডেলে পরিষেবা প্রদানকারী বণিক নম্বর
            'mch_id' => '',
            // প্রয়োজনীয় - বণিক গোপনীয় কী
            'mch_secret_key' => '',
            // প্রয়োজনীয় - বণিক সিক্রেট সার্টিফিকেট স্ট্রিং বা পথ
            'mch_secret_cert' => '',
            // প্রয়োজনীয় - বণিক পাবলিক সার্টিফিকেট পথ
            'mch_public_cert_path' => '',
            // প্রয়োজনীয়
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // ঐচ্ছিক - পাবলিক যে app_id
            'mp_app_id' => '2016082000291234',
            // ঐচ্ছিক - ছোটা প্রোগ্রামের জন্য app_id
            'mini_app_id' => '',
            // ঐচ্ছিক- app এর জন্য app_id
            'app_id' => '',
            // ঐচ্ছিক - কম্বাইন এর app_id
            'combine_app_id' => '',
            // ঐচ্ছিক - কম্বাইন বণিক নম্বর
            'combine_mch_id' => '',
            // ঐচ্ছিক - পরিষেবা প্রদানকারী মোডেলে, সাব পাবলিক আইডি
            'sub_mp_app_id' => '',
            // ঐচ্ছিক - পরিষেবা প্রদানকারী মোডেলে, সাব অ্যাপ এর app_id
            'sub_app_id' => '',
            // ঐচ্ছিক - পরিষেবা প্রদানকারী মোডেলে, সাব ছোটপ্রোগ্রাম এর app_id
            'sub_mini_app_id' => '',
            // ঐচ্ছিক - সাব বণিক আইডি
            'sub_mch_id' => '',
            // ঐচ্ছিক - ওয়েবসাইট পাবলিক সার্টিফিকেট পথ, ঐচ্ছিক, সাক্ষাতকারে সুপারিপ্রান্ত পরামাণবী অভিযোগ ভাল সুপারপ্রান্তের পরামাণবী বেশি,estবা এর পরামাণবপটি ম্যানও ধারণা
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // ঐচ্ছিক - ডিফল্ট হলো সাধারণ মোড। এটা সাধারণভাবে। মোড ২টির জন্য অবাগ্ত: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'সক্ষম' => মিথ্যা,
        'file' => runtime_path().'/logs/alipay.log',
        'স্তর' => 'debug', // উপস্থাপনার জন্য সুপারবাহী গণপর্যায়ে info, ডেভেলপম্যান্ট জন্য debugগণצ\r
        'type' => 'একক', // ঐচ্ছিক, বন্ধুত্ব  daily
        'max_file' => 30, // ঐচ্ছিক, টাইপ ল-êtreবা টা জানি সুপারপ্রান্তসুপারপরিষ্পর্ধীতা
    ],
    'http' => [ // ঐচ্ছিক
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // আরও গণনা সন্দার্ভ [গাজল](https://guzzle-cn.readthedocs.io/zh\_CN/latest/request-options.html)
    ],
    '_বলবো' => সত্য,
];
```
## আদান

`config` মেথড ডিরেক্টলি কল করুন
```php
// কনফিগ ফাইল config/payment.php পেতে
$config = Config::get('payment');
Pay::config($config);
```
> লক্ষ্য করুন: আপনি যদি আলীপে স্যান্ডবক্স মোডে থাকেন, তবে আপনার কনফিগ ফাইলে `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` অবশ্যই চালু করতে হবে, ডিফল্টভাবে এটি সাধারণ মোডে থাকে।

## পেমেন্ট (ওয়েব)

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
    // ১। কনফিগ ফাইল config/payment.php পেতে
    $config = Config::get('payment');

    // ২। কনফিগারেশন ইনিশিয়ালাইজ করুন
    Pay::config($config);

    // ৩। ওয়েব পেমেন্ট
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // get মেথড ব্যবহার করুন
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## অ্যাসিঙ্ক্রোনাস কলব্যাক

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: অ্যাসিনক্রনাস বিজ্ঞপ্তি
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // ১। কনফিগ ফাইল config/payment.php পেতে
    $config = Config::get('payment');

    // ২। কনফিগারেশন ইনিশিয়ালাইজ করুন
    Pay::config($config);

    // ৩। আলিপে বিজ্ঞপ্তি প্রসেস
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // অনুগ্রহ করে trade_status এবং অন্যান্য যোগাযোগ নিরীক্ষা করেন, TRADE_SUCCESS বা TRADE_FINISHED যখন হলেই, আলিপে কেবলমাত্র সম্মানিত করে যে কেনার প্রস্থান সফল হয়েছে।
    // ১. ব্যবসায়ীরা এই বিজ্ঞপ্তিতে out_trade_no ক্ষেত্রের মান তাদের সিস্টেমে তৈরি অর্ডার নম্বরের সাথে মেলে কিনা তা যাচাই করতে হবে; 
    // ২. তৈরি অর্ডারের মোট পরিমাণটি প্রাপ্ত পরিমাণ কিনা তা নিরীক্ষা করতে হবে (অর্থাত ব্যবসায়ী অর্ডার তৈরির সময়ের পরিমাণ); 
    // ৩. অথবা মেলার ব্যাপারে বিজ্ঞপ্তিতে seller_id (বা seller_email) প্রকারের কি উঠে নি তা নিরীক্ষা করতে হবে;
    // ৪. app_id এরই সঙ্গে আপনি ব্যবহারকারী 
    // ৫. অন্যান্য ব্যবসায়িক লজিক
    // ===================================================================================================

    // ৫। আলিপে বিজ্ঞপ্তি প্রসেস
    return new Response(200, [], 'success');
}
```

> লক্ষ্য করুন: আলিপে বিজ্ঞপ্তির উত্তর পেতে `return Pay::alipay()->success();` ব্যবহার করা যাবে না, এটি আপনি মিডলওয়্যার সমস্যা সৃষ্টি করতে পারে। তাই আপনার আলিপের জবাবদিহি দেওয়ার জন্য webman এর জবাব দেওয়ার জন্য হলে `support\Response;` ব্যবহার করা আবশ্যক।

## সিঙ্ক্রোনাস কলব্যাক

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc:  বাংলাদেশি টাকা সিঙ্ক্রোনাস বিজ্ঞপ্তি
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('বাংলাদেশি টাকা সিঙ্ক্রোনাস বিজ্ঞপ্তি'.json_encode($request->get()));
    return 'success';
}
```
## সম্পূর্ণ কোডের উদাহরণ

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## অধিক তথ্যের জন্য

অফিসিয়াল ডকুমেন্টেশনে প্রবেশ করুন https://pay.yansongda.cn/docs/v3/
