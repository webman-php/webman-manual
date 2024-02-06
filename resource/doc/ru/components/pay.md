# SDK для оплаты


### Адрес проекта

 https://github.com/yansongda/pay

### Установка

```php
composer require yansongda/pay -vvv
```

###Использование

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
        // Метод шифрования: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Если используется режим сертификата открытого ключа, укажите следующие параметры, и измените ali_public_key на путь к открытому ключу сертификата Alipay с расширением .crt, например, (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // путь к открытому ключу сертификата приложения
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // путь к корневому сертификату Alipay
        'log' => [ // вариантный
            'file' => './logs/alipay.log',
            'level' => 'info', // рекомендуется уровень info для окружения, debug для разработки
            'type' => 'single', // вариантный, может быть ежедневный
            'max_file' => 30, // вариантный, действителен только при ежедневном типе, по умолчанию 30 дней
        ],
        'http' => [ // вариантный
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Для дополнительных параметров конфигурации см. [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // вариантный, установка этого параметра переведет в режим песочницы
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - тест',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// в фреймворке laravel просто `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Да, проверка подписи так просто!

        // Номер заказа: $data->out_trade_no
        // Номер транзакции Alipay: $data->trade_no
        // Общая сумма заказа: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // Да, проверка подписи так просто!

            // Проверьте `trade_status` и другую логику в уведомлении об уплате; в уведомлении об уплате Alipay, только при состоянии уведомления о совершенной покупке как TRADE_SUCCESS или TRADE_FINISHED, Alipay считает оплату покупателем успешной.
            // 1. Торговец должен проверить, является ли `out_trade_no` идентификатор заказа, созданный в системе торговца;
            // 2. Проверьте, действительно ли `total_amount` соответствует фактической сумме заказа (т.е. сумме заказа торговца);
            // 3. Проверьте, является ли `seller_id` (или `seller_email`) информацией по транзакции для данной сделки соответственно (иногда у торговца может быть несколько `seller_id/seller_email`);
            // 4. Проверьте, является ли `app_id` этого магазина.
            // 5. Другая логика
            Log::debug('Уведомление Alipay', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// в фреймворке laravel просто `return $alipay->success()`
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
        'app_id' => 'wxb3fxxxxxxxxxxx', // APPID от общественного аккаунта
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // APPID мини-приложения
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // необязательный параметр, используется при возврате средств и др.
        'cert_key' => './cert/apiclient_key.pem',// необязательный параметр, используется при возврате средств и др.
        'log' => [ // необязательный
            'file' => './logs/wechat.log',
            'level' => 'info', // рекомендуется уровень info для окружения, debug для разработки
            'type' => 'single', // необязательный, может быть ежедневный
            'max_file' => 30, // необязательный, действителен только при ежедневном типе, по умолчанию 30 дней
        ],
        'http' => [ // необязательный
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Для дополнительных параметров конфигурации см. [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // необязательный, dev/hk; при установке на `hk` включается шлюз Hong Kong.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **единица измерения: фен
            'body' => 'test body - тест',
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
            $data = $pay->verify(); // Да, проверка подписи так просто!

            Log::debug('Уведомление Wechat', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// в фреймворке laravel просто `return $pay->success()`
    }
}
```

### Больше контента

Посетите https://pay.yanda.net.cn/docs/2.x/overview
