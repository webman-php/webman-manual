## Файл конфигурации маршрутов
Файл конфигурации маршрутов плагина находится в `plugin/название_плагина/config/route.php`

## Маршрут по умолчанию
Путь к URL-адресам приложения плагина всегда начинается с `/app`, например, для `plugin\foo\app\controller\UserController` URL-адрес будет `http://127.0.0.1:8787/app/foo/user`

## Отключение маршрута по умолчанию
Если вы хотите отключить маршрут по умолчанию для определенного приложения плагина, установите что-то подобное в файле конфигурации маршрутов:
```php
Route::disableDefaultRoute('foo');
```

## Обработка 404 ошибки
Если вы хотите установить обработчик для определенного приложения плагина, передайте имя плагина в качестве второго аргумента, например:
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
