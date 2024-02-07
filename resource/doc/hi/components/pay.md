# भुगतान SDK


### प्रोजेक्ट पता

https://github.com/yansongda/pay

### स्थापना

```php
composer require yansongda/pay -vvv
```

### उपयोग

**अलीपे**

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
        // एन्क्रिप्शन मोड: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // जानकारी के संबंध में: **RSA2**  
        'log' => [ // वैकल्पिक
            'file' => './logs/alipay.log',
            'level' => 'info', // उत्पादन मानक के लिए सलाह दी जाती है, डेवलपमेंट स्तर के लिए डीबग
            'type' => 'single', // वैकल्पिक, वैकल्पिक रूप दैनिक।
            'max_file' => 30, // वैकल्पिक, प्रकार 30 दिन
        ],
        'http' => [ // वैकल्पिक
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // और अधिक विन्यास के विकल्प [गुज़ल](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) को देखें
        ],
        'mode' => 'dev', // वैकल्पिक, इस पैरामीटर को सेट करें, तो यह सेटिंग सैंडबॉक्स मोड में जायेगा
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - परीक्षण',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// लारावेल फ्रेमवर्क में कृपया सीधे `return $alipay` करें
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // हाँ, साइन इस तरह से आसान है!

        // आदेश संख्या: $data->out_trade_no
        // आलीपे लेनदेन संख्या: $data->trade_no
        // आदेश की कुल राशि: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // हाँ, साइन इस तरह से आसान है!

            // कृपया खुद व्यापार स्थिति की जांच और अन्य तर्कों की जाँच के लिए व्यापार तर्क का मूल्यांकन करें, अलीपे के व्यापार सूचना में, केवल जब व्यापार सूचना स्थिति TRADE_SUCCESS या TRADE_FINISHED होती है, तो अलीपे खरीदार की भुगतान सफल मानता है।
            // 1. व्यापार डेटा में out_trade_no के लिए व्यापार प्रणाली में बनाया गया क्या है, वहीं करें।
            // 2. total_amount के बारे में तय करें कि क्या यह, व्यापार के बनाए गए डेटा की वास्तविक माना है (यानी व्यापार आदेश के समय मान)।
            // 3. विकल्पी रूप से, यह भी सत्यापित करें कि सूचना में seller_id (या seller_email) out_trade_no का कुछ ऑप्रेशन करने वाले के संबंध में है (कभी-कभी, एक व्यापार के कई seller_id / seller_email हो सकते हैं)।
            // 4. विलय पहचान करें कि यह क्या अप्लिकेशन है वास्तव में इस वींडो का।
            // 5. अन्य व्यापार तर्क की अवस्था

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// लारावेल फ्रेमवर्क में कृपया सीधे `return $alipay->success()` करें
    }
}
```

**व्यापा**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // ऐप ऐपिडी
        'app_id' => 'wxb3fxxxxxxxxxxx', // सार्वजनिक संख्या ऐप आईडी
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // छोटे अनुप्रयोग ऐप आईडी
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // वैकल्पिक, वापसी आदि समय में उपयोग किया जाता है
        'cert_key' => './cert/apiclient_key.pem',// वैकल्पिक, वापसी आदि समय में उपयोग किया जाता है
        'log' => [ // वैकल्पिक
            'file' => './logs/wechat.log',
            'level' => 'info', // उत्पादन मानक के लिए सलाह दी जाती है, डेवलपमेंट स्तर के लिए डीबग
            'type' => 'single', // वैकल्पिक, वैकल्पिक रूप दैनिक।
            'max_file' => 30, // वैकल्पिक, प्रकार 30 दिन
        ],
        'http' => [ // वैकल्पिक
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // और अधिक विन्यास के अनुसार [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) को देखें
        ],
        'mode' => 'dev', // वैकल्पिक, dev/hk; `hk` के रूप में, हॉंगकॉन्ग गेटवे के लिए।
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **इकाई: सेण्ट**
            'body' => 'test body - परीक्षण',
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
            $data = $pay->verify(); // हाँ, साइन इस तरह से आसान है!

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// लारावेल फ्रेमवर्क में कृपया सीधे `return $pay->success()` करें
    }
}
```

### अधिक सामग्री

जाएं https://pay.yanda.net.cn/docs/2.x/overview
