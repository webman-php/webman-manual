Vielen Dank für die Bereitstellung der Webman-Dokumentation. Ich werde jetzt mit der Übersetzung beginnen und Sie mit dem Übersetzungsfortschritt auf dem Laufenden halten.
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
        // Verschlüsselungsmethode: **RSA2**
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Wenn der Public-Key-Zertifikatsmodus verwendet wird, bitte die folgenden beiden Parameter konfigurieren und das alipay_public_key in den Pfad des Alipay-Public-Key-Zertifikats mit der Endung .crt ändern, zum Beispiel (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // Pfad zum Anwendungs-Public-Key-Zertifikat
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // Pfad zum Alipay-Root-Zertifikat
        'log' => [ // optional
            'file' => './logs/alipay.log',
            'level' => 'info', // Es wird empfohlen, den Grad in der Produktionsumgebung auf info zu setzen und in der Entwicklungsphase auf debug
            'type' => 'single', // optional, täglich wählbar.
            'max_file' => 30, // optional, gültig bei Typ täglich, standardmäßig 30 Tage
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Weitere Konfigurationsoptionen finden Sie unter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional, legen Sie diesen Parameter fest, um in den Sandbox-Modus zu gelangen
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'Testsubjekt - 测试',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// im Laravel-Framework bitte direkt `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Ja, die Überprüfung ist so einfach!

        // Bestellnummer: $data->out_trade_no
        // Alipay-Transaktionsnummer: $data->trade_no
        // Gesamtmenge der Bestellung: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try{
            $data = $alipay->verify(); // Ja, die Überprüfung ist so einfach!

            // Bitte selbst die trade_status überprüfen und andere Logik überprüfen. Im Alipay-Geschäftsmeldung wird nur dann, wenn der Geschäftsbenachrichitgungsstatus TRA... 
TRANS_SUCCESS oder TRADE_FINISHED ist, von Alipay angenommen, dass der Käufer die Zahlung erfolgreich geleistet hat.
            // 1. Der Händler muss prüfen, ob die out_trade_no in den Benachrichtigungsdaten die Bestellnummer ist, die im Händlersystem erstellt wurde;
            // 2. Überprüfen, ob total_amount wirklich der tatsächliche Betrag dieser Bestellung ist (d. h. der Betrag, der bei der Erstellung der Händlerbestellung angegeben wurde);
            // 3. Überprüfen, ob seller_id (oder seller_email) in der Benachrichtigung mit out_trade_no der entsprechenden betrieblichen Partei dieses Buchungsbelegs entspricht (manchmal hat ein Händler möglicherweise mehrere seller_id/seller_email);
            // 4. Überprüfen, ob die app_id mit dem eigenen Händler identisch ist.
            // 5. Weitere Geschäftslogik
            Log::debug('Alipay-Benachrichtigung', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// im Laravel-Framework bitte direkt `return $alipay->success()`
    }
}
```

**WeChat**

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
        'cert_client' => './cert/apiclient_cert.pem', // optional, wird im Falle von Rückerstattungen u.ä. benötigt
        'cert_key' => './cert/apiclient_key.pem',// optional, wird im Falle von Rückerstattungen u.ä. benötigt
        'log' => [ // optional
            'file' => './logs/wechat.log',
            'level' => 'info', // Es wird empfohlen, den Grad in der Produktionsumgebung auf info zu setzen und in der Entwicklungsphase auf debug
            'type' => 'single', // optional, täglich wählbar.
            'max_file' => 30, // optional, gültig bei Typ täglich, standardmäßig 30 Tage
        ],
        'http' => [ // optional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Weitere Konfigurationsoptionen finden Sie unter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // optional, dev/hk; bei `hk` ist es das Gateway in Hongkong.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **Einheit: Cent**
            'body' => 'Testkörper - 测试',
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
            $data = $pay->verify(); // Ja, die Überprüfung ist so einfach!

            Log::debug('WeChat-Benachrichtigung', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// im Laravel-Framework bitte direkt `return $pay->success()`
    }
}
```


### Mehr Inhalte

Besuchen Sie https://pay.yanda.net.cn/docs/2.x/overview
