# Guide de démarrage rapide

Le modèle webman est basé sur [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Chaque table de base de données a un "modèle" correspondant pour interagir avec cette table. Vous pouvez interroger les données de la table à l'aide du modèle et insérer de nouveaux enregistrements dans la table.

Avant de commencer, assurez-vous d'avoir configuré la connexion à la base de données dans `config/database.php`.

> Remarque : Pour prendre en charge les observateurs de modèle, Eloquent ORM nécessite l'importation supplémentaire de `composer require "illuminate/events"` [Exemple](#模型观察者)

## Exemple
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Nom de la table associée au modèle
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Redéfinir la clé primaire, par défaut c'est id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indique si l'entretien des horodatages est automatique
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Nom de la table
Vous pouvez spécifier une table personnalisée en définissant l'attribut table sur le modèle :
```php
class User extends Model
{
    /**
     * Nom de la table associée au modèle
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Clé primaire
Eloquent suppose également que chaque table a une colonne de clé primaire nommée id. Vous pouvez redéfinir cette convention en définissant un attribut protégé $primaryKey.
```php
class User extends Model
{
    /**
     * Redéfinir la clé primaire, par défaut c'est id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent suppose que la clé primaire est une valeur entière incrémentielle, ce qui signifie que par défaut, la clé primaire sera automatiquement convertie en un type entier. Si vous souhaitez utiliser une clé primaire non incrémentielle ou non numérique, vous devez définir l'attribut public $incrementing sur false.
```php
class User extends Model
{
    /**
     * Indique si la clé primaire du modèle est incrémentée
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Si votre clé primaire n'est pas un entier, vous devez définir l'attribut protégé $keyType du modèle sur string :
```php
class User extends Model
{
    /**
     * "Type" d'ID incrémenté automatiquement.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Horodatage
Par défaut, Eloquent s'attend à ce que votre table de données contienne created_at et updated_at. Si vous ne voulez pas qu'Eloquent gère automatiquement ces deux colonnes, définissez l'attribut $timestamps du modèle sur false :
```php
class User extends Model
{
    /**
     * Indique si l'entretien des horodatages est automatique
     *
     * @var bool
     */
    public $timestamps = false;
}
```
Si vous avez besoin de formater l'horodatage de stockage, définissez l'attribut $dateFormat de votre modèle. Cet attribut détermine la façon dont les propriétés de date sont stockées dans la base de données, ainsi que le format de sérialisation du modèle en tableau ou en JSON :
```php
class User extends Model
{
    /**
     * Format de stockage de l'horodatage
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Si vous devez personnaliser les noms de champ de stockage de l'horodatage, vous pouvez définir les valeurs des constantes CREATED_AT et UPDATED_AT dans le modèle :
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Connexion à la base de données
Par défaut, les modèles Eloquent utiliseront la connexion de base de données par défaut configurée pour votre application. Si vous souhaitez spécifier une connexion différente pour le modèle, définissez l'attribut $connection :
```php
class User extends Model
{
    /**
     * Nom de connexion du modèle
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Valeurs par défaut
Si vous souhaitez définir des valeurs par défaut pour certaines propriétés du modèle, vous pouvez les définir dans l'attribut $attributes du modèle :
```php
class User extends Model
{
    /**
     * Valeurs par défaut du modèle.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Récupération du modèle
Une fois que vous avez créé un modèle et sa table de base de données associée, vous pouvez interroger la base de données. Imaginez chaque modèle Eloquent comme un puissant constructeur de requêtes, que vous pouvez utiliser pour interroger plus rapidement la table associée. Par exemple :
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Remarque : Puisque les modèles Eloquent sont également des constructeurs de requêtes, vous devriez également lire tous les [méthodes disponibles du constructeur de requêtes](queries.md) que vous pouvez utiliser dans les requêtes Eloquent.

## Contraintes supplémentaires
La méthode all d'Eloquent retourne tous les résultats du modèle. Comme chaque modèle Eloquent agit en tant que constructeur de requêtes, vous pouvez également ajouter des conditions de requête, puis utiliser la méthode get pour obtenir les résultats de la requête :
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Rechargement du modèle
Vous pouvez utiliser les méthodes fresh et refresh pour recharger un modèle. La méthode fresh recharge le modèle à partir de la base de données. L'instance de modèle existante n'est pas affectée :
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

La méthode refresh utilise les nouvelles données de la base de données pour recharger le modèle existant. De plus, les relations déjà chargées seront également rechargées :
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Collection
Les méthodes all et get d'Eloquent peuvent interroger plusieurs résultats et renvoyer une instance de `Illuminate\Database\Eloquent\Collection`. La classe `Collection` offre de nombreuses méthodes d'aide pour manipuler les résultats Eloquent :
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Utilisation du curseur
La méthode cursor vous permet de parcourir la base de données à l'aide d'un curseur, elle exécute la requête une seule fois. Lors du traitement d'une grande quantité de données, la méthode cursor peut considérablement réduire l'utilisation de la mémoire :
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursor renvoie une instance `Illuminate\Support\LazyCollection`. Les [Collections paresseuses](https://laravel.com/docs/7.x/collections#lazy-collections) vous permettent d'utiliser la plupart des méthodes de collection Laravel, et chargent uniquement un modèle à la fois en mémoire :
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Sous-requêtes SELECT
Eloquent propose une prise en charge avancée des sous-requêtes, vous pouvez extraire des informations de tables connexes avec une seule requête. Par exemple, supposons que nous avons une table de destinations destinations et une table de vols flights vers ces destinations. La table de vols contient un champ arrival_at, indiquant quand le vol arrive à destination.

Grâce aux méthodes select et addSelect fournies par la fonctionnalité des sous-requêtes, nous pouvons interroger toutes les destinations destinations en un seul appel de méthode, ainsi que le nom du dernier vol arrivant à chaque destination :
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Trier selon une sous-requête
De plus, la fonction orderBy du constructeur de requêtes prend également en charge les sous-requêtes. Nous pouvons utiliser cette fonction pour trier toutes les destinations en fonction de l'heure d'arrivée du dernier vol à destination. De même, cela peut être accompli en une seule requête à la base de données :
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```
## Récupération d'un seul modèle / collection
En plus de récupérer tous les enregistrements dans une table spécifiée, vous pouvez utiliser les méthodes find, first ou firstWhere pour récupérer un seul enregistrement. Ces méthodes renvoient une seule instance de modèle, au lieu de renvoyer une collection de modèles :
```php
// Rechercher un modèle par clé primaire...
$flight = app\model\Flight::find(1);

// Trouver le premier modèle correspondant à la condition de recherche...
$flight = app\model\Flight::where('active', 1)->first();

// Trouver la première implémentation rapide correspondant à la condition de recherche...
$flight = app\model\Flight::firstWhere('active', 1);
```

Vous pouvez également utiliser un tableau de clés primaires comme paramètre pour la méthode find, elle renverra une collection d'enregistrements correspondants :
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Parfois, vous souhaiterez exécuter d'autres actions si vous ne trouvez pas de valeur lors de la recherche du premier résultat. La méthode firstOr retournera le premier résultat s'il est trouvé, sinon elle exécutera le rappel donné. La valeur de retour du rappel sera retournée par la méthode firstOr :
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
La méthode firstOr accepte également un tableau de colonnes pour la recherche :
```php
$model = app\model\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## Exception de "Non trouvé"
Parfois, vous voudrez déclencher une exception en cas de modèle non trouvé. Cela est très utile dans les contrôleurs et les routes. Les méthodes findOrFail et firstOrFail récupéreront le premier résultat de la requête, et si aucun résultat n'est trouvé, elles lèveront une exception de type Illuminate\Database\Eloquent\ModelNotFoundException :
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```
## Collections

Vous pouvez également utiliser les méthodes count, sum et max fournies par le constructeur de requêtes pour effectuer des opérations sur des collections. Ces méthodes ne renverront qu'une valeur scalaire appropriée au lieu d'une instance de modèle :
```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## Insertion

Pour ajouter un nouvel enregistrement à la base de données, créez d'abord une nouvelle instance de modèle, définissez les attributs de l'instance, puis appelez la méthode save :
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Ajouter un nouvel enregistrement dans la table des utilisateurs
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Valider la demande

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

Les horodatages created_at et updated_at seront automatiquement définis (lorsque la propriété $timestamps du modèle est true), ils n'ont pas besoin d'être définis manuellement.

## Mise à jour

La méthode save peut également être utilisée pour mettre à jour des modèles existants dans la base de données. Pour mettre à jour un modèle, récupérez-le d'abord, définissez les propriétés à mettre à jour, puis appelez la méthode save. De même, l'horodatage updated_at sera automatiquement mis à jour, il n'est donc pas nécessaire de le définir manuellement :
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Mise à jour par lot
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Vérification des changements de propriété
Eloquent fournit les méthodes isDirty, isClean et wasChanged pour vérifier l'état interne du modèle et déterminer comment ses attributs ont changé depuis leur chargement initial. La méthode isDirty permet de déterminer si un attribut a été modifié depuis le chargement du modèle. Vous pouvez également transmettre un nom d'attribut spécifique pour vérifier un attribut particulier. La méthode isClean est l'inverse de isDirty et accepte également des paramètres d'attribut facultatifs :
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
La méthode wasChanged détermine si un attribut a été modifié lors de la dernière sauvegarde du modèle au cours du cycle de vie actuel de la requête. Vous pouvez également transmettre le nom de l'attribut pour voir si un attribut spécifique a été modifié :
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

## Affectation en masse
Vous pouvez également utiliser la méthode create pour enregistrer de nouveaux modèles. Cette méthode renverra une instance de modèle. Cependant, avant de l'utiliser, vous devez spécifier les attributs fillable ou guarded sur le modèle, car par défaut, tous les modèles Eloquent ne permettent pas l'affectation en masse.

Lorsqu'un utilisateur transmet accidentellement des paramètres HTTP inattendus qui modifient des champs de la base de données que vous n'avez pas l'intention de modifier, une vulnérabilité d'affectation en masse se produit. Par exemple, un utilisateur malveillant pourrait transmettre un paramètre is_admin via une requête HTTP, puis l'utiliser avec la méthode create pour se promouvoir en administrateur.

Avant de commencer, vous devez définir quels attributs du modèle peuvent être affectés en masse. Vous pouvez le faire en utilisant la propriété $fillable sur le modèle. Par exemple, pour autoriser l'affectation en masse de l'attribut name du modèle Flight :
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributs du modèle pouvant être affectés en masse.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
Une fois que vous avez défini les attributs pouvant être affectés en masse, vous pouvez insérer de nouvelles données dans la base de données en utilisant la méthode create. La méthode create renverra l'instance de modèle enregistrée :
```php
$flight = app\model\Flight::create(['name' => 'Flight 10']);
```
Si vous avez déjà une instance de modèle, vous pouvez passer un tableau à la méthode fill pour effectuer une affectation :
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable peut être considéré comme une "liste blanche" pour l'affectation en masse. Vous pouvez également utiliser la propriété $guarded pour ce faire. La propriété $guarded contient un tableau d'attributs qui ne sont pas autorisés à être affectés en masse. Autrement dit, $guarded fonctionnera plus comme une "liste noire". Remarque : vous ne pouvez utiliser que $fillable ou $guarded, pas les deux en même temps. Dans l'exemple suivant, tous les attributs sauf price peuvent faire l'objet d'une affectation en masse :
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributs du modèle non autorisés à être affectés en masse.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Si vous souhaitez autoriser l'affectation en masse pour tous les attributs, vous pouvez définir $guarded comme un tableau vide :
```php
/**
 * Attributs du modèle non autorisés à être affectés en masse.
 *
 * @var array
 */
protected $guarded = [];
```
## Autres méthodes de création
firstOrCreate / firstOrNew

Il existe deux méthodes que vous pouvez utiliser pour l'affectation en masse : firstOrCreate et firstOrNew. La méthode firstOrCreate recherchera des données dans la base de données en utilisant la paire clé/valeur donnée. Si le modèle n'est pas trouvé dans la base de données, il insérera un enregistrement contenant les attributs du premier paramètre et éventuellement les attributs du deuxième paramètre.

La méthode firstOrNew fonctionne de la même manière que firstOrCreate en tentant de rechercher des enregistrements dans la base de données en utilisant les attributs donnés. Cependant, si firstOrNew ne trouve pas le modèle correspondant, elle renverra une nouvelle instance de modèle. Veuillez noter que la méthode firstOrNew ne sauvegarde pas cette nouvelle instance dans la base de données, vous devrez appeler manuellement la méthode save pour ce faire :
```php
// Rechercher un vol par nom et le créer s'il n'existe pas...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Vol 10']);

// Rechercher un vol par nom et créer un enregistrement avec nom, retard et heure d'arrivée...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Vol 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Rechercher un vol par nom et créer une nouvelle instance...
$flight = app\modle\Flight::firstOrNew(['name' => 'Vol 10']);

// Rechercher un vol par nom et créer une nouvelle instance avec nom, retard et heure d'arrivée...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Vol 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

```

Il peut également arriver que vous souhaitiez mettre à jour un modèle existant ou en créer un nouveau s'il n'existe pas. La méthode updateOrCreate facilite cela en une seule opération. Similaire à la méthode firstOrCreate, updateOrCreate persiste le modèle, donc pas besoin d'appeler save() :
```php
// Si un vol d'Oakland à San Diego existe, le prix est fixé à 99 dollars.
// Sinon, créer un nouveau modèle.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);

```

## Suppression de modèles

Vous pouvez appeler la méthode delete sur une instance de modèle pour supprimer l'instance :
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Suppression par clé primaire
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Suppression par requête
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Copie de modèle
Vous pouvez utiliser la méthode replicate pour créer une nouvelle instance non enregistrée. Cette méthode est très utile lorsque les instances de modèles partagent de nombreuses propriétés similaires.
```php
$shipping = App\Address::create([
    'type' => 'expédition',
    'line_1' => '123 rue Exemple',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'facturation'
]);

$billing->save();

```

## Comparaison de modèles
Parfois, vous devrez vérifier si deux modèles sont "identiques". La méthode is peut être utilisée pour vérifier rapidement si deux modèles ont la même clé primaire, le même tableau et la même connexion à la base de données :
```php
if ($post->is($anotherPost)) {
    //
}
```

## Observateurs de modèles

Pour utiliser les observateurs de modèles, veuillez consulter[Laravel 中的模型事件与 Observer](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Remarque : Pour que Eloquent ORM prenne en charge les observateurs de modèles, vous devez importer supplémentairement composer require "illuminate/events"

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

