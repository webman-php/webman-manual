# 流程 de création et de publication de plugins de base

## Principe
1. En prenant l'exemple d'un plugin de cross-origin, le plugin se compose de trois parties : un fichier de programme middleware cross-origin, un fichier de configuration middleware.php, et un Install.php généré automatiquement à l'aide d'une commande.
2. Nous utilisons une commande pour empaqueter ces trois fichiers et les publier sur composer.
3. Lorsqu'un utilisateur installe le plugin cross-origin avec composer, Install.php du plugin copiera le fichier de programme middleware cross-origin ainsi que le fichier de configuration dans `{projet principal}/config/plugin`, afin que webman puisse les charger. Ce qui permet d'appliquer automatiquement les configurations des fichiers middleware cross-origin.
4. Lorsqu'un utilisateur supprime ce plugin avec composer, Install.php supprimera les fichiers de programm middleware cross-origin correspondants et le fichier de configuration, réalisant ainsi la désinstallation automatique du plugin.

## Normes
1. Le nom du plugin se compose de deux parties : le "fabricant" et le "nom du plugin", par exemple `webman/push`, qui correspond au nom du package composer.
2. Les fichiers de configuration du plugin sont tous stockés dans le dossier `config/plugin/fabricant/plugin` (la commande console créera automatiquement ce dossier de configuration). Si le plugin n'a pas besoin de configuration, le dossier de configuration créé automatiquement doit être supprimé.
3. Les dossiers de configuration du plugin prennent en charge uniquement les fichiers suivants : app.php (configuration principale du plugin), bootstrap.php (configuration de démarrage du processus), route.php (configuration des routes), middleware.php (configuration des middlewares), process.php (configuration des processus personnalisés), database.php (configuration de la base de données), redis.php (configuration redis), thinkorm.php (configuration thinkorm). Ces configurations seront automatiquement reconnues par webman.
4. Le plugin utilise la méthode suivante pour accéder à la configuration : `config('plugin.fabricant.plugin.nom du fichier de configuration.configuration spécifique');` par exemple `config('plugin.webman.push.app.app_key')`.
5. Si le plugin a sa propre configuration de base de données, elle est accessible de la manière suivante : `illuminate/database` : `Db::connection('plugin.fabricant.plugin.connexion spécifique')`, `thinkrom` : `Db::connection('plugin.fabricant.plugin.connexion spécifique')`.
6. Si le plugin doit placer des fichiers métier dans le répertoire `app/`, il faut s'assurer qu'ils ne entrent pas en conflit avec les projets utilisateur ou d'autres plugins.
7. Le plugin devrait éviter autant que possible de copier des fichiers ou des répertoires dans le projet principal. Par exemple, le fichier de middleware du plugin cross-origin doit être placé dans `vendor/webman/cros/src`, sans nécessité de copier dans le projet principal.
8. Il est recommandé d'utiliser des majuscules pour les espaces de noms des plugins, par exemple Webman/Console.

## Exemple

**Installation de la commande `webman/console`**

`composer require webman/console`

#### Création du plugin

Supposons que le plugin créé s'appelle `foo/admin` (le nom doit être en minuscules pour être compatible avec le nom du projet composer). Exécutez la commande suivante : `php webman plugin:create --name=foo/admin`

Une fois le plugin créé, deux dossiers seront générés : `vendor/foo/admin` pour stocker les fichiers du plugin, et `config/plugin/foo/admin` pour stocker la configuration associée du plugin.

> Remarque
> Le dossier `config/plugin/foo/admin` prend en charge les configurations suivantes : app.php (configuration principale du plugin), bootstrap.php (configuration de démarrage du processus), route.php (configuration des routes), middleware.php (configuration des middlewares), process.php (configuration des processus personnalisés), database.php (configuration de la base de données), redis.php (configuration redis), thinkorm.php (configuration thinkorm). Le format des configurations est identique à celui de webman et ces configurations seront automatiquement reconnues et fusionnées dans la configuration.

#### Exporter le plugin

Une fois le développement du plugin terminé, exécutez la commande suivante pour l'exporter : `php webman plugin:export --name=foo/admin`

