# Ligne de commande

Composant en ligne de commande Webman

## Installation
```
composer require webman/console
```

## Table des matières

### Génération de code
- [make:controller](#make-controller) - Générer une classe contrôleur
- [make:model](#make-model) - Générer une classe modèle à partir d'une table de base de données
- [make:crud](#make-crud) - Générer un CRUD complet (modèle + contrôleur + validateur)
- [make:middleware](#make-middleware) - Générer une classe middleware
- [make:command](#make-command) - Générer une classe de commande console
- [make:bootstrap](#make-bootstrap) - Générer une classe d'initialisation bootstrap
- [make:process](#make-process) - Générer une classe de processus personnalisé

### Construction et déploiement
- [build:phar](#build-phar) - Emballer le projet en archive PHAR
- [build:bin](#build-bin) - Emballer le projet en binaire autonome
- [install](#install) - Exécuter le script d'installation Webman

### Commandes utilitaires
- [version](#version) - Afficher la version du framework Webman
- [fix-disable-functions](#fix-disable-functions) - Corriger les fonctions désactivées dans php.ini
- [route:list](#route-list) - Afficher toutes les routes enregistrées

### Gestion des plugins d'application (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Créer un nouveau plugin d'application
- [app-plugin:install](#app-plugin-install) - Installer un plugin d'application
- [app-plugin:uninstall](#app-plugin-uninstall) - Désinstaller un plugin d'application
- [app-plugin:update](#app-plugin-update) - Mettre à jour un plugin d'application
- [app-plugin:zip](#app-plugin-zip) - Emballer un plugin d'application en ZIP

### Gestion des plugins (plugin:*)
- [plugin:create](#plugin-create) - Créer un nouveau plugin Webman
- [plugin:install](#plugin-install) - Installer un plugin Webman
- [plugin:uninstall](#plugin-uninstall) - Désinstaller un plugin Webman
- [plugin:enable](#plugin-enable) - Activer un plugin Webman
- [plugin:disable](#plugin-disable) - Désactiver un plugin Webman
- [plugin:export](#plugin-export) - Exporter le code source d'un plugin

### Gestion des services
- [start](#start) - Démarrer les processus worker Webman
- [stop](#stop) - Arrêter les processus worker Webman
- [restart](#restart) - Redémarrer les processus worker Webman
- [reload](#reload) - Recharger le code sans interruption
- [status](#status) - Consulter l'état des processus worker
- [connections](#connections) - Obtenir les informations de connexion des processus worker

## Génération de code

<a name="make-controller"></a>
### make:controller

Générer une classe contrôleur.

**Utilisation :**
```bash
php webman make:controller <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du contrôleur (sans suffixe) |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Générer le contrôleur dans le répertoire du plugin spécifié |
| `--path` | `-P` | Chemin personnalisé du contrôleur |
| `--force` | `-f` | Écraser si le fichier existe |
| `--no-suffix` | | Ne pas ajouter le suffixe « Controller » |

**Exemples :**
```bash
# Créer UserController dans app/controller
php webman make:controller User

# Créer dans un plugin
php webman make:controller AdminUser -p admin

# Chemin personnalisé
php webman make:controller User -P app/api/controller

# Écraser le fichier existant
php webman make:controller User -f

# Créer sans suffixe « Controller »
php webman make:controller UserHandler --no-suffix
```

**Structure du fichier généré :**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**Notes :**
- Les contrôleurs sont placés par défaut dans `app/controller/`
- Le suffixe du contrôleur défini dans la configuration est ajouté automatiquement
- Demande une confirmation d'écrasement si le fichier existe (idem pour les autres commandes)

<a name="make-model"></a>
### make:model

Générer une classe modèle à partir d'une table de base de données. Prend en charge Laravel ORM et ThinkORM.

**Utilisation :**
```bash
php webman make:model [name]
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Non | Nom de la classe modèle, peut être omis en mode interactif |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Générer le modèle dans le répertoire du plugin spécifié |
| `--path` | `-P` | Répertoire cible (relatif à la racine du projet) |
| `--table` | `-t` | Spécifier le nom de la table ; recommandé lorsque le nom ne suit pas la convention |
| `--orm` | `-o` | Choisir l'ORM : `laravel` ou `thinkorm` |
| `--database` | `-d` | Spécifier le nom de connexion à la base de données |
| `--force` | `-f` | Écraser le fichier existant |

**Notes sur les chemins :**
- Par défaut : `app/model/` (application principale) ou `plugin/<plugin>/app/model/` (plugin)
- `--path` est relatif à la racine du projet, ex. `plugin/admin/app/model`
- Lors de l'utilisation simultanée de `--plugin` et `--path`, ils doivent pointer vers le même répertoire

**Exemples :**
```bash
# Créer le modèle User dans app/model
php webman make:model User

# Spécifier le nom de table et l'ORM
php webman make:model User -t wa_users -o laravel

# Créer dans un plugin
php webman make:model AdminUser -p admin

# Chemin personnalisé
php webman make:model User -P plugin/admin/app/model
```

**Mode interactif :** Lorsque le nom est omis, entre dans un flux interactif : sélection de la table → saisie du nom du modèle → saisie du chemin. Prise en charge : Entrée pour voir plus, `0` pour créer un modèle vide, `/mot-clé` pour filtrer les tables.

**Structure du fichier généré :**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (primary key)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

Les annotations `@property` sont générées automatiquement à partir de la structure de la table. Prend en charge MySQL et PostgreSQL.

<a name="make-crud"></a>
### make:crud

Générer en une fois le modèle, le contrôleur et le validateur à partir d'une table de base de données, pour une capacité CRUD complète.

**Utilisation :**
```bash
php webman make:crud
```

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--table` | `-t` | Spécifier le nom de la table |
| `--model` | `-m` | Nom de la classe modèle |
| `--model-path` | `-M` | Répertoire du modèle (relatif à la racine du projet) |
| `--controller` | `-c` | Nom de la classe contrôleur |
| `--controller-path` | `-C` | Répertoire du contrôleur |
| `--validator` | | Nom de la classe validateur (nécessite `webman/validation`) |
| `--validator-path` | | Répertoire du validateur (nécessite `webman/validation`) |
| `--plugin` | `-p` | Générer les fichiers dans le répertoire du plugin spécifié |
| `--orm` | `-o` | ORM : `laravel` ou `thinkorm` |
| `--database` | `-d` | Nom de connexion à la base de données |
| `--force` | `-f` | Écraser les fichiers existants |
| `--no-validator` | | Ne pas générer le validateur |
| `--no-interaction` | `-n` | Mode non interactif, utiliser les valeurs par défaut |

**Flux d'exécution :** Lorsque `--table` n'est pas spécifié, entre dans la sélection interactive des tables ; le nom du modèle est déduit par défaut du nom de la table ; le nom du contrôleur par défaut est nom du modèle + suffixe contrôleur ; le nom du validateur par défaut est nom du contrôleur sans suffixe + `Validator`. Chemins par défaut : modèle `app/model/`, contrôleur `app/controller/`, validateur `app/validation/` ; pour les plugins : sous-répertoires correspondants sous `plugin/<plugin>/app/`.

**Exemples :**
```bash
# Génération interactive (confirmation étape par étape après la sélection de la table)
php webman make:crud

# Spécifier le nom de la table
php webman make:crud --table=users

# Spécifier le nom de la table et le plugin
php webman make:crud --table=users --plugin=admin

# Spécifier les chemins
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# Ne pas générer le validateur
php webman make:crud --table=users --no-validator

# Non interactif + écrasement
php webman make:crud --table=users --no-interaction --force
```

**Structure des fichiers générés :**

Modèle (`app/model/User.php`) :
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

Contrôleur (`app/controller/UserController.php`) :
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

Validateur (`app/validation/UserValidator.php`) :
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => 'Clé primaire',
        'username' => 'Nom d\'utilisateur'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Notes :**
- La génération du validateur est ignorée si `webman/validation` n'est pas installé ou activé (installer avec `composer require webman/validation`)
- Les `attributes` du validateur sont générés automatiquement à partir des commentaires des champs de la base de données ; sans commentaires, pas d'`attributes`
- Les messages d'erreur du validateur prennent en charge l'i18n ; la langue est sélectionnée via `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

Générer une classe middleware et l'enregistrer automatiquement dans `config/middleware.php` (ou `plugin/<plugin>/config/middleware.php` pour les plugins).

**Utilisation :**
```bash
php webman make:middleware <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du middleware |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Générer le middleware dans le répertoire du plugin spécifié |
| `--path` | `-P` | Répertoire cible (relatif à la racine du projet) |
| `--force` | `-f` | Écraser le fichier existant |

**Exemples :**
```bash
# Créer le middleware Auth dans app/middleware
php webman make:middleware Auth

# Créer dans un plugin
php webman make:middleware Auth -p admin

# Chemin personnalisé
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Structure du fichier généré :**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**Notes :**
- Placé par défaut dans `app/middleware/`
- Le nom de la classe est automatiquement ajouté au fichier de configuration middleware pour activation

<a name="make-command"></a>
### make:command

Générer une classe de commande console.

**Utilisation :**
```bash
php webman make:command <command-name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `command-name` | Oui | Nom de la commande au format `group:action` (ex. `user:list`) |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Générer la commande dans le répertoire du plugin spécifié |
| `--path` | `-P` | Répertoire cible (relatif à la racine du projet) |
| `--force` | `-f` | Écraser le fichier existant |

**Exemples :**
```bash
# Créer la commande user:list dans app/command
php webman make:command user:list

# Créer dans un plugin
php webman make:command user:list -p admin

# Chemin personnalisé
php webman make:command user:list -P plugin/admin/app/command

# Écraser le fichier existant
php webman make:command user:list -f
```

**Structure du fichier généré :**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**Notes :**
- Placé par défaut dans `app/command/`

<a name="make-bootstrap"></a>
### make:bootstrap

Générer une classe d'initialisation bootstrap. La méthode `start` est automatiquement appelée au démarrage du processus, généralement pour une initialisation globale.

**Utilisation :**
```bash
php webman make:bootstrap <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom de la classe Bootstrap |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Générer dans le répertoire du plugin spécifié |
| `--path` | `-P` | Répertoire cible (relatif à la racine du projet) |
| `--force` | `-f` | Écraser le fichier existant |

**Exemples :**
```bash
# Créer MyBootstrap dans app/bootstrap
php webman make:bootstrap MyBootstrap

# Créer sans activation automatique
php webman make:bootstrap MyBootstrap no

# Créer dans un plugin
php webman make:bootstrap MyBootstrap -p admin

# Chemin personnalisé
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Écraser le fichier existant
php webman make:bootstrap MyBootstrap -f
```

**Structure du fichier généré :**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**Notes :**
- Placé par défaut dans `app/bootstrap/`
- Lors de l'activation, la classe est ajoutée à `config/bootstrap.php` (ou `plugin/<plugin>/config/bootstrap.php` pour les plugins)

<a name="make-process"></a>
### make:process

Générer une classe de processus personnalisé et l'écrire dans `config/process.php` pour démarrage automatique.

**Utilisation :**
```bash
php webman make:process <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom de la classe processus (ex. MyTcp, MyWebsocket) |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--plugin` | `-p` | Générer dans le répertoire du plugin spécifié |
| `--path` | `-P` | Répertoire cible (relatif à la racine du projet) |
| `--force` | `-f` | Écraser le fichier existant |

**Exemples :**
```bash
# Créer dans app/process
php webman make:process MyTcp

# Créer dans un plugin
php webman make:process MyProcess -p admin

# Chemin personnalisé
php webman make:process MyProcess -P plugin/admin/app/process

# Écraser le fichier existant
php webman make:process MyProcess -f
```

**Flux interactif :** Demande dans l'ordre : écouter sur un port ? → type de protocole (websocket/http/tcp/udp/unixsocket) → adresse d'écoute (IP:port ou chemin unix socket) → nombre de processus. Le protocole HTTP demande également le mode intégré ou personnalisé.

**Structure du fichier généré :**

Processus sans écoute (uniquement `onWorkerStart`) :
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

Les processus TCP/WebSocket en écoute génèrent les modèles de rappel `onConnect`, `onMessage`, `onClose` correspondants.

**Notes :**
- Placé par défaut dans `app/process/` ; la configuration du processus est écrite dans `config/process.php`
- La clé de configuration est le snake_case du nom de la classe ; échoue si elle existe déjà
- Le mode HTTP intégré réutilise le fichier de processus `app\process\Http`, ne génère pas de nouveau fichier
- Protocoles pris en charge : websocket, http, tcp, udp, unixsocket

## Construction et déploiement

<a name="build-phar"></a>
### build:phar

Emballer le projet en archive PHAR pour distribution et déploiement.

**Utilisation :**
```bash
php webman build:phar
```

**Démarrage :**

Accéder au répertoire build et exécuter

```bash
php webman.phar start
```

**Notes :**
* Le projet emballé ne prend pas en charge reload ; utiliser restart pour mettre à jour le code

* Pour éviter une taille de fichier excessive et une utilisation mémoire importante, configurer exclude_pattern et exclude_files dans config/plugin/webman/console/app.php pour exclure les fichiers inutiles.

* L'exécution de webman.phar crée un répertoire runtime au même emplacement pour les journaux et fichiers temporaires.

* Si votre projet utilise un fichier .env, placer .env dans le même répertoire que webman.phar.

* webman.phar ne prend pas en charge les processus personnalisés sous Windows

* Ne jamais stocker les fichiers uploadés par l'utilisateur dans le paquet phar ; manipuler les uploads utilisateur via phar:// est dangereux (vulnérabilité de désérialisation phar). Les uploads utilisateur doivent être stockés séparément sur disque en dehors du phar. Voir ci-dessous.

* Si votre activité nécessite d'uploader des fichiers vers le répertoire public, extraire le répertoire public au même emplacement que webman.phar et configurer config/app.php :
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Utiliser la fonction utilitaire public_path($relative_path) pour obtenir le chemin réel du répertoire public.


<a name="build-bin"></a>
### build:bin

Emballer le projet en binaire autonome avec runtime PHP intégré. Aucune installation PHP requise sur l'environnement cible.

**Utilisation :**
```bash
php webman build:bin [version]
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `version` | Non | Version PHP (ex. 8.1, 8.2), par défaut la version PHP actuelle, minimum 8.1 |

**Exemples :**
```bash
# Utiliser la version PHP actuelle
php webman build:bin

# Spécifier PHP 8.2
php webman build:bin 8.2
```

**Démarrage :**

Accéder au répertoire build et exécuter

```bash
./webman.bin start
```

**Notes :**
* Fortement recommandé : la version PHP locale doit correspondre à la version de build (ex. PHP 8.1 local → build avec 8.1) pour éviter les problèmes de compatibilité
* Le build télécharge les sources PHP 8 mais ne les installe pas localement ; n'affecte pas l'environnement PHP local
* webman.bin ne fonctionne actuellement que sur Linux x86_64 ; non pris en charge sur macOS
* Le projet emballé ne prend pas en charge reload ; utiliser restart pour mettre à jour le code
* .env n'est pas emballé par défaut (contrôlé par exclude_files dans config/plugin/webman/console/app.php) ; placer .env dans le même répertoire que webman.bin au démarrage
* Un répertoire runtime est créé dans le répertoire webman.bin pour les fichiers de journal
* webman.bin ne lit pas de php.ini externe ; pour des paramètres php.ini personnalisés, utiliser custom_ini dans config/plugin/webman/console/app.php
* Exclure les fichiers inutiles via config/plugin/webman/console/app.php pour éviter une taille de paquet excessive
* Le build binaire ne prend pas en charge les coroutines Swoole
* Ne jamais stocker les fichiers uploadés par l'utilisateur dans le paquet binaire ; manipuler via phar:// est dangereux (vulnérabilité de désérialisation phar). Les uploads utilisateur doivent être stockés séparément sur disque en dehors du paquet.
* Si votre activité nécessite d'uploader des fichiers vers le répertoire public, extraire le répertoire public au même emplacement que webman.bin et configurer config/app.php comme ci-dessous, puis reconstruire :
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Exécuter le script d'installation du framework Webman (appelle `\Webman\Install::install()`), pour l'initialisation du projet.

**Utilisation :**
```bash
php webman install
```

## Commandes utilitaires

<a name="version"></a>
### version

Afficher la version workerman/webman-framework.

**Utilisation :**
```bash
php webman version
```

**Notes :** Lit la version depuis `vendor/composer/installed.php` ; retourne un échec si impossible à lire.

<a name="fix-disable-functions"></a>
### fix-disable-functions

Corriger `disable_functions` dans php.ini, en supprimant les fonctions requises par Webman.

**Utilisation :**
```bash
php webman fix-disable-functions
```

**Notes :** Supprime les fonctions suivantes (et les correspondances de préfixe) de `disable_functions` : `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Ignore si php.ini introuvable ou `disable_functions` vide. **Modifie directement le fichier php.ini** ; sauvegarde recommandée.

<a name="route-list"></a>
### route:list

Lister toutes les routes enregistrées au format tableau.

**Utilisation :**
```bash
php webman route:list
```

**Exemple de sortie :**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Colonnes de sortie :** URI, Method, Callback, Middleware, Name. Les callbacks Closure s'affichent comme « Closure ».

## Gestion des plugins d'application (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Créer un nouveau plugin d'application, générant la structure de répertoires complète et les fichiers de base sous `plugin/<name>`.

**Utilisation :**
```bash
php webman app-plugin:create <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du plugin ; doit correspondre à `[a-zA-Z0-9][a-zA-Z0-9_-]*`, ne peut pas contenir `/` ou `\` |

**Exemples :**
```bash
# Créer un plugin d'application nommé foo
php webman app-plugin:create foo

# Créer un plugin avec tiret
php webman app-plugin:create my-app
```

**Structure de répertoires générée :**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, etc.
├── api/Install.php  # Hooks d'installation/désinstallation/mise à jour
├── public/
└── install.sql
```

**Notes :**
- Le plugin est créé sous `plugin/<name>/` ; échoue si le répertoire existe déjà

<a name="app-plugin-install"></a>
### app-plugin:install

Installer un plugin d'application, exécutant `plugin/<name>/api/Install::install($version)`.

**Utilisation :**
```bash
php webman app-plugin:install <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du plugin ; doit correspondre à `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Exemples :**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Désinstaller un plugin d'application, exécutant `plugin/<name>/api/Install::uninstall($version)`.

**Utilisation :**
```bash
php webman app-plugin:uninstall <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du plugin |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--yes` | `-y` | Ignorer la confirmation, exécuter directement |

**Exemples :**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Mettre à jour un plugin d'application, exécutant `Install::beforeUpdate($from, $to)` et `Install::update($from, $to, $context)` dans l'ordre.

**Utilisation :**
```bash
php webman app-plugin:update <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du plugin |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--from` | `-f` | Version de départ, par défaut la version actuelle |
| `--to` | `-t` | Version cible, par défaut la version actuelle |

**Exemples :**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Emballer un plugin d'application en fichier ZIP, sortie vers `plugin/<name>.zip`.

**Utilisation :**
```bash
php webman app-plugin:zip <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du plugin |

**Exemples :**
```bash
php webman app-plugin:zip foo
```

**Notes :**
- Exclut automatiquement `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__`, etc.

## Gestion des plugins (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Créer un nouveau plugin Webman (forme de paquet Composer), générant le répertoire de configuration `config/plugin/<name>` et le répertoire source du plugin `vendor/<name>`.

**Utilisation :**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du paquet plugin au format `vendor/package` (ex. `foo/my-admin`) ; doit suivre la nomenclature des paquets Composer |

**Exemples :**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Structure générée :**
- `config/plugin/<name>/app.php` : Configuration du plugin (inclut le commutateur `enable`)
- `vendor/<name>/composer.json` : Définition du paquet plugin
- `vendor/<name>/src/` : Répertoire source du plugin
- Ajoute automatiquement le mapping PSR-4 au `composer.json` racine du projet
- Exécute `composer dumpautoload` pour actualiser l'autoloading

**Notes :**
- Le nom doit être au format `vendor/package` : lettres minuscules, chiffres, `-`, `_`, `.`, et doit contenir un `/`
- Échoue si `config/plugin/<name>` ou `vendor/<name>` existe déjà
- Erreur si l'argument et `--name` sont fournis tous deux avec des valeurs différentes

<a name="plugin-install"></a>
### plugin:install

Exécuter le script d'installation du plugin (`Install::install()`), copiant les ressources du plugin vers le répertoire du projet.

**Utilisation :**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du paquet plugin au format `vendor/package` (ex. `foo/my-admin`) |

**Options :**

| Option | Description |
|--------|-------------|
| `--name` | Spécifier le nom du plugin en option ; utiliser soit ceci soit l'argument |

**Exemples :**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Exécuter le script de désinstallation du plugin (`Install::uninstall()`), supprimant les ressources du plugin du projet.

**Utilisation :**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du paquet plugin au format `vendor/package` |

**Options :**

| Option | Description |
|--------|-------------|
| `--name` | Spécifier le nom du plugin en option ; utiliser soit ceci soit l'argument |

**Exemples :**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Activer le plugin, définissant `enable` à `true` dans `config/plugin/<name>/app.php`.

**Utilisation :**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du paquet plugin au format `vendor/package` |

**Options :**

| Option | Description |
|--------|-------------|
| `--name` | Spécifier le nom du plugin en option ; utiliser soit ceci soit l'argument |

**Exemples :**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Désactiver le plugin, définissant `enable` à `false` dans `config/plugin/<name>/app.php`.

**Utilisation :**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du paquet plugin au format `vendor/package` |

**Options :**

| Option | Description |
|--------|-------------|
| `--name` | Spécifier le nom du plugin en option ; utiliser soit ceci soit l'argument |

**Exemples :**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Exporter la configuration du plugin et les répertoires spécifiés du projet vers `vendor/<name>/src/`, et générer `Install.php` pour emballage et publication.

**Utilisation :**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**Arguments :**

| Argument | Requis | Description |
|----------|----------|-------------|
| `name` | Oui | Nom du paquet plugin au format `vendor/package` |

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--name` | | Spécifier le nom du plugin en option ; utiliser soit ceci soit l'argument |
| `--source` | `-s` | Chemin à exporter (relatif à la racine du projet) ; peut être spécifié plusieurs fois |

**Exemples :**
```bash
# Exporter le plugin, inclut par défaut config/plugin/<name>
php webman plugin:export foo/my-admin

# Exporter en plus app, config, etc.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Notes :**
- Le nom du plugin doit suivre la nomenclature des paquets Composer (`vendor/package`)
- Si `config/plugin/<name>` existe et n'est pas dans `--source`, il est automatiquement ajouté à la liste d'export
- Le `Install.php` exporté inclut `pathRelation` pour utilisation par `plugin:install` / `plugin:uninstall`
- `plugin:install` et `plugin:uninstall` exigent que le plugin existe dans `vendor/<name>`, avec la classe `Install` et la constante `WEBMAN_PLUGIN`

## Gestion des services

<a name="start"></a>
### start

Démarrer les processus worker Webman. Mode DEBUG par défaut (premier plan).

**Utilisation :**
```bash
php webman start
```

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | Démarrer en mode DAEMON (arrière-plan) |

<a name="stop"></a>
### stop

Arrêter les processus worker Webman.

**Utilisation :**
```bash
php webman stop
```

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | Arrêt gracieux ; attendre la fin des requêtes en cours avant de quitter |

<a name="restart"></a>
### restart

Redémarrer les processus worker Webman.

**Utilisation :**
```bash
php webman restart
```

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--daemon` | `-d` | Exécuter en mode DAEMON après redémarrage |
| `--graceful` | `-g` | Arrêt gracieux avant redémarrage |

<a name="reload"></a>
### reload

Recharger le code sans interruption. Pour rechargement à chaud après mise à jour du code.

**Utilisation :**
```bash
php webman reload
```

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--graceful` | `-g` | Rechargement gracieux ; attendre la fin des requêtes en cours avant de recharger |

<a name="status"></a>
### status

Consulter l'état d'exécution des processus worker.

**Utilisation :**
```bash
php webman status
```

**Options :**

| Option | Raccourci | Description |
|--------|----------|-------------|
| `--live` | `-d` | Afficher les détails (état en direct) |

<a name="connections"></a>
### connections

Obtenir les informations de connexion des processus worker.

**Utilisation :**
```bash
php webman connections
```
