# Ограничитель частоты запросов

Ограничитель частоты webman с поддержкой ограничения через аннотации.
Поддерживает драйверы apcu, redis и memory.

## Исходный код

https://github.com/webman-php/limiter

## Установка

```
composer require webman/limiter
```

## Использование

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // По умолчанию ограничение по IP, временное окно по умолчанию 1 секунда
        return 'Максимум 10 запросов на IP в секунду';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, ограничение по ID пользователя, требуется session('user.id') не пустая
        return 'Максимум 100 поисков на пользователя за 60 секунд';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Только 1 email на человека в минуту')]
    public function sendMail(): string
    {
        // key: Limit::SID, ограничение по session_id
        return 'Email успешно отправлен';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Сегодняшние купоны закончились, попробуйте завтра')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Каждый пользователь может получить только один купон в день')]
    public function coupon(): string
    {
        // key: 'coupon', пользовательский ключ для глобального ограничения, макс 100 купонов в день
        // Также ограничение по ID пользователя, каждый пользователь один купон в день
        return 'Купон успешно отправлен';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Максимум 5 SMS на номер в день')]
    public function sendSms2(): string
    {
        // Когда key переменная: [класс, статический_метод], напр. [UserController::class, 'getMobile'] использует возвращаемое значение UserController::getMobile() как ключ
        return 'SMS успешно отправлено';
    }

    /**
     * Пользовательский ключ, получить номер телефона, должен быть статический метод
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Частота ограничена', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Исключение по умолчанию при превышении: support\limiter\RateLimitException, изменяемо через параметр exception
        return 'ok';
    }

}
```

**Примечания**

* Использует алгоритм фиксированного окна
* Временное окно ttl по умолчанию: 1 секунда
* Установить окно через ttl, напр. `ttl:60` для 60 секунд
* Размерность ограничения по умолчанию: IP (по умолчанию `127.0.0.1` не ограничен, см. конфигурацию ниже)
* Встроено: ограничение IP, UID (требует `session('user.id')` не пустую), SID (по `session_id`)
* При использовании nginx-прокси передавать заголовок `X-Forwarded-For` для ограничения по IP, см. [nginx прокси](../others/nginx-proxy.md)
* Вызывает `support\limiter\RateLimitException` при превышении, пользовательский класс исключения через `exception:xx`
* Сообщение об ошибке по умолчанию при превышении: `Too Many Requests`, пользовательское сообщение через `message:xx`
* Сообщение об ошибке по умолчанию изменяемо через [перевод](translation.md), ссылка Linux:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

Иногда разработчики хотят вызывать ограничитель напрямую в коде, см. следующий пример:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile используется как ключ здесь
        Limiter::check($mobile, 5, 24*60*60, 'Максимум 5 SMS на номер в день');
        return 'SMS успешно отправлено';
    }
}
```

## Конфигурация

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Эти IP не ограничиваются (действует только когда key Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Включить ограничение частоты
* **driver**: Один из `auto`, `apcu`, `memory`, `redis`; `auto` автоматически выбирает между `apcu` (приоритет) и `memory`
* **stores**: Конфигурация Redis, `connection` соответствует ключу в `config/redis.php`
* **ip_whitelist**: IP в белом списке не ограничиваются (действует только когда key `Limit::IP`)

## Выбор драйвера

**memory**

* Введение
  Расширения не требуются, лучшая производительность.

* Ограничения
  Ограничение только для текущего процесса, нет совместного использования данных между процессами, ограничение в кластере не поддерживается.

* Сценарии использования
  Среда разработки Windows; сценарии без строгого ограничения; защита от CC-атак.

**apcu**

* Установка расширения
  Требуется расширение apcu, настройки php.ini:

```
apc.enabled=1
apc.enable_cli=1
```

Расположение php.ini с помощью `php --ini`

* Введение
  Отличная производительность, поддерживает совместное использование между процессами.

* Ограничения
  Кластер не поддерживается

* Сценарии использования
  Любая среда разработки; ограничение одного сервера в продакшене; кластер без строгого ограничения; защита от CC-атак.

**redis**

* Зависимости
  Требуются расширение redis и компонент Redis, установка:

```
composer require -W webman/redis illuminate/events
```

* Введение
  Производительность ниже apcu, поддерживает точное ограничение одного сервера и кластера

* Сценарии использования
  Среда разработки; один сервер в продакшене; среда кластера
