# भुगतान SDK (V3)

## परियोजना पता

https://github.com/yansongda/pay

## स्थापना

```php
composer require yansongda/pay ^3.0.0
```

## उपयोग

> टिप्पणी: निम्नलिखित दस्तावेज प्रदान करने के लिए, हम इसे अभी भुगतान के रेतीर्थ के लिए एलीपै अंतरिक्ष के रूप में लेकर चल रहे हैं, इसमें कोई समस्या हो तो कृपया तुरंत प्रतिक्रिया करें!

### विन्यास फ़ाइल

मान लें कि निम्नलिखित विन्यास फ़ाइल `config/payment.php` है

```php
<?php
/**
 * @desc भुगतान कॉन्फ़िगरेशन फ़ाइल
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // आवश्यक - ऐप आईडी दिया गया लेन-देन की अनुमति
            'app_id' => '20160909004708941',
            // अनिवार्य - ऐप निजी कुंजी स्ट्रिंग या पथ
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // अनिवार्य - ऐप सार्वजनिक कुंजी प्रमाण पत्र पथ
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // आवश्यक - अलीपा सार्वजनिक कुंजी प्रमाण पत्र पथ
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // अनिवार्य - अलीपा रूट सर्ट पथ
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // वैकल्पिक - सिंक्रोनाइज़ेशन कॉलबैक पता
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // वैकल्पिक - असिंक्रोनाउस कॉलबैक पता
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // वैकल्पिक - सर्विस प्रोवाइडर मोड के साथ सेवा प्रदाता का आईडी, जब मोड सेवा  Pay::MODE_SERVICE  के रूप में इस्तेमाल किया जाता है
            'service_provider_id' => '',
            // वैकल्पिक - डिफ़ॉल्ट रूप में नॉर्मल मोड के लिए। विकल्प के रूप में: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // आवश्यक - व्यापारी संख्या, सेवा प्रदाता मोड में सेवा प्रदाता संख्या
            'mch_id' => '',
            // आवश्यक - व्यापारी गुप्त कुंजी
            'mch_secret_key' => '',
            // आवश्यक - व्यापारी निजी कुंजी स्ट्रिंग या पथ
            'mch_secret_cert' => '',
            // आवश्यक - व्यापारी सार्वजनिक कुंजी प्रमाण पत्र पथ
            'mch_public_cert_path' => '',
            // आवश्यक
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // वैकल्पिक - जनम सार्वजनिक का एप्लिकेशन आईडी
            'mp_app_id' => '2016082000291234',
            // वैकल्पिक - मिनी अनुप्रयोग का ऐप आईडी
            'mini_app_id' => '',
            // वैकल्पिक-ऐप्लिकेशन का ऐप आईडी
            'app_id' => '',
            // वैकल्पिक - संयुक्त ऐप्लिकेशन आईडी
            'combine_app_id' => '',
            // वैकल्पिक - संयुक्त व्यापारी संख्या
            'combine_mch_id' => '',
            // वैकल्पिक - सर्विस प्रोवाइडर मोड में, उप जनम पब्लिक का एप्लिकेशन आईडी
            'sub_mp_app_id' => '',
            // वैकल्पिक - सर्विस प्रोवाइडर मोड में, उप ऐप का ऐप आईडी
            'sub_app_id' => '',
            // वैकल्पिक - सर्विस प्रोवाइडर मोड में, अप छोटा-बड़ा अनुप्रयोग का ऐप आईडी
            'sub_mini_app_id' => '',
            // वैकल्पिक - सर्विस प्रोवाइडर मोड में, उप व्यापारी आईडी
            'sub_mch_id' => '',
            // वैकल्पिक - व्यापारी सार्वजनिक कुंजी प्रमाण पत्र पथ,  वैकल्पिक, मजबूत प्रदान करता है php-fpm के मोड में इस पैरामीटर को कॉन्फ़िगर करने के लिए
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // वैकल्पिक - डिफ़ॉल्ट रूप में नॉर्मल मोड के लिए। विकल्प के रूप में: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // सलाह दी जाती है कि उत्पादन परिवेश को इन्फो, विकास परिवेश को डीबग बदला जाए
        'type' => 'single', // वैकल्पिक, डेली के लिए उपलब्ध, लेने के लिए सक्रिय
        'max_file' => 30, // ऐययनल, प्रकार डेली के लिए पहले से वैलिड है, डिफ़ॉल्ट 30 दिन
    ],
    'http' => [ // प्रायोजन
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // और अधिक कॉन्फ़िगरेशन विकल्प के लिए यहाँ जांच करें [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> ध्यान दें: प्रमाण पत्र निर्दिष्ट नहीं करता है, ऊपर का उदाहरण फ़ाइल में फिर डायरेक्टरी में रखा गया है `payment`.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### आरंभिककरण

सीधे `config` मेथड को बुलाना
```php
// यहां वहाँसे कॉन्फिगरेशन फ़ाइल मिलेगी config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> ध्यान दें: यदि आप एलिपे सैंडबॉक्स मोड का उपयोग कर रहे हैं, तो सुनिश्चित करें कि सेटिंग फ़ाइल `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` चाहिए, इस विकल्प को डिफ़ॉल्ट रूप में डिफ़ॉल्ट रूप में है।

### भुगतान (वेब)

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
    // 1. कॉन्फ़िग फ़ाइल प्राप्त करें config/payment.php
    $config = Config::get('payment');

    // 2. कॉन्फ़िगरेशन आरंभ
    Pay::config($config);

    // 3. वेब भुगतान
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman भुगतान',
        '_method' => 'get' // get तरीके से परिवर्तन
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### कॉलबैक

#### असिंक्रोन कॉलबैक

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: अलीपै असिंक्रोन अधिसूचना
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. कॉन्फ़िग फ़ाइल प्राप्त करें config/payment.php
    $config = Config::get('payment');

    // 2. कॉन्फ़िगरेशन आरंभ
    Pay::config($config);

    // 3. अलीपै कॉलबैक प्रसंस्करण
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // trade_status के लिए एक निर्णय और अन्य लॉजिक जैसे कि केवल व्यापार सूचना स्थिति TRADE_SUCCESS या TRADE_FINISHED
    // केवल जब व्यापार सूचना स्थिति TRADE_SUCCESS या TRADE_FINISHED होती है, तो अलीपै ही मानता है कि खरीदार द्वारा भुगतान सफल हुआ है।
    // 1. व्यापार अधिसूचना डेटा में व्यापार का out_trade_no मार्चेंट सिस्टम में बनाया गया आर्डर नंबर है कि मार्चेंट को लेनदेन के लिए वैध है;
    // 2. total_amount के बारे में जांचें कि क्या इस आर्डर का मूल्य (यानी मार्चेंट आर्डर बनाते समय राशि) है;
    // 3. क्या सेलर_आईडी (या सेलर_ईमेल) सत्यापित करें कि out_trade_no इस बिल का संबंधित क्रियाकलापकर्ता है;
    // 4. app_id का सत्यापन करें कि वह मार्चेंट खुद है।
    // 5. अन्य व्यापार लॉजिक
    // ===================================================================================================

    // 5. अलीपै कॉलबैक प्रसंस्करण
    return new Response(200, [], 'सफलता');
}
```
> ध्यान दें: अलीपै कॉलबैक क
