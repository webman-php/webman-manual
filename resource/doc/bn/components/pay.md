# পেমেন্ট SDK


### প্রকল্প ঠিকানা

https://github.com/yansongda/pay

### ইনস্টলেশন

```php
composer require yansongda/pay -vvv
```

### ব্যবহার 

**আলিপে**
 
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
        // এনক্রিপশন মেথড: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // পাবলিক কীঃ পয়েন্টটি পারাঃ রিটার্নস উআরএল,
        'log' => [ // ঐচ্ছিক
            'file' => './logs/alipay.log',
            'level' => 'info', // প্রস্তাবিত প্রোডাকশন পর্যায়ে পর্যবেক্ষণ রেজিস্টার করা হতে পারে info, ডেভেলপমেন্ট পর্যায়ে ডেভেলপ হতে পারে debug
            'type' => 'single', // ঐচ্ছিক, একক রোজার জন্য একক, দৈনিক হলে প্রযোজ্য।
            'max_file' => 30, // ঐচ্ছিক, প্রকারভের জন্য প্রযোজ্য হলে সুযোগ প্রকাশনার মান 30 দিনে।
        ],
        'http' => [ // এচটিটিপিঃ ঐচ্ছিক
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // অন্যান্য কনফিগারেশন বিষয়গুলির জন্য অনুরোধ [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // ঐচ্ছিক, এই পরামতা সেট করুন, এটি স্যান্ডবক্স মোডে প্রবেশ করবে
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - পরীক্ষা',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// লারাভেল ফ্রেমওয়ার্কে সরাসরি `ফিরি $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // হা, এই টি যাচাই করুন!
        
        // আর্ডার আইডিঃ $data->out_trade_no
        // আলিপে লেনদেন আইডিঃ $data->trade_no
        // অর্ডার মোট পরিমাণঃ $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // হা, এই টি যাচাই করুন!
        
            // দয়া করে trade_status এর জন্য স্বনিকারণ এবং অন্যান্য লজিক চেক করুন, আলিপের ব্যবসায়িক বিজ্ঞপ্তিতে, কেবলমাত্র লেনদেন বিজ্ঞপ্তির অবস্থাটি ট্রেড_সাকসেস বা ট্রেড_ফিনিশ হলে, আলিপে মনে করে মোনস্তা পরিশোধ সফল হয়েছে।
            // 1. কোম্পানি অপ্সন_ট্রেড_নোট টি পুষ্ট করুন যে আউট_ট্রেড_নো আপনার ব্যবস্থা তে তৈরী হয়েছে;
            // 2. মাল্টাল অ্যা‌মাউন্ট আপনার অর্থের প্রকৃত পরিমাণ (অনুরূপ এমবিবিডি নির্দিষ্ট করা) হল (অর্থাৎ প্রাক্তন অর্ডার তৈরি করার সময়ের পরিমাণ);
            // 3. ট্রাড থেকেটর ট্রেড নোট এই নকবাইয়ার দ্বারা পরিচালিতা বহন করে (কিছুবার, একটি নেগডড গ্রাহক এর জন্য একটি কোড একাধিক রাইাতার আইডি/রাইাতার ইমেল থাকতে পারে);
            // 4. অ্যাপিডি আপনার নিজস্ব ছাড়াও এই কম্পানি;
            // 5. অন্যান্য ব্যবসায়িক বিজ্ঞানী পরিস্থিতি

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// লারাভেল ফ্রেমওয়ার্কে সরাসরি `ফিরি $alipay->success()`
    }
}
```

**ওয়েচ্যাট**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // অ্যাপ অ্যাপলিকেশন আইডিঃ
        'app_id' => 'wxb3fxxxxxxxxxxx', // জনগণের অ্যাপ আইডিঃ
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // ছোট অ্যাপ্লিকেশন অ্যাপ আইডিঃ
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // ঐচ্ছিক, প্রত্যাহার ইত্যাদিতে ব্যবহার করা হয়
        'cert_key' => './cert/apiclient_key.pem',// ঐচ্ছিক, প্রত্যাহার ইত্যাদিতে ব্যবহার করা হয়
        'log' => [ // ঐচ্ছিক
            'file' => './logs/wechat.log',
            'level' => 'info', // প্রস্তাবিত প্রোডাকশন পর্যায়ে পর্যবেক্ষণ রেজিস্টার করা হতে পারে info, ডেভেলপমেন্ট পর্যায়ে ডেভেলপ হতে পারে debug
            'type' => 'single', // ঐচ্ছিক, একক রোজার জন্য একক, দৈনিক হলে প্রযোজ্য।
            'max_file' => 30, // ঐচ্ছিক, প্রকারভের জন্য প্রযোজ্য হলে স
