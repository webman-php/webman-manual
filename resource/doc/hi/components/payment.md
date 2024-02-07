# भुगतान SDK (V3)

## प्रोजेक्ट पता

https://github.com/yansongda/pay

## स्थापना

```php
composer require yansongda/pay ^3.0.0
```

## उपयोग

> टिप्पणी: निम्नलिखित दस्तावेज़ को अलीपे सैंडबॉक्स वातावरण के रूप में वातावरण का विवरण दिया गया है, यदि कोई समस्या है, कृपया समय पर सूचित करें!

## कॉन्फ़िग फ़ाइल

यह मान लेते हैं कि निम्नलिखित कॉन्फ़िग फ़ाइल `config/payment.php` है

```php
<?php
/**
 * @desc भुगतान कॉन्फ़िगरेशन फ़ाइल
 * @author टाइनीवैन (शाओबो वान)
 * @date २०२२/०३/११ २०:१५
 */
return [
    'alipay' => [
        'default' => [
            // आवश्यक - app_id जो अलीपे द्वारा किया गया है
            'app_id' => '20160909004708941',
            // आवश्यक - एप्लीकेशन निजी कुंजी स्ट्रिंग या पथ
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // आवश्यक - एप्लीकेशन सार्वजनिक कुंजी प्रमाणपत्र पथ
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // आवश्यक - अलीपे सार्वजनिक कुंजी प्रमाणपत्र पथ
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // आवश्यक - अलीपे रूट प्रमाणपत्र पथ
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // वैकल्पिक - समक्ष कॉलबैक पता
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // वैकल्पिक - असमक्ष कॉलबैक पता
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // वैकल्पिक - सेवा प्रदाता मोडल में सेवा प्रदाता आईडी, जब मोड  Pay::MODE_SERVICE  होता है तो इस पैरामीटर का उपयोग करें
            'service_provider_id' => '',
            // वैकल्पिक - डिफ़ॉल्ट रूप में होता है। उपलब्ध है: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // आवश्यक - व्यापार मान है, सेवा प्रदाता मोड में सेवा प्रदाता व्यापार मान
            'mch_id' => '',
            // आवश्यक - व्यापार गुप्त कुंजी
            'mch_secret_key' => '',
            // आवश्यक - व्यापार निजी कुंजी स्ट्रिंग या पथ
            'mch_secret_cert' => '',
            // आवश्यक - व्यापार सार्वजनिक कुंजी प्रमाणपत्र पथ
            'mch_public_cert_path' => '',
            // आवश्यक
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // वैकल्पिक - जनसामान्य app_id
            'mp_app_id' => '2016082000291234',
            // वैकल्पिक - छोटे प्रोग्राम का एप_id
            'mini_app_id' => '',
            // वैकल्पिक- ऐप का एप_id
            'app_id' => '',
            // वैकल्पिक - संयुक्त app_id
            'combine_app_id' => '',
            // वैकल्पिक - संयुक्त व्यापार मान
            'combine_mch_id' => '',
            // वैकल्पिक - सेवा प्रदाता मोड में, उप-जनसामान्य app_id
            'sub_mp_app_id' => '',
            // वैकल्पिक - सेवा प्रदाता मोड में, उप app का एप_id
            'sub_app_id' => '',
            // वैकल्पिक - सेवा प्रदाता मोड में, उप छोटे प्रोग्राम का एप_id
            'sub_mini_app_id' => '',
            // वैकल्पिक - सेवा प्रदाता मोड में, उप व्यापार आईडी
            'sub_mch_id' => '',
            // वैकल्पिक - वेबसाइट पब्लिक सर्टिफिकेट पथ, वैकल्पिक, जोर्पल्ली php-fpm मोड में इस पैरामीटर को विनिमय करने की सराहना की जाती है
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // वैकल्पिक - डिफ़ॉल्ट रूप में होता है। उपलब्ध है: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // उत्पादन मान्यता के लिए सुझाव किए जाने वाले स्तर को सुधारने के लिए info, डेवलपमेंट वातावरण के लिए debug
        'type' => 'single', // वैकल्पिक, वैकल्पिक डेली
        'max_file' => 30, // वैकल्पिक, जब प्रकार दैनिक होता है तो मान्य होता है, डिफ़ॉल्ट रूप में 30 दिन
    ],
    'http' => [ // वैकल्पिक
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // अधिक कॉन्फ़िगरेशन आइटम [Guzzle] के लिए [Guzzle] (https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) की देखें
    ],
    '_force' => true,
];
```
> ध्यान दें: प्रमाणपत्र निर्देशिका निर्धारित नहीं करती है, ऊपर का उदाहरण फ़्रेमवर्क के `payment` निर्देशिका में रखे गए हैं

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## प्रारंभिकरण

