## ThinkORM

### Установка ThinkORM

`composer require -W webman/think-orm`

После установки необходимо выполнить restart (перезагрузка) (reload не работает).

> **Подсказка**
> Если установка завершается неудачно, это может быть связано с использованием прокси через composer. Попробуйте выполнить `composer config -g --unset repos.packagist` для отмены прокси в composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) фактически является плагином для автоматической установки `toptink/think-orm`. Если ваша версия webman ниже `1,2` и вы не можете использовать плагин, обратитесь к статье [Ручная установка и настройка think-orm](https://www.workerman.net/a/1289).

### Файл конфигурации
Измените файл конфигурации `config/thinkorm.php` в соответствии с реальной ситуацией.

### Использование

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### Создание модели

Модель ThinkOrm наследует `think\Model`, примерно так:

```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * Таблица, связанная с моделью.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Первичный ключ, связанный с таблицей.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

Вы также можете использовать следующую команду для создания модели на основе thinkorm:

```php
php webman make:model table_name
```

> **Подсказка**
> Для выполнения этой команды необходимо установить `webman/console` с помощью команды `composer require webman/console ^1.2.13`.

> **Обратите внимание**
> Если команда make:model обнаруживает, что в основном проекте используется `illuminate/database`, то будет создан файл модели на основе `illuminate/database`, а не на основе thinkorm. В этом случае можно принудительно создать модель на основе think-orm, добавив параметр tp в команду, аналогичную `php webman make:model table_name tp` (если это не работает, обновите `webman/console`).
