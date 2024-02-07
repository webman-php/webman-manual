# Zahlungs-SDK (V3)

## Projektadresse

https://github.com/yansongda/pay

## Installation

```php
composer require yansongda/pay ^3.0.0
```

## Verwendung

> Hinweis: Die folgende Dokumentation basiert auf der Umgebung des Alipay-Sandbox. Wenn Sie Probleme haben, lassen Sie es uns bitte wissen!

### Konfigurationsdatei

Angenommen, es gibt folgende Konfigurationsdatei `config/payment.php`

```php
<?php
/**
 * @desc Zahlungskonfigurationsdatei
 * @author Tinywan(ShaoBo Wan)
 * @date 11.03.2022 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Erforderlich - von Alipay zugewiesene app_id
            'app_id' => '20160909004708941',
            // Erforderlich - Anwendungs-Privatschlüssel, Zeichenkette oder Pfad
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Erforderlich - Anwendungsöffentliches Zertifikatpfad
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Erforderlich - Alipay öffentliches Zertifikatpfad
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Erforderlich - Alipay-Root-Zertifikatpfad
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Optional - Synchronisationsrückrufadresse
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Optional - Asynchroner Rückrufadresse
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Optional - Anbieter-ID im Serviceprovider-Modus, bei Verwendung dieses Parameters, wenn mode auf Pay::MODE_SERVICE gesetzt ist
            'service_provider_id' => '',
            // Optional - Standardmäßig Normalmodus. Kann sein: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Erforderlich - Händlernummer, im Serviceprovider-Modus die Händler-ID des Diensteanbieters
            'mch_id' => '',
            // Erforderlich - Händlergeheimschlüssel
            'mch_secret_key' => '',
            // Erforderlich - Händler-Privatschlüssel, Zeichenkette oder Pfad
            'mch_secret_cert' => '',
            // Erforderlich - Händler öffentliches Zertifikatspfad
            'mch_public_cert_path' => '',
            // Erforderlich
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Optional - app_id des offiziellen Kontos
            'mp_app_id' => '2016082000291234',
            // Optional - app_id der Mini-Programme
            'mini_app_id' => '',
            // Optional - app_id der App
            'app_id' => '',
            // Optional - kombinierte app_id
            'combine_app_id' => '',
            // Optional - kombinierter Händlernummer
            'combine_mch_id' => '',
            // Optional - Im Serviceprovider-Modus, app_id des Sub-Offiziellen-Kontos
            'sub_mp_app_id' => '',
            // Optional - Im Serviceprovider-Modus, app_id des Sub-App
            'sub_app_id' => '',
            // Optional - Im Serviceprovider-Modus, app_id des Sub-Mini-Programms
            'sub_mini_app_id' => '',
            // Optional - Im Serviceprovider-Modus, Sub-Händlernummer
            'sub_mch_id' => '',
            // Optional - WeChat öffentliches Zertifikatspfad, optional, dringend empfohlen, dass dieser Parameter im PHP-FPM-Modus konfiguriert wird
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Optional - Standardmäßig Normalmodus. Kann sein: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Empfohlene Einstellungen für die Produktionsumgebung: info, für die Entwicklungsumgebung: debug
        'type' => 'single', // Optional, täglich wählbar.
        'max_file' => 30, // Optional, wirksam, wenn type auf täglich gesetzt ist, standardmäßig 30 Tage
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Weitere Konfigurationsoptionen finden Sie unter [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Hinweis: Das Verzeichnis für Zertifikate ist nicht vorgeschrieben, das obige Beispiel befindet sich im Verzeichnis `payment` des Frameworks

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Initialisierung

Verwenden Sie die `config`-Methode, um direkt zu initialisieren:
```php
// Holen Sie sich die Konfigurationsdatei config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Hinweis: Wenn sich das Alipay im Sandbox-Modus befindet, stellen Sie sicher, dass Sie die Konfigurationsdatei öffnen `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, diese Option ist standardmäßig auf den Normalmodus gesetzt.

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
        '_method' => 'get' // Verwendung der GET-Methode zur Umleitung
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
 * @desc: 『Alipay』Asynchroner Rückruf
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
    // Bitte überprüfen Sie selbst den trade_status und führen Sie weitere Logikprüfungen durch. Alipay erkennt eine erfolgreich abgeschlossene Zahlung erst, wenn der Transaktionsbenachrichtigungsstatus auf TRADE_SUCCESS oder TRADE_FINISHED gesetzt ist.
    // 1. Der Händler muss prüfen, ob die out_trade_no in den Benachrichtigungsdaten die Bestellnummer ist, die im Händlersystem erstellt wurde;
    // 2. Überprüfen Sie, ob total_amount tatsächlich der tatsächliche Betrag der Bestellung ist (d. h. der beim Händler erstellte Betrag);
    // 3. Überprüfen Sie, ob seller_id (oder seller_email) in der Benachrichtigung für die betreffende Transaktion die zugehörige Partei ist;
    // 4. Überprüfen Sie, ob app_id dem Händler selbst gehört.
    // 5. weitere geschäftliche Logik
    // ===================================================================================================

    // 5. Alipay-Rückrufverarbeitung
    return new Response(200, [], 'Erfolg');
}
```
> Hinweis: Die Antwort auf den Alipay-Rückruf kann nicht mit `return Pay::alipay()->success();` des Plugins erfolgen, da dies zu Middleware-Problemen führen würde. Daher sollten Sie für die Alipay-Antwort die Response-Klasse von Webman verwenden `support\Response;`

#### Synchroner Rückruf

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『Alipay』Synchroner Rückruf
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『Alipay』Synchroner Rückruf'.json_encode($request->get()));
    return 'Erfolg';
}
```

## Vollständiger Beispielcode

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Weitere Informationen

Besuchen Sie die offizielle Dokumentation unter https://pay.yansongda.cn/docs/v3/
