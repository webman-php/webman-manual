# Construtor de Consulta

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

## Resultados em blocos
Se precisar lidar com milhares de registros de banco de dados, ler esses dados de uma vez pode ser demorado e pode levar a limites de memória. Nesse caso, você pode considerar usar o método `chunkById`. Esse método obtém um pequeno pedaço do conjunto de resultados de uma vez e o passa para uma função de fechamento para processamento. Por exemplo, podemos dividir todos os dados da tabela `users` em pequenos pedaços de 100 registros para processar de uma vez:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Você pode interromper a obtenção dos resultados em blocos retornando false na função de fechamento.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Processar os registros...

    return false;
});
```
> Nota: Não exclua dados no retorno, pois isso pode resultar em alguns registros não estarem incluídos no conjunto de resultados.

## Agregações
O construtor de consulta também fornece vários métodos de agregação, como count, max, min, avg, sum, etc.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Verificar se o registro existe
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Expressões Raw
Protótipo
```php
selectRaw($expression, $bindings = [])
```
Às vezes, pode ser necessário usar expressões raw em uma consulta. Você pode usar `selectRaw()` para criar uma expressão raw:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```
Da mesma forma, também são fornecidos os métodos de expressão raw `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()`.

`Db::raw($value)` também é usado para criar uma expressão raw, mas não possui a funcionalidade de vinculação de parâmetros e deve ser usado com cuidado para evitar problemas de injeção de SQL.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Declarações de junção
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

## Declarações de União
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Declaração Where
Protótipo
```php
where($column, $operator = null, $value = null)
```
O primeiro argumento é o nome da coluna, o segundo é qualquer operador suportado pelo sistema de banco de dados, e o terceiro é o valor a ser comparado com a coluna.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Quando o operador é igual, pode ser omitido, logo, esta expressão tem o mesmo efeito que a anterior
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

Você também pode enviar um array de condições para a função where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

O método orWhere e o método where aceitam os mesmos parâmetros:
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

Os métodos whereBetween / orWhereBetween verificam se o valor do campo está entre dois valores dados:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Os métodos whereNotBetween / orWhereNotBetween verificam se o valor do campo não está entre dois valores dados:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Os métodos whereIn / whereNotIn / orWhereIn / orWhereNotIn verificam se o valor do campo deve existir em um array específico:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

Os métodos whereNull / whereNotNull / orWhereNull / orWhereNotNull verificam se o campo especificado deve ser NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

O método whereNotNull verifica se o campo especificado não é NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

Os métodos whereDate / whereMonth / whereDay / whereYear / whereTime são usados para comparar o valor do campo com a data especificada:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

O método whereColumn / orWhereColumn é usado para comparar se os valores de dois campos são iguais:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Você também pode passar um operador de comparação
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// O método whereColumn também pode aceitar um array
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

## Ordenar por
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Ordenar aleatoriamente
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Ordenar aleatoriamente pode ter um grande impacto na performance do servidor, por isso é desaconselhado o seu uso.

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

## Inserir
Inserir um único registro
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Inserir múltiplos registros
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Obter ID incrementado
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> Observação: Ao usar o PostgreSQL, o método insertGetId por padrão considerará 'id' como o nome do campo de incremento automático. Se você deseja obter o ID de outra "sequência", pode passar o nome do campo como segundo parâmetro para o método insertGetId.

## Atualizar
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Atualizar ou Inserir
Às vezes, você pode querer atualizar um registro existente no banco de dados ou criar um novo, caso não haja registro correspondente:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
O método updateOrInsert primeiro tentará encontrar um registro no banco de dados usando a chave e valor fornecidos no primeiro parâmetro. Se encontrar um registro, usará os valores do segundo parâmetro para atualizar o registro. Se não encontrar um registro, será inserido um novo registro, com dados obtidos de ambas as arrays.

## Incrementar e Decrementar
Ambos os métodos aceitam pelo menos um parâmetro: o nome da coluna a ser modificada. O segundo parâmetro é opcional e controla a quantidade pela qual a coluna deve ser incrementada ou decrementada:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Você também pode especificar os campos a serem atualizados durante a operação:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```
## Excluir
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Se você precisar limpar a tabela, você pode usar o método truncate, que irá excluir todas as linhas e redefinir o ID autoincremental para zero:
```php
Db::table('users')->truncate();
```

## Bloqueio Pessimista
O construtor de consulta também inclui algumas funções que podem ajudar a implementar o "bloqueio pessimista" na sintaxe de seleção. Se você deseja implementar um "bloqueio compartilhado" na consulta, você pode usar o método sharedLock. O bloqueio compartilhado impede que as colunas de dados selecionadas sejam alteradas até que a transação seja confirmada:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Ou, você pode usar o método lockForUpdate. O uso do bloqueio "update" evita que as linhas sejam modificadas ou selecionadas por outros bloqueios compartilhados:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Depuração
Você pode usar os métodos dd ou dump para exibir o resultado da consulta ou a instrução SQL. O uso do método dd exibe as informações de depuração e interrompe a execução da solicitação. O método dump também exibe as informações de depuração, mas não interrompe a execução da solicitação:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Nota**
> A depuração requer a instalação do `symfony/var-dumper`, o comando é `composer require symfony/var-dumper`.
