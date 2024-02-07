# SDK для оплаты (V3)

## Адрес проекта
https://github.com/yansongda/pay

## Установка

```php
composer require yansongda/pay ^3.0.0
```

## Использование

> Обратите внимание: документация составлена для среды тестирования Alipay, если у вас возникнут проблемы, пожалуйста, дайте знать!

## Файл конфигурации

Предположим, у вас есть следующий файл конфигурации `config/payment.php`

```php
<?php
/**
 * @desc Файл конфигурации для оплаты
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Обязательно - app_id, выданный Alipay
            'app_id' => '20160909004708941',
            // Обязательно - приватный ключ приложения, строка или путь
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Обязательно - путь к общедоступному сертификату приложения
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Обязательно - путь к общедоступному сертификату Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Обязательно - путь к корневому сертификату Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Необязательно - URL для синхронного вызова
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Необязательно - URL для асинхронного вызова
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Необязательно - id поставщика услуг в режиме поставщика услуг, используется при mode равном Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Необязательно - по умолчанию нормальный режим. Может быть: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Обязательно - идентификатор продавца, в режиме поставщика услуг - идентификатор продавца поставщика услуг
            'mch_id' => '',
            // Обязательно - секретный ключ продавца
            'mch_secret_key' => '',
            // Обязательно - приватный ключ продавца, строка или путь
            'mch_secret_cert' => '',
            // Обязательно - путь к общедоступному сертификату продавца
            'mch_public_cert_path' => '',
            // Обязательно
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Необязательно - app_id для общедоступной публичной акции
            'mp_app_id' => '2016082000291234',
            // Необязательно - app_id для мини-приложения
            'mini_app_id' => '',
            // Необязательно - app_id для приложения
            'app_id' => '',
            // Необязательно - app_id для объединения
            'combine_app_id' => '',
            // Необязательно - идентификатор продавца для объединения
            'combine_mch_id' => '',
            // Необязательно - в режиме поставщика услуг, app_id для дочерней публичной акции
            'sub_mp_app_id' => '',
            // Необязательно - в режиме поставщика услуг, app_id для дочернего приложения
            'sub_app_id' => '',
            // Необязательно - в режиме поставщика услуг, app_id для дочернего мини-приложения
            'sub_mini_app_id' => '',
            // Необязательно - в режиме поставщика услуг, идентификатор продавца для дочерних
            'sub_mch_id' => '',
            // Необязательно - путь к общедоступному сертификату WeChat, необязательно, но настоятельно рекомендуется настроить этот параметр, особенно в режиме php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Необязательно - по умолчанию нормальный режим. Может быть: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Рекомендуется уровень отладки для производственной среды и уровень отладки для разработки
        'type' => 'single', // необязательно, может быть ежедневно.
        'max_file' => 30, // необязательно, действует при daily по умолчанию 30 дней
    ],
    'http' => [ // необязательно
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // для дополнительных параметров конфигурации см. [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```

> Обратите внимание: в приведенном примере не оговорено местоположение файлов сертификатов, они помещены в каталог `payment` фреймворка

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

## Инициализация

Инициализируем прямым вызовом метода `config`

```php
// Получаем файл конфигурации config/payment.php
$config = Config::get('payment');
Pay::config($config);
```

> Обратите внимание: если установлен режим песочницы Alipay, не забудьте включить опцию конфигурации `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, по умолчанию это нормальный режим.

## Оплата (веб)

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
    // 1. Получаем файл конфигурации config/payment.php
    $config = Config::get('payment');

    // 2. Инициализируем конфигурацию
    Pay::config($config);

    // 3. Оплата через веб
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'оплата webman',
        '_method' => 'get' // используем метод get для перехода
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

## Асинхронный вызов

```php
use support\Request;
use Webman\Config;
use Yansongda\Pay\Pay;

/**
 * @desc: Асинхронное уведомление от 'Alipay'
 * @param Request $request
 * @return Response
 */
public function alipayNotify(Request $request): Response
{
    // 1. Получаем файл конфигурации config/payment.php
    $config = Config::get('payment');

    // 2. Инициализируем конфигурацию
    Pay::config($config);

    // 3. Обработка асинхронного вызова от Alipay
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Пожалуйста, сами проверьте trade_status и другую логику, связанную с ним, только при статусе уведомления о сделке TRADE_SUCCESS или TRADE_FINISHED Alipay будет рассматривать оплату покупателя как успешную.
    // 1. Мерчант должен проверить, желаемый ли out_trade_no совпадает с номером заказа, созданным в системе мерчанта.
    // 2. Проверьте, действительно ли total_amount является фактической суммой этого заказа (т.е. сумма заказа мерчанта при его создании).
    // 3. Проверьте, совпадает ли seller_id (или seller_email) в уведомлении с id (или email) операционной стороны этого заказа out_trade_no.
    // 4. Проверьте, является ли app_id фактически для этого мерчанта.
    // 5. Другая бизнес-логика
    // ===================================================================================================

// 5. Обработка асинхронного вызова от Alipay
return new Response(200, [], 'success');
}
```
> Обратите внимание: не используйте в ответ на асинхронный вызов от Alipay функцию `return Pay::alipay()->success();`, так как это вызовет проблемы с посредником. Поэтому для ответа на уведомление от Alipay требуется использовать класс ответа `support\Response;`.
## Синхронный обратный вызов

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: "Alipay" синхронное уведомление
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Синхронное уведомление "Alipay"'.json_encode($request->get()));
    return 'success';
}
```

## Полный пример кода

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Больше информации

Посетите официальную документацию по ссылке https://pay.yansongda.cn/docs/v3/
