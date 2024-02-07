## Normes de développement de plugins d'application

### Exigences des plugins d'application
* Les plugins ne doivent pas contenir de code, d'icônes, d'images ou tout autre contenu portant atteinte aux droits d'auteur.
* Le code source du plugin doit être complet et ne doit pas être crypté.
* Le plugin doit offrir des fonctionnalités complètes et ne doit pas être simplement une fonctionnalité simple.
* Une documentation complète des fonctionnalités doit être fournie.
* Les plugins ne doivent pas inclure de sous-marché.
* Aucun texte ou lien promotionnel ne doit figurer dans le plugin.

### Identification des plugins d'application
Chaque plugin d'application a une identification unique, composée de lettres. Cette identification affecte le nom du répertoire source du plugin, l'espace de noms de la classe, et le préfixe de la table de la base de données du plugin.

Par exemple, si le développeur a choisi "foo" comme identifiant de plugin, le répertoire source du plugin sera `{projet_principal}/plugin/foo`, l'espace de noms correspondant sera `plugin\foo`, et le préfixe de la table sera `foo_`.

Étant donné que l'identification est unique sur l'ensemble du réseau, les développeurs doivent vérifier la disponibilité de l'identification avant de commencer le développement, en se rendant à l'adresse [Vérification de l'identification de l'application](https://www.workerman.net/app/check).

### Base de données
* Les noms de table doivent être composés de lettres minuscules `a-z` et de traits de soulignement `_`.
* Les tables de données du plugin doivent avoir pour préfixe l'identification du plugin. Par exemple, la table "article" du plugin "foo" sera nommée `foo_article`.
* La clé primaire de la table devrait être "id".
* Le moteur de stockage doit être le moteur InnoDB.
* L'encodage de caractères doit être en utf8mb4_general_ci.
* Il est possible d'utiliser l'ORM de Laravel ou de Think pour la base de données.
* Il est recommandé d'utiliser le champ de type DateTime pour les données temporelles.

### Normes de codage

#### Normes PSR
Le code doit être conforme à la spécification de chargement PSR4.

#### Nommer les classes en utilisant une notation CamelCase commençant par une majuscule
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Les attributs et les méthodes des classes doivent commencer par une minuscule suivie de CamelCase
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
     * Obtenir les commentaires
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
Les attributs et les fonctions des classes doivent être accompagnés de commentaires indiquant un résumé, les paramètres et le type de retour.

#### Indentation
L'indentation du code doit utiliser quatre espaces au lieu de tabulations.

#### Contrôles de flux
Un espace doit suivre les mots clés de contrôle de flux (if, for, while, foreach, etc.), et les accolades du code de contrôle de flux doivent être sur la même ligne que la parenthèse de fin.
```php
foreach ($users as $uid => $user) {

}
```

#### Nom des variables temporaires
Il est recommandé de nommer les variables temporaires en commençant par une minuscule suivie de CamelCase (non obligatoire).
```php
$articleCount = 100;
```
