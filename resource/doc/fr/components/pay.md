# SDK de paiement

### Projet d'adresse

https://github.com/yansongda/pay

### Installation

```php
composer require yansongda/pay -vvv
```

### Utilisation

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
        // Mode de cryptage : **RSA2**
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Mode de cryptage : **RSA2**
        'log' => [ // facultatif
            'file' => './logs/alipay.log',
            'level' => 'info', // Il est recommandé de définir le niveau comme info pour l'environnement de production et debug pour l'environnement de développement
            'type' => 'single', // facultatif, choix quotidien.
            'max_file' => 30, // facultatif, valable uniquement lorsque le type est quotidien, par défaut 30 jours
        ],
        'http' => [ // facultatif
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Pour plus de paramètres de configuration, veuillez consulter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // facultatif, définissez ce paramètre pour activer le mode bac à sable
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 测试',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // Dans le framework Laravel, veuillez simplement retourner `$alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Oui, la vérification est aussi simple que cela !

        // Numéro de commande : $data->out_trade_no
        // Numéro de transaction Alipay : $data->trade_no
        // Montant total de la commande : $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // Oui, la vérification est aussi simple que cela !

            // Veuillez procéder à votre propre vérification de trade_status et d'autres logiques de vérification. Dans la notification commerciale d'Alipay, Alipay ne considérera la réussite du paiement par l'acheteur que lorsque l'état de notification commerciale est TRADE_SUCCESS ou TRADE_FINISHED.
            // 1. Le commerçant doit vérifier si out_trade_no dans les données de notification est le numéro de commande créé dans le système du commerçant.
            // 2. Vérifiez si total_amount est effectivement le montant réel de cette commande (c'est-à-dire le montant au moment de la création de la commande par le commerçant).
            // 3. Vérifier que seller_id (ou seller_email) dans la notification correspond à l'entité opérationnelle correspondante de ce billet à out_trade_no (parfois un commerçant peut avoir plusieurs seller_id/seller_email) ;
            // 4. Vérifiez si app_id est le commerçant lui-même.
            // 5. Autres situations logiques commerciales

            Log::debug('Notification Alipay', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // Dans le framework Laravel, veuillez simplement retourner `$alipay->success()`
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
        'app_id' => 'wxb3fxxxxxxxxxxx', // APPID de l'application publique
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // APPID de l'application mini-programme
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // facultatif, utilisé pour les remboursements, etc.
        'cert_key' => './cert/apiclient_key.pem',// facultatif, utilisé pour les remboursements, etc.
        'log' => [ // facultatif
            'file' => './logs/wechat.log',
            'level' => 'info', // Il est recommandé de définir le niveau comme info pour l'environnement de production et debug pour l'environnement de développement
            'type' => 'single', // facultatif, choix quotidien.
            'max_file' => 30, // facultatif, valable uniquement lorsque le type est quotidien, par défaut 30 jours
        ],
        'http' => [ // facultatif
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Pour plus de paramètres de configuration, veuillez consulter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // facultatif, dev/hk ; lorsque défini sur `hk`, il s'agit d'une passerelle de Hong Kong.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **Unité: centimes**
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
            $data = $pay->verify(); // Oui, la vérification est aussi simple que cela !

            Log::debug('Notification Wechat', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send(); // Dans le framework Laravel, veuillez simplement retourner `$pay->success()`
    }
}
```

### Pour en savoir plus

Visitez https://pay.yanda.net.cn/docs/2.x/overview
