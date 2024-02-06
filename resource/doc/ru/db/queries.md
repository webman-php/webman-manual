```php
# Конструктор запросов
## Получить все строки
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

## Получить определенные столбцы
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Получить одну строку
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Получить один столбец
```php
$titles = Db::table('roles')->pluck('title');
```
Указать значение поля id в качестве индекса
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Получить одно значение (поле)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Удалить дубликаты
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Результаты по частям
Если вам нужно обрабатывать тысячи записей из базы данных, одновременное чтение этих данных будет занимать много времени и может привести к превышению памяти. В этом случае вы можете рассмотреть использование метода chunkById. Этот метод извлекает небольшой набор результатов и передает его в функцию замыкания для обработки. Например, мы можем разбить все данные таблицы users на небольшие порции размером в 100 записей для обработки их по частям:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Вы можете завершить получение результатов по частям, возвращая ложь из замыкания.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Обработайте записи...

    return false;
});
```

> Примечание: Не удаляйте данные в обратном вызове, это может привести к тому, что некоторые записи не попадут в набор результатов

## Агрегирование

Конструктор запросов также предоставляет различные методы агрегирования, такие как count, max, min, avg, sum и т. д.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Проверка существования записей
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Сырые выражения
Прототип
```php
selectRaw($expression, $bindings = [])
```
Иногда вам может понадобиться использовать сырые SQL-выражения в запросе. Вы может использовать `selectRaw()` для создания сырого SQL-выражения:

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

То же самое, также предоставляются методы сырых выражений `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()`.


`Db::raw($value)` также используется для создания сырого SQL-выражения, но он не имеет функции привязки параметров, поэтому при использовании его следует быть осторожным из-за возможности SQL-инъекций.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## Запросы с объединением
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

## Запросы с объединением
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Условия Where
Прототип 
```php
where($column, $operator = null, $value = null)
```
Первый параметр - это имя столбца, второй параметр - любой оператор, поддерживаемый системой базы данных, третий - значение, с которым нужно сравнить столбец
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Когда оператор – это равно, можно опустить его, поэтому это выражение эквивалентно предыдущему
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

Вы также можете передать массив условий в функцию where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

Метод orWhere имеет те же параметры, что и метод where:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Вы также можете передать замыкание в качестве первого параметра метода orWhere:
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

Метод whereBetween / orWhereBetween проверяет, находится ли значение поля между двумя заданными значениями:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Метод whereNotBetween / orWhereNotBetween проверяет, находится ли значение поля вне двух заданных значений:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Методы whereIn / whereNotIn / orWhereIn / orWhereNotIn проверяют, должны ли значения поля существовать в заданном массиве:
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

Метод whereColumn / orWhereColumn используется для сравнения значений двух полей:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Также можно передать оператор сравнения
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

whereExists
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

## Сортировка по
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Случайная сортировка
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Случайная сортировка сильно влияет на производительность сервера, и ее использование не рекомендуется

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Вы также можете передать несколько параметров методу groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## Смещение / лимит
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```
```
## Вставка
Вставка одной строки
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Вставка нескольких строк
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Увеличение ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Примечание: при использовании PostgreSQL метод insertGetId будет по умолчанию использовать id в качестве имени автоинкрементного поля. Если вы хотите получить ID из другой "последовательности", вы можете передать имя поля в качестве второго аргумента методу insertGetId.

## Обновление
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Обновить или вставить
Иногда вы можете захотеть обновить существующую запись в базе данных, или создать ее, если совпадающей записи нет:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Метод updateOrInsert сначала попробует найти совпадение с записью в базе данных, используя ключ и значение из первого параметра. Если запись найдена, то она будет обновлена значениями из второго параметра. Если запись не найдена, будет вставлена новая запись с данными из двух массивов.

## Увеличение и уменьшение
Оба из этих методов принимают как минимум один аргумент - имя столбца, который нужно изменить. Второй аргумент является опциональным и контролирует количество увеличения или уменьшения столбца:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Также вы можете указать поле для обновления в процессе операции:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Удаление
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Если вам нужно очистить таблицу, вы можете использовать метод truncate, который удалит все строки и сбросит автоинкрементное значение ID на ноль:
```php
Db::table('users')->truncate();
```
## Пессимистическая блокировка
Конструктор запросов также включает несколько методов, которые могут помочь вам реализовать "пессимистическую блокировку" в синтаксисе select. Если вам нужно реализовать "общую блокировку" в запросе, вы можете использовать метод sharedLock. Общая блокировка предотвращает изменение выбранных столбцов данных до подтверждения транзакции:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Или вы можете использовать метод lockForUpdate. Использование "блокировки обновления" позволяет избежать изменения или выбора строк другими общими блокировками:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Отладка
Вы можете использовать методы dd или dump для вывода результатов запроса или SQL-запроса. Использование метода dd может показать отладочную информацию и затем остановить выполнение запроса. Метод dump также может показать отладочную информацию, но не остановит выполнение запроса:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Примечание**
> Для отладки необходимо установить `symfony/var-dumper`, команда `composer require symfony/var-dumper`

