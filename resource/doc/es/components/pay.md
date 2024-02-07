Entendido, ¿podrías por favor enviarme el texto que requieres que traduzca al español?
# SDK de pago

### Proyecto de dirección

https://github.com/yansongda/pay

### Instalación

```php
composer require yansongda/pay -vvv
```

### Uso

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
        // Método de encriptación: **RSA2**  
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Si está utilizando el modo de certificado público, configure los siguientes dos parámetros y modifique ali_public_key a la ruta del certificado público de Alipay con extensión .crt, por ejemplo (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // Ruta del certificado público de la aplicación
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // Ruta del certificado raíz de Alipay
        'log' => [ // opcional
            'file' => './logs/alipay.log',
            'level' => 'info', // Se recomienda ajustar el nivel en producción a info, y en desarrollo a debug
            'type' => 'single', // opcional, diario.
            'max_file' => 30, // opcional, válido cuando el tipo es diario, por defecto 30 días
        ],
        'http' => [ // opcional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Para obtener más opciones de configuración, consulte [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // opcional, establezca este parámetro para entrar en modo sandbox
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 测试',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // en el marco de Laravel, use directamente `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // ¡Sí, verificar es tan simple como esto!

        // Número de pedido: $data->out_trade_no
        // Número de transacción de Alipay: $data->trade_no
        // Monto total del pedido: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);
    
        try {
            $data = $alipay->verify(); // ¡Sí, verificar es tan simple como esto!

            // Por favor, verifique el trade_status y realice otras verificaciones lógicas. En las notificaciones comerciales de Alipay, solo cuando el estado de notificación de la transacción es TRADE_SUCCESS o TRADE_FINISHED, Alipay considerará que el comprador ha realizado el pago con éxito.
            // 1. El comerciante debe verificar si out_trade_no en los datos de la notificación es el número de pedido creado en el sistema del comerciante;
            // 2. Verifique si total_amount es realmente el monto real de ese pedido (es decir, el monto en el momento de creación del pedido del comerciante);
            // 3. Verifique que seller_id (o seller_email) en la notificación sea el operador correspondiente de este recibo (a veces un comerciante puede tener varios seller_id/seller_email);
            // 4. Verifique que app_id sea el propio comerciante.
            // 5. Cualquier otra lógica comercial
            Log::debug('Notificación de Alipay', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // en el marco de Laravel, use directamente `return $alipay->success()`
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
        'app_id' => 'wxb3fxxxxxxxxxxx', // APPID de la cuenta pública
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // APPID de la miniaplicación
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // opcional, utilizado en reembolsos, etc.
        'cert_key' => './cert/apiclient_key.pem', // opcional, utilizado en reembolsos, etc.
        'log' => [ // opcional
            'file' => './logs/wechat.log',
            'level' => 'info', // Se recomienda ajustar el nivel en producción a info, y en desarrollo a debug
            'type' => 'single', // opcional, diario.
            'max_file' => 30, // opcional, válido cuando el tipo es diario, por defecto 30 días
        ],
        'http' => [ // opcional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Para obtener más opciones de configuración, consulte [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // opcional, dev/hk; cuando es `hk`, se refiere a la pasarela de Hong Kong.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **unidad: centavos**
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
            $data = $pay->verify(); // ¡Sí, verificar es tan simple como esto!

            Log::debug('Notificación de Wechat', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }
        
        return $pay->success()->send();// en el marco de Laravel, use directamente `return $pay->success()`
    }
}
```


### Más Contenido

Visite https://pay.yanda.net.cn/docs/2.x/overview
