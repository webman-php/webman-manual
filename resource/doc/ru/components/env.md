# vlucas/phpdotenv

## Описание
`vlucas/phpdotenv` - это компонент для загрузки переменных среды, который используется для разделения конфигураций для различных сред (например, разработки, тестирования и т. д.).

## Ссылка на проект
https://github.com/vlucas/phpdotenv
  
## Установка
 
```php
composer require vlucas/phpdotenv
 ```
  
## Использование

#### Создание файла `.env` в корне проекта
**.env**
```
DB_HOST = 127.0.0.1
DB_PORT = 3306
DB_NAME = test
DB_USER = foo
DB_PASSWORD = 123456
```

#### Изменение файла конфигурации
**config/database.php**
```php
return [
    // По умолчанию используемая база данных
    'default' => 'mysql',

    // Конфигурации для различных баз данных
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('DB_HOST'),
            'port'        => getenv('DB_PORT'),
            'database'    => getenv('DB_NAME'),
            'username'    => getenv('DB_USER'),
            'password'    => getenv('DB_PASSWORD'),
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
```

> **Подсказка**
> Рекомендуется добавить файл `.env` в список игнорируемых файлов `.gitignore`, чтобы избежать его попадания в репозиторий. Для репозитория добавьте файл-образец конфигурации `.env.example`; при развертывании проекта скопируйте `.env.example` в `.env` и внесите изменения в конфигурации в соответствии с текущей средой, таким образом, проект будет загружать различные конфигурации в разных средах.

> **Внимание**
> `vlucas/phpdotenv` может иметь ошибки в версии PHP TS (с версией поддержкой потоков). Рекомендуется использовать версию NTS (не поддерживающую потоки).
> Текущую версию PHP можно узнать, выполнив команду `php -v`.

## Дополнительная информация
Посетите https://github.com/vlucas/phpdotenv
