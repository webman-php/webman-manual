# Ödeme SDK'sı (V3)

## Proje Adresi
https://github.com/yansongda/pay

## Kurulum

```php
composer require yansongda/pay ^3.0.0
```

## Kullanım
> Not: Aşağıdaki belgelerde ödeme SDK'sını Alipay sandbox ortamıyla kullanmaya yönelik olarak yazılmıştır, herhangi bir sorunuz varsa lütfen hemen geri bildirimde bulunun!

## Yapılandırma Dosyası
Aşağıdaki yapılandırma dosyası olduğunu varsayalım: `config/payment.php`

```php
<?php
/**
 * @desc Ödeme yapılandırma dosyası
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Zorunlu - Alipay tarafından verilen app_id
            'app_id' => '20160909004708941',
            // Zorunlu - Uygulama özel anahtarı dize veya yol
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Zorunlu - Uygulama genel anahtar sertifikası yolu
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Zorunlu - Alipay genel anahtar sertifikası yolu
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Zorunlu - Alipay kök sertifikası yolu
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Seçimlik - Senkronize geri çağrı adresi
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Seçimlik - Asenkron geri çağrı adresi
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Seçimlik - Hizmet sağlayıcı kimliği, mod Pay::MODE_SERVICE olduğunda bu parametre kullanılır
            'service_provider_id' => '',
            // Seçimlik - Varsayılan normal mod. Şunlar için seçeneklidir: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Zorunlu - Tüccar numarası, hizmet sağlayıcı modunda tüccar numarası
            'mch_id' => '',
            // Zorunlu - Tüccar gizli anahtarı
            'mch_secret_key' => '',
            // Zorunlu - Tüccar özel anahtar dize veya yol
            'mch_secret_cert' => '',
            // Zorunlu - Tüccar genel anahtar sertifikası yolu
            'mch_public_cert_path' => '',
            // Zorunlu
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Seçimlik - Halka açık numara uygulaması app_id
            'mp_app_id' => '2016082000291234',
            // Seçimlik - Mini uygulama app_id
            'mini_app_id' => '',
            // Seçimlik - app app_id
            'app_id' => '',
            // Seçimlik - Birleştirme app_id
            'combine_app_id' => '',
            // Seçimlik - Birleştirme tüccar numarası
            'combine_mch_id' => '',
            // Seçimlik - Hizmet sağlayıcı modunda, alt halka açık numarası app_id
            'sub_mp_app_id' => '',
            // Seçimlik - Hizmet sağlayıcı modunda, alt app app_id
            'sub_app_id' => '',
            // Seçimlik - Hizmet sağlayıcı modunda, alt mini uygulaması app_id
            'sub_mini_app_id' => '',
            // Seçimlik - Hizmet sağlayıcı modunda, alt tüccar kimliği
            'sub_mch_id' => '',
            // Seçimlik - WeChat genel anahtar sertifikası yolu, isteğe bağlı, php-fpm modunda bu parametrenin yapılandırılması şiddetle tavsiye edilir
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Seçimlik - Varsayılan normal mod. Şunlar için seçeneklidir: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Üretim ortamı için önerilen düzey info, geliştirme ortamı için debug
        'type' => 'single', // isteğe bağlı, günlük olarak seçeneklidir.
        'max_file' => 30, // isteğe bağlı, tip günlük ise etkin, varsayılan 30 gün
    ],
    'http' => [ // isteğe bağlı
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Daha fazla yapılandırma öğesi için [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)'a bakınız
    ],
    '_force' => true,
];
```
> Not: Sertifika dizini belirtilmemiştir, yukarıdaki örnek, çerçeve içindeki `payment` dizinine yerleştirilmiştir.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## Başlatma
Doğrudan `config` yöntemini çağırarak başlatın
```php
// Yapılandırma dosyasını config/payment.php al
$config = Config::get('payment');
Pay::config($config);
```
> Not: Alipay sandbox modu ise, mutlaka yapılandırma dosyasında `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` opsiyonunu açık olduğundan emin olun, bu seçenek varsayılan olarak normal mod içindir.

## Ödeme (Web)
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
    // 1. Yapılandırma dosyasını config/payment.php al
    $config = Config::get('payment');

    // 2. Yapılandırmayı başlat
    Pay::config($config);

    // 3. Web ödeme
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman payment',
        '_method' => 'get' // Get yöntemi kullanarak yönlendirin
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```
## Asenkron Geri Arama

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: 'Alipay' Asenkron Bildirim
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Yapılandırma dosyasını al config/payment.php
    $config = Config::get('payment');

    // 2. Yapılandırmayı başlat
    Pay::config($config);

    // 3. Alipay geri arama işleme
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // trade_status'u kendiniz kontrol edin ve diğer mantığı kontrol edin, sadece işlem bildirimi durumu TRADE_SUCCESS veya TRADE_FINISHED olduğunda Alipay alıcı ödemenin başarılı olduğunu kabul eder.
    // 1. Tüccarın bu bildirim verilerinde out_trade_no'nun tüccar sisteminde oluşturulan sipariş numarası olup olmadığını doğrulaması gerekir;
    // 2. total_amount'ın gerçekten bu siparişin gerçek tutarı olup olmadığını kontrol edin (yani, tüccar siparişi oluştururkenki tutar);
    // 3. out_trade_no bu belgenin karşılık gelen işlem kaydının işlem yapma tarafı olup olmadığını kontrol edin;
    // 4. app_id'nin bu tüccarın kendisi olup olmadığını onaylayın.
    // 5. Diğer iş mantığı durumları
    // ===================================================================================================

    // 5. Alipay geri arama işleme
    return new Response(200, [], 'başarılı');
}
```
> Not: Alipay geri aramayı yanıtlamak için 'return Pay::alipay()->success();' gibi bir eklenti kullanamazsınız, bunu yaparsanız middleware sorunları ortaya çıkar. Bu nedenle Alipay yanıtlaması için webman'ın response sınıfını `support\Response;` kullanmanız gerekmektedir.

## Senkron Geri Arama

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『Alipay』Senkron Bildirim
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『Alipay』Senkron Bildirim'.json_encode($request->get()));
    return 'başarılı';
}
```

## Tam Örnek Kod

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Daha Fazla Bilgi

Resmi dökümantasyona göz atın: https://pay.yansongda.cn/docs/v3/
