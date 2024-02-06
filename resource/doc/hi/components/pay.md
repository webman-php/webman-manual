# भुगतान SDK


### परियोजना पता

https://github.com/yansongda/pay

### स्थापना

```php
कम्पोजर कमांडर require yansongda/pay -vvv
```

### उपयोग

**आलीपे**

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
        // एन्क्रिप्शन मोड:RSA2
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // RSA2** जैसी एन्क्रिप्टिंग प्रकार
        'log' => [ // वैकल्पिक
            'file' => './logs/alipay.log',
            'level' => 'info', // उत्पादन परिवेश के लिए सुझाव दिया जाता है, संविकास परिवेश के लिए debug
            'type' => 'single', // वैकल्पिक, वैकल्पिक, रोजाना के लिए
            'max_file' => 30, // वैकल्पिक, प्रकार 30 दिन की स्वीकृति के लिए प्रभावी
        ],
        'http' => [ // वैकल्पिक
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // और अधिक कॉन्फ़िगरेशन आइटम के लिए कृपया देखें [गुज्जल](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // वैकल्पिक, इस पैरामीटर को सेट करना, इसे सैंडबॉक्स मोड में प्रवेश कर देगा
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - परीक्षण',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// लारावेल फ्रेमवर्क में कृपया सीधे `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // हां, सत्यापन इतना ही सरल है!

        // आर्डर नंबर: $data->out_trade_no
        // आलीपे व्यापार नंबर: $data->trade_no
        // आर्डर की कुल मात्रा: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try{
            $data = $alipay->verify(); // हां, सत्यापन इतना ही सरल है!

            // स्वयं ट्रेड स्थिति की जांच करें और अन्य और कार्यप्रवाह के लिए निर्णय करें, आलीपे के व्यापार सूचना में, TRADE_SUCCESS या TRADE_FINISHED होते ही, आलीपे केवल उसे ग्राहक का भुगतान सफल मानता है।
            // 1, व्ाणिज्यिक को याचित करें कि सूचना डेटा में out_trade_no की आउट ट्रेड नंबर व्यापार तंत्र में बनाई गई आर्डर नंबर है;
उसके बाद, जनर की मान्यता करना च (@तिनिव उत्पन्न जिस समय पे्र निर्णय) होंै;ुं मात्रा का जन्यन में उकिरत है;ữी सूचना;

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// लाराेवेल फ्रेमवर्क में कृपया सीधे `return $alipay->success()`
    }
}
```

**वेरीक्स**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // ऐप ऐपआईडी
        'app_id' => 'wxb3fxxxxxxxxxxx', // सार्वजनिक आईडी
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // छोटे अनुप्रयोग अनुप्रयोग आईडी
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // optional, वापसी आदि स्थिति में उपयोग करते समय
        'cert_key' => './cert/apiclient_key.pem',// optional, वापसी आदि स्थिति में उपयोग करते समय
        'log' => [ // वैकल्पिक
            'file' => './logs/wechat.log',
            'level' => 'info', // उत्पादन परिवेश के लिए सुझाव दिया जाता है, संविकास परिवेश के लिए debug
            'type' => 'single', // वैकल्पिक, रोजाना-के लिए
            'max_file' => 30, // वैकल्पिक, प्रकार 30 दिन की स्वीकृति के लिए प्रभावी
        ],
        'http' => [ // वैकल्पिक
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // और अधिक कॉन्फ़िगरेशन आइटम के लिए कृपया देखें [गुज्जल](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // वैकल्पिक, डेव/हांगकांग; `हांगकांग` होने पर, हांगकांग गेटवे के लिए।
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **इकाई:फीस**
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
            $data = $pay->verify(); // हां, सत्यापन इतना ही सरल है!

            Log::debug('Wechat notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $pay->success()->send();// लारावेल फ्रेमवर्क में कृपया सीधे `returns $pay->success()`
    }
}
```

### अधिक सामग्री

https://pay.yanda.net.cn/docs/2.x/overview
