# Нагрузочное тестирование

## Какие факторы влияют на результаты нагрузочного тестирования?
* Сетевая задержка между сервером и машиной, где проводится нагрузочное тестирование (рекомендуется использовать внутреннюю сеть или тестирование на локальной машине)
* Пропускная способность сети между машиной и сервером (рекомендуется использовать внутреннюю сеть или тестирование на локальной машине)
* Включен ли HTTP keep-alive (рекомендуется включить)
* Достаточно ли большое количество одновременных соединений (для тестирования во внешней сети рекомендуется использовать как можно большее количество одновременных соединений)
* Разумное количество процессов на сервере (рекомендуется, чтобы количество процессов для сервиса "Hello, world" соответствовало количеству процессоров, а для бизнес-логики, взаимодействующей с базой данных, было в четыре раза или более, чем количество процессоров)
* Собственная производительность бизнес-логики (например, используется ли внешняя база данных)

## Что такое HTTP keep-alive?
Механизм HTTP Keep-Alive - это технология, которая позволяет отправлять несколько HTTP запросов и ответов через одно TCP соединение, что сильно влияет на результаты тестирования производительности. Если отключить keep-alive, количество запросов в секунду может существенно упасть. В настоящее время браузеры по умолчанию включают keep-alive, то есть при обращении к определенному HTTP-адресу браузер сохраняет соединение и использует его для следующего запроса, что способствует повышению производительности. Рекомендуется включать keep-alive при проведении нагрузочного тестирования.

## Как включить HTTP keep-alive во время нагрузочного тестирования?
Если вы используете программу ab для нагрузочного тестирования, вам нужно добавить параметр -k, например `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Для apipost необходимо вернуть заголовок gzip в ответной части для включения keep-alive (исправление ошибки apipost, см. ниже).
Для других программ для нагрузочного тестирования keep-alive обычно включен по умолчанию.

## Почему количество запросов в секунду при тестировании во внешней сети такое низкое?
Большая задержка во внешней сети приводит к низкой производительности запросов в секунду, что является нормальным явлением. Например, при тестировании страницы baidu QPS может составлять всего несколько десятков. Рекомендуется проводить тестирование во внутренней сети или на локальной машине, чтобы исключить влияние сетевой задержки. Если необходимо провести тестирование во внешней сети, можно увеличить количество одновременных соединений для увеличения пропускной способности (при условии достаточной пропускной способности).

## Почему производительность снижается после проксирования через Nginx?
Запуск Nginx требует затрат системных ресурсов. Кроме того, взаимодействие между Nginx и webman также требует некоторых ресурсов. Однако ресурсы системы ограничены, и webman не может получить все ресурсы системы, поэтому некоторое снижение производительности системы является нормальным явлением. Для минимизации влияния проксирования через Nginx можно рассмотреть отключение логов Nginx (`access_log off;`), а также включение keep-alive между Nginx и webman, см. [проксирование через Nginx](nginx-proxy.md).

Кроме того, HTTPS потребляет больше ресурсов по сравнению с HTTP из-за необходимости выполнения SSL/TLS рукопожатия, шифрования данных, увеличенного размера пакетов и использования большей пропускной способности, что приводит к снижению производительности. Если использовать короткие соединения (не включать HTTP keep-alive) при тестировании HTTPS, каждый запрос потребует дополнительного SSL/TLS рукопожатия и обмена данными, что существенно снизит производительность. Рекомендуется включать HTTP keep-alive при тестировании HTTPS.

## Как узнать, что система достигла предела производительности?
Как правило, если процессор достигает 100%, это означает, что производительность системы достигла предела. Если процессор все еще имеет свободные ресурсы, значит, предел еще не достигнут, и в этом случае можно увеличить количество одновременных соединений для увеличения QPS. Если увеличение одновременных соединений не увеличивает QPS, возможно, причина в недостаточном количестве процессов webman. В этом случае можно увеличить количество процессов. Если и это не помогает, следует проверить достаточность пропускной способности.

## Почему результаты моего нагрузочного тестирования показывают, что производительность webman ниже, чем у фреймворка gin на go?
[Результаты techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) показывают, что все показатели webman в разы превышают gin, включая чистый текст, запросы к базе данных, обновление базы данных и прочее. Если ваши результаты отличаются, возможно, это связано с тем, что вы используете ORM в webman, что приводит к существенной потере производительности. Рекомендуется попробовать сравнить webman с использованием нативного PDO и gin с использованием нативного SQL.

## Насколько снизится производительность при использовании ORM в webman?
Ниже представлены результаты тестирования:

**Среда**
Облачный сервер Alibaba с 4 ядрами и 4 ГБ памяти, случайный запрос одной записи из 100 000 записей в формате JSON.

**При использовании нативного PDO**
Производительность webman составляет 17 800 запросов в секунду.

**При использовании Db::table() из Laravel**
Производительность webman снижается до 9 400 запросов в секунду.

**При использовании Model из Laravel**
Производительность webman снижается до 7 200 запросов в секунду.

Аналогичные результаты получаются при использовании thinkORM. 

> **Примечание:** 
> Несмотря на снижение производительности при использовании ORM, для большинства бизнес-логик это будет достаточно. Мы должны найти баланс между эффективностью разработки, удержанием и производительностью, а не просто стремиться к максимальной производительности.

## Почему при нагрузочном тестировании через apipost производительность так низкая?
Нагрузочный модуль apipost имеет ошибку: если сервер не возвращает заголовок gzip, то keep-alive не будет сохранено, что приведет к существенному снижению производительности. Для решения этой проблемы данные следует сжимать при возврате и добавлять заголовок gzip, например:

```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```

Кроме того, в некоторых случаях apipost не может предоставить удовлетворительное давление, что проявляется в снижении QPS примерно на 50% по сравнению с ab (Apache Benchmark). Рекомендуется для нагрузочного тестирования использовать инструменты ab, wrk или другие специализированные программы, а не apipost.

## Установка подходящего числа процессов
По умолчанию webman открывает количество процессов, равное CPU*4. На самом деле, для бизнеса "Hello, world" без сетевого ввода-вывода оптимально устанавливать количество процессов таким же, как количество ядер процессора, чтобы уменьшить переключение процессов. Если речь идет о бизнесе с блокировкой ввода-вывода, таких как базы данных, redis и других, количество процессов можно установить в 3-8 раз больше количества ядер процессора, поскольку для достижения большого количества одновременных соединений требуется больше процессов, и расходы на переключение процессов по сравнению с блокировкой ввода-вывода можно считать пренебрежимо малыми.

## Некоторые рекомендуемые значения для нагрузочного тестирования

**Облачный сервер 4 ядра 4 ГБ, 16 процессов, тестирование на локальной машине/внутренней сети**

| - | С включенным keep-alive | С выключенным keep-alive |
|--|-----|-----|
| hello world | 80-160 тыс. запросов в секунду | 10-30 тыс. запросов в секунду |
| Одиночный запрос к базе данных | 10-20 тыс. запросов в секунду | 10 тыс. запросов в секунду |

[**Нагрузочные тесты от третьих лиц techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

## Примеры команд для нагрузочного тестирования

**ab**
```shell
# 100 000 запросов, 200 одновременных соединений, keep-alive включен
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100 000 запросов, 200 одновременных соединений, keep-alive выключен
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```shell
# Нагрузочное тестирование 200 одновременных соединений в течение 10 секунд, keep-alive включен (по умолчанию)
wrk -c 200 -d 10s http://example.com
```
