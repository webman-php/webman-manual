# Библиотека управления доступом Casbin webman-permission

## Объяснение

Он основан на [PHP-Casbin](https://github.com/php-casbin/php-casbin), мощном и эффективном открытом фреймворке управления доступом, поддерживающем модели управления доступом, такие как `ACL`, `RBAC`, `ABAC` и т. д.

## Адрес проекта

https://github.com/Tinywan/webman-permission

## Установка

```php
composer require tinywan/webman-permission
```
>Это расширение требует PHP 7.1+ и [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998), официальная документация: https://www.workerman.net/doc/webman#/db/others

## Настройка

### Регистрация службы

Создайте файл конфигурации `config/bootstrap.php` со следующим содержимым:

```php
// ...
webman\permission\Permission::class,
```

### Файл конфигурации Model

Создайте файл конфигурации `config/casbin-basic-model.conf` со следующим содержимым:

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

Создайте файл конфигурации `config/permission.php` со следующим содержимым:

```php
<?php

return [
    /*
     *По умолчанию разрешение
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        'logger' => 'log',
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Настройки модели
            */
            'model' => [
                'config_type' => 'file',
                'config_file_path' => config_path() . '/casbin-basic-model.conf',
                'config_text' => '',
            ],

            // Адаптер .
            'adapter' => webman\permission\adapter\DatabaseAdapter::class,

            /*
            * Настройки базы данных.
            */
            'database' => [
                // Имя подключения к базе данных, оставьте пустым, чтобы использовать настройки по умолчанию.
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

// добавить права доступа для пользователя
Permission::addPermissionForUser('eve', 'articles', 'read');
// добавить роль для пользователя.
Permission::addRoleForUser('eve', 'writer');
// добавить права доступа для правила
Permission::addPolicy('writer', 'articles','edit');
```

Вы можете проверить, имеет ли пользователь такие права доступа:

```php
if (Permission::enforce("eve", "articles", "edit")) {
    // разрешить eve редактировать статьи
} else {
    // отклонить запрос, показать ошибку
}
````

## Промежуточное ПО авторизации

Создайте файл `app/middleware/AuthorizationMiddleware.php` (если папка не существует, создайте ее) следующим образом:

```php
<?php

/**
 * Промежуточное ПО авторизации
 * Автор ShaoBo Wan (Tinywan)
 * Дата и время 2021/09/07 14:15
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
				throw new \Exception('Извините, у вас нет прав доступа к этому интерфейсу');
			}
		} catch (CasbinException $exception) {
			throw new \Exception('Исключение авторизации' . $exception->getMessage());
		}
		return $next($request);
	}
}
```

Добавьте глобальное промежуточное ПО в `config/middleware.php`:

```php
return [
    // Глобальное промежуточное ПО
    '' => [
        // ... здесь опущены другие промежуточное ПО
        app\middleware\AuthorizationMiddleware::class,
    ]
];
```

## Благодарность

[Casbin](https://github.com/php-casbin/php-casbin), вы можете найти всю документацию на его [официальном сайте](https://casbin.org/).

## Лицензия

Этот проект лицензирован по [лицензии Apache 2.0](LICENSE).
