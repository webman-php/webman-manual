# SDK de paiement (V3)

## Adresse du projet

[https://github.com/yansongda/pay](https://github.com/yansongda/pay)

## Installation

```php
composer require yansongda/pay ^3.0.0
```

## Utilisation

> Note: La documentation est rédigée pour l'environnement de test d'Alipay. Veuillez nous contacter si vous avez des questions !

### Fichier de configuration

Supposons que vous avez le fichier de configuration suivant `config/payment.php`

```php
<?php
/**
 * @desc Fichier de configuration de paiement
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Obligatoire - app_id attribué par Alipay
            'app_id' => '20160909004708941',
            // Obligatoire - Clé privée de l'application en chaîne ou chemin
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obligatoire - Chemin du certificat public de l'application
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obligatoire - Chemin du certificat public d'Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obligatoire - Chemin du certificat racine d'Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Optionnel - URL de retour synchronisée
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Optionnel - URL de retour asynchronisée
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Optionnel - ID du fournisseur de services en mode fournisseur de services, à utiliser lorsque le mode est Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Optionnel - Par défaut en mode normal. Peut être : MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obligatoire - Numéro de commerçant, numéro de commerçant du fournisseur de services
            'mch_id' => '',
            // Obligatoire - Clé secrète du commerçant
            'mch_secret_key' => '',
            // Obligatoire - Clé privée du commerçant en chaîne ou chemin
            'mch_secret_cert' => '',
            // Obligatoire - Chemin du certificat public du commerçant
            'mch_public_cert_path' => '',
            // Obligatoire
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Optionnel - app_id du compte public
            'mp_app_id' => '2016082000291234',
            // Optionnel - app_id du mini-programme
            'mini_app_id' => '',
            // Optionnel - app_id de l'application
            'app_id' => '',
            // Optionnel - app_id combiné
            'combine_app_id' => '',
            // Optionnel - Numéro de commerçant combiné
            'combine_mch_id' => '',
            // Optionnel - app_id du compte public enfant en mode fournisseur de services
            'sub_mp_app_id' => '',
            // Optionnel - app_id de l'application enfant en mode fournisseur de services
            'sub_app_id' => '',
            // Optionnel - app_id du mini-programme enfant en mode fournisseur de services
            'sub_mini_app_id' => '',
            // Optionnel - ID de commerçant enfant en mode fournisseur de services
            'sub_mch_id' => '',
            // Optionnel - Chemin du certificat public de WeChat, optionnel, il est fortement recommandé de configurer ce paramètre en mode PHP-FPM
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Optionnel - Par défaut en mode normal. Peut être : MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Il est recommandé d'ajuster le niveau à info pour l'environnement de production et à debug pour l'environnement de développement
        'type' => 'single', // optionnel, peut être quotidien.
        'max_file' => 30, // optionnel, valable uniquement lorsque le type est daily, par défaut 30 jours
    ],
    'http' => [ // optionnel
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Pour plus de paramètres de configuration, veuillez consulter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
];
```
> Note: Aucune norme n'est spécifiée pour le répertoire des certificats, l'exemple ci-dessus les place dans le répertoire `payment` du framework

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Initialisation

Initialisez directement en appelant la méthode `config`
```php
// Obtenez le fichier de configuration config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Note: Si c'est en mode sandbox Alipay, n'oubliez pas d'activer l'option du fichier de configuration `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, cette option est par défaut en mode normal

### Paiement (Web)

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
    // 1. Obtenez le fichier de configuration config/payment.php
    $config = Config::get('payment');

    // 2. Initialisez la configuration
    Pay::config($config);

    // 3. Paiement Web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'Paiement webman',
        '_method' => 'get' // Utilisez la méthode de redirection get
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Rappel

#### Rappel asynchrone

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: Rappel asynchrone d'『Alipay』
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Obtenez le fichier de configuration config/payment.php
    $config = Config::get('payment');

    // 2. Initialisez la configuration
    Pay::config($config);

    // 3. Traitement du rappel d'Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Veuillez vérifier le statut de la transaction et d'autres logiques. Alipay considère seulement que l'acheteur a effectué un paiement avec succès lorsque l'état de la transaction est TRADE_SUCCESS ou TRADE_FINISHED.
    // 1. Le commerçant doit valider si out_trade_no dans les données de notification est le numéro de commande créé dans le système du commerçant ;
    // 2. Vérifiez si total_amount est bien le montant réel de cette commande (c'est-à-dire le montant de la commande créée par le commerçant) ;
    // 3. Vérifiez que seller_id (ou seller_email) dans la notification est l'opérateur correspondant à la commande out_trade_no ;
    // 4. Vérifiez si app_id est bien le commerçant lui-même ;
    // 5. Autres logiques métier
    // ===================================================================================================

    // 5. Traitement du rappel d'Alipay
    return new Response(200, [], 'success');
}
```
> Note: Vous ne pouvez pas utiliser `return Pay::alipay()->success();` du plugin pour répondre au rappel d'Alipay, cela posera problème si vous utilisez des middleware. Par conséquent, pour répondre à Alipay, vous devez utiliser la classe de réponse de Webman `support\Response;`

#### Rappel synchrone

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Rappel synchrone d'『Alipay』
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Rappel synchrone d\'『Alipay』' . json_encode($request->get()));
    return 'success';
}
```

## Code complet de l'exemple

[https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php](https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php)

## Pour en savoir plus

Consultez la documentation officielle [https://pay.yansongda.cn/docs/v3/](https://pay.yansongda.cn/docs/v3/)
