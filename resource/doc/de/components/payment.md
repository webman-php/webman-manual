# Zahlungs-SDK (V3)

## Projektadresse

https://github.com/yansongda/pay

## Installation

```php
composer require yansongda/pay ^3.0.0
```

## Verwendung

> Hinweis: Die folgende Dokumentation wurde für die Alibaba Sandbox-Umgebung verfasst. Bitte lassen Sie uns wissen, wenn Sie Probleme haben!

### Konfigurationsdatei

Angenommen, es gibt die folgende Konfigurationsdatei `config/payment.php`

```php
<?php
/**
 * @desc Zahlungskonfigurationsdatei
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Erforderlich - Von Alipay zugewiesene app_id
            'app_id' => '20160909004708941',
            // Erforderlich - Anwendungsprivatschlüssel Zeichenfolge oder Pfad
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Erforderlich - Pfad zum öffentlichen Zertifikat von Alipay
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Erforderlich - Pfad zum Alipay-Root-Zertifikat
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Optionale - Synchronisierter Rückruf
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Optionale - Asynchroner Rückruf
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Optionale - Dienstanbieter-ID im Serviceprovider-Modus, die bei Modus als Pay::MODE_SERVICE verwendet wird
            'service_provider_id' => '',
            // Optionale - Standardmäßig im Normalmodus. Kann sein: MODUS_NORMAL, MODUS_SANDBOX, MODUS_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Erforderlich - Händlernummer, im Service-Provider-Modus die Händlernummer des Service-Providers
            'mch_id' => '',
            // Erforderlich - Händlergeheimschlüssel
            'mch_secret_key' => '',
            // Erforderlich - Händlerprivater Schlüssel Zeichenfolge oder Pfad
            'mch_secret_cert' => '',
            // Erforderlich - Pfad zum öffentlichen Zertifikat des Händlers
            'mch_public_cert_path' => '',
            // Erforderlich
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Optionale - App_ID des öffentlichen Kontos
            'mp_app_id' => '2016082000291234',
            // Optionale - App_ID der Mini-Programms
            'mini_app_id' => '',
            // Optionale - App_ID der App
            'app_id' => '',
            // Optionale - Kombinierte App_ID
            'combine_app_id' => '',
            // Optionale - Kombinierte Händlernummer
            'combine_mch_id' => '',
            // Optionale - Im Service-Provider-Modus untergeordnete App_ID des öffentlichen Kontos
            'sub_mp_app_id' => '',
            // Optionale - Im Service-Provider-Modus untergeordnete App_ID der App
            'sub_app_id' => '',
            // Optionale - Im Service-Provider-Modus untergeordnete App_ID des Mini-Programms
            'sub_mini_app_id' => '',
            // Optionale - Im Service-Provider-Modus untergeordnete Händlernummer
            'sub_mch_id' => '',
            // Optionale - Pfad des WeChat-Öffentlichkeitszertifikats, optional, wird dringend empfohlen, dieses Parameter im php-fpm-Modus zu konfigurieren
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Optionale - Standardmäßig im Normalmodus. Kann sein: MODUS_NORMAL, MODUS_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Im Produktionsumfeld wird empfohlen, den Level auf info zu setzen und im Entwicklungsumfeld auf debug
        'type' => 'single', // Optional, kann täglich sein.
        'max_file' => 30, // Optional, bei täglicher Einstellung gültig, standardmäßig 30 Tage
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Weitere Konfigurationsmöglichkeiten finden Sie unter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Achtung: Das Verzeichnis für Zertifikate ist nicht festgelegt. Das obige Beispiel wird im Verzeichnis des Frameworks `payment` gespeichert.

```php
├── Zahlung
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Initialisierung

Rufen Sie die `config` Methode direkt auf, um die Initialisierung vorzunehmen
```php
// Holen Sie sich die Konfigurationsdatei config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Achtung: Wenn Sie den Alipay-Sandbox-Modus verwenden, vergessen Sie nicht, die Option `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` in der Konfigurationsdatei zu aktivieren, da diese standardmäßig im normalen Modus gesetzt ist.

### Zahlung (Web)

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
    // 1. Holen Sie sich die Konfigurationsdatei config/payment.php
    $config = Config::get('payment');

    // 2. Konfiguration initialisieren
    Pay::config($config);

    // 3. Web-Zahlung
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman Zahlung',
        '_method' => 'get' // Verwenden Sie die GET-Methode zur Umleitung
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Rückruf

#### Asynchroner Rückruf

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc:『Alipay』asynchrone Benachrichtigung
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Holen Sie sich die Konfigurationsdatei config/payment.php
    $config = Config::get('payment');

    // 2. Konfiguration initialisieren
    Pay::config($config);

    // 3. Alipay-Rückrufverarbeitung
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Bitte überprüfen Sie den trade_status und andere Logik selbst. Alipay betrachtet die Zahlung nur als erfolgreich, wenn der Handelsbenachrichtigungsstatus TRADE_SUCCESS oder TRADE_FINISHED ist.
    // 1. Der Händler muss prüfen, ob out_trade_no in den Benachrichtigungsdaten die Bestellnummer im Händlersystem ist;
    // 2. Überprüfen Sie, ob total_amount tatsächlich der tatsächliche Betrag dieser Bestellung ist (d. h. der Betrag, der bei der Erstellung der Händlerbestellung vorgegeben wurde);
    // 3. Überprüfen Sie, ob seller_id (oder seller_email) in der Benachrichtigung mit der Partei übereinstimmt, die die Transaktion für out_trade_no durchgeführt hat;
    // 4. Überprüfen Sie, ob app_id dem Händler selbst gehört.
    // 5. Andere Geschäftslogik
    // ===================================================================================================

    // 5. Alipay-Rückrufverarbeitung
    return new Response(200, [], 'Erfolg');
}
```
> Achtung: Verwenden Sie nicht `return Pay::alipay()->success();` des Plugins zur Antwort auf den Alipay-Rückruf. Bei der Verwendung von Middleware kann es zu Problemen kommen. Daher verwenden Sie für die Alipay-Antwort die Response-Klasse von Webman `support\Response;`

#### Synchroner Rückruf

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『Alipay』synchroner Rückruf
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『Alipay』synchroner Rückruf'.json_encode($request->get()));
    return 'Erfolg';
}
```

## Vollständiger Beispielcode

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Weitere Informationen

Besuchen Sie die offizielle Dokumentation unter https://pay.yansongda.cn/docs/v3/
