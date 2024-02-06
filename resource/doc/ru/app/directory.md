# Структура каталога

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

Мы видим, что у приложения-плагина есть такая же структура каталогов и файлов конфигурации, как и у webman. Фактически, опыт разработки практически не отличается от обычного приложения webman.
Каталог плагина и его название следуют стандарту PSR4, поскольку плагины находятся в каталоге plugin, пространства имен начинаются с plugin, например `plugin\foo\app\controller\UserController`.

## Об каталоге api
У каждого плагина есть каталог api. Если ваше приложение предоставляет внутренние интерфейсы для вызова из других приложений, вам нужно разместить интерфейсы в каталоге api.
Обратите внимание, что здесь речь идет об интерфейсах вызова функций, а не об интерфейсах сетевого вызова.
Например, `плагин электронной почты` предоставляет интерфейс `Email::send()` в файле `plugin/email/api/Email.php` для отправки электронной почты из других приложений.
Кроме того, файл `plugin/email/api/Install.php` генерируется автоматически для вызова рынка плагинов webman-admin при выполнении операций установки или удаления.
