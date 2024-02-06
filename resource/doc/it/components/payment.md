# SDK di pagamento (V3)

## Indirizzo del Progetto

https://github.com/yansongda/pay

## Installazione

```php
compositore richiede yansongda/pay ^3.0.0
```

## Uso

> Nota: La seguente documentazione è redatta con l'ambiente sandbox di Alipay come ambiente. In caso di problemi, ti preghiamo di segnalarli tempestivamente!

### File di configurazione

Assumendo di avere il seguente file di configurazione `config/payment.php`

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
            // Obbligatorio - Chiave privata dell'applicazione come stringa o percorso
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obbligatorio - Percorso del certificato pubblico dell'applicazione
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obbligatorio - Percorso del certificato pubblico di Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obbligatorio - Percorso del certificato radice di Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Opzionale - URL di callback sincrono
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Opzionale - URL di callback asincrono
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Opzionale - ID del fornitore di servizi in modalità fornitore di servizi, da utilizzare quando la modalità è Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Opzionale - Predefinito in modalità normale. Può essere: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obbligatorio - Numero di commerciante, in modalità fornitore di servizi è il numero di commerciante del fornitore di servizi
            'mch_id' => '',
            // Obbligatorio - Chiave segreta del commerciante
            'mch_secret_key' => '',
            // Obbligatorio - Chiave privata del commerciante come stringa o percorso
            'mch_secret_cert' => '',
            // Obbligatorio - Percorso del certificato pubblico del commerciante
            'mch_public_cert_path' => '',
            // Obbligatorio
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Opzionale - app_id dell'account pubblico
            'mp_app_id' => '2016082000291234',
            // Opzionale - app_id dell'applicazione mini
            'mini_app_id' => '',
            // Opzionale - app_id dell'app
            'app_id' => '',
            // Opzionale - app_id della combinazione
            'combine_app_id' => '',
            // Opzionale - ID del commerciante della combinazione
            'combine_mch_id' => '',
            // Opzionale - In modalità fornitore di servizi, app_id del sotto-account pubblico
            'sub_mp_app_id' => '',
            // Opzionale - In modalità fornitore di servizi, app_id dell'app sottostante
            'sub_app_id' => '',
            // Opzionale - In modalità fornitore di servizi, app_id dell'applicazione mini sottostante
            'sub_mini_app_id' => '',
            // Opzionale - In modalità fornitore di servizi, ID del commerciante sottostante
            'sub_mch_id' => '',
            // Opzionale - Percorso del certificato pubblico di WeChat, opzionale, fortemente consigliato configurare questo parametro in modalità php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Opzionale - Predefinito in modalità normale. Può essere: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Suggeriamo di impostare questo livello su info per l'ambiente di produzione e su debug per l'ambiente di sviluppo
        'type' => 'single', // opzionale, può essere quotidiano
        'max_file' => 30, // opzionale, valido solo quando il tipo è giornaliero, predefinito 30 giorni
    ],
    'http' => [ // opzionale
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Per ulteriori opzioni di configurazione, vedere [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Nota: La directory dei certificati non è vincolata, gli esempi sopra sono posizionati nella directory `payment` del framework

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Inizializzazione 

Inizializza direttamente chiamando il metodo `config`
```php
// Ottieni il file di configurazione config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Nota: Se ci si trova in modalità sandbox di Alipay, assicurarsi di abilitare l'opzione nel file di configurazione `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, questa opzione di solito è impostata su modalità normale.

### Pagamento (sito web)

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
    // 1. Ottieni il file di configurazione config/payment.php
    $config = Config::get('payment');

    // 2. Inizializza la configurazione
    Pay::config($config);

    // 3. Pagamento Web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'pagamento webman',
        '_method' => 'get' // Utilizza il metodo get per il reindirizzamento
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Callback

#### Callback asincrono

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: Notifica asincrona di 'Alipay'
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Ottieni il file di configurazione config/payment.php
    $config = Config::get('payment');

    // 2. Inizializza la configurazione
    Pay::config($config);

    // 3. Gestione del callback di Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Si prega di verificare autonomamente lo stato della transazione e fare altre verifiche logiche.
    // Alipay considera la transazione come riuscita solo se lo stato della transazione è TRADE_SUCCESS o TRADE_FINISHED.
    // 1. Il commerciante deve verificare se out_trade_no in questi dati di notifica è il numero d'ordine creato nel sistema del commerciante;
    // 2. Verificare che total_amount corrisponda effettivamente all'importo effettivo dell'ordine (cioè l'importo dell'ordine creato dal commerciante);
    // 3. Verificare se seller_id (o seller_email) nella notifica corrisponde alla parte operativa corrispondente del out_trade_no;
    // 4. Verificare se app_id corrisponde al commerciante stesso.
    // 5. Altro logica aziendale
    // ===================================================================================================

    // 5. Convalida del callback di Alipay
    return new Response(200, [], 'success');
}
```
> Nota: Non è possibile utilizzare il metodo `return Pay::alipay()->success();` del plugin per rispondere al callback di Alipay, poiché potrebbero sorgere problemi di middleware. Pertanto, la risposta a Alipay deve essere gestita tramite la classe di risposta di Webman `support\Response;`

#### Callback sincrono

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
    Log::info('Notifica sincrona di \'Alipay\'' . json_encode($request->get()));
    return 'success';
}
```

## Codice completo dell'esempio

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Ulteriori dettagli

Visita la documentazione ufficiale https://pay.yansongda.cn/docs/v3/
