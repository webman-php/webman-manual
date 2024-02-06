# Introdução rápida

O modelo webman baseia-se no [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Cada tabela de banco de dados possui um "modelo" correspondente para interagir com essa tabela. Você pode usar o modelo para consultar os dados da tabela e inserir novos registros na tabela.

Antes de começar, certifique-se de ter configurado a conexão com o banco de dados no arquivo `config/database.php`.

> Nota: Para suportar observadores de modelo, o Eloquent ORM requer a importação adicional `composer require "illuminate/events"` [Exemplo](#observadores-de-modelo)

## Exemplo
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Redefinir a chave primária, que por padrão é id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indica se os carimbos de data e hora são mantidos automaticamente
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Nome da tabela
Você pode especificar uma tabela personalizada definindo a propriedade `table` no modelo:
```php
class User extends Model
{
    /**
     * Nome da tabela associada ao modelo
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Chave primária
O Eloquent assume que cada tabela de dados possui uma coluna de chave primária chamada id. Você pode definir uma propriedade protegida `$primaryKey` para redefinir essa convenção.
```php
class User extends Model
{
    /**
     * Redefinir a chave primária, que por padrão é id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

O Eloquent assume que a chave primária é um valor inteiro incremental, o que significa que por padrão a chave primária será automaticamente convertida para o tipo int. Se você deseja usar uma chave primária não incremental ou não numérica, deve definir a propriedade pública `$incrementing` como false.
```php
class User extends Model
{
    /**
     * Indica se a chave primária do modelo é incremental
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Se sua chave primária não for um número inteiro, você precisará definir a propriedade protegida `$keyType` do modelo como string:
```php
class User extends Model
{
    /**
     * "Tipo" de ID auto incrementado.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Carimbos de data e hora
Por padrão, o Eloquent espera que sua tabela de dados contenha as colunas created_at e updated_at. Se você não quer que o Eloquent gerencie automaticamente essas duas colunas, defina a propriedade `$timestamps` do modelo como false:
```php
class User extends Model
{
    /**
     * Indica se os carimbos de data e hora são mantidos automaticamente
     *
     * @var bool
     */
    public $timestamps = false;
}
```
Se precisar personalizar o formato do carimbo de data e hora, defina a propriedade `$dateFormat` no seu modelo. Esta propriedade determina como as propriedades de data são armazenadas no banco de dados, bem como o formato de serialização do modelo para array ou JSON:
```php
class User extends Model
{
    /**
     * Formato de armazenamento do carimbo de data e hora
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Se você precisar personalizar os nomes das colunas de carimbos de data e hora, pode definir os valores das constantes CREATED_AT e UPDATED_AT no seu modelo:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Conexão com o banco de dados
Por padrão, os modelos Eloquent usarão a conexão padrão configurada para sua aplicação. Se desejar especificar uma conexão diferente para o modelo, defina a propriedade `$connection` no modelo:
```php
class User extends Model
{
    /**
     * Nome da conexão do modelo
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Valores padrão
Se desejar definir valores padrão para algumas propriedades do modelo, pode definir a propriedade `$attributes` no modelo:
```php
class User extends Model
{
    /**
     * Valores padrão do modelo.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Recuperação do modelo
Após criar o modelo associado a uma tabela, você pode consultar os dados do banco de dados. Imagine cada modelo Eloquent como um poderoso construtor de consulta que permite consultar rapidamente os dados associados a ele. Por exemplo:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Dica: Como os modelos Eloquent também são construtores de consulta, você também deve ler todos os métodos disponíveis em [Construtores de consulta](queries.md) e utilizá-los em consultas Eloquent.

## Restrições adicionais
O método all do Eloquent retorna todos os resultados do modelo. Como cada modelo Eloquent funciona como um construtor de consulta, você também pode adicionar restrições de consulta e, em seguida, obter os resultados da consulta usando o método get:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Recarregar o modelo
Você pode usar os métodos fresh e refresh para recarregar o modelo. O método fresh recarregará o modelo do banco de dados, sem afetar a instância existente:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

O método refresh reatribuirá os novos dados do banco de dados ao modelo existente. Além disso, os relacionamentos carregados também serão recarregados:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Coleção
Os métodos all e get do Eloquent podem recuperar vários resultados, retornando uma instância de `Illuminate\Database\Eloquent\Collection`. A classe `Collection` fornece muitos métodos auxiliares para manipular os resultados do Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Utilizando cursores
O método cursor permite iterar sobre o banco de dados usando cursores, executando a consulta apenas uma vez. Ao lidar com grandes quantidades de dados, o método cursor pode reduzir significativamente o uso de memória:
```php
foreach (app\model\User::where('sex', 1)->cursor() as $user) {
    //
}
```

O cursor retorna uma instância de `Illuminate\Support\LazyCollection`. [Coleções preguiçosas](https://laravel.com/docs/7.x/collections#lazy-collections) permitem usar a maioria dos métodos de coleções do Laravel, carregando apenas um único modelo na memória de cada vez:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Subconsultas Select
O Eloquent oferece suporte a subconsultas avançadas, permitindo extrair informações de tabelas relacionadas em uma única consulta. Por exemplo, digamos que tenhamos uma tabela de destinos (destinations) e uma tabela de voos para esses destinos (flights). A tabela flights contém um campo arrival_at que indica quando o voo chegou ao destino.

Usando a função de subconsulta proporcionada pelos métodos select e addSelect, podemos consultar todos os destinos e o nome do último voo a chegar a cada destino em uma única consulta:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```
## Ordenação por subconsulta
Além disso, o método orderBy do construtor de consulta também suporta subconsultas. Podemos usar esse recurso para classificar todos os destinos com base no tempo de chegada do último voo a cada destino. Novamente, isso pode ser feito com apenas uma consulta ao banco de dados:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Recuperação de um único modelo/colecção
Além de recuperar todos os registros de uma tabela especificada, você pode usar os métodos find, first ou firstWhere para recuperar um único registro. Esses métodos retornam uma única instância de modelo, em vez de uma coleção de modelos:
```php
// Localize um modelo por chave primária...
$flight = app\model\Flight::find(1);

// Localize o primeiro modelo correspondente à condição de consulta...
$flight = app\model\Flight::where('active', 1)->first();

// Localize o primeiro modelo correspondente à condição de consulta usando um atalho...
$flight = app\model\Flight::firstWhere('active', 1);
```

Também é possível chamar o método find com uma matriz de chaves primárias como argumento, que retornará uma coleção correspondente de registros:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Às vezes, você pode querer executar ações diferentes se não encontrar um modelo ao procurar o primeiro resultado. O método firstOr retornará o primeiro resultado se encontrado, caso contrário, executará o retorno de chamada fornecido. O valor de retorno do retorno de chamada será o valor retornado pelo método firstOr:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
O método firstOr também aceita uma matriz de colunas como argumento para a consulta:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## Exceção "Modelo não encontrado"
Às vezes, você pode querer lançar uma exceção quando não encontrar um modelo. Isso é útil em controladores e rotas. Os métodos findOrFail e firstOrFail irão buscar o primeiro resultado da consulta e, se não encontrado, lançar uma exceção Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```
## Coleção de Pesquisa
Você também pode utilizar os métodos count, sum e max, oferecidos pelo construtor de consultas, e outras funções de coleção para operar sobre coleções. Esses métodos retornarão apenas valores escalares apropriados em vez de uma instância do modelo:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Inserção
Para inserir um novo registro no banco de dados, primeiro crie uma nova instância do modelo, defina os atributos da instância e, em seguida, chame o método save:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Adicionar um novo registro à tabela de usuários
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validar a solicitação

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

Os carimbos de data created_at e updated_at serão automaticamente definidos (quando a propriedade $timestamps no modelo for verdadeira), portanto, não é necessário definir manualmente.

## Atualização
O método save também pode ser usado para atualizar um modelo existente no banco de dados. Para atualizar um modelo, você precisa primeiro recuperá-lo, definir os atributos a serem atualizados e, em seguida, chamar o método save. Da mesma forma, o carimbo de data updated_at será automaticamente atualizado, portanto, não é necessário definir manualmente:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Atualização em lote
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Verificação de Mudanças nos Atributos
O Eloquent fornece os métodos isDirty, isClean e wasChanged para verificar o estado interno do modelo e determinar como seus atributos mudaram desde a carga inicial. O método isDirty determina se algum atributo foi alterado desde o carregamento do modelo. Você pode passar um nome de atributo específico para determinar se algum atributo específico está sujo. O método isClean é o oposto de isDirty e também aceita um parâmetro de atributo opcional:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
O método wasChanged determina se algum atributo foi alterado desde a última vez que o modelo foi salvo durante o ciclo de solicitação atual. Você também pode passar o nome do atributo para ver se um atributo específico foi alterado:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```
## Atribuição em Massa
Você também pode usar o método create para salvar um novo modelo. Este método retornará a instância do modelo. No entanto, antes de usar, você precisa especificar os atributos fillable ou guarded no modelo, pois por padrão, todos os modelos Eloquent não aceitam atribuições em massa.

Quando os usuários enviam parâmetros HTTP inesperados e esses parâmetros modificam os campos do banco de dados que não devem ser alterados, uma vulnerabilidade de atribuição em massa ocorrerá. Por exemplo, um usuário mal-intencionado pode enviar o parâmetro is_admin por meio de uma solicitação HTTP e depois passá-lo para o método create, permitindo que o usuário se promova a administrador.

Portanto, antes de começar, você deve definir quais atributos do modelo podem ser atribuídos em massa. Você pode realizar isso usando a propriedade $fillable do modelo. Por exemplo, para permitir a atribuição em massa do atributo name do modelo Flight:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
Depois de definirmos os atributos que podem ser atribuídos em massa, podemos usar o método create para inserir novos dados no banco de dados. O método create retornará a instância do modelo salvo:

```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
Se você já tiver uma instância do modelo, poderá passar uma matriz para o método fill para atribuir valores:
```php
$flight->fill(['name' => 'Flight 22']);
```
$fillable pode ser considerado como uma "lista branca" de atribuições em massa e você também pode usar a propriedade $guarded para implementar uma "lista negra" de atribuições em massa. A propriedade $guarded contém um array de atributos que não são permitidos em atribuições em massa. Em outras palavras, $guarded funcionará mais como uma "lista negra". Importante: você só pode usar $fillable ou $guarded, não ambos simultaneamente. No exemplo a seguir, todos os atributos, exceto price, podem ser atribuídos em massa:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Atributos que não são permitidos em atribuições em massa.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Se você deseja permitir que todos os atributos sejam atribuídos em massa, pode definir $guarded como um array vazio:
```php
/**
 * Atributos que não são permitidos em atribuições em massa.
 *
 * @var array
 */
protected $guarded = [];
```
## Outros Métodos de Criação
firstOrCreate/ firstOrNew

Aqui estão dois métodos que você pode usar para atribuições em massa: firstOrCreate e firstOrNew. O método firstOrCreate tentará corresponder os dados do banco de dados com um par chave/valor fornecido. Se o modelo não for encontrado no banco de dados, será inserido um registro contendo os atributos do primeiro parâmetro e, opcionalmente, os atributos do segundo parâmetro.

O método firstOrNew funcionará da mesma forma que o método firstOrCreate ao tentar encontrar um registro correspondente com os atributos fornecidos. No entanto, se o método firstOrNew não encontrar um modelo correspondente, ele retornará uma nova instância do modelo. Observe que a instância retornada por firstOrNew ainda não foi salva no banco de dados e você precisará chamar manualmente o método save:
```php
// Procurar voo pelo nome, se não encontrar, criar...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Procurar voo pelo nome ou criar um novo com nome, atrasado e hora de chegada...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Procurar voo pelo nome, se não encontrar, criar uma instância nova...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Procurar voo pelo nome ou criar uma nova instância com nome, atrasado e hora de chegada...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

Você pode encontrar situações em que deseja atualizar um modelo existente ou criar um novo modelo se ele não existir. O método updateOrCreate realiza essas duas etapas em um único passo. Semelhante ao método firstOrCreate, updateOrCreate persiste o modelo, portanto, não é necessário chamar save():

```php
// Se houver um voo de Oakland para San Diego, defina o preço como $99.
// Se não houver um modelo correspondente, ele será criado.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Excluir Modelo

Você pode chamar o método delete em uma instância do modelo para excluí-la:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Excluir Modelo por Chave Primária
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Excluir Modelo por Consulta
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Clonar Modelo
Você pode usar o método replicate para criar uma nova instância ainda não salva no banco de dados. Este método é muito útil quando as instâncias do modelo compartilham muitos atributos:
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();
```

## Comparação de Modelos
Às vezes, pode ser necessário determinar se dois modelos são "iguais". O método is pode ser usado para verificar rapidamente se dois modelos têm a mesma chave primária, tabela e conexão com o banco de dados:
```php
if ($post->is($anotherPost)) {
    //
}
```


## Observadores de Modelos
Consulte o [Evento de Modelo e Observador no Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Observação: Para que o Eloquent ORM suporte observadores de modelos, é necessário importar adicionalmente o pacote composer require "illuminate/events".
```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```

