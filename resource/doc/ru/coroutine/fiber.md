# Корутины

> **Требования для корутин**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Команда для обновления webman `composer require workerman/webman-framework ^1.5.0`
> Команда для обновления workerman `composer require workerman/workerman ^5.0.0`
> Для использования корутин Fiber необходимо установить `composer require revolt/event-loop ^1.0.0`

# Пример
### Задержка ответа

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Задержка на 1.5 секунды
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` аналогичен встроенной функции `sleep()` в PHP, с той разницей, что `Timer::sleep()` не блокирует процесс.

### Отправка HTTP-запроса

> **Примечание**
> Необходимо установить composer require workerman/http-client ^2.0.0

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // Асинхронная отправка запроса синхронным способом
        return $response->getBody()->getContents();
    }
}
```
Такой запрос, как `$client->get('http://example.com')`, не блокирующий, это улучшает производительность веб-приложения при неблокирующей отправке HTTP-запроса в webman.

Дополнительную информацию можно найти по ссылке [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Добавление класса support\Context

Класс `support\Context` используется для хранения данных контекста запроса, которые будут автоматически удалены по завершении запроса. Другими словами, жизненный цикл контекстных данных совпадает с жизненным циклом запроса. `support\Context` поддерживает среду корутин Fiber, Swoole, Swow.

### Swoole-корутины

После установки расширения swoole (требуется swoole>=5.0), включите Swoole-корутины через настройку config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Дополнительную информацию можно найти по ссылке [событийно-ориентированный воркер](https://www.workerman.net/doc/workerman/appendices/event.html)

### Загрязнение глобальных переменных

В среде корутин запрещается хранить информацию о состоянии **запроса** в глобальных или статических переменных, так как это может привести к загрязнению глобальных переменных, например:

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

Установив количество процессов равным 1, когда мы делаем два последовательных запроса
http://127.0.0.1:8787/test?name=lilei
http://127.0.0.1:8787/test?name=hanmeimei
Мы ожидаем, что результаты двух запросов будут соответственно `lilei` и `hanmeimei`, но на самом деле оба возвращают `hanmeimei`.
Это происходит потому, что второй запрос перезаписывает статическую переменную `$name`, и к моменту завершения сна первого запроса статическая переменная `$name` уже стала равной `hanmeimei`.

**Правильным подходом является использование контекста для хранения состояния запроса**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**Локальные переменные не вызывают загрязнение данных**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
Поскольку `$name` является локальной переменной, другие корутины не могут получить к ней доступ, поэтому использование локальных переменных является безопасным в контексте корутин.

# О корутинах
Корутины не являются универсальным средством, и их внедрение требует учета проблемы загрязнения глобальных/статических переменных и необходимости установки контекста. Кроме того, отладка багов в среде корутин сложнее, чем в блокирующем программировании.

Веб-фреймворк webman с блокирующим программированием, на самом деле уже достаточно быстрый. Согласно данным за последние три года с [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2), производительность базы данных веб-фрограммы webman выше, чем у фреймворков gin и echo на языке go практически вдвое, и на 40 раз выше, чем у традиционного фреймворка Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

Когда база данных, Redis находятся во внутренней сети, производительность многопроцессорного блокирующего программирования часто выше, чем у корутин, поскольку создание, планирование, уничтожение корутин может стоить больше, чем переключение процессов, поэтому введение корутин в этом случае может не значительно повысить производительность.

# Когда использовать корутины
Когда в бизнесе есть медленные запросы, например, когда бизнесу требуется запросить сторонний API, можно использовать [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) для асинхронного выполнения HTTP-запроса в режиме корутины, чтобы повысить параллельные возможности приложения.
