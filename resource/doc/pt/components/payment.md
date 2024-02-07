# SDK de Pagamento (V3)

## Endereço do Projeto

 https://github.com/yansongda/pay

## Instalação

```php
composer require yansongda/pay ^3.0.0
```

## Uso

> Observação: A seguir, a documentação será escrita usando o ambiente de sandbox do Alipay. Se houver algum problema, por favor, forneça um retorno!

## Arquivo de Configuração

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
            // Obrigatório - chave privada do aplicativo (string ou caminho)
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obrigatório - Caminho do certificado público do aplicativo
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obrigatório - Caminho do certificado público do Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obrigatório - Caminho do certificado raiz do Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Opcional - URL de retorno sincronizado
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Opcional - URL de retorno assíncrono
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Opcional - ID do provedor de serviços no modo de serviço, quando o modo for Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Opcional - Padrão para o modo normal. Pode ser: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obrigatório - número do comerciante, para o modo de serviço seria o número do comerciante do provedor de serviços
            'mch_id' => '',
            // Obrigatório - chave secreta do comerciante
            'mch_secret_key' => '',
            // Obrigatório - chave privada do comerciante (string ou caminho)
            'mch_secret_cert' => '',
            // Obrigatório - Caminho do certificado público do comerciante
            'mch_public_cert_path' => '',
            // Obrigatório
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Opcional - app_id do público em geral
            'mp_app_id' => '2016082000291234',
            // Opcional - app_id do mini-aplicativo
            'mini_app_id' => '',
            // Opcional - app_id do aplicativo
            'app_id' => '',
            // Opcional - app_id do aplicativo composto
            'combine_app_id' => '',
            // Opcional - número do comerciante composto
            'combine_mch_id' => '',
            // Opcional - No modo de serviço, app_id do subpúblico
            'sub_mp_app_id' => '',
            // Opcional - No modo de serviço, app_id do subaplicativo
            'sub_app_id' => '',
            // Opcional - No modo de serviço, app_id do subminiaplicativo
            'sub_mini_app_id' => '',
            // Opcional - No modo de serviço, ID do subcomerciante
            'sub_mch_id' => '',
            // Opcional - Caminho do certificado público do WeChat, opcional, é altamente recomendável configurar este parâmetro no modo fpm do php
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Opcional - Padrão para o modo normal. Pode ser: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // É recomendado ajustar o nível para info em ambientes de produção e debug em ambientes de desenvolvimento
        'type' => 'single', // opcional, pode ser diário
        'max_file' => 30, // opcional, válido apenas quando o tipo é diário, padrão de 30 dias
    ],
    'http' => [ // opcional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Para mais opções de configuração, consulte [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```

> Observação: Não há uma especificação para o diretório de certificados; o exemplo acima coloca os certificados no diretório `payment` do framework

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## Inicialização

Chame o método `config` diretamente.

```php
// Obtenha o arquivo de configuração config/payment.php
$config = Config::get('payment');
Pay::config($config);
```

> Observação: Se estiver em modo sandbox do Alipay, lembre-se de ativar a configuração `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, que, por padrão, é o modo normal.

## Pagamento (Página da Web)

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
    // 1. Obter arquivo de configuração config/payment.php
    $config = Config::get('payment');

    // 2. Inicializar configuração
    Pay::config($config);

    // 3. Pagamento da página da web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'pagamento webman',
        '_method' => 'get' // Use o método get para redirecionar
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## Callback Assíncrono

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: 'Alipay' notificação assíncrona
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Obter arquivo de configuração config/payment.php
    $config = Config::get('payment');

    // 2. Inicializar configuração
    Pay::config($config);

    // 3. Processamento do callback do Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Certifique-se de verificar o trade_status e sua lógica adicional. Apenas quando o status do aviso de transação for TRADE_SUCCESS ou TRADE_FINISHED, o Alipay considerará que o pagamento foi efetuado com sucesso.
    // 1. O comerciante deve verificar se o out_trade_no nestes dados de aviso é o número do pedido criado no sistema do comerciante;
    // 2. Verifique se o total_amount corresponde ao valor real do pedido (ou seja, o valor do pedido criado pelo comerciante);
    // 3. Verifique se o seller_id (ou seller_email) no aviso corresponde à entidade operacional deste pedido out_trade_no;
    // 4. Verifique se o app_id corresponde ao próprio comerciante.
    // 5. Lógica de negócios adicional
    // ===================================================================================================

    // 5. Processamento do callback do Alipay
    return new Response(200, [], 'sucesso');
}
```

> Observação: Não use `return Pay::alipay()->success();` para responder ao callback do Alipay, pois isso pode gerar problemas com middlewares. Portanto, para responder ao Alipay, é necessário usar a classe de resposta do webman `support\Response;`
## Chamada de retorno síncrona

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Notificação síncrona do Alipay
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Notificação síncrona do Alipay ' . json_encode($request->get()));
    return 'sucesso';
}
```

## Código completo do exemplo

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Mais conteúdo

Acesse a documentação oficial em https://pay.yansongda.cn/docs/v3/
