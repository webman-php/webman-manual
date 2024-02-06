# SDK de pago (V3)

## Dirección del proyecto

 https://github.com/yansongda/pay

## Instalación

```php
composer require yansongda/pay ^3.0.0
```

## Uso

> Nota: El documento se redactará en el entorno de Alipay Sandbox, si hay algún problema, ¡infórmanos de inmediato!

### Archivo de configuración

Supongamos que hay un archivo de configuración llamado `config/payment.php`

```php
<?php
/**
 * @desc Archivo de configuración de pago
 * Autor Tinywan(ShaoBo Wan)
 * Fecha 11/03/2022 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Obligatorio: app_id asignado por Alipay
            'app_id' => '20160909004708941',
            // Obligatorio: clave privada de la aplicación como cadena o ruta
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Obligatorio: ruta del certificado público de la aplicación
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Obligatorio: ruta del certificado público de Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Obligatorio: ruta del certificado raíz de Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Opcional: dirección de retorno sincrónico
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Opcional: dirección de retorno asincrónico
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Opcional: id del proveedor de servicios en el modo de proveedor de servicios, se utiliza este parámetro cuando el modo es Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Opcional: por defecto es el modo normal. Puede ser: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Obligatorio: número de comerciante, en modo de proveedor de servicios es el número de comerciante del proveedor de servicios
            'mch_id' => '',
            // Obligatorio: clave secreta del comerciante
            'mch_secret_key' => '',
            // Obligatorio: clave privada del comerciante como cadena o ruta
            'mch_secret_cert' => '',
            // Obligatorio: ruta del certificado público del comerciante
            'mch_public_cert_path' => '',
            // Obligatorio
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Opcional: app_id del servicio público
            'mp_app_id' => '2016082000291234',
            // Opcional: app_id de la aplicación pequeña
            'mini_app_id' => '',
            // Opcional: app_id de la aplicación
            'app_id' => '',
            // Opcional: app_id combinado
            'combine_app_id' => '',
            // Opcional: número de comerciante combinado
            'combine_mch_id' => '',
            // Opcional: modo de servicio público, app_id de la subcuenta pública
            'sub_mp_app_id' => '',
            // Opcional: modo de servicio público, app_id de la sub-aplicación
            'sub_app_id' => '',
            // Opcional: modo de servicio público, app_id de la sub aplicación pequeña
            'sub_mini_app_id' => '',
            // Opcional: modo de servicio público, id del subcomerciante
            'sub_mch_id' => '',
            // Opcional: ruta del certificado público de WeChat, opcional, se recomienda configurar este parámetro en modo php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Opcional: por defecto es el modo normal. Puede ser: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Se recomienda ajustar el nivel a info para entornos de producción y a debug para entornos de desarrollo
        'type' => 'single', // opcional, diario es una opción.
        'max_file' => 30, // opcional, válido cuando el tipo es diario, por defecto 30 días
    ],
    'http' => [ // opcional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Para más opciones de configuración, consulta [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Nota: No hay un directorio de certificados especificado, el ejemplo anterior se encuentra en el directorio de `payment` del framework

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Inicialización

Inicializar directamente llamando al método `config`
```php
// Obtener el archivo de configuración config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Nota: Si está en modo sandbox de Alipay, asegúrate de activar la configuración `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,` en el archivo de configuración. Esta opción está configurada por defecto en el modo normal.

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

    // 3. Pago en el sitio web
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'Pago en webman',
        '_method' => 'get' // Usar el método get para redirigir
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Llamada de retorno

#### Llamada de retorno asincrónica

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: Notificación asincrónica de 'Alipay'
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Obtener el archivo de configuración config/payment.php
    $config = Config::get('payment');

    // 2. Inicializar la configuración
    Pay::config($config);

    // 3. Procesar la llamada de retorno de Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Por favor, realice su propia comprobación del estado de la transacción y otras lógicas. Alipay solo reconocerá el pago del comprador cuando el estado de la notificación de transacción es TRADE_SUCCESS o TRADE_FINISHED.
    // 1. El comerciante debe verificar si out_trade_no en los datos de la notificación es el número de pedido creado en el sistema del comerciante;
    // 2. Verifique si total_amount es el monto real de la orden (es decir, el monto de la orden creada por el comerciante);
    // 3. Verifique si seller_id (o seller_email) en la notificación corresponde al operador de este pedido out_trade_no;
    // 4. Verifique si app_id corresponde al propio comerciante.
    // 5. Otras lógicas comerciales
    // ===================================================================================================

    // 5. Procesar la llamada de retorno de Alipay
    return new Response(200, [], 'success');
}
```
> Nota: No se puede utilizar el método `return Pay::alipay()->success();` del plugin para responder a la llamada de retorno de Alipay. Si se utiliza un middleware, se presentarán problemas con el middleware. Por lo tanto, al responder a Alipay, se debe usar la clase de respuesta de webman `support\Response;`

#### Llamada de retorno sincrónica

```php
uso support\Request;
uso Yansongda\Pay\Pay;

/**
 * @desc: Notificación sincrónica de 'Alipay'
 * @param Request $request
 * Autor Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Notificación sincrónica de "Alipay"'.json_encode($request->get()));
    return 'success';
}
```

## Código completo de ejemplo

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Más contenido

Visite la documentación oficial en https://pay.yansongda.cn/docs/v3/
