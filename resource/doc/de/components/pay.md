# Zahlungs-SDK

### Projektadresse

https://github.com/yansongda/pay

### Installation

```php
composer require yansongda/pay -vvv
```

### Verwendung

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
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        'log' => [ // Optional
            'file' => './logs/alipay.log',
            'level' => 'info', // Es wird empfohlen, den Level für die Produktionsumgebung auf info und für die Entwicklungsumgebung auf debug einzustellen
            'type' => 'single', // Optional, täglich wählbar.
            'max_file' => 30, // Optional, gültig bei type täglich, standardmäßig 30 Tage
        ],
        'http' => [ // Optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Weitere Konfigurationen finden Sie unter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // Optional, mit diesem Parameter wird der Sandbox-Modus aktiviert
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'Testthema - test',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // Im Laravel-Framework bitte einfach `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Ja, so einfach ist die Verifizierung!

        // Bestellnummer: $data->out_trade_no
        // Alipay-Transaktionsnummer: $data->trade_no
        // Gesamtbetrag der Bestellung: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try {
            $data = $alipay->verify(); // Ja, so einfach ist die Verifizierung!

            // Bitte überprüfen Sie den trade_status und führen Sie logische Überprüfungen in Alipay-Benachrichtigungen durch. Alipay erkennt die Zahlung als erfolgreich, nur wenn der Transaktionsbenachrichtigungsstatus TRADE_SUCCESS oder TRADE_FINISHED ist.
            // 1. Das Geschäft muss prüfen, ob die out_trade_no in den Benachrichtigungsdaten die Bestellnummer ist, die im Geschäftssystem erstellt wurde.
            // 2. Überprüfen Sie, ob total_amount tatsächlich der tatsächliche Betrag der Bestellung ist (dh der Betrag, der bei der Erstellung der Geschäftsbestellung festgelegt wurde)
            // 3. Überprüfen Sie, ob seller_id (oder seller_email) in der Benachrichtigung mit out_trade_no von diesem Satz von Dokumenten übereinstimmt (manchmal hat ein Händler möglicherweise mehrere seller_id/seller_email)
            // 4. Bestätigen Sie, dass app_id das Geschäft selbst ist.
            // 5. Andere Geschäftslogiksituationen

            Log::debug('Alipay-Benachrichtigung', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // Im Laravel-Framework bitte einfach `return $alipay->success()`
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
        'app_id' => 'wxb3fxxxxxxxxxxx', // Öffentliche APP-ID
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // Mini-Programm APP-ID
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // Optional, wird bei Rückerstattungen usw. verwendet
        'cert_key' => './cert/apiclient_key.pem', // Optional, wird bei Rückerstattungen usw. verwendet
        'log' => [ // Optional
            'file' => './logs/wechat.log',
            'level' => 'info', // Es wird empfohlen, den Level für die Produktionsumgebung auf info und für die Entwicklungsumgebung auf debug einzustellen
            'type' => 'single', // Optional, täglich wählbar.
            'max_file' => 30, // Optional, gültig bei type täglich, standardmäßig 30 Tage
        ],
        'http' => [ // Optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Weitere Konfigurationen finden Sie unter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // Optional, dev/hk;Wenn "hk" gesetzt ist, gilt es als Gateway in Hongkong.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **Einheit: Cent**
            'body' => 'Testkörper - test',
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

        try {
            $data = $pay->verify(); // Ja, so einfach ist die Verifizierung!

            Log::debug('WeChat-Benachrichtigung', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $pay->success()->send(); // Im Laravel-Framework bitte einfach `return $pay->success()`
    }
}
```

### Weitere Inhalte

Besuchen Sie https://pay.yanda.net.cn/docs/2.x/overview