सीधा `config` मेथड को इनिशियलाइज़ करें
```php
// कॉन्फ़िग फ़ाइल को प्राप्त करें config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> ध्यान दें: अगर यह एक अलीपे सैंडबॉक्स मोड है, तो सुनिश्चित करें कि आप परियोजना फ़ाइल में `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` सेट करते हैं, यह विकल्प डिफ़ॉल्ट रूप में सामान्य मोड के लिए होता है।
## भुगतान (वेब)

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
    // 1. कॉन्फ़िग फ़ाइल config/payment.php प्राप्त करें
    $config = Config::get('payment');

    // 2. विन्यास आरंभ करें
    Pay::config($config);

    // 3. वेब भुगतान
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // get विधि का उपयोग करें
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## असिंक्रोनस कॉलबैक

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc:  ॉपयाल असिंक्रोनस सूचना
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. कॉन्फ़िग फ़ाइल config/payment.php प्राप्त करें
    $config = Config::get('payment');

    // 2. विन्यास आरंभ करें
    Pay::config($config);

    // 3. अलीपे कॉलबैक प्रसंस्करण
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // कृपया स्वयं व्यापार की स्थिति की जांच और अन्य तार्किक निर्णय के लिए व्यापार की स्थिति की जांच करें, 
        // केवल जब ट्रांजेक्शन सूचना व्यवस्था TRADE_SUCCESS या TRADE_FINISHED हो, 
        // तब ही अलीपे उसे खरीदार के द्वारा पेमेंट सफल मानता है।
    // 1. व्यापार को यह सत्यापित करने की आवश्यकता है कि आउट ट्रेड नंबर क्या व्यापार प्रणाली में बनाया गया आदेश नंबर है;
    // 2. क्या आखिरी राशि वास्तविक राशि (अर्थात व्यापार आदेश बनाते समय की राशि) है, इसे जांचें।
    // 3. सैलर आईडी (या सैलर ईमेल) का यहाँ जांचें कि क्या आउट ट्रेड नंबर इस व्यवहार के लिए है।
    // 4. ऐप आईडी यह विवधता है कि क्या व्यापार स्वयं है।
    // 5. अन्य व्यावसायिक तार्किक स्थितियाँ
    // ===================================================================================================

    // 5. अलीपे कॉलबैक प्रसंस्करण
    return new Response(200, [], 'success');
}
```
> ध्यान दें: कृपया प्लगइन इस्तेमाल न करें `return Pay::alipay()->success();` अलीपे कॉलबैक का जवाब देने के लिए, यदि आप मध्यवर्ता का उपयोग करते हैं तो मध्यवर्ता समस्या हो सकती है। इसलिए, अलीपे का जवाब देने के लिए आपको वेबमैन के प्रतिक्रिया वर्ग `support\Response;` का उपयोग करना होगा।

## सिंक्रोनस कॉलबैक

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc:  ॉपयाल सिंक्रोनस सूचना
 * @param Request $request
 * लेखक Tinywan (शाओबो वान)
 */
public function alipayReturn(Request $request)
{
    Log::info('ॉपयाल सिंक्रोनस सूचना'.json_encode($request->get()));
    return 'success';
}
```

## पूर्ण मिसाल कोड

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## और अधिक सामग्री

आधिकारिक दस्तावेज़ https://pay.yansongda.cn/docs/v3/ पर जाएं
