# Библиотека управления доступом Casbin webman-permission

## Описание

Он основан на [PHP-Casbin](https://github.com/php-casbin/php-casbin), мощном и эффективном фреймворке управления доступом с открытым исходным кодом, который поддерживает модели управления доступом, такие как `ACL`, `RBAC`, `ABAC` и другие.

## Адрес проекта

https://github.com/Tinywan/webman-permission

## Установка

```php
composer require tinywan/webman-permission
```
> Это расширение требует PHP 7.1+ и [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998), официальная документация: https://www.workerman.net/doc/webman#/db/others

## Настройка

### Регистрация сервиса
Создайте файл конфигурации `config/bootstrap.php` с похожим содержимым:

```php
    // ...
    webman\permission\Permission::class,
```

### Файл конфигурации модели

Создайте файл конфигурации `config/casbin-basic-model.conf` с похожим содержимым:

```conf
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
```

### Файл конфигурации политики

Создайте файл конфигурации `config/permission.php` с похожим содержимым:

```php
<?php

return [
    /*
     *Default  Permission
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Настройка модели
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Адаптер
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Настройки базы данных
            */
            'database' => [
                // Имя подключения к базе данных, если не заполнено, используется конфигурация по умолчанию.
                'connection' => '',
                // Имя таблицы политики (без префикса таблицы)
                'rules_name' => 'rule',
                // Полное имя таблицы политики.
                'rules_table' => 'train_rule',
            ],
        ],
    ],
];
```

## Быстрый старт

```php
use webman\permission\Permission;

// добавить права пользователю
Permission::addPermissionForUser('eve', 'articles', 'read');
// добавить роль пользователю.
Permission::addRoleForUser('eve', 'writer');
// добавить права для роли
Permission::addPolicy('writer', 'articles','edit');
```

Вы можете проверить, обладает ли пользователь такими правами:

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // разрешить eve редактировать статьи
} else {
    // отклонить запрос, показать ошибку
}
````

## Middleware авторизации

Создайте файл `app/middleware/AuthorizationMiddleware.php` (если директории не существует, создайте ее) следующим образом:

```php
<?php

/**
 * Middleware авторизации
 * Автор: ShaoBo Wan (Tinywan)
 * Дата и время: 2021/09/07 14:15
 */

declare(strict_types=1);

namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;
use Casbin\Exceptions\CasbinException;
use webman\permission\Permission;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $uri = $request->path();
        try {
            $userId = 10086;
            $action = $request->method();
            if (!Permission::enforce((string) $userId, $uri, strtoupper($action))) {
                throw new \Exception('Извините, у вас нет доступа к этому интерфейсу');
            }
        } catch (CasbinException $exception) {
            throw new \Exception('Ошибка авторизации: ' . $exception->getMessage());
        }
        return $next($request);
    }
}
```

Добавьте глобальный middleware в `config/middleware.php` как показано ниже:

```php
return [
    // Глобальный middleware
    '' => [
        // ... Здесь опущены другие middleware
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Благодарность

[Casbin](https://github.com/php-casbin/php-casbin), вы можете посмотреть всю документацию на их [официальном веб-сайте](https://casbin.org/).

## Лицензия

Этот проект лицензирован по лицензии [Apache 2.0 license](LICENSE).
