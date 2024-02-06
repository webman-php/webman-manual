# Normes de développement des plugins d'application

## Exigences pour les plugins d'application
* Les plugins ne peuvent pas contenir de code, d'icônes, d'images ou autres éléments violant les droits d'auteur.
* Le code source des plugins doit être complet et ne peut pas être chiffré.
* Les plugins doivent offrir des fonctionnalités complètes et ne peuvent pas être simplement des fonctionnalités basiques.
* Une documentation complète des fonctionnalités doit être fournie.
* Les plugins ne peuvent pas contenir de sous-marché intégré.
* Aucun texte ou lien promotionnel n'est autorisé à l'intérieur des plugins.

## Identification des plugins d'application
Chaque plugin d'application possède une identité unique composée de lettres. Cette identité influence le nom du répertoire où se trouve le code source du plugin, l'espace de noms des classes du plugin, et le préfixe des tables de la base de données du plugin.

Supposons qu'un développeur utilise "foo" comme identifiant de plugin. Le répertoire du code source du plugin sera `{projet_principal}/plugin/foo`, l'espace de noms correspondant du plugin sera `plugin\foo`, et le préfixe des tables de base de données du plugin sera `foo_`.

Étant donné que l'identifiant est unique sur l'ensemble du réseau, les développeurs doivent vérifier la disponibilité de l'identifiant avant de commencer le développement, en utilisant l'adresse de vérification d'identifiant d'application [Vérification d'identifiant d'application](https://www.workerman.net/app/check).

## Base de données
* Les noms de table se composent de lettres minuscules de l'alphabet `a-z` et du caractère souligné `_`.
* Les tables de données du plugin doivent utiliser l'identifiant du plugin comme préfixe. Par exemple, la table "article" du plugin "foo" sera `foo_article`.
* La clé primaire des tables doit être l'index "id".
* Le moteur de stockage doit utiliser le moteur InnoDB de manière uniforme.
* L'encodage des caractères doit être en utf8mb4_general_ci.
* L'ORM de la base de données peut utiliser Laravel ou Think-ORM.
* Les champs de date et d'heure sont recommandés à utiliser de type DateTime.

## Normes de codage

#### Norme PSR
Le code doit être conforme à la norme de chargement PSR4.

#### Nommer les classes en utilisant la notation CamelCase avec une majuscule en début de mot
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Nommer les attributs et les méthodes des classes en utilisant la notation CamelCase avec une minuscule en début de mot
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Méthodes qui ne nécessitent pas d'authentification
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Obtenir des commentaires
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Commentaires
Les attributs et les fonctions des classes doivent inclure des commentaires décrivant la fonction, les paramètres et le type de retour.

#### Indentation
Le code doit utiliser 4 espaces pour l'indentation, et non des tabulations.

#### Contrôle de flux
Les mots-clés de contrôle de flux (if, for, while, foreach, etc.) doivent être suivis d'un espace, et les accolades de début et de fin de code de contrôle de flux doivent être sur la même ligne que la parenthèse terminale.

```php
foreach ($users as $uid => $user) {

}
```

#### Nommage des variables temporaires
Il est recommandé de nommer les variables temporaires en utilisant la notation CamelCase avec une minuscule en début de mot (non obligatoire).

```php
$articleCount = 100;
```
