# Ödeme SDK'sı (V3)

## Proje Adresi

https://github.com/yansongda/pay

## Kurulum

```php
composer require yansongda/pay ^3.0.0
```

## Kullanım

> Not: Aşağıdaki dökümanlar için ödeme altyapısı olarak Alipay'in sandbox ortamını kullanarak yazılmıştır, herhangi bir sorun için lütfen geri bildirimde bulunun!

### Yapılandırma Dosyası

Aşağıdaki yapılandırma dosyası `config/payment.php` olarak var kabul edilir.

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
            // Zorunlu - Alipay tarafından atanmış app_id
            'app_id' => '20160909004708941',
            // Zorunlu- Uygulama özel anahtarı Dize veya yolu
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Zorunlu- Uygulama genel anahtar sertifikası yolu
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Zorunlu- Alipay genel anahtar sertifikası yolu
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Zorunlu- Alipay kök sertifikası yolu
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Opsiyonel- Senkron geri çağırma adresi
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Opsiyonel- Asenkron geri çağırma adresi
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Opsiyonel- MODE_SERVICE modu için hizmet sağlayıcı kimliği, mode Pay::MODE_SERVICE olduğunda bu parametre kullanılır
            'service_provider_id' => '',
            // Opsiyonel- Varsayılan olarak normal mod. Seçenekler: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Zorunlu- Satıcı numarası, hizmet sağlayıcı modunda satıcı numarası olacak
            'mch_id' => '',
            // Zorunlu- Satıcı gizli anahtarı
            'mch_secret_key' => '',
            // Zorunlu- Satıcı özel anahtar Dize veya yolu
            'mch_secret_cert' => '',
            // Zorunlu- Satıcı genel anahtar sertifikası yolu
            'mch_public_cert_path' => '',
            // Zorunlu
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Opsiyonel- Halka açık uygulama kimliği
            'mp_app_id' => '2016082000291234',
            // Opsiyonel- Mini uygulama kimliği
            'mini_app_id' => '',
            // Opsiyonel- Uygulama kimliği
            'app_id' => '',
            // Opsiyonel- Kombine uygulama kimliği
            'combine_app_id' => '',
            // Opsiyonel- Kombine satıcı numarası
            'combine_mch_id' => '',
            // Opsiyonel- Hizmet sağlayıcı modunda, alt halka açık uygulama kimliği
            'sub_mp_app_id' => '',
            // Opsiyonel- Hizmet sağlayıcı modunda, alt uygulama kimliği
            'sub_app_id' => '',
            // Opsiyonel- Hizmet sağlayıcı modunda, alt mini uygulama kimliği
            'sub_mini_app_id' => '',
            // Opsiyonel- Hizmet sağlayıcı modunda, alt satıcı numarası
            'sub_mch_id' => '',
            // Opsiyonel- Wechat genel anahtar sertifikası yolu, isteğe bağlı, php-fpm modunda bu parametrenin yapılandırılmasını şiddetle öneririz
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Opsiyonel- Varsayılan olarak normal mod. Seçenekler: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Üretim ortamında seviye ayarı olarak info önerilir, geliştirme ortamında debug olabilir
        'type' => 'single', // Opsiyonel, seçenek günlük.
        'max_file' => 30, // Opsiyonel, tür günlük olduğunda geçerli, varsayılan 30 gün
    ],
    'http' => [ // isteğe bağlı
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Daha fazla yapılandırma seçenekleri için [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html) adresine bakınız
    ],
    '_force' => true,
];
```
> Not: Sertifika dizini belirtilmemiştir, yukarıdaki örnekte çerçevenin `payment` dizini altında olduğu varsayılmıştır.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Başlatma

`config` yöntemini kullanarak doğrudan yapılandırma yapınız.

```php
// Yapılandırma dosyası config/payment.php al
$config = Config::get('payment');
Pay::config($config);
```
> Not: Eğer Alipay sandbox modu kullanılıyorsa, mutlaka yapılandırma dosyasında `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` seçeneğini açık olarak bırakmayı unutmayın, bu seçenek varsayılan olarak normal moda ayarlanmıştır.

### Ödeme (Web)

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
    // 1. Yapılandırma dosyası config/payment.php al
    $config = Config::get('payment');

    // 2. Yapılandırmayı başlat
    Pay::config($config);

    // 3. Web ödeme
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'webman ödeme',
        '_method' => 'get' // Get yöntemi kullan
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Geri Arama

#### Asenkron Geri Arama

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc:『Alipay』Asenkron bildirim
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Yapılandırma dosyası config/payment.php al
    $config = Config::get('payment');

    // 2. Yapılandırmayı başlat
    Pay::config($config);

    // 3. Alipay geri çağırma işlemi
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Lütfen trade_status'ü ve diğer durumları kendi başınıza kontrol edin. Sadece işlem bildirimi TRADE_SUCCESS veya TRADE_FINISHED ise, Alipay ödemenin başarıyla yapıldığını kabul edecektir.
    // 1. Satıcının bu bildirim verisindeki out_trade_no'nun, satıcı sisteminde oluşturulan sipariş numarası olup olmadığını doğrulaması gerekmektedir;
    // 2. total_amount'ın gerçekte bu siparişin gerçek tutarı olup olmadığı kontrol edilmelidir (yani, satıcı sipariş oluştururken belirtilen tutar);
    // 3. seller_id (veya seller_email)'nın out_trade_no bu belgenin karşılık gelen işlem tarafı olup olmadığının doğrulanması gerekmektedir;
    // 4. app_id'nin satıcının kendi app_id'si olup olmadığı kontrol edilmelidir.
    // 5. Diğer iş mantığı durumları
    // ===================================================================================================

    // 5. Alipay geri çağırma işlemi
    return new Response(200, [], 'success');
}
```

> Not: Alipay geri bildirimini cevaplamak için, lütfen eklentiyi `return Pay::alipay()->success();` kullanmayın, middleware kullanıyorsanız bir middleware sorunu ortaya çıkabilir. Bu yüzden Alipay geri bildirimine webman'ın yanıt sınıfı `support\Response;` kullanmanız gerekmektedir.

#### Senkron Geri Arama

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: 『Alipay』Senkron bildirim
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('『Alipay』Senkron bildirim'.json_encode($request->get()));
    return 'success';
}
```

## Tam Örnek Kodu

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Daha Fazla İçerik

Resmi dökümantasyon için ziyaret edin: https://pay.yansongda.cn/docs/v3/
