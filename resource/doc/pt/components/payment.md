# SDK de pagamento (V3)

## Endereço do projeto

 https://github.com/yansongda/pay

## Instalação

```php
composer require yansongda/pay ^3.0.0
```

## Uso

> Nota: A seguir, o documento será escrito utilizando o ambiente de sandbox do Alipay. Se houver algum problema, não hesite em fornecer feedback!

### Arquivo de configuração

Suponha que haja o seguinte arquivo de configuração `config/payment.php`

```php
<?php
/**
 * @desc Arquivo de configuração de pagamento
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Obrigatório - app_id atribuído pelo Alipay
            'app_id' => '20160909004708941',
            // Obrigatório - Chave privada do aplicativo como string ou caminho
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obrigatório - Caminho do certificado público do aplicativo
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obrigatório - Caminho do certificado público do Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obrigatório - Caminho do certificado raiz do Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Opcional - Endereço de retorno síncrono
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Opcional - Endereço de retorno assíncrono
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Opcional - ID do provedor de serviços no modo de provedor de serviços, usado quando o modo é Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Opcional - Padrão é o modo normal. Pode ser: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obrigatório - Número do comerciante, em modo de fornecedor de serviços, é o número do comerciante do fornecedor de serviços
            'mch_id' => '',
            // Obrigatório - Chave secreta do comerciante
            'mch_secret_key' => '',
            // Obrigatório - Chave privada do comerciante como string ou caminho
            'mch_secret_cert' => '',
            // Obrigatório - Caminho do certificado público do comerciante
            'mch_public_cert_path' => '',
            // Obrigatório
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Opcional - app_id para conta pública 
            'mp_app_id' => '2016082000291234',
            // Opcional - app_id para mini aplicativo
            'mini_app_id' => '',
            // Opcional - app_id para aplicativo
            'app_id' => '',
            // Opcional - app_id combinado
            'combine_app_id' => '',
            // Opcional - número do comerciante combinado
            'combine_mch_id' => '',
            // Opcional - No modo de provedor de serviços, app_id para subconta pública
            'sub_mp_app_id' => '',
            // Opcional - No modo de provedor de serviços, app_id para sub aplicativo
            'sub_app_id' => '',
            // Opcional - No modo de provedor de serviços, app_id para sub mini aplicativo
            'sub_mini_app_id' => '',
            // Opcional - No modo de provedor de serviços, identificação do subcomerciante
            'sub_mch_id' => '',
            // Opcional - Caminho do certificado público do WeChat, opcional, é altamente recomendável configurar este parâmetro no modo php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Opcional - Padrão é o modo normal. Pode ser: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // É recomendado ajustar o nível para info em ambientes de produção e para debug em ambientes de desenvolvimento
        'type' => 'single', // opcional, diário é uma opção
        'max_file' => 30, // opcional, válido apenas para type daily, padrão 30 dias
    ],
    'http' => [ // opcional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Para mais opções de configuração, consulte [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Nota: Não há diretrizes para o diretório do certificado. O exemplo acima é colocado no diretório `payment` do framework `base_path()`.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Inicialização

Chame diretamente o método `config`
```php
// Obter o arquivo de configuração config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Nota: Se estiver no modo sandbox do Alipay, lembre-se de ativar a opção de configuração `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, que por padrão é o modo normal.

### Pagamento (página da web)

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @param Request $request
 * @return string
 */
public function payment(Request $request)
{
    // 1. Obter o arquivo de configuração config/payment.php
    $config = Config::get('payment');

    // 2. Inicializar a configuração
    Pay::config($config);

    // 3. Pagamento na web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'pagamento webman',
        '_method' => 'get' // Usar o método get para redirecionamento
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Retorno

#### Retorno assíncrono

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: Notificação assíncrona do Alipay
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Obter o arquivo de configuração config/payment.php
    $config = Config::get('payment');

    // 2. Inicializar a configuração
    Pay::config($config);

    // 3. Processamento do retorno do Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Por favor, verifique o trade_status por conta própria e faça outras verificações lógicas. O Alipay só considera o pagamento como bem-sucedido se o estado de notificação da transação for TRADE_SUCCESS ou TRADE_FINISHED.
    // 1. O comerciante precisa verificar se out_trade_no nestes dados de notificação é o número do pedido criado no sistema do comerciante
    // 2. Verificar se o total_amount é o valor real do pedido (o valor quando o pedido foi criado)
    // 3. Validar se seller_id (ou seller_email) nesta notificação corresponde à parte operacional correspondente ao pedido out_trade_no
    // 4. Verificar se app_id é o próprio comerciante
    // 5. Outras situações lógicas
    // ===================================================================================================

    // 5. Processamento de retorno do Alipay
    return new Response(200, [], 'success');
}
```
> Nota: Não é possível usar o método `return Pay::alipay()->success();` do próprio plug-in para responder ao retorno do Alipay, pois poderá ocorrer problemas com middleware. Portanto, para responder ao Alipay, é necessário usar a classe de resposta do webman `support\Response;`

#### Retorno síncrono

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Notificação síncrona do Alipay
 * @param Request $request
 * @return string
 */
public function alipayReturn(Request $request)
{
    Log::info('Notificação síncrona do Alipay'.json_encode($request->get()));
    return 'success';
}
```

## Código completo do exemplo

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Mais conteúdo

Visite a documentação oficial em https://pay.yansongda.cn/docs/v3/
