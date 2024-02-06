# Webman

## Инструкции

Webman - это мощный и эффективный фреймворк управления доступом с открытым исходным кодом, который поддерживает множество моделей контроля доступа.

## Адрес проекта

https://github.com/teamones-open/casbin

## Установка

```php
composer require teamones/casbin
```

## Официальный сайт Casbin

Для подробных инструкций по использованию вы можете просмотреть официальную китайскую документацию. Здесь будет рассказано только о том, как настроить и использовать веб-мэн.

https://casbin.org/docs/zh-CN/overview

## Структура каталогов

```
.
├── config                        Каталог конфигурации
│   ├── casbin-restful-model.conf Файл конфигурации используемой модели доступа
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
     * Напишите вашу обратимую миграцию, используя этот метод.
     *
     * Более подробную информацию о написании миграций можно найти здесь:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * В этом методе могут использоваться следующие команды, и Phinx
     * автоматически откатит их при откате:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Любые другие разрушающие изменения вызовут ошибку при попытке
     * откатить миграцию.
     *
     * Не забудьте вызвать "create()" или "update()", а НЕ "save()" при работе
     * с классом Table.
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

Смотрите синтаксис конфигурации модели правил доступа: https://casbin.org/docs/zh-CN/syntax-for-models

```php

<?php

return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Файл конфигурации модели правил доступа
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'model', // модель или адаптер
            'class' => \app\model\Rule::class,
        ],
    ],
    // Можно настроить несколько моделей доступа
    'rbac' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-rbac-model.conf', // Файл конфигурации модели правил доступа
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

Текущая оболочка composer использует метод модели think-orm, для других orm см. vendor/teamones/src/adapters/DatabaseAdapter.php

Затем измените конфигурацию

```php
return [
    'default' => [
        'model' => [
            'config_type' => 'file',
            'config_file_path' => config_path() . '/casbin-restful-model.conf', // Файл конфигурации модели правил доступа
            'config_text' => '',
        ],
        'adapter' => [
            'type' => 'adapter', // Здесь тип настраивается как адаптер
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
# 1. Использование конфигурации по умолчанию default
Enforcer::addPermissionForUser('user1', '/user', 'read');

# 2. Использование настраиваемой конфигурации rbac
Enforcer::instance('rbac')->addPermissionForUser('user1', '/user', 'read');
```

### Обзор часто используемых API

Для получения дополнительной информации о способах использования API обратитесь к официальной документации

- Управление API: https://casbin.org/docs/zh-CN/management-api
- RBAC API: https://casbin.org/docs/zh-CN/rbac-api

```php
# Добавление разрешений для пользователя

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

# Получение пользователей для ролей

Enforcer::getUsersForRole('role1');

# Проверка принадлежности пользователя к роли

Enforcer::hasRoleForUser('user1', 'role1');

# Удаление роли пользователя

Enforcer::deleteRoleForUser('user1', 'role1');

# Удаление всех ролей пользователя

Enforcer::deleteRolesForUser('user1');

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
