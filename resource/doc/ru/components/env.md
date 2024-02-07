# vlucas/phpdotenv

## Описание
`vlucas/phpdotenv` - это компонент загрузки переменных окружения, используемый для разделения конфигураций для различных окружений (например, разработки, тестирования и т. д.).

## Ссылка на проект
https://github.com/vlucas/phpdotenv

## Установка
```php
composer require vlucas/phpdotenv
```

## Использование

#### Создайте файл `.env` в корне проекта
**.env**
```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=test
DB_USER=foo
DB_PASSWORD=123456
```

#### Измените файл конфигурации
**config/database.php**
```php
return [
    // По умолчанию используемый тип базы данных
    'default' => 'mysql',

    // Конфигурации различных баз данных
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
> Рекомендуется добавить файл `.env` в список игнорируемых файлов `.gitignore`, чтобы избежать его добавления в репозиторий кода. Также добавьте файл `env.example` в репозиторий в качестве примера конфигурации. При развертывании проекта скопируйте `env.example` в `.env` и измените конфигурацию в файле `.env` в соответствии с текущим окружением. Это позволит проекту загружать различные конфигурации в разных окружениях.

> **Внимание**
> `vlucas/phpdotenv` может содержать ошибки в версии PHP TS (версия с поддержкой потоков), поэтому используйте версию NTS (версия без поддержки потоков).
> Чтобы узнать текущую версию PHP, выполните команду `php -v`.

## Дополнительная информация

Посетите https://github.com/vlucas/phpdotenv
