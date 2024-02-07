# Démarrage rapide

Le modèle webman est basé sur [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Chaque table de base de données a un "modèle" correspondant pour interagir avec cette table. Vous pouvez utiliser le modèle pour interroger les données de la table et insérer de nouveaux enregistrements dans la table.

Avant de commencer, assurez-vous que la connexion à la base de données est configurée dans `config/database.php`.

> Remarque : Pour prendre en charge les observateurs de modèle, Eloquent ORM nécessite également l'importation supplémentaire de `composer require "illuminate/events"` [Exemple](#模型观察者)

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
     * Redéfinit la clé primaire qui est normalement 'id'
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indique si la gestion des horodatages est automatique
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Nom de la table
Vous pouvez spécifier une table personnalisée en définissant la propriété de table sur le modèle :
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
Eloquent suppose également qu'une colonne de clé primaire nommée 'id' existe dans chaque table. Vous pouvez redéfinir cette convention en définissant une propriété protégée $primaryKey :
```php
class User extends Model
{
    /**
     * Redéfinit la clé primaire qui est normalement 'id'
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent suppose que la clé primaire est une valeur entière incrémentielle, ce qui signifie que par défaut, la clé primaire sera automatiquement convertie en type entier. Si vous souhaitez utiliser une clé primaire non incrémentielle ou non numérique, vous devez définir la propriété publique $incrementing sur false :
```php
class User extends Model
{
    /**
     * Indique si la clé primaire du modèle est auto-incrémentée
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Si votre clé primaire n'est pas un entier, vous devez définir la propriété protégée $keyType sur "string" dans le modèle :
```php
class User extends Model
{
    /**
     * Type d'ID auto-incrémenté.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Horodatage
Par défaut, Eloquent s'attend à ce que les colonnes created_at et updated_at existent dans votre table. Si vous ne souhaitez pas qu'Eloquent gère automatiquement ces deux colonnes, définissez la propriété $timestamps du modèle sur false :
```php
class User extends Model
{
    /**
     * Indique si la gestion automatique des horodatages est activée
     *
     * @var bool
     */
    public $timestamps = false;
}
```

Si vous avez besoin de personnaliser le format des horodatages, définissez la propriété $dateFormat dans votre modèle. Cette propriété détermine le format de stockage des attributs de date dans la base de données, ainsi que le format de sérialisation du modèle dans un tableau ou en JSON :
```php
class User extends Model
{
    /**
     * Format de stockage des horodatages
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Si vous devez personnaliser les noms des champs de stockage des horodatages, vous pouvez définir les valeurs des constantes CREATED_AT et UPDATED_AT dans le modèle :
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Connexion à la base de données
Par défaut, les modèles Eloquent utiliseront la connexion à la base de données par défaut configurée pour votre application. Si vous souhaitez spécifier une connexion différente pour le modèle, définissez la propriété $connection :
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
Si vous devez définir des valeurs par défaut pour certaines propriétés du modèle, vous pouvez définir la propriété $attributes sur le modèle :
```php
class User extends Model
{
    /**
     * Valeurs par défaut du modèle
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Recherche de modèle
Une fois que vous avez créé un modèle et sa table de base de données associée, vous pouvez interroger les données de la base de données. Imaginez chaque modèle Eloquent comme un puissant constructeur de requêtes que vous pouvez utiliser pour interroger rapidement les tables liées. Par exemple :
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Astuce : Étant donné que les modèles Eloquent sont également des constructeurs de requêtes, vous devriez également consulter tous les [méthodes disponibles dans le constructeur de requêtes](queries.md). Ces méthodes sont utilisables dans les requêtes Eloquent.

## Contraintes supplémentaires
La méthode all d'Eloquent renverra tous les résultats du modèle. Comme chaque modèle Eloquent agit en tant que constructeur de requêtes, vous pouvez également ajouter des conditions de recherche, puis obtenir les résultats de la requête à l'aide de la méthode get :
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Rechargement du modèle
Vous pouvez recharger un modèle en utilisant les méthodes fresh et refresh. La méthode fresh rechargera le modèle depuis la base de données. L'instance existante du modèle ne sera pas affectée :
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

La méthode refresh attribue à nouveau au modèle existant les nouvelles données de la base de données. De plus, les relations chargées seront également rechargées :
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Collection
Les méthodes all et get d'Eloquent peuvent interroger de multiples résultats et retourner une instance de `Illuminate\Database\Eloquent\Collection`. La classe `Collection` propose de nombreuses fonctions auxiliaires pour traiter les résultats Eloquent :
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Utilisation de curseurs
La méthode cursor vous permet de parcourir la base de données en utilisant un curseur, ce qui ne nécessite qu'une seule requête. Lorsque vous manipulez de grandes quantités de données, la méthode cursor peut grandement réduire l'utilisation de la mémoire :
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

La méthode cursor renvoie une instance de `Illuminate\Support\LazyCollection`. Les [collections paresseuses](https://laravel.com/docs/7.x/collections#lazy-collections) vous permettent d'utiliser la plupart des méthodes de collection Laravel, en chargeant un seul modèle en mémoire à la fois :
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Sous-requêtes de sélection
Eloquent offre un support avancé pour les sous-requêtes, vous permettant d'extraire des informations de tables connexes en une seule requête. Par exemple, supposons que nous ayons une table de destination `destinations` et une table de vol vers cette destination `flights`. La table `flights` contient un champ `arrival_at` représentant l'heure d'arrivée du vol à la destination.

En utilisant les méthodes select et addSelect des sous-requêtes, nous pouvons interroger toutes les destinations `destinations`, ainsi que le nom du dernier vol arrivé à chaque destination :
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Tri selon une sous-requête
De plus, la fonction orderBy du constructeur de requêtes prend également en charge les sous-requêtes. Nous pouvons utiliser cette fonction pour trier toutes les destinations en fonction de l'heure d'arrivée du dernier vol à chaque destination. De même, cela ne nécessite qu'une seule requête à la base de données :
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```
## Rechercher un modèle / une collection
En plus de récupérer tous les enregistrements d'une table de données spécifiée, vous pouvez utiliser les méthodes find, first ou firstWhere pour rechercher un seul enregistrement. Ces méthodes renvoient une seule instance de modèle au lieu d'une collection de modèles :
```php
// Rechercher un modèle par clé primaire...
$vol = app\model\Flight::find(1);

// Trouver le premier modèle correspondant à la condition de recherche...
$vol = app\model\Flight::where('active', 1)->first();

// Rechercher la première implémentation du modèle correspondant à la condition de recherche...
$vol = app\model\Flight::firstWhere('active', 1);
```

Vous pouvez également utiliser un tableau de clés primaires comme argument pour appeler la méthode find, ce qui renverra une collection d'enregistrements correspondants :
```php
$vols = app\model\Flight::find([1, 2, 3]);
```

Parfois, vous pouvez souhaiter exécuter d'autres actions lorsque le premier résultat n'est pas trouvé. La méthode firstOr retournera le premier résultat trouvé et exécutera le rappel donné s'il n'y a pas de résultat. La valeur de retour du rappel sera ensuite renvoyée par la méthode firstOr :
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

## Exception "introuvable"
Parfois, vous souhaitez déclencher une exception lorsqu'un modèle n'est pas trouvé. Cela est très utile dans les contrôleurs et les itinéraires. Les méthodes findOrFail et firstOrFail récupéreront le premier résultat de la requête et lèveront une exception Illuminate\Database\Eloquent\ModelNotFoundException s'il n'est pas trouvé :
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```

## Récupération d'une collection
Vous pouvez également utiliser les méthodes count, sum et max fournies par le constructeur de requêtes, ainsi que d'autres fonctions de collection pour manipuler la collection. Ces méthodes ne renverront qu'une valeur scalaire appropriée au lieu d'une instance de modèle :
```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## Insertion
Pour insérer un nouvel enregistrement dans la base de données, créez d'abord une nouvelle instance de modèle, définissez les attributs de l'instance, puis appelez la méthode save :
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Ajouter un nouvel enregistrement à la table des utilisateurs
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Valider la requête

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```
Les horodatages created_at et updated_at seront automatiquement définis (lorsque la propriété $timestamps du modèle est true), donc inutile de les définir manuellement.

## Mise à jour
La méthode save peut également être utilisée pour mettre à jour un modèle existant dans la base de données. Pour mettre à jour un modèle, vous devrez d'abord le récupérer, définir les attributs à mettre à jour, puis appeler la méthode save. De même, l'horodatage updated_at sera automatiquement mis à jour, donc pas besoin de le définir manuellement :
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Mise à jour en masse
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Vérifier les changements d'attributs
Eloquent fournit les méthodes isDirty, isClean et wasChanged pour vérifier l'état interne du modèle et déterminer comment ses attributs ont changé depuis leur chargement initial. La méthode isDirty détermine si des attributs ont été modifiés depuis le chargement du modèle. Vous pouvez transmettre un nom d'attribut spécifique pour déterminer si un attribut particulier a été modifié. La méthode isClean est l'inverse de isDirty et accepte également des paramètres d'attributs facultatifs :
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
La méthode wasChanged détermine si des attributs ont été modifiés lors de la dernière sauvegarde du modèle au cours de la durée de vie de la requête actuelle. Vous pouvez également transmettre le nom de l'attribut pour voir s'il a été modifié :
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

## Affectation de masse
Vous pouvez également utiliser la méthode create pour enregistrer un nouveau modèle. Cette méthode renverra une instance de modèle. Cependant, avant de l'utiliser, vous devez spécifier les attributs fillable ou guarded sur le modèle, car tous les modèles Eloquent sont par défaut non autorisés à effectuer des affectations de masse.

Lorsqu'un utilisateur transmet des paramètres HTTP inattendus et que ces paramètres modifient des champs de la base de données que vous ne voulez pas modifier, une faille d'affectation de masse se produit. Par exemple, un utilisateur malveillant pourrait transmettre un paramètre is_admin via une requête HTTP, puis le transmettre à la méthode create, ce qui lui permettrait de se promouvoir en administrateur.

Par conséquent, avant de commencer, vous devriez définir quels attributs du modèle peuvent être modifiés en masse. Vous pouvez le faire en utilisant la propriété $fillable du modèle. Par exemple, permettre à l'attribut name du modèle Flight d'être modifié en masse :
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributs pouvant être modifiés en masse.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
Une fois que vous avez défini les attributs qui peuvent être modifiés en masse, vous pouvez insérer de nouvelles données dans la base de données en utilisant la méthode create. La méthode create renverra l'instance de modèle sauvegardée :
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
Si vous avez déjà une instance de modèle, vous pouvez passer un tableau à la méthode fill pour définir les valeurs :
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable peut être considéré comme une "liste blanche" pour les affectations de masse. Vous pouvez également utiliser la propriété $guarded pour cela. La propriété $guarded contient un tableau d'attributs qui ne sont pas autorisés pour les affectations de masse. Autrement dit, $guarded fonctionne plus comme une "liste noire". Remarque : vous ne pouvez utiliser que $fillable ou $guarded, pas les deux en même temps. Dans l'exemple suivant, tous les attributs sauf price peuvent être affectés en masse :
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributs non autorisés pour les affectations de masse.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

Si vous souhaitez autoriser l'affectation de masse pour tous les attributs, vous pouvez définir $guarded comme un tableau vide :
```php
/**
 * Attributs non autorisés pour les affectations de masse.
 *
 * @var array
 */
protected $guarded = [];
```
## Autres méthodes de création
firstOrCreate/ firstOrNew
Il existe deux méthodes que vous pourriez utiliser pour effectuer des affectations en masse : firstOrCreate et firstOrNew. La méthode firstOrCreate correspond aux données de la base de données à l'aide des paires clé/valeur fournies. Si le modèle n'est pas trouvé dans la base de données, un enregistrement contenant les attributs du premier argument ainsi que les attributs optionnels du deuxième argument sera inséré.

La méthode firstOrNew tente de trouver un enregistrement dans la base de données en fonction des attributs donnés, tout comme la méthode firstOrCreate. Cependant, si la méthode firstOrNew ne trouve pas le modèle correspondant, elle renverra une nouvelle instance de modèle. Veuillez noter que l'instance de modèle renvoyée par firstOrNew n'a pas encore été sauvegardée dans la base de données, vous devrez appeler manuellement la méthode save pour la sauvegarder :

```php
// Recherche du vol par nom, création s'il n'existe pas...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Vol 10']);

// Recherche du vol par nom ou création avec les attributs name, delayed et arrival_time...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Vol 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Recherche du vol par nom, création d'une instance s'il n'existe pas...
$flight = app\modle\Flight::firstOrNew(['name' => 'Vol 10']);

// Recherche du vol par nom ou création d'une nouvelle instance avec les attributs name, delayed et arrival_time...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Vol 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

Vous pourriez également rencontrer des cas où vous souhaitez mettre à jour un modèle existant ou créer un nouveau modèle s'il n'existe pas. La méthode updateOrCreate permet de le faire en une seule étape. Similaire à la méthode firstOrCreate, updateOrCreate persiste le modèle, donc pas besoin d'appeler save() :

```php
// Si un vol d'Oakland à San Diego existe, le prix est fixé à 99 dollars.
// S'il n'y a pas de correspondance pour le modèle existant, il en crée un nouveau.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Supprimer un modèle

Vous pouvez appeler la méthode delete sur une instance de modèle pour la supprimer :

```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Supprimer un modèle par clé primaire

```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## Supprimer un modèle par requête

```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Dupliquer un modèle

Vous pouvez utiliser la méthode replicate pour créer une nouvelle instance non sauvegardée dans la base de données, ce qui s'avère très utile lorsque les instances de modèle partagent de nombreuses propriétés similaires.

```php
$shipping = App\Address::create([
    'type' => 'expédition',
    'line_1' => '123, rue de l'exemple',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'facturation'
]);

$billing->save();
```

## Comparer les modèles

Parfois, vous devrez peut-être vérifier si deux modèles sont "identiques". La méthode is peut être utilisée pour vérifier rapidement si deux modèles ont la même clé primaire, table et connexion à la base de données :

```php
if ($post->is($anotherPost)) {
    //
}
```

## Observateurs de modèles

Veuillez vous reporter à [Événements de modèle et observateur dans Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Remarque : Pour que Eloquent ORM prenne en charge les observateurs de modèles, vous devez importer en plus composer require "illuminate/events"

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
