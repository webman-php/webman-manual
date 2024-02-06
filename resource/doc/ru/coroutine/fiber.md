# Корутины

> **Требования к корутинам**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Команда для обновления webman `composer require workerman/webman-framework ^1.5.0`
> Команда для обновления workerman `composer require workerman/workerman ^5.0.0`
> Для установки корутин Fiber необходимо выполнить команду `composer require revolt/event-loop ^1.0.0`

# Пример
### Отложенный ответ

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Пауза на 1,5 секунды
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` аналогичен встроенной функции `sleep()` в PHP, но отличие в том, что `Timer::sleep()` не блокирует процесс.

### Выполнение HTTP-запроса

> **Обратите внимание**
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
        $response = $client->get('http://example.com'); // Асинхронное выполнение синхронного запроса
        return $response->getBody()->getContents();
    }
}
```
Такой запрос `$client->get('http://example.com')` также является неблокирующим и может быть использован в webman для асинхронного выполнения HTTP-запросов с целью повышения производительности приложения.

Дополнительная информация [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Добавление класса support\Context

Класс `support\Context` используется для хранения контекстных данных запроса, которые автоматически удаляются после завершения запроса. Это означает, что время жизни контекстных данных совпадает с временем жизни запроса. `support\Context` поддерживает среды выполнения корутин Fiber, Swoole, Swow.

### Корутины Swoole
После установки расширения swoole (требуется swoole>=5.0) можно включить корутины Swoole с помощью настройки config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Дополнительная информация [workerman событийный цикл](https://www.workerman.net/doc/workerman/appendices/event.html)

### Загрязнение глобальных переменных

В среде корутин запрещено сохранять информацию о **связанном с запросом** состоянии в глобальных переменных или статических переменных, так как это может привести к загрязнению глобальных переменных, например

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

Установив количество процессов в 1, если мы запустим два последовательных запроса
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
мы ожидаем, что результаты двух запросов будут соответственно `lilei` и `hanmeimei`, но на самом деле в обоих случаях возвращается `hanmeimei`. 
Это происходит потому, что второй запрос перезаписывает статическую переменную `$name`, и к моменту завершения паузы в первом запросе статическая переменная `$name` уже становится равной `hanmeimei`.

**Правильным подходом является использование контекста для хранения данных состояния запроса**
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
Поскольку переменная `$name` является локальной, корутины не могут взаимодействовать с локальными переменными, поэтому использование локальных переменных безопасно для корутин.

# О корутинах
Корутины не являются универсальным средством, и их внедрение требует внимания к проблеме загрязнения глобальных/статических переменных и необходимости установки контекста. Кроме того, отладка корутины в общей сложности сложнее, чем отладка блокирующего программирования.

Блокирующее программирование в webman фактически уже достаточно быстро, как видно из данных тестирования в течение трех последних лет на [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2), где webman с бизнес-логикой базы данных показывает производительность почти в 2 раза выше, чем у веб-фреймворков на языке go, таких как gin и echo, и выше чем у традиционных фреймворков, например laravel, примерно в 40 раз.
![](../../assets/img/benchemarks-go-sw.png?)

Когда базы данных и Redis находятся в локальной сети, производительность многопроцессорных блокировок может быть выше, чем у корутин, так как затраты на создание, планирование и уничтожение корутин могут быть выше, чем затраты на переключение процессов, когда базы данных, Redis и т. д. достаточно быстрые. Поэтому введение корутин в этом случае не приведет к значительному увеличению производительности.

# Когда использовать корутины
Если в приложении имеются медленные запросы, например, при необходимости доступа к стороннему API, можно использовать [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) для асинхронного выполнения HTTP-вызовов с целью повышения параллельной производительности приложения.
