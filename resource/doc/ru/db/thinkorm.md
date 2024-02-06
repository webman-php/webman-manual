## ThinkORM

### Установка ThinkORM

`composer require -W webman/think-orm`

После установки требуется restart перезапустить (reload не работает)

> **Подсказка**
> Если установка завершится неудачей, это может быть связано с тем, что вы используете прокси для композера. Попробуйте выполнить `composer config -g --unset repos.packagist` для отключения прокси композера.

> [webman/think-orm](https://www.workerman.net/plugin/14) фактически является плагином для автоматической установки `toptink/think-orm`. Если ваша версия webman ниже `1.2`, и вы не можете использовать плагин, прочтите статью [Ручная установка и настройка think-orm](https://www.workerman.net/a/1289).

### Файл конфигурации
Измените файл конфигурации в соответствии с реальной ситуацией `config/thinkorm.php`

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

Модель ThinkOrm наследует `think\Model`, подобно следующему
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';
    
}
```

Вы также можете использовать следующую команду для создания модели на основе thinkorm
```
php webman make:model table_name
```

> **Подсказка**
> Для выполнения этой команды требуется установить `webman/console`, команда установки выглядит так: `composer require webman/console ^1.2.13`

> **Обратите внимание**
> Команда make:model, если обнаружит, что основной проект использует `illuminate/database`, создаст файлы моделей на основе `illuminate/database`, а не на основе think-orm. В этом случае вы можете использовать дополнительный параметр tp, чтобы принудительно создать модель на основе think-orm, команда будет похожа на `php webman make:model table_name tp` (если это не приведет к результату, обновите `webman/console`)
