# Конфигурационный файл

## Расположение
Конфигурационный файл webman находится в каталоге `config/`. В проекте можно получить соответствующую конфигурацию через функцию `config()`.

## Получение конфигурации

Получение всех конфигураций
```php
config();
```

Получение всех конфигураций из `config/app.php`
```php
config('app');
```

Получение конфигурации `debug` из `config/app.php`
```php
config('app.debug');
```

Если конфигурация является массивом, то можно получить значение внутренних элементов массива через `.` , например
```php
config('file.key1.key2');
```

## Значение по умолчанию
```php
config($key, $default);
```
config передает значение по умолчанию вторым параметром, если конфигурация не существует, то возвращает значение по умолчанию. Если значение по умолчанию не установлено и конфигурация не существует, то возвращает null.

## Пользовательская конфигурация
Разработчики могут добавить свой собственный файл конфигурации в каталог `config/`, например

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Использование при получении конфигурации**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Изменение конфигурации
webman не поддерживает динамическое изменение конфигурации, все конфигурации должны быть изменены в соответствующем файле конфигурации и перезагружены или перезапущены с помощью команды reload или restart.

> **Замечание**
> Конфигурации сервера `config/server.php` и процесса `config/process.php` не поддерживают перезагрузку, для вступления в силу необходимо перезапустить.
