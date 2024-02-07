# SDK di pagamento (V3)

## Indirizzo del progetto
 https://github.com/yansongda/pay

## Installazione

```php
composer require yansongda/pay ^3.0.0
```

## Utilizzo

> Nota: il documento è redatto prendendo come esempio l'ambiente sandbox di Alipay. Se ci sono problemi, ti preghiamo di darci prontamente un feedback!

## File di configurazione

Supponiamo di avere il seguente file di configurazione `config/payment.php`

```php
<?php
/**
 * @desc File di configurazione del pagamento
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Obbligatorio - app_id assegnato da Alipay
            'app_id' => '20160909004708941',
            // Obbligatorio - Chiave privata dell'applicazione, stringa o percorso
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obbligatorio - percorso del certificato pubblico dell'applicazione
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obbligatorio - percorso del certificato pubblico di Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obbligatorio - percorso del certificato radice di Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Opzionale - URL di callback sincrona
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Opzionale - URL di callback asincrona
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Opzionale - ID del fornitore di servizi in modalità provider, da utilizzare quando la modalità è Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Opzionale - default è il modo normale. Opzioni: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obbligatorio - numero commerciale, in modalità fornitore di servizi fornire il numero commerciale del fornitore di servizi
            'mch_id' => '',
            // Obbligatorio - Chiave segreta commerciale
            'mch_secret_key' => '',
            // Obbligatorio - Chiave privata commerciale, stringa o percorso
            'mch_secret_cert' => '',
            // Obbligatorio - percorso del certificato pubblico commerciale
            'mch_public_cert_path' => '',
            // Obbligatorio
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Opzionale - app_id del public account
            'mp_app_id' => '2016082000291234',
            // Opzionale - app_id di mini program
            'mini_app_id' => '',
            // Opzionale - app_id di app
            'app_id' => '',
            // Opzionale - app_id dell'applicazione combinata
            'combine_app_id' => '',
            // Opzionale - numero commerciale combinato
            'combine_mch_id' => '',
            // Opzionale - In modalità fornitore di servizi, app_id del conta pubblico figlio
            'sub_mp_app_id' => '',
            // Opzionale - In modalità fornitore di servizi, app_id dell'app
            'sub_app_id' => '',
            // Opzionale - In modalità fornitore di servizi, app_id del mini program
            'sub_mini_app_id' => '',
            // Opzionale - In modalità fornitore di servizi, ID commerciale figlio
            'sub_mch_id' => '',
            // Opzionale - percorso del certificato pubblico di WeChat, opzionale. È fortemente consigliato configurare questo parametro quando si utilizza la modalità php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Opzionale - default è il modo normale. Opzioni: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Consigliato livello di produzione da modificare in info, ambiente di sviluppo in debug
        'type' => 'single', // opzionale, scelta giornaliera
        'max_file' => 30, // opzionale, valido solo quando il tipo è giornaliero, di default 30 giorni
    ],
    'http' => [ // opzionale
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Per ulteriori opzioni di configurazione fare riferimento a [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Nota: i percorsi dei certificati non sono specificati, l'esempio sopra mostra che vengono posizionati nella directory `payment` del framework.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## Inizializzazione

Inizializzare direttamente con il metodo `config`.
```php
// Ottenere il file di configurazione config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Nota: se si utilizza la modalità sandbox di Alipay, assicurati di abilitare l'opzione nel file di configurazione `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` che per impostazione predefinita è impostata su modalità normale.

## Pagamento (pagina web)

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
    // 1. Ottenimento del file di configurazione config/payment.php
    $config = Config::get('payment');

    // 2. Inizializzazione della configurazione
    Pay::config($config);

    // 3. Pagamento web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'pagamento webman',
        '_method' => 'get' // Utilizzare la modalità get per il reindirizzamento
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## Callback asincrono

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: 'Alipay' notifica asincrona
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Ottenimento del file di configurazione config/payment.php
    $config = Config::get('payment');

    // 2. Inizializzazione della configurazione
    Pay::config($config);

    // 3. Gestione della notifica di Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Si prega di verificare autonomamente lo stato della transazione e altre logiche. Alipay riconoscerà il pagamento come avvenuto solo quando lo stato della transazione sarà TRADE_SUCCESS o TRADE_FINISHED.
    // 1. Il commerciante deve verificare se l'out_trade_no in questi dati di notifica corrisponde al numero di ordine creato nel sistema del commerciante;
    // 2. Verificare che il total_amount sia effettivamente l'importo effettivo dell'ordine (cioè l'importo dell'ordine creato dal commerciante);
    // 3. Verificare se il seller_id (o seller_email) nella notifica corrisponde all'operatore corrispondente di out_trade_no;
    // 4. Verificare se l'app_id corrisponde al commerciante stesso.
    // 5. Altre logiche commerciali
    // ===================================================================================================

    // 5. Gestione della notifica di Alipay
    return new Response(200, [], 'success');
}
```
> Nota: Non utilizzare il metodo `return Pay::alipay()->success();` di default della libreria per rispondere alla notifica di Alipay, poiché potrebbero verificarsi problemi con i middleware. Pertanto, per rispondere ad Alipay è necessario utilizzare la classe di risposta di webman, `support\Response`.
## Callback sincrono

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Notifica sincrona di 'Alipay'
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Notifica sincrona di Alipay: '.json_encode($request->get()));
    return 'success';
}
```
## Codice completo dell'esempio

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Ulteriori informazioni

Visita la documentazione ufficiale https://pay.yansongda.cn/docs/v3/
