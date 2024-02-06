# SDK de paiement (V3)

## Adresse du projet

 https://github.com/yansongda/pay

## Installation

```php
composer require yansongda/pay ^3.0.0
```

## Utilisation 

> Remarque : La documentation est rédigée sur l'environnement de la sandbox Alipay. Veuillez signaler tout problème !

### Fichier de configuration

Supposons qu'il existe le fichier de configuration suivant `config/payment.php`

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
            // Obligatoire - Clé privée de l'application, chaîne ou chemin
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obligatoire - Chemin du certificat public de l'application
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obligatoire - Chemin du certificat public Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obligatoire - Chemin du certificat racine Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Optionnel - Adresse de retour synchrone
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Optionnel - Adresse de retour asynchrone
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Optionnel - Identifiant du fournisseur de services en mode fournisseur de services, à utiliser lorsque le mode est Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Optionnel - Par défaut, le mode normal. Peut être : MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obligatoire - Numéro de commerçant, dans le cadre du mode de fournisseur de services, il s'agit du numéro de commerçant fournisseur de services
            'mch_id' => '',
            // Obligatoire - Clé secrète du commerçant
            'mch_secret_key' => '',
            // Obligatoire - Clé privée du commerçant, chaîne ou chemin
            'mch_secret_cert' => '',
            // Obligatoire - Chemin du certificat public du commerçant
            'mch_public_cert_path' => '',
            // Obligatoire
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Optionnel - app_id de l'abonnement
            'mp_app_id' => '2016082000291234',
            // Optionnel - app_id pour les mini programmes
            'mini_app_id' => '',
            // Optionnel - app_id pour l'application
            'app_id' => '',
            // Optionnel - app_id pour la fusion
            'combine_app_id' => '',
            // Optionnel - Numéro de commerçant pour la fusion
            'combine_mch_id' => '',
            // Optionnel - En mode fournisseur de services, app_id du sous-abonnement
            'sub_mp_app_id' => '',
            // Optionnel - En mode fournisseur de services, app_id de l'application
            'sub_app_id' => '',
            // Optionnel - En mode fournisseur de services, app_id pour les mini programmes
            'sub_mini_app_id' => '',
            // Optionnel - En mode fournisseur de services, identifiant du sous-commerçant
            'sub_mch_id' => '',
            // Optionnel - Chemin du certificat public de WeChat, optionnel, il est fortement recommandé de configurer ce paramètre en mode php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Optionnel - Par défaut, le mode normal. Peut être : MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Il est conseillé d'ajuster le niveau de production à info, et pour le développement à debug
        'type' => 'single', // optionnel, quotidien.
        'max_file' => 30, // optionnel, valable pour 30 jours par défaut lorsque le type est quotidien
    ],
    'http' => [ // optionnel
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Pour plus de paramètres de configuration, veuillez consulter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Remarque : Aucune règle n'est spécifiée pour les certificats. Dans l'exemple ci-dessus, ils sont placés dans le répertoire `payment` du framework

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Initialisation

Initialiser directement en appelant la méthode `config`
```php
// Obtenir le fichier de configuration config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Note : Si vous êtes en mode sandbox Alipay, assurez-vous de activer l'option de configuration `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` qui est par défaut en mode normal.

### Paiement (site web)

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

    // 2. Initialiser la configuration
    Pay::config($config);

    // 3. Paiement en ligne
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'paiement webman',
        '_method' => 'get' // Utiliser la méthode get pour redirection
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
 * @desc: Rappel asynchrone d'Alipay
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Obtenez le fichier de configuration config/payment.php
    $config = Config::get('payment');

    // 2. Initialiser la configuration
    Pay::config($config);

    // 3. Traitement du rappel Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Veuillez vérifier manuellement l'état de la transaction et les autres logiques. Alipay considère que l'acheteur a payé avec succès uniquement lorsque l'état de notification de la transaction est TRADE_SUCCESS ou TRADE_FINISHED
    // 1. Le commerçant doit vérifier si out_trade_no figure dans les données de notification comme le numéro de commande créé dans le système du commerçant ;
    // 2. Vérifiez si total_amount correspond réellement au montant réel de la commande (c'est-à-dire le montant lors de la création de la commande par le commerçant) ;
    // 3. Vérifiez que seller_id (ou seller_email) dans la notification correspond à l'entité opérationnelle correspondante de la commande out_trade_no ;
    // 4. Vérifiez que app_id est le commerçant lui-même.
    // 5. Autres logiques métier
    // ===================================================================================================

    // 5. Traitement du rappel Alipay
    return new Response(200, [], 'success');
}
```
> Remarque : Ne pas utiliser la méthode `return Pay::alipay()->success();` pour répondre au rappel d'Alipay. Cela entraînera des problèmes liés à l'utilisation du middleware. Par conséquent, pour répondre à Alipay, vous devez utiliser la classe de réponse de webman `support\Response;`

#### Rappel synchrone

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Rappel synchrone d'Alipay
 * @param Request $request
 * @return Response
 */
public function alipayReturn(Request $request): Response
{
    Log::info('Rappel synchrone d\'Alipay'.json_encode($request->get()));
    return 'success';
}
```

## Code complet de l'exemple

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Pour plus d'informations

Consultez la documentation officielle https://pay.yansongda.cn/docs/v3/
