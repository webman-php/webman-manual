# Конструктор запросов
## Получение всех строк
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function all(Request $request)
    {
        $users = Db::table('users')->get();
        return view('user/all', ['users' => $users]);
    }
}
```

## Получение определенных столбцов
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Получение одной строки
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Получение одного столбца
```php
$titles = Db::table('roles')->pluck('title');
```
Указание значения поля id в качестве индекса
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Получение одного значения (поля)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Удаление дубликатов
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Частичные результаты
Если вам нужно обработать тысячи записей в базе данных, чтение их всех сразу будет занимать много времени и может привести к превышению объема памяти. В этом случае может быть разумно использовать метод `chunkById`. Этот метод извлекает небольшой блок результата и передает его в функцию замыкания для обработки. Например, мы можем разбить все данные таблицы `users` на блоки по 100 записей для обработки:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Вы можете вернуть false в замыкании, чтобы прекратить получение частичных результатов:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Обработка записей...

    return false;
});
```

> Примечание: не удаляйте записи в колбеке, это может привести к тому, что некоторые записи не будут включены в результаты

## Агрегаты
Конструктор запросов также предоставляет различные методы агрегации, такие как count, max, min, avg, sum и т. д.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Проверка существования записи
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Оригинальные выражения
Прототип
```php
selectRaw($expression, $bindings = [])
```
Иногда вам может понадобиться использовать оригинальные выражения в запросе. Вы можете использовать `selectRaw()` для создания оригинального выражения:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

Также доступны методы для оригинальных выражений: `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()`.

`Db::raw($value)` также используется для создания оригинального выражения, но он не имеет функционала привязки параметров, поэтому нужно быть осторожным с проблемами SQL-инъекций при его использовании.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## Оператор объединения (Join)
```php
// join
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// leftJoin            
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// rightJoin
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// crossJoin    
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

## Оператор объединения (Union)
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Оператор WHERE
Прототип 
```php
where($колонка, $оператор = null, $значение = null)
```
Первый параметр - это название столбца, второй параметр - любой оператор, поддерживаемый системой базы данных, третий - значение, с которым будет сравниваться столбец.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Когда оператор равен, его можно опустить, поэтому это выражение эквивалентно предыдущему
$users = Db::table('users')->where('votes', 100)->get();

$users = Db::table('users')
                ->where('votes', '>=', 100)
                ->get();

$users = Db::table('users')
                ->where('votes', '<>', 100)
                ->get();

$users = Db::table('users')
                ->where('name', 'like', 'T%')
                ->get();
```

Также вы можете передавать массивы условий в функцию where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

Метод orWhere принимает те же параметры, что и метод where:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Вы также можете передать замыкание в качестве первого параметра методу orWhere:
```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$users = Db::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();

```

Методы whereBetween / orWhereBetween проверяют, находится ли значение поля между двумя заданными значениями:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Методы whereNotBetween / orWhereNotBetween проверяют, находится ли значение поля вне двух заданных значений:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Методы whereIn / whereNotIn / orWhereIn / orWhereNotIn проверяют, должно ли значение поля присутствовать в указанном массиве:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

Методы whereNull / whereNotNull / orWhereNull / orWhereNotNull проверяют, должно ли указанное поле быть NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

Метод whereNotNull проверяет, должно ли указанное поле быть не NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

Методы whereDate / whereMonth / whereDay / whereYear / whereTime используются для сравнения значения поля с указанной датой:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

Методы whereColumn / orWhereColumn используются для сравнения значений двух полей:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Вы также можете передать оператор сравнения
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// Метод whereColumn также может принимать массив
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Группировка параметров
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
               $query->where('votes', '>', 100)
                     ->orWhere('title', '=', 'Admin');
           })
           ->get();
```

Метод whereExists
```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$users = Db::table('users')
           ->whereExists(function ($query) {
               $query->select(Db::raw(1))
                     ->from('orders')
                     ->whereRaw('orders.user_id = users.id');
           })
           ->get();
```

## Сортировка по порядку
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Случайный порядок
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Случайный порядок сильно влияет на производительность сервера, рекомендуется воздерживаться от его использования

## Группировка / Имеющее
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Вы также можете передавать несколько параметров в метод groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## Смещение / Лимит
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## Вставка
Вставка одной записи
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Вставка нескольких записей
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Получить ID автоматически
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> Обратите внимание: при использовании PostgreSQL метод insertGetId по умолчанию будет использовать id как имя автоматически увеличиваемого поля. Если вы хотите получить идентификатор из другой "последовательности", вы можете передать имя поля вторым параметром методу insertGetId.

## Обновление
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Обновить или вставить
Иногда вам может потребоваться обновить существующую запись в базе данных или создать ее, если соответствующая запись отсутствует:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Метод updateOrInsert в первую очередь попытается найти соответствующую запись в базе данных, используя ключ и значение из первого параметра. Если запись существует, она будет обновлена значениями из второго параметра. Если запись не найдена, будет вставлена новая запись, используя данные из обоих массивов.

## Увеличить & Уменьшить
Оба эти метода принимают как минимум один параметр: столбец для изменения. Второй параметр является необязательным и используется для контроля увеличения или уменьшения количества:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Вы также можете указать желаемые поля для обновления в процессе операции:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```
## Удаление
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Если вам нужно очистить таблицу, вы можете использовать метод `truncate`, который удалит все строки и сбросит инкрементный идентификатор на ноль:
```php
Db::table('users')->truncate();
```

## Пессимистическая блокировка
Конструктор запросов также содержит некоторые функции, которые могут помочь вам реализовать "пессимистическую блокировку" в синтаксисе `select`. Если вы хотите реализовать "разделенную блокировку" в запросе, вы можете использовать метод `sharedLock`. Разделенная блокировка предотвращает изменение выбранных столбцов данных до завершения транзакции:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Или вы можете использовать метод `lockForUpdate`. Использование блокировки "для обновления" позволяет избежать изменения или выбора строк другими разделенными блокировками:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Отладка
Вы можете использовать методы `dd` или `dump` для вывода результатов запроса или SQL-запроса. Использование метода `dd` позволяет отобразить отладочную информацию, а затем остановить выполнение запроса. Метод `dump` также может отображать отладочную информацию, но не останавливает выполнение запроса:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Примечание**
> Для отладки необходимо установить `symfony/var-dumper` с помощью команды `composer require symfony/var-dumper`.
