# Ödeme SDK


### Proje Adresi

 https://github.com/yansongda/pay

### Kurulum

```php
composer require yansongda/pay -vvv
```

### Kullanım

**Alipay**

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
        // Şifreleme yöntemi: **RSA2**
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Genel anahtar sertifikası kullanımı için, aşağıdaki iki parametreyi yapılandırın ve ali_public_key'i (.crt uzantılı) alipay genel anahtar sertifikası yoluna, örneğin (./cert/alipayCertPublicKey_RSA2.crt):
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', //Uygulama genel anahtar sertifikası yolu
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', //Alipay kök sertifikası yolu
        'log' => [ // isteğe bağlı
            'file' => './logs/alipay.log',
            'level' => 'info', // Üretim ortamında düzeyin info olarak ayarlanması tavsiye edilir, geliştirme ortamında ise debug olarak
            'type' => 'single', // isteğe bağlı, günlük.
            'max_file' => 30, // isteğe bağlı, türünün günlük olması durumunda geçerli, varsayılan olarak 30 gün
        ],
        'http' => [ // isteğe bağlı
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Daha fazla yapılandırma seçeneği için [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) sayfasına bakın
        ],
        'mode' => 'dev', // isteğe bağlı, bu parametre ayarlandığında, sandbox moduna girecektir
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 测试',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // laravel çerçevesinde doğrudan `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Evet, imzalama işlemi bu kadar basit!

        // Sipariş numarası: $data->out_trade_no
        // Alipay işlem numarası: $data->trade_no
        // Toplam tutar: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // Evet, imzalama işlemi bu kadar basit!

            // trade_status'ü kendiniz kontrol edin ve diğer mantıksal durumları kontrol edin. Alipay işlem bildiriminde, yalnızca işlem bildirim durumu TRADE_SUCCESS veya TRADE_FINISHED olduğunda, Alipay'in alıcı ödemesini başarılı kabul edeceğini unutmayın.
            // 1. Satıcının bu bildirim verilerindeki out_trade_no'nun satıcı sisteminde oluşturulan sipariş numarası olup olmadığını doğrulaması gereklidir;
            // 2. total_amount tutarının gerçekten bu siparişin gerçek tutarı olup olmadığı kontrol edilmelidir (yani, satıcı sipariş oluşturduğunda miktarı);
            // 3. Bildirimdeki seller_id (veya seller_email) out_trade_no bu işlemin karşılık gelen işlem tarafı olup olmadığını kontrol eder (bazen bir satıcının birden fazla seller_id/seller_email'i olabilir);
            // 4. app_id'nin kendi satıcı kimliği olup olmadığını doğrulayın.
            // 5. Diğer iş mantığı durumları

            Log::debug('Alipay bildirimi', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // laravel çerçevesinde doğrudan `return $alipay->success()`
    }
}
```

**Wechat**

```php
<?php

namespace App\Http\Controllers;

use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class PayController
{
    protected $config = [
        'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
        'app_id' => 'wxb3fxxxxxxxxxxx', // Halka açık APPID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // Mini uygulama APPID
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // isteğe bağlı, iade vb. durumlarda kullanılır
        'cert_key' => './cert/apiclient_key.pem', // isteğe bağlı, iade vb. durumlarda kullanılır
        'log' => [ // isteğe bağlı
            'file' => './logs/wechat.log',
            'level' => 'info', // Üretim ortamında düzeyin info olarak ayarlanması tavsiye edilir, geliştirme ortamında ise debug olarak
            'type' => 'single', // isteğe bağlı, günlük.
            'max_file' => 30, // isteğe bağlı, türünün günlük olması durumunda 30, varsayılan olarak 30 gün
        ],
        'http' => [ // isteğe bağlı
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Daha fazla yapılandırma seçeneği için [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) sayfasına bakın
        ],
        'mode' => 'dev', // isteğe bağlı, dev/hk; `hk` olarak ayarlandığında, Hong Kong ağ geçididir.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **Birim: fen**
            'body' => 'test body - 测试',
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
            $data = $pay->verify(); // Evet, imzalama işlemi bu kadar basit!

            Log::debug('Wechat bildirimi', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send(); // laravel çerçevesinde doğrudan `return $pay->success()`
    }
}
```

### Daha Fazla İçerik
 https://pay.yanda.net.cn/docs/2.x/overview adresini ziyaret edin
