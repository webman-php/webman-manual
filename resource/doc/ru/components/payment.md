# SDK для оплаты (V3)


## Ссылка на проект

https://github.com/yansongda/pay

## Установка

```php
composer require yansongda/pay ^3.0.0
```

## Использование

> Примечание: Документация составлена для среды песочницы Alipay. Если у вас возникли проблемы, пожалуйста, дайте нам знать!

### Файл конфигурации

Предположим, у нас есть следующий файл конфигурации `config/payment.php`.

```php
<?php
/**
 * @desc Файл настроек платежей
 * @author Tinywan(ShaoBo Wan)
 * @date 2022/03/11 20:15
 */
return [
    'alipay' => [
        'default' => [
            // Обязательный - выданный Alipay идентификатор приложения
            'app_id' => '20160909004708941',
            // Обязательный - строка закрытого ключа приложения или путь
            'app_secret_cert' => 'MIIEpAIBAAKCxxxxxxxxxxxxxxP4r3m4OUmD/+XDgCg==',
            // Обязательный - путь к открытому сертификату приложения
            'app_public_cert_path' => base_path().'/payment/appCertPublicKey_2016090900470841.crt',
            // Обязательный - путь к открытому сертификату Alipay
            'alipay_public_cert_path' => base_path().'/payment/alipayCertPublicKey_RSA2.crt',
            // Обязательный - путь к корневому сертификату Alipay
            'alipay_root_cert_path' => base_path().'/payment/alipayRootCert.crt',
            // Необязательный - URL для синхронного обратного вызова
            'return_url' => 'https://webman.tinywan.cn/payment/alipay-return',
            // Необязательный - URL для асинхронного обратного вызова
            'notify_url' => 'https://webman.tinywan.cn/payment/alipay-notify',
            // Необязательный - идентификатор провайдера услуг в режиме поставщика услуг, используемый при mode = Pay::MODE_SERVICE
            'service_provider_id' => '',
            // Необязательный - по умолчанию используется нормальный режим. Доступно: MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'default' => [
            // Обязательный - номер торгового представительства, в режиме поставщика услуг это номер торгового представительства поставщика услуг
            'mch_id' => '',
            // Обязательный - ключ торгового представительства
            'mch_secret_key' => '',
            // Обязательный - строка закрытого ключа торгового представительства или путь
            'mch_secret_cert' => '',
            // Обязательный - путь к открытому сертификату торгового представительства
            'mch_public_cert_path' => '',
            // Обязательный
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // Необязательный - app_id публичного аккаунта
            'mp_app_id' => '2016082000291234',
            // Необязательный - app_id мини-приложения
            'mini_app_id' => '',
            // Необязательный - app_id приложения
            'app_id' => '',
            // Необязательный - app_id объединенного приложения
            'combine_app_id' => '',
            // Необязательный - номер торгового представительства в объединенном заказе
            'combine_mch_id' => '',
            // Необязательный - в режиме поставщика услуг, app_id дочернего публичного аккаунта
            'sub_mp_app_id' => '',
            // Необязательный - в режиме поставщика услуг, app_id дочернего приложения
            'sub_app_id' => '',
            // Необязательный - в режиме поставщика услуг, app_id дочернего мини-приложения
            'sub_mini_app_id' => '',
            // Необязательный - в режиме поставщика услуг, идентификатор дочернего торгового представительства
            'sub_mch_id' => '',
            // Необязательный - путь к открытому сертификату WeChat, опционально, настоятельно рекомендуется настроить этот параметр в режиме php-fpm
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
            ],
            // Необязательный - по умолчанию используется нормальный режим. Доступно: MODE_NORMAL, MODE_SERVICE
            'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,
        ]
    ],
    'logger' => [
        'enable' => false,
        'file' => runtime_path().'/logs/alipay.log',
        'level' => 'debug', // Для уровня продакшена рекомендуется установить уровень info, для разработки — debug
        'type' => 'single', // опционально, доступно ежедневно
        'max_file' => 30, // опционально, действительно, если тип установлен ежедневно, по умолчанию 30 дней
    ],
    'http' => [ // опционально
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // Более подробную информацию о конфигурации можно найти [здесь](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true,
];
```
> Примечание: В приведенном выше примере для каталога сертификатов нет строгого назначения, образец выше размещен в каталоге `payment` фреймворка.

```php
├── payment
│   ├── alipayCertPublicKey_RSA2.crt
│   ├── alipayRootCert.crt
│   └── appCertPublicKey_2016090900470841.crt
```

### Инициализация

Просто вызовите метод `config` для инициализации
```php
// Получить файл конфигурации config/payment.php
$config = Config::get('payment');
Pay::config($config);
```
> Примечание: Если это режим песочницы Alipay, необходимо обязательно включить опцию в конфигурационном файле `'mode' => \Yansongda\Pay\Pay::MODE_SANDBOX,`, по умолчанию это нормальный режим.

### Оплата (веб-страница)

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
    // 1. Получить файл конфигурации config/payment.php
    $config = Config::get('payment');

    // 2. Инициализировать конфигурацию
    Pay::config($config);

    // 3. Оплата через веб-страницу
    $order = [
        'out_trade_no' => time(),
        'total_amount' => '8888.88',
        'subject' => 'оплата через webman',
        '_method' => 'get' // использовать метод get для перенаправления
    ];
    return Pay::alipay()->web($order)->getBody()->getContents();
}
```

### Обратный вызов

#### Асинхронный обратный вызов

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
    // 1. Получить файл конфигурации config/payment.php
    $config = Config::get('payment');

    // 2. Инициализировать конфигурацию
    Pay::config($config);

    // 3. Обработка обратного вызова от 'Alipay'
    $result = Pay::alipay()->callback($request->post());

    // ===================================================================================================
    // Пожалуйста, самостоятельно проверьте статус операции и прочие логические условия. Только если статус торговой операции равен TRADE_SUCCESS или TRADE_FINISHED, 'Alipay' считает, что покупатель успешно оплатил.
    // 1. Требуется проверка, является ли out_trade_no в уведомлении номером заказа, созданным в системе продавца;
    // 2. Проверка: является ли total_amount фактической суммой этого заказа (т.е. суммой заказа продавца в момент создания);
    // 3. Проверка, является ли seller_id (или seller_email) в уведомлении соответствующим полем out_trade_no в этой операции;
    // 4. Проверка, является ли app_id тем же самым продавцом.
    // 5. Остальные логические условия
    // ===================================================================================================

    // 4. Обработка обратного вызова от 'Alipay'
    return new Response(200, [], 'успех');
}
```
> Примечание: Нельзя использовать метод `return Pay::alipay()->success();` для ответа на обратный вызов 'Alipay', поскольку это может вызвать проблемы с промежуточным программным обеспечением. Поэтому для ответа на обратный вызов 'Alipay' необходимо использовать класс ответа webman `support\Response;`

#### Синхронный обратный вызов

```php
use support\Request;
use Yansongda\Pay\Pay;

/**
 * @desc: Синхронное уведомление от 'Alipay'
 * @param Request $request
 * @author Tinywan(ShaoBo Wan)
 */
public function alipayReturn(Request $request)
{
    Log::info('Синхронное уведомление от 'Alipay' '.json_encode($request->get()));
    return 'успех';
}
```

## Полный пример кода

https://github.com/Tinywan/webman-admin/blob/main/app/controller/Test.php

## Дополнительная информация

Посетите официальную документацию https://pay.yansongda.cn/docs/v3/
