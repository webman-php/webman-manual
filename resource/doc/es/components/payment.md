# SDK de pago (V3)

## Dirección del proyecto

https://github.com/yansongda/pay

## Instalación

```php
composer require yansongda/pay ^3.0.0
```

## Uso

> Nota: El siguiente documento se redacta utilizando el entorno de sandbox de Alipay. Si hay algún problema, ¡háganoslo saber de inmediato!

### Archivo de configuración

Supongamos que existe el siguiente archivo de configuración `config/payment.php`

```php
<?php
/**
 * @desc Archivo de configuración de pago
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Obligatorio - app_id asignado por Alipay
            'app_id' => '20160909004708941',
            // Obligatorio - Clave privada de la aplicación (cadena o ruta)
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obligatorio - Ruta del certificado público de la aplicación
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obligatorio - Ruta del certificado público de Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obligatorio - Ruta del certificado raíz de Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Opcional - URL de retorno síncrono
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Opcional - URL de retorno asincrónico
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Opcional - ID del proveedor de servicios en el modo de proveedor de servicios, se utiliza este parámetro cuando el modo es Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Opcional - Por defecto es el modo normal. Puede ser: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obligatorio - Número de comerciante, en el modo de proveedor de servicios es el número de comerciante del proveedor de servicios
            'mch_id' => '',
            // Obligatorio - Clave secreta del comerciante
            'mch_secret_key' => '',
            // Obligatorio - Ruta de la clave privada del comerciante (cadena o ruta)
            'mch_secret_cert' => '',
            // Obligatorio - Ruta del certificado público del comerciante
            'mch_public_cert_path' => '',
            // Obligatorio
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Opcional - app_id del servicio público
            'mp_app_id' => '2016082000291234',
            // Opcional - app_id del mini programa
            'mini_app_id' => '',
            // Opcional - app_id de la aplicación
            'app_id' => '',
            // Opcional - app_id combinado
            'combine_app_id' => '',
            // Opcional - Número de comerciante combinado
            'combine_mch_id' => '',
            // Opcional - En el modo de proveedor de servicios, app_id del servicio público secundario
            'sub_mp_app_id' => '',
            // Opcional - En el modo de proveedor de servicios, app_id de la aplicación secundaria
            'sub_app_id' => '',
            // Opcional - En el modo de proveedor de servicios, app_id del mini programa secundario
            'sub_mini_app_id' => '',
            // Opcional - En el modo de proveedor de servicios, ID del comerciante secundario
            'sub_mch_id' => '',
            // Opcional - Ruta del certificado público de WeChat, opcional, se recomienda encarecidamente configurar este parámetro en el modo php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Opcional - Por defecto es el modo normal. Puede ser: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Se recomienda ajustar el nivel a info en entornos de producción y a debug en entornos de desarrollo
        'type' => 'single', // opcional, puede ser diario
        'max_file' => 30, // opcional, efectivo sólo cuando el tipo es diario, por defecto 30 días
    ],
    'http' => [ // opcional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Para más opciones de configuración, consulte [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Nota: No se especifica el directorio de los certificados, el ejemplo anterior los coloca en el directorio `payment` del marco

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Inicialización

Llame directamente al método `config` para inicializar
```php
// Obtener el archivo de configuración config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Nota: Si está en modo sandbox de Alipay, asegúrese de habilitar la opción en el archivo de configuración `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, esta opción por defecto es el modo normal.

### Pago (sitio web)

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
    // 1. Obtener el archivo de configuración config/payment.php
    $config = Config::get('payment');

    // 2. Inicializar la configuración
    Pay::config($config);

    // 3. Pago en línea
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'pago webman',
        '_method' => 'get' // utilizar método get para redireccionar
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Devolución

#### Devolución asincrónica

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: Notificación asincrónica de Alipay
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Obtener el archivo de configuración config/payment.php
    $config = Config::get('payment');

    // 2. Inicializar la configuración
    Pay::config($config);

    // 3. Procesar la devolución de Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Realice la verificación del estado de la transacción y otras lógicas. Alipay considerará que el pago del comprador ha sido exitoso sólo si el estado de la transacción es TRADE_SUCCESS o TRADE_FINISHED.
    // 1. El comerciante debe verificar si out_trade_no en los datos de notificación es el número de pedido creado en el sistema del comerciante;
    // 2. Verifique si total_amount es realmente el monto real de la orden (es decir, el monto cuando se creó la orden del comerciante);
    // 3. Verifique si seller_id (o seller_email) en la notificación es la parte correspondiente al pedido out_trade_no;
    // 4. Verifique si app_id es la propia del comerciante.
    // 5. Otras lógicas comerciales
    // ===================================================================================================

    // 5. Procesar la devolución de Alipay
    return new Response(200, [], 'success');
}
```
> Nota: No se puede utilizar el método `return Pay::alipay()->success();` del complemento para responder a la devolución de Alipay. Si utiliza middleware, podría surgir un problema con el middleware. Por lo tanto, para responder a Alipay, es necesario utilizar la clase de respuesta de webman `support\Response;`

#### Devolución sincrónica

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Notificación sincrónica de Alipay
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Notificación sincrónica de Alipay' . json_encode($request->get()));
    return 'success';
}
```

## Código completo de ejemplo

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Para más información

Visite la documentación oficial https://pay.yansongda.cn/docs/v3/
