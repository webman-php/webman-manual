# Упаковка в phar

phar - это файл упаковки в PHP, подобный JAR, с помощью которого вы можете упаковать свой проект webman в единый файл phar для удобного развертывания.

**Здесь большое спасибо [fuzqing](https://github.com/fuzqing) за PR.**

> **Обратите внимание**
> Необходимо отключить параметры конфигурации phar в `php.ini`, установив `phar.readonly = 0`

## Установка командной строки
`composer require webman/console`

## Настройка
Откройте файл `config/plugin/webman/console/app.php` и установите `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`, чтобы при упаковке проекта исключить некоторые ненужные каталоги и файлы и избежать излишнего увеличения размера упаковки.

## Упаковка
В корневом каталоге проекта webman выполните команду `php webman phar:pack`.
Это создаст файл `webman.phar` в каталоге build.

> Конфигурация упаковки находится в `config/plugin/webman/console/app.php`

## Команды для запуска и остановки
**Запуск**
`php webman.phar start` или `php webman.phar start -d`

**Остановка**
`php webman.phar stop`

**Просмотр статуса**
`php webman.phar status`

**Просмотр состояния подключения**
`php webman.phar connections`

**Перезапуск**
`php webman.phar restart` или `php webman.phar restart -d`

## Пояснения
* После запуска webman.phar в каталоге, где находится webman.phar, будет создан каталог runtime для временных файлов, таких как журналы.

* Если в вашем проекте используется файл .env, его необходимо разместить в каталоге, где находится webman.phar.

* Если вашему приложению требуется загрузка файлов в каталог public, необходимо выделить каталог public и разместить его в каталоге, где находится webman.phar, затем настроить `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Для нахождения реального расположения каталога public бизнес может использовать вспомогательную функцию `public_path()`.

* webman.phar не поддерживает запуск настраиваемых процессов в Windows.
