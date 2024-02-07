# Casbin

## Описание

Casbin - это мощный и эффективный фреймворк управления доступом с открытым исходным кодом, механизм управления правами которого поддерживает несколько моделей управления доступом.

## URL проекта

https://github.com/teamones-open/casbin

## Установка

```php
composer require teamones/casbin
```

## Официальный сайт Casbin

Для подробного использования можно просмотреть официальную китайскую документацию, здесь мы расскажем только о том, как настроить и использовать в webman.

https://casbin.org/docs/zh-CN/overview

## Структура каталогов

``` 
.
├── config                        Папка конфигурации
│   ├── casbin-restful-model.conf Файл настроек используемой модели разрешений
│   ├── casbin.php                Конфигурация casbin
......
├── database                      Файлы базы данных
│   ├── migrations                Файлы миграции
│   │   └── 20210218074218_create_rule_table.php
......
```

## Файлы миграции базы данных

```php
<?php

use Phinx\Migration\AbstractMigration;

class CreateRuleTable extends AbstractMigration
{
    /**
     * Метод изменения.
     *
     * Напишите ваши обратимые миграции с использованием этого метода.
     *
     * Более подробная информация о написании миграций доступна здесь:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * В этом методе можно использовать следующие команды, и Phinx автоматически их откатит при откате:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Любые другие разрушающие изменения приведут к ошибке при попытке отката миграции.
     *
     * Помните, что при работе с классом Table нужно вызывать "create()" или "update()", а не "save()".
     */
    public function change()
    {
        $table = $this->table('rule', ['id' => false, 'primary_key' => ['id'], 'engine' => 'InnoDB', 'collation' => 'utf8mb4_general_ci', 'comment' => 'Таблица правил']);

        //Добавление полей данных
        $table->addColumn('id', 'integer', ['identity' => true, 'signed' => false, 'limit' => 11, 'comment' => 'Идентификатор'])
            ->addColumn('ptype', 'char', ['default' => '', 'limit' => 8, 'comment' => 'Тип правила'])
            ->addColumn('v0', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v1', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v2', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v3', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v4', 'string', ['default' => '', 'limit' => 128])
            ->addColumn('v5', 'string', ['default' => '', 'limit' => 128]);

        //Выполнение создания
        $table->create();
    }
}

```

## Конфигурация casbin

Синтаксис конфигурации модели разрешений можно найти по ссылке: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Файл конфигурации модели разрешений
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // модель или адаптер
            'class' => \app\model\Rule::class,
        ],
    ],
    // Можно настроить несколько моделей разрешений
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Файл конфигурации модели разрешений
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // модель или адаптер
            'class' => \app\model\RBACRule::class,
        ],
    ],
];
```

### Адаптер

В текущей обертке composer используется метод model think-orm, для других ORM см. vendor/teamones/src/adapters/DatabaseAdapter.php

Затем измените конфигурацию

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Файл конфигурации модели разрешений
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // здесь тип настраивается как адаптер
            'class' => \app\adapter\DatabaseAdapter::class,
        ],
    ],
];
``` 

## Использование

### Импорт

```php
# Импорт
use teamones\casbin\Enforcer;
```

### Два способа использования

```php
# 1. Использование конфигурации default по умолчанию
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Использование настраиваемой конфигурации rbac
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Обзор популярных API

Для получения более подробной информации об использовании API обратитесь к официальной документации

- API управления: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# Добавление разрешения для пользователя

Enforcer::addPermissionForUser('user1', '/user', 'read');

# Удаление разрешения у пользователя

Enforcer::deletePermissionForUser('user1', '/user', 'read');

# Получение всех разрешений пользователя

Enforcer::getPermissionsForUser('user1'); 

# Добавление роли для пользователя

Enforcer::addRoleForUser('user1', 'role1');

# Добавление разрешения для роли

Enforcer::addPermissionForUser('role1', '/user', 'edit');

# Получение всех ролей

Enforcer::getAllRoles();

# Получение всех ролей пользователя

Enforcer::getRolesForUser('user1');

# Получение пользователей по роли

Enforcer::getUsersForRole('role1');

# Проверка, принадлежит ли пользователь какой-либо роли

Enforcer::hasRoleForUser('use1', 'role1');

# Удаление роли у пользователя

Enforcer::deleteRoleForUser('use1', 'role1');

# Удаление всех ролей пользователя

Enforcer::deleteRolesForUser('use1');

# Удаление роли

Enforcer::deleteRole('role1');

# Удаление разрешения

Enforcer::deletePermission('/user', 'read');

# Удаление всех разрешений пользователя или роли

Enforcer::deletePermissionsForUser('user1');
Enforcer::deletePermissionsForUser('role1');

# Проверка разрешения, возвращает true или false

Enforcer::enforce("user1", "/user", "edit");
```
