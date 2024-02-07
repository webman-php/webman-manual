# Começando rapidamente

O modelo webman é baseado no [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Cada tabela do banco de dados tem um "modelo" correspondente para interagir com essa tabela. Você pode usar o modelo para consultar os dados da tabela e inserir novos registros na tabela.

Antes de começar, verifique se a conexão com o banco de dados está configurada em `config/database.php`.

> Nota: Para que o Eloquent ORM suporte observadores de modelos, é necessário importar adicionalmente `composer require "illuminate/events"` [Exemplo](#模型观察者)

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
     * Indica se mantém automaticamente os carimbos de data/hora
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Nome da tabela
Você pode especificar uma tabela de dados personalizada definindo o atributo `table` no modelo:
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
O Eloquent presume que cada tabela de dados tem uma coluna de chave primária chamada id. Você pode definir um atributo protegido `$primaryKey` para reescrever esse padrão.
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

O Eloquent presume que a chave primária é um valor inteiro autoincrementável, o que significa que, por padrão, a chave primária é automaticamente convertida para o tipo int. Se deseja usar uma chave primária não incremental ou não numérica, é necessário definir o atributo público `$incrementing` como false.
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

Se a sua chave primária não for um número inteiro, você precisa definir o atributo protegido `$keyType` no modelo como string:
```php
class User extends Model
{
    /**
     * "Tipo" do ID autoincrementável
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Carimbos de data/hora
Por padrão, o Eloquent espera que a sua tabela de dados tenha as colunas created_at e updated_at. Se não quiser que o Eloquent gerencie automaticamente essas duas colunas, defina o atributo $timestamps do modelo como false:
```php
class User extends Model
{
    /**
     * Indica se mantém automaticamente os carimbos de data/hora
     *
     * @var bool
     */
    public $timestamps = false;
}
```

Se precisar personalizar o formato de carimbos de data/hora, defina o atributo $dateFormat no seu modelo. Este atributo determina como as propriedades de data são armazenadas no banco de dados e como o modelo é serializado para um array ou formato JSON:
```php
class User extends Model
{
    /**
     * Formato de armazenamento de carimbos de data/hora
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Se precisar personalizar os nomes dos campos de armazenamento de carimbos de data/hora, pode definir os valores das constantes CREATED_AT e UPDATED_AT no modelo:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Conexão com o banco de dados
Por padrão, os modelos Eloquent usarão a conexão com o banco de dados padrão configurada em sua aplicação. Se quiser especificar uma conexão diferente para o modelo, defina o atributo $connection:
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
Se desejar definir valores padrão para algumas propriedades do modelo, pode definir o atributo $attributes no modelo:
```php
class User extends Model
{
    /**
     * Valores padrão das propriedades do modelo
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Recuperação do modelo
Após criar o modelo e a tabela de banco de dados associada, pode consultar os dados do banco de dados. Pode imaginar cada modelo Eloquent como um construtor de consultas poderoso, e usá-lo para consultar a tabela associada de forma mais rápida. Por exemplo:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```

> Dica: Como os modelos Eloquent também são construtores de consultas, é recomendado ler todos os métodos disponíveis em [Construtor de consultas](queries.md). Pode usar esses métodos em consultas Eloquent.

## Restrições adicionais
O método all do Eloquent retornará todos os resultados do modelo. Como cada modelo Eloquent atua como um construtor de consultas, também pode adicionar condições de consulta e, em seguida, usar o método get para obter os resultados da consulta:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Recarregar o modelo
Pode usar os métodos fresh e refresh para recarregar o modelo. O método fresh irá recuperar o modelo do banco de dados. A instância existente do modelo não será afetada:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

O método refresh usa os novos dados do banco de dados para recarregar a instância existente do modelo. Além disso, os relacionamentos carregados serão recarregados:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Coleções
Os métodos all e get do Eloquent podem recuperar vários resultados e retornar uma instância de `Illuminate\Database\Eloquent\Collection`. A classe `Collection` fornece uma variedade de métodos auxiliares para lidar com os resultados Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```
## Uso de cursor
O método cursor permite usar um cursor para percorrer o banco de dados, executando a consulta apenas uma vez. Ao lidar com grandes volumes de dados, o método cursor pode reduzir significativamente o uso de memória:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

O método cursor retorna uma instância `Illuminate\Support\LazyCollection`. As [coleções Lazy](https://laravel.com/docs/7.x/collections#lazy-collections) permitem usar a maioria dos métodos de coleção do Laravel e carregar apenas um único modelo na memória de cada vez:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```
## Subconsultas Select
O Eloquent fornece suporte avançado para subconsultas, permitindo extrair informações das tabelas relacionadas em uma única consulta. Por exemplo, suponha que tenhamos uma tabela de destinos chamada destinations e uma tabela de voos para esses destinos chamada flights. A tabela flights contém um campo arrival_at, que indica quando o voo chegou ao destino.

Usando os métodos select e addSelect fornecidos pela funcionalidade de subconsulta, podemos buscar todos os destinos destinations em uma única consulta, juntamente com o nome do último voo que chegou a cada destino:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```
## Ordenação com base em Subconsultas
Além disso, o método orderBy do construtor de consulta também suporta subconsultas. Podemos usar essa funcionalidade para ordenar todos os destinos com base no horário de chegada do último voo ao destino. Novamente, isso pode ser feito em uma única consulta ao banco de dados:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Recuperando um Único Modelo / Coleção
Além de recuperar todos os registros de uma tabela especificada, você pode usar os métodos find, first ou firstWhere para recuperar um único registro. Esses métodos retornam uma única instância de modelo, em vez de uma coleção de modelos:
```php
// Buscar um modelo por chave primária...
$flight = app\model\Flight::find(1);

// Buscar o primeiro modelo que atende à condição da consulta...
$flight = app\model\Flight::where('active', 1)->first();

// Buscar o primeiro modelo que atende à condição da consulta de forma rápida...
$flight = app\model\Flight::firstWhere('active', 1);
```
Você também pode usar um array de chaves primárias como argumento para o método find, que retornará uma coleção dos registros correspondentes:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```
Às vezes, você pode querer executar outras ações caso não encontre o primeiro resultado. O método firstOr retornará o primeiro resultado encontrado e, se não houver nenhum resultado, executará a função de retorno de chamada fornecida. O valor retornado pela função de retorno de chamada será o valor de retorno do método firstOr:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
O método firstOr também aceita um array de campos para consulta:
```php
$model = app\model\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```
## Exceção "Modelo Não Encontrado"
Às vezes, você deseja lançar uma exceção quando um modelo não é encontrado. Isso é útil em controladores e rotas. Os métodos findOrFail e firstOrFail buscarão o primeiro resultado da consulta e, se não encontrarem, lançarão uma exceção ModelNotFoundException do Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```

## Recuperando Coleções
Você também pode usar os métodos count, sum e max fornecidos pelo construtor de consultas e outras funções de coleção para manipular coleções. Esses métodos retornarão apenas valores escalares apropriados, em vez de uma instância de modelo:
```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## Inserção
Para inserir um novo registro no banco de dados, crie primeiro uma nova instância de modelo, defina os atributos e, em seguida, chame o método save:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Adiciona um novo registro à tabela de usuários
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validar a requisição

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```
Os carimbos de data created_at e updated_at serão automaticamente definidos (quando a propriedade $timestamps do modelo estiver definida como verdadeira), sem necessidade de atribuição manual.

## Atualização
O método save também pode ser usado para atualizar um modelo existente no banco de dados. Para atualizar um modelo, primeiro recupere-o, defina os atributos a serem atualizados e, em seguida, chame o método save. Da mesma forma, o carimbo de data updated_at será automaticamente atualizado, sem necessidade de atribuição manual:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Atualização em Lote
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Verificação de Mudanças de Atributos
O Eloquent fornece os métodos isDirty, isClean e wasChanged para verificar o estado interno do modelo e determinar como seus atributos mudaram desde a carga inicial. O método isDirty determina se algum atributo foi alterado desde a carga do modelo. Você pode passar um nome de atributo específico para determinar se um atributo específico foi modificado. O método isClean é o oposto de isDirty e também aceita um parâmetro opcional de atributo:
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
O método wasChanged determina se houve mudanças nos atributos desde a última vez que o modelo foi salvo durante o ciclo de solicitação atual. Você também pode passar o nome do atributo para ver se um atributo específico foi alterado:
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
## Atribuição em massa
Também é possível usar o método create para salvar um novo modelo. Este método retornará uma instância do modelo. No entanto, antes de usar, é necessário especificar as propriedades fillable ou guarded no modelo, pois por padrão, todos os modelos Eloquent não permitem atribuições em massa.

Uma vulnerabilidade de atribuição em massa ocorre quando um usuário envia parâmetros HTTP inesperados e esses parâmetros alteram campos no banco de dados que não deveriam ser alterados. Por exemplo, um usuário mal-intencionado pode enviar o parâmetro is_admin através de uma solicitação HTTP e, em seguida, passá-lo para o método create, o que permitiria ao usuário se promover a administrador.

Portanto, antes de começar, é importante definir quais propriedades do modelo podem ser atribuídas em massa. Isso pode ser feito através da propriedade $fillable do modelo. Por exemplo, para permitir que a propriedade name do modelo Flight seja atribuída em massa:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Propriedades que podem ser atribuídas em massa.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
Uma vez que tenhamos definido as propriedades que podem ser atribuídas em massa, podemos inserir novos dados no banco de dados usando o método create. Este método retornará a instância do modelo salvo:
```php
$flight = app\modle\Flight::create(['name' => 'Voo 10']);
```
Se você já tiver uma instância do modelo, pode passar um array para o método fill para atribuir valores:
```php
$flight->fill(['name' => 'Voo 22']);
```

A propriedade $fillable pode ser considerada como uma "lista branca" para atribuições em massa, mas também é possível utilizar a propriedade $guarded. A propriedade $guarded contém um array de atributos que não podem ser atribuídos em massa. Em outras palavras, $guarded funciona mais como uma "lista negra". Importante ressaltar: é possível utilizar apenas $fillable ou $guarded, não ambos simultaneamente. No exemplo a seguir, todos os atributos, exceto price, podem ser atribuídos em massa:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Atributos que não podem ser atribuídos em massa.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Se desejar permitir que todos os atributos sejam atribuídos em massa, basta definir $guarded como um array vazio:
```php
/**
 * Atributos que não podem ser atribuídos em massa.
 *
 * @var array
 */
protected $guarded = [];
```

## Outros métodos de criação
firstOrCreate/firstOrNew
Aqui estão dois métodos que podem ser úteis para atribuições em massa: firstOrCreate e firstOrNew. O método firstOrCreate tentará corresponder aos dados no banco de dados usando pares chave/valor fornecidos. Se o modelo não for encontrado no banco de dados, ele será inserido, com as propriedades do primeiro parâmetro e, opcionalmente, as propriedades do segundo parâmetro.

O método firstOrNew funciona de forma semelhante ao firstOrCreate, tentando encontrar um registro no banco de dados com as propriedades fornecidas. No entanto, se o método firstOrNew não encontrar um modelo correspondente, ele retornará uma nova instância do modelo. É importante observar que a instância retornada por firstOrNew ainda não foi salva no banco de dados e você precisará chamar o método save manualmente:
```php
// Busca o voo pelo nome, se não existir, cria...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Voo 10']);

// Busca o voo pelo nome e, se não existir, cria com as propriedades 'atrasado' e 'hora_chegada'...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Voo 10'],
    ['atrasado' => 1, 'hora_chegada' => '11:30']
);

// Busca o voo pelo nome, se não existir, cria uma nova instância...
$flight = app\modle\Flight::firstOrNew(['name' => 'Voo 10']);

// Busca o voo pelo nome e, se não existir, cria uma nova instância com as propriedades 'atrasado' e 'hora_chegada'...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Voo 10'],
    ['atrasado' => 1, 'hora_chegada' => '11:30']
);
```

Também é possível encontrar casos em que seja desejável atualizar um modelo existente ou criar um novo modelo se ele não existir. Isso pode ser feito de uma vez com o método updateOrCreate. Similar ao método firstOrCreate, o updateOrCreate persiste o modelo, então não é necessário chamar save():
```php
// Se existir um voo de Oakland para San Diego, o preço será de 99 dólares.
// Se não houver um modelo correspondente, um novo será criado.
$flight = app\modle\Flight::updateOrCreate(
    ['partida' => 'Oakland', 'destino' => 'San Diego'],
    ['preço' => 99, 'desconto' => 1]
);
```

## Excluir modelo
É possível chamar o método delete em uma instância do modelo para excluí-la:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Excluir modelo pelo ID
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## Excluir modelo por consulta
```php
$linhasExcluidas = app\modle\Flight::where('ativo', 0)->delete();
```
## Replicar Modelo
Você pode usar o método replicate para criar uma nova instância do modelo que ainda não foi salva no banco de dados. Este método é útil quando várias instâncias de modelo compartilham muitas propriedades em comum.
```php
$endereco = App\Endereço::create([
    'tipo' => 'remessa',
    'linha_1' => '123 Rua Exemplo',
    'cidade' => 'Victorville',
    'estado' => 'CA',
    'código_postal' => '90001',
]);

$cobranca= $endereco->replicate()->fill([
    'tipo' => 'cobrança '
]);

$cobranca->save();
```

## Comparação de Modelos
Às vezes, pode ser necessário verificar se dois modelos são "iguais". O método is pode ser usado para verificar rapidamente se dois modelos têm a mesma chave primária, tabela e conexão de banco de dados:
```php
if ($post->is($outraPostagem)) {
    //
}
```

## Observadores de Modelos
Para aprender mais sobre eventos e observadores de modelos em Laravel, consulte [Eventos do Modelo e Observador no Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel).

Importante: para o Eloquent ORM suportar observadores de modelos, é necessário importar adicionamente o Illuminate/events via composer require "illuminate/events".
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

