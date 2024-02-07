# Упаковка в phar

phar - это разновидность упаковочного файла в PHP, аналогичный JAR, который можно использовать для упаковки вашего проекта webman в один phar-файл для удобного развёртывания.

**Здесь большое спасибо [fuzqing](https://github.com/fuzqing) за PR.**

> **Обратите внимание**
> Необходимо отключить опцию конфигурации phar в `php.ini`, установив `phar.readonly = 0`.

## Установка командной строки
`composer require webman/console`

## Настройка
Откройте файл `config/plugin/webman/console/app.php` и установите `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`, чтобы исключить некоторые бесполезные каталоги и файлы из упаковки пользовательского проекта и избежать излишнего увеличения размера упаковки.

## Упаковка
Запустите команду `php webman phar:pack` в корневом каталоге проекта webman.
Это создаст файл `webman.phar` в каталоге build.

> Настройки упаковки находятся в файле `config/plugin/webman/console/app.php`.

## Команды запуска и остановки
**Запуск**
`php webman.phar start` или `php webman.phar start -d`

**Остановка**
`php webman.phar stop`

**Просмотр статуса**
`php webman.phar status`

**Просмотр статуса соединения**
`php webman.phar connections`

**Перезапуск**
`php webman.phar restart` или `php webman.phar restart -d`

## Примечание
* После запуска webman.phar в каталоге, где находится webman.phar, будет создан каталог runtime для временных файлов, таких как журналы.

* Если ваш проект использует файл .env, необходимо поместить файл .env в каталог, где находится webman.phar.

* Если для вашего бизнеса требуется загрузка файлов в каталог public, он также должен быть извлечен и размещен в каталоге, где находится webman.phar, и необходимо настроить `config/app.php`.
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Бизнес может использовать вспомогательную функцию `public_path()` для нахождения фактического расположения каталога public.

* webman.phar не поддерживает запуск настраиваемых процессов в Windows.
