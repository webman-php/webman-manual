# SDK de Pagamento


### Endereço do Projeto

https://github.com/yansongda/pay

### Instalação

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
        // Método de criptografia: **RSA2**
        'private_key' => 'MIIEpAIBAAKCAQEAs6+F2leOgOrvj9jTeDhb5q46GewOjqLBlGSs/bVL4Z3fMr3p+Q1Tux/6uogeVi/eHd84xvQdfpZ87A1SfoWnEGH5z15yorccxSOwWUI+q8gz51IWqjgZxhWKe31BxNZ+prnQpyeMBtE25fXp5nQZ/pftgePyUUvUZRcAUisswntobDQKbwx28VCXw5XB2A+lvYEvxmMv/QexYjwKK4M54j435TuC3UctZbnuynSPpOmCu45ZhEYXd4YMsGMdZE5/077ZU1aU7wx/gk07PiHImEOCDkzqsFo0Buc/knGcdOiUDvm2hn2y1XvwjyFOThsqCsQYi4JmwZdRa8kvOf57nwIDAQABAoIBAQCw5QCqln4VTrTvcW+msB1ReX57nJgsNfDLbV2dG8mLYQemBa9833DqDK6iynTLNq69y88ylose33o2TVtEccGp8Dqluv6yUAED14G6LexS43KtrXPgugAtsXE253ZDGUNwUggnN1i0MW2RcMqHdQ9ORDWvJUCeZj/AEafgPN8AyiLrZeL07jJz/uaRfAuNqkImCVIarKUX3HBCjl9TpuoMjcMhz/MsOmQ0agtCatO1eoH1sqv5Odvxb1i59c8Hvq/mGEXyRuoiDo05SE6IyXYXr84/Nf2xvVNHNQA6kTckj8shSi+HGM4mO1Y4Pbb7XcnxNkT0Inn6oJMSiy56P+CpAoGBAO1O+5FE1ZuVGuLb48cY+0lHCD+nhSBd66B5FrxgPYCkFOQWR7pWyfNDBlmO3SSooQ8TQXA25blrkDxzOAEGX57EPiipXr/hy5e+WNoukpy09rsO1TMsvC+v0FXLvZ+TIAkqfnYBgaT56ku7yZ8aFGMwdCPL7WJYAwUIcZX8wZ3dAoGBAMHWplAqhe4bfkGOEEpfs6VvEQxCqYMYVyR65K0rI1LiDZn6Ij8fdVtwMjGKFSZZTspmsqnbbuCE/VTyDzF4NpAxdm3cBtZACv1Lpu2Om+aTzhK2PI6WTDVTKAJBYegXaahBCqVbSxieR62IWtmOMjggTtAKWZ1P5LQcRwdkaB2rAoGAWnAPT318Kp7YcDx8whOzMGnxqtCc24jvk2iSUZgb2Dqv+3zCOTF6JUsV0Guxu5bISoZ8GdfSFKf5gBAo97sGFeuUBMsHYPkcLehM1FmLZk1Q+ljcx3P1A/ds3kWXLolTXCrlpvNMBSN5NwOKAyhdPK/qkvnUrfX8sJ5XK2H4J8ECgYAGIZ0HIiE0Y+g9eJnpUFelXvsCEUW9YNK4065SD/BBGedmPHRC3OLgbo8X5A9BNEf6vP7fwpIiRfKhcjqqzOuk6fueA/yvYD04v+Da2MzzoS8+hkcqF3T3pta4I4tORRdRfCUzD80zTSZlRc/h286Y2eTETd+By1onnFFe2X01mwKBgQDaxo4PBcLL2OyVT5DoXiIdTCJ8KNZL9+kV1aiBuOWxnRgkDjPngslzNa1bK+klGgJNYDbQqohKNn1HeFX3mYNfCUpuSnD2Yag53Dd/1DLO+NxzwvTu4D6DCUnMMMBVaF42ig31Bs0jI3JQZVqeeFzSET8fkoFopJf3G6UXlrIEAQ==',
        // Se estiver usando a modalidade de certificado público, por favor configure os seguintes parâmetros e altere ali_public_key para o caminho do certificado público do Alipay com sufixo .crt, por exemplo (./cert/alipayCertPublicKey_RSA2.crt)
        // 'app_cert_public_key' => './cert/appCertPublicKey.crt', // Caminho do certificado público do aplicativo
        // 'alipay_root_cert' => './cert/alipayRootCert.crt', // Caminho do certificado raiz do Alipay
        'log' => [ // opcional
            'file' => './logs/alipay.log',
            'level' => 'info', // Sugerimos ajustar para info em produção, e debug em desenvolvimento
            'type' => 'single', // opcional, diário.
            'max_file' => 30, // opcional, válido apenas para type daily, padrão 30 dias
        ],
        'http' => [ // opcional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Para mais opções de configuração, consulte [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // opcional, definindo este parâmetro, você entrará no modo sandbox
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - testar',
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send(); // no framework Laravel, por favor apenas `return $alipay`
    }

    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // Sim, é tão simples verificar a assinatura!

        // Número do pedido: $data->out_trade_no
        // Número da transação do Alipay: $data->trade_no
        // Montante total do pedido: $data->total_amount
    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try {
            $data = $alipay->verify(); // Sim, é tão simples verificar a assinatura!

            // Por favor, faça a validação do trade_status e outras lógicas. No caso de notificações comerciais do Alipay, somente quando o estado da transação for TRADE_SUCCESS ou TRADE_FINISHED, o Alipay considerará o pagamento do comprador bem-sucedido.
            // 1. O comerciante precisa verificar se o out_trade_no nestes dados de notificação é o número do pedido criado no sistema do comerciante;
            // 2. Verificar se o total_amount é de fato o montante real deste pedido (ou seja, o montante do pedido criado pelo comerciante);
            // 3. Verificar se o seller_id (ou seller_email) na notificação corresponde ao partido que operou este pedido de pagamento (Às vezes, um comerciante pode ter múltiplos seller_id/seller_email);
            // 4. Validar se app_id é realmente o próprio comerciante.
            // 5. Outras lógicas comerciais

            Log::debug('Notificação do Alipay', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send(); // no framework Laravel, por favor apenas `return $alipay->success()`
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
        'app_id' => 'wxb3fxxxxxxxxxxx', // APPID da conta pública
        'miniapp_id' => 'wxb3fxxxxxxxxxxx', // APPID do Mini Programa
        'mch_id' => '14577xxxx',
        'key' => 'mF2suE9sU6Mk1Cxxxxxxxxxxx',
        'notify_url' => 'http://yanda.net.cn/notify.php',
        'cert_client' => './cert/apiclient_cert.pem', // opcional, utilizado em casos de reembolso, entre outros
        'cert_key' => './cert/apiclient_key.pem', // opcional, utilizado em casos de reembolso, entre outros
        'log' => [ // opcional
            'file' => './logs/wechat.log',
            'level' => 'info', // Sugerimos ajustar para info em produção, e debug em desenvolvimento
            'type' => 'single', // opcional, diário.
            'max_file' => 30, // opcional, válido apenas para type daily, padrão 30 dias
        ],
        'http' => [ // opcional
            'timeout' => 5.0,
            'connect_timeout' => 5.0,
            // Para mais opções de configuração, consulte [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
        'mode' => 'dev', // opcional, dev/hk;quando definido como `hk`, usa o gateway de Hong Kong.
    ];

    public function index()
    {
        $order = [
            'out_trade_no' => time(),
            'total_fee' => '1', // **unidade: centavos**
            'body' => 'test body - testar',
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
            $data = $pay->verify(); // Sim, é tão simples verificar a assinatura!

            Log::debug('Notificação do WeChat', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $pay->success()->send(); // no framework Laravel, por favor apenas `return $pay->success()`
    }
}
```

### Mais Conteúdos

Acesse https://pay.yanda.net.cn/docs/2.x/overview