> Remarque
> Après l'exportation, le dossier `config/plugin/foo/admin` est copié dans `vendor/foo/admin/src`, et un fichier Install.php est généré automatiquement. Install.php est utilisé pour exécuter certaines opérations lors de l'installation et de la désinstallation automatique du plugin.
> Par défaut, lors de l'installation, les configurations du dossier `vendor/foo/admin/src` sont copiées dans `config/plugin` du projet actuel.
> Par défaut, lors de la désinstallation, les fichiers de configuration de `config/plugin` du projet actuel sont supprimés.
> Vous pouvez modifier Install.php pour effectuer des opérations personnalisées lors de l'installation et de la désinstallation du plugin.

#### Soumettre le plugin

* Supposons que vous ayez déjà un compte sur [github](https://github.com) et [packagist](https://packagist.org)
* Sur [github](https://github.com), créez un nouveau projet admin et téléversez-y le code. Si l'adresse du projet est `https://github.com/your-username/admin`
* Rendez-vous sur `https://github.com/your-username/admin/releases/new` pour publier une nouvelle version, par exemple `v1.0.0`
* Sur [packagist](https://packagist.org), cliquez sur `Submit` dans la barre de navigation, puis soumettez l'adresse de votre projet sur github `https://github.com/your-username/admin`. Votre plugin est ainsi publié.

> **Astuce**
> Si lors de la soumission du plugin à `packagist` un conflit apparaît, vous pouvez modifier le nom du fabricant. Par exemple, changer `foo/admin` en `myfoo/admin`.

Par la suite, lorsque le code de votre plugin est mis à jour, synchronisez le code sur github, puis republiez une nouvelle version à l'adresse `https://github.com/your-username/admin/releases/new`. Ensuite, sur la page `https://packagist.org/packages/foo/admin`, cliquez sur le bouton `Update` pour mettre à jour la version.

## Ajout de commandes au plugin
Parfois, un plugin nécessite des commandes personnalisées pour fournir des fonctionnalités d'assistance. Par exemple, lors de l'installation du plugin `webman/redis-queue`, le projet inclura automatiquement une commande `redis-queue:consumer`, permettant à l'utilisateur de lancer la commande `php webman redis-queue:consumer send-mail` pour générer une classe de consommateur SendMail.php dans le projet, ce qui facilite le développement.

Supposons que le plugin `foo/admin` ait besoin d'ajouter la commande `foo-admin:add`, suivez les étapes ci-dessous.

#### Créer une nouvelle commande

**Créez un nouveau fichier de commande `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php
namespace Foo\Admin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command {
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'Description de la commande ici';
    protected function configure() {
        $this->addArgument('name', InputArgument::REQUIRED, 'Ajouter un nom');
    }
    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument('name');
        $output->writeln("Ajout admin $name");
        return self::SUCCESS;
    }
}
```

> **Remarque**
> Pour éviter les conflits de commandes entre les plugins, le format de la ligne de commande est recommandé comme suit : `fabricant-nom du plugin:commande spécifique`, par exemple, toutes les commandes du plugin `foo/admin` devraient commencer par `foo-admin:`, par exemple `foo-admin:add`.

#### Ajouter une configuration

**Créer une nouvelle configuration `config/plugin/foo/admin/command.php`**

```php
<?php
use Foo\Admin\FooAdminAddCommand;
return [
    FooAdminAddCommand::class,
    // ....vous pouvez ajouter plusieurs configurations...
];
```

> **Astuce**
> `command.php` est utilisé pour configurer des commandes personnalisées pour le plugin. Chaque élément du tableau correspond à un fichier de classe de commande et chaque classe de commande correspond à une commande. Lorsque l'utilisateur exécute une commande, `webman/console` chargera automatiquement toutes les commandes personnalisées configurées dans `command.php` de chaque plugin. Pour en savoir plus sur les commandes, veuillez consulter la section Commandes (console.md).

#### Exporter

Exécutez la commande `php webman plugin:export --name=foo/admin` pour exporter le plugin et le soumettre à `packagist`. Ainsi, après installation du plugin `foo/admin`, la commande `foo-admin:add` sera ajoutée. L'exécution de la commande `php webman foo-admin:add jerry` affichera `Ajout admin jerry`.
