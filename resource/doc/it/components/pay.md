# SDK di pagamento

### Indirizzo del progetto
 https://github.com/yansongda/pay

### Installazione
```php
composer require yansongda/pay -vvv
```

### Utilizzo

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
        // Metodo di crittografia: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Se si utilizza il metodo del certificato chiave pubblica, si prega di configurare i seguenti due parametri e modificare ali_public_key in percorso al certificato di chiave pubblica di Alipay con estensione .crt, ad esempio (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // Percorso del certificato di chiave pubblica dell'applicazione
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // Percorso del certificato radice di Alipay
        'log' => [ // opzionale
            'file' => './logs/alipay.log',
            'level' => 'info', // Si consiglia di regolare il livello per l'ambiente di produzione a info e per lo sviluppo a debug
            'type' => 'single', // opzionale, opzioni giornaliere.
            'max_file' => 30, // opzionale, valido solo quando il tipo è giornaliero, predefinito 30 giorni
        ],
        'http' => [ // opzionale
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Per ulteriori opzioni di configurazione, fare riferimento a [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // opzionale, impostando questo parametro, si entrerà in modalità sandbox
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 测试',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // Nel framework Laravel si prega di utilizzare direttamente `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Sì, la verifica è così semplice!

        // Numero d'ordine: $data->out_trade_no
        // Numero di transazione Alipay: $data->trade_no
        // Importo totale dell'ordine: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try{
            $data = $alipay->verify(); // Sì, la verifica è così semplice!

            // Si prega di fare autonomamente la verifica dello stato del commercio e altre logiche basate sullo stato del pagamento. Nelle notifiche di business di Alipay, solo quando lo stato di notifica del commercio è TRADE_SUCCESS o TRADE_FINISHED, Alipay considererà il pagamento da parte dell'acquirente completato.
            // 1. Il commerciante deve verificare se out_trade_no nei dati di notifica è uguale al numero d'ordine creato nel sistema del commerciante;
            // 2. Verificare se total_amount è effettivamente l'importo effettivo di quell'ordine (ovvero l'importo dell'ordine del commerciante al momento della creazione);
            // 3. Verificare che seller_id (o seller_email) nei dati di notifica corrisponda al soggetto operativo associato a out_trade_no (a volte un commerciante può avere più seller_id/seller_email);
            // 4. Verificare se app_id è effettivamente il commerciante stesso.
            // 5. Altre situazioni logiche commerciali

            Log::debug('Notifica di Alipay', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // Nel framework Laravel si prega di utilizzare direttamente `return $alipay->success()`
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
        'app_id' => 'wxb3fxxxxxxxxxxx', // APPID del pubblico
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // APPID dell'applicazione
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // opzionale, utilizzato in situazioni come i rimborsi
        'cert_key' => './cert/apiclient_key.pem', // opzionale, utilizzato in situazioni come i rimborsi
        'log' => [ // opzionale
            'file' => './logs/wechat.log',
            'level' => 'info', // Si consiglia di regolare il livello per l'ambiente di produzione a info e per lo sviluppo a debug
            'type' => 'single', // opzionale, opzioni giornaliere.
            'max_file' => 30, // opzionale, valido solo quando il tipo è giornaliero, predefinito 30 giorni
        ],
        'http' => [ // opzionale
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Per ulteriori opzioni di configurazione, fare riferimento a [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // opzionale, dev/hk; quando è `hk`, si tratta del gateway di Hong Kong.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **Unità: centesimi**
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
            $data = $pay->verify(); // Sì, la verifica è così semplice!

            Log::debug('Notifica di WeChat', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send(); // Nel framework Laravel si prega di utilizzare direttamente `return $pay->success()`
    }
}
```

### Per ulteriori informazioni

Visita https://pay.yanda.net.cn/docs/2.x/overview
