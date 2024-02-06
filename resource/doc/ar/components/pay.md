# دليل تطوير خدمة الدفع

### عنوان المشروع

https://github.com/yansongda/pay

### التثبيت

```php
composer require yansongda/pay -vvv
```

###الاستخدام 

**الدفع عبر الأليبي**

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
        // طريقة التشفير:  **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // في حالة استخدام وضع شهادة المفتاح العام ، الرجاء تكوين المعلمتين التاليتين وتغيير ali_public_key إلى مسار شهادة مفتاح الأمان للأليباي الذي ينتهي بـ (.crt) ، مثال: (./cert/alipayCertPublicKey_RSA2.crt) 
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //مسار شهادة مفتاح التطبيق العام
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', //مسار شهادة جذر الأليباي
        'log' => [ // اختياري
            'file' => './logs/alipay.log',
            'level' => 'info', // يُوصى بضبط مستوى الإنتاج إلى info ، وبينما يكون مستوى التطوير debug
            'type' => 'single', // اختياري، يومي.
            'max_file' => 30, //اختياري، عندما يكون النوع يومي ساري المفعول ،الافتراضي 30 يوم
        ],
        'http' => [ // اختياري
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // لمزيد من خيارات التكوين يرجى الرجوع إلى [غزل](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // اختياري, عند تعيين هذه المعلمة، سندخل وضع الرمال
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - اختبار',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// في إطار لارافيل يرجى "return $alipay" مباشرةً
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // نعم، التحقق من التوقيع هكذا بسيط!

        // رقم الطلب: $data->out_trade_no
        // رقم المعاملة الأليباي: $data->trade_no
        // إجمالي المبلغ: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // نعم، التحقق من التوقيع هكذا بسيط!

            // يرجى مراجعة trade_status وتنفيذ أي منطق أخرى. في إشعار الأعمال لدى الأليباي ، يتم اعتبار دفع المشتري ناجحًا فقط عندما يكون حالة الإخطار التجاري TRADE_SUCCESS أو TRADE_FINISHED. 
            // 1. يجب على التاجر التحقق مما إذا كان out_trade_no يطابق رقم الطلب المنشأ في نظام التطبيق
            // 2. تحقق مما إذا كان total_amount بالفعل المبلغ الفعلي لهذا الطلب (أي المبلغ الذي تم إنشاء الطلب به)
            // 3. التحقق من البائع المدرج في الإخطار (أو البريد الإلكتروني للبائع) إذا كانت واحدة (في بعض الأحيان قد يكون لدى تاجر واحد عدة بائعين / بريد إلكتروني للبائع)
            // 4. التحقق من أن app_id هو للتاجر نفسه.
            // 5. مراجعة أي منطق أعمال أخرى
            
            Log::debug('إشعار الأليبي', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// في إطار لارافيل يرجى "return $alipay->success()" مباشرةً
    }
}
```


**الدفع عبر ويشات**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // APPID العام
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // APPID الخاص بالتطبيق الصغير
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // اختياري، يستخدم في حالات الاسترجاع
        'cert_key' => './cert/apiclient_key.pem',// اختياري، يستخدم في حالات الاسترجاع
        'log' => [ // اختياري
            'file' => './logs/wechat.log',
            'level' => 'info', // يُوصى بضبط مستوى الإنتاج إلى info ، وبينما يكون مستوى التطوير debug
            'type' => 'single', // اختياري, يومي.
            'max_file' => 30, // اختياري، عندما يكون النوع حكومي، الافتراضي 30 يوم
        ],
        'http' => [ // اختياري
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // لمزيد من خيارات التكوين يرجى الرجوع إلى [غزل](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // اختياري, dev/hk; عندما تكون `hk` يكون لديك بوابة هونغ كونغ
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **الوحدة: فلس**
            'body' => 'test body - اختبار',
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
            $data = $pay->verify(); // نعم، التحقق من التوقيع هكذا بسيط!

            Log::debug('إشعار ويشات', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// في إطار لارافيل يرجى "return $pay->success()" مباشرةً
    }
}
```

### مزيد من المحتويات
 
أضغط هنا: https://pay.yanda.net.cn/docs/2.x/overview
