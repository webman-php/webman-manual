# Construtor de Consultas
## Obter todas as linhas
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

## Obter colunas específicas
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Obter uma linha
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Obter uma coluna
```php
$titles = Db::table('roles')->pluck('title');
```
Especificar o valor do campo id como índice
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Obter um único valor (campo)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Distinct
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Resultados em bloco
Se você precisar manipular milhares de registros de banco de dados, buscar todos esses dados de uma vez será demorado e pode resultar em estouro de memória, neste caso, você pode considerar usar o método `chunkById`. Este método obtém um pequeno conjunto de resultados do conjunto de resultados e os passa para uma função de fechamento para processamento. Por exemplo, podemos dividir todos os dados da tabela de usuários em pequenos blocos de 100 registros cada para processá-los:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Você pode interromper a obtenção de resultados em blocos retornando falso no fechamento
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Processar os registros...

    return false;
});
```

> Nota: Não exclua dados na função de fechamento, isso pode resultar em alguns registros não sendo incluídos no conjunto de resultados.

## Agregações
O construtor de consultas também oferece várias operações de agregação, como count, max, min, avg, sum, etc.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Verificar se um registro existe
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Expressões SQL nativas
Prototype
```php
selectRaw($expression, $bindings = [])
```
Às vezes, você pode precisar usar expressões SQL nativas na consulta. Você pode usar o método `selectRaw()` para criar uma expressão SQL nativa:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```
Da mesma forma, existem também os métodos `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()` para expressões SQL nativas.

`Db::raw($value)` também é usado para criar uma expressão SQL nativa, no entanto, não possui a funcionalidade de vinculação de parâmetros, portanto, deve ser usado com cuidado para evitar problemas de injeção de SQL.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Declarações de Junção
```php
// junção
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

## Declarações de União
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Declarações de Clausula Where
Prototype
```php
where($column, $operator = null, $value = null)
```
O primeiro parâmetro é o nome da coluna, o segundo é qualquer operador compatível com o sistema de banco de dados e o terceiro é o valor com o qual a coluna será comparada.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Quando o operador é o sinal de igual, pode ser omitido, portanto, esta declaração é equivalente à anterior
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

É possível passar um array de condições para a função where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

O método orWhere possui parâmetros semelhantes aos do método where:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Você pode passar um closure para o método orWhere como primeiro parâmetro:
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

whereBetween / orWhereBetween verifica se o valor do campo está entre dois valores fornecidos:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween verifica se o valor do campo está fora dos dois valores fornecidos:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn verifica se o valor do campo está presente em um array específico:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull verifica se o campo especificado é NULL ou não:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull verifica se o campo especificado não é NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime compara o valor do campo com uma data fornecida:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn compara se os valores de dois campos são iguais:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Também é possível passar um operador de comparação
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// O método whereColumn também aceita arrays
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Agrupamento de parâmetros
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

## orderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Classificação Aleatória
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> A classificação aleatória afeta significativamente o desempenho do servidor, por isso não é recomendada.

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Você pode passar vários parâmetros para o método groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## offset / limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## Inserção
Inserir um único registro
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Inserir vários registros
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## ID Incremental
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Nota: Quando estiver utilizando PostgreSQL, o método insertGetId irá assumir que o 'id' é o nome do campo de incremento automático. Se você precisar obter o ID de outra "sequência", pode passar o nome do campo como segundo parâmetro para o método insertGetId.

## Atualização
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Atualizar ou Inserir
Às vezes, você pode querer atualizar um registro existente no banco de dados, ou criar um se não houver nenhum registro correspondente:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
O método updateOrInsert tentará primeiro localizar um registro no banco de dados usando a chave e o valor do primeiro parâmetro. Se encontrar um registro, ele será atualizado com os valores do segundo parâmetro. Se não encontrar nenhum registro, será inserido um novo registro com os dados dos dois arrays.

## Incremento & Decremento
Ambos os métodos aceitam pelo menos um parâmetro: o nome da coluna a ser modificada. O segundo parâmetro é opcional e controla o valor a ser incrementado ou decrementado:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Você também pode especificar o campo a ser atualizado durante a operação:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Exclusão
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Se você precisar limpar uma tabela, pode usar o método truncate, que irá eliminar todas as linhas e reiniciar o ID incremental para zero:
```php
Db::table('users')->truncate();
```

## Bloqueio Pessimista
O construtor de consultas também inclui alguns métodos que podem ajudar a implementar "bloqueio pessimista" em consultas de seleção. Se desejar implementar um "bloqueio compartilhado" em uma consulta, pode usar o método sharedLock. O bloqueio compartilhado impede que as colunas de dados selecionadas sejam alteradas até que a transação seja confirmada:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Ou, você pode usar o método lockForUpdate. Usar o bloqueio de "atualização" evita que outras travas compartilhadas modifiquem ou selecione linhas:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Depuração
Você pode usar os métodos dd ou dump para exibir os resultados da consulta ou as instruções SQL. Usar o método dd exibe as informações de depuração e interrompe a execução da requisição. O método dump também exibe as informações de depuração, mas não interrompe a execução da requisição:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Nota**
> A depuração requer a instalação do `symfony/var-dumper`, execute o comando `composer require symfony/var-dumper`
