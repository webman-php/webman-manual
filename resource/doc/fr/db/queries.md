# Constructeur de requête
## Obtenir toutes les lignes
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

## Obtenir des colonnes spécifiques
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Obtenir une ligne
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Obtenir une colonne
```php
$titles = Db::table('roles')->pluck('title');
```
Spécifier la valeur de l'identifiant comme index
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Obtenir une seule valeur (colonne)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Distinct
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Résultats par blocs
Si vous avez besoin de traiter des milliers d'enregistrements de base de données, il est très long de les lire tous en une seule fois et cela risque de dépasser la mémoire. Dans ce cas, vous pouvez envisager d'utiliser la méthode `chunkById`. Cette méthode récupère un petit bloc du jeu de résultats à la fois et le passe à une fonction de clôture pour le traitement. Par exemple, nous pouvons découper toutes les données de la table 'users' en blocs de 100 enregistrements à traiter une fois :

```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Vous pouvez arrêter de récupérer les résultats par bloc en retournant false dans la fonction de clôture.

```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Traiter les enregistrements...

    return false;
});
```

> Remarque : Ne supprimez pas de données dans la fonction de rappel, cela pourrait entraîner l'exclusion de certains enregistrements du jeu de résultats.

## Agrégation
Le constructeur de requête fournit également diverses méthodes d'agrégation, telles que count, max, min, avg, sum, etc.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Vérifier si un enregistrement existe
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Expression brute
Modèle
```php
selectRaw($expression, $bindings = [])
```
Parfois, vous devrez peut-être utiliser une expression brute dans une requête. Vous pouvez utiliser `selectRaw()` pour créer une expression brute :

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

De même, les méthodes d'expressions brutes `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()` sont également fournies.

`Db::raw($value)` est également utilisé pour créer une expression brute, mais il n'a pas de fonctionnalité de liaison de paramètres, alors soyez prudent avec les problèmes d'injection SQL lors de son utilisation.

```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```
## Instruction Join
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

## Instruction Union
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Clause Where
Prototype
```php
where($column, $operator = null, $value = null)
```
Le premier paramètre est le nom de la colonne, le deuxième paramètre est un opérateur supporté par n'importe quel système de base de données, et le troisième est la valeur à comparer de cette colonne.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Lorsque l'opérateur est égal, il peut être omis, donc cette expression est équivalente à la précédente
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

Vous pouvez également passer un tableau de conditions à la fonction where :
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

La méthode orWhere accepte les mêmes paramètres que la méthode where :
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Vous pouvez passer une fonction anonyme à la méthode orWhere comme premier paramètre :
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

Les méthodes whereBetween / orWhereBetween vérifient si la valeur de la colonne se situe entre deux valeurs données :
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Les méthodes whereNotBetween / orWhereNotBetween vérifient si la valeur de la colonne se situe en dehors de deux valeurs données :
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Les méthodes whereIn / whereNotIn / orWhereIn / orWhereNotIn vérifient si la valeur de la colonne doit être présente dans un tableau spécifié :
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

Les méthodes whereNull / whereNotNull / orWhereNull / orWhereNotNull vérifient si la colonne spécifiée doit être NULL :
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

La méthode whereNotNull vérifie si la colonne spécifiée ne doit pas être NULL :
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

Les méthodes whereDate / whereMonth / whereDay / whereYear / whereTime comparent la valeur de la colonne avec une date donnée :
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

La méthode whereColumn / orWhereColumn compare si les valeurs de deux colonnes sont égales :
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Vous pouvez également passer un opérateur de comparaison
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// La méthode whereColumn peut également accepter un tableau
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();
```

Groupement de paramètres
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

WhereExists
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

## OrderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Tri aléatoire
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Le tri aléatoire peut avoir un impact considérable sur les performances du serveur, son utilisation n'est pas recommandée.

## GroupBy / Having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Vous pouvez passer plusieurs paramètres à la méthode groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## Offset / Limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## Insertion
Insertion d'un seul enregistrement
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Insertion de plusieurs enregistrements
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## ID Auto-incrémenté
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> Remarque : Lors de l'utilisation de PostgreSQL, la méthode insertGetId utilisera par défaut 'id' comme nom de champ à auto-incrémentation. Si vous devez obtenir l'ID à partir d'une autre "séquence", vous pouvez passer le nom du champ en tant que deuxième paramètre à la méthode insertGetId.

## Mise à jour
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Mise à jour ou insertion
Parfois, vous pouvez souhaiter mettre à jour un enregistrement existant dans la base de données, ou le créer s'il n'existe pas :
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
La méthode updateOrInsert tentera d'abord de trouver un enregistrement correspondant avec les clés et valeurs du premier paramètre. Si l'enregistrement existe, il sera mis à jour avec les valeurs du deuxième paramètre. Si l'enregistrement n'est pas trouvé, un nouvel enregistrement avec les données des deux tableaux sera inséré.

## Incrémentation & Décrémentation
Ces deux méthodes acceptent au moins un paramètre : la colonne à modifier. Le deuxième paramètre est facultatif et contrôle la valeur par laquelle la colonne est incrémentée ou décrémentée :
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Vous pouvez également spécifier les champs à mettre à jour pendant l'opération :
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Supprimer
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Si vous avez besoin de vider la table, vous pouvez utiliser la méthode truncate, qui supprimera toutes les lignes et réinitialisera l'auto-incrément à zéro :
```php
Db::table('users')->truncate();
```

## Verrou pessimiste
Le générateur de requêtes inclut également certaines fonctions qui vous aideront à implémenter des verrouillages pessimistes dans la syntaxe de sélection. Si vous souhaitez implémenter un "verrou partagé" dans votre requête, vous pouvez utiliser la méthode sharedLock. Le verrou partagé empêche les colonnes sélectionnées d'être modifiées jusqu'à ce que la transaction soit validée :
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Ou, vous pouvez utiliser la méthode lockForUpdate. L'utilisation du verrou "update" empêche que les lignes soient modifiées ou sélectionnées par d'autres verrous partagés :
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```
## Débogage

Vous pouvez utiliser la méthode `dd` ou `dump` pour afficher les résultats de la requête ou les instructions SQL. La méthode `dd` affiche les informations de débogage et arrête l'exécution de la demande, tandis que la méthode `dump` affiche également des informations de débogage, mais ne stoppe pas l'exécution de la demande :
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Remarque**
> Pour le débogage, veuillez installer `symfony/var-dumper` en utilisant la commande `composer require symfony/var-dumper`.
