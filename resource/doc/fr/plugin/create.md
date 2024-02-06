# Plugin de génération de processus de base et de publication

## Principe
1. Pour un exemple de plugin de traversée de domaine, le plugin se compose de trois parties : un fichier de programme middleware pour la traversée de domaine, un fichier de configuration du middleware middleware.php, et un fichier Install.php généré par commande.
2. Nous utilisons une commande pour empaqueter ces trois fichiers et les publier sur composer.
3. Lorsque l'utilisateur installe le plugin de traversée de domaine via composer, Install.php du plugin copiera le fichier de programme middleware de traversée de domaine et le fichier de configuration dans `{projet principal}/config/plugin`, pour les charger sur webman et activer automatiquement la configuration du fichier de programme middleware de traversée de domaine.
4. Lorsque l'utilisateur supprime le plugin via composer, Install.php supprimera les fichiers de programme middleware de traversée de domaine et les fichiers de configuration associés, afin de désinstaller automatiquement le plugin.

## Normes
1. Le nom du plugin se compose de deux parties, le "fabricant" et le "nom du plugin". Par exemple, `webman/push`, correspondant au nom du paquet composer.
2. Les fichiers de configuration des plugins sont uniformément placés dans `config/plugin/fabricant/nom du plugin/` (la commande console créera automatiquement le répertoire de configuration). Si le plugin ne nécessite aucune configuration, le répertoire de configuration créé automatiquement doit être supprimé.
3. Le répertoire de configuration du plugin ne prend en charge que les configurations principales telles que app.php (configuration principale du plugin), bootstrap.php (configuration de démarrage du processus), route.php (configuration des routes), middleware.php (configuration du middleware), process.php (configuration des processus personnalisés), database.php (configurations de la base de données), redis.php (configurations de Redis), thinkorm.php (configurations de ThinkORM). Ces configurations sont automatiquement reconnues par webman.
4. Les plugins utilisent la méthode suivante pour obtenir une configuration : `config('plugin.fabricant.nom du plugin.nom du fichier de configuration.configuration spécifique')`, par exemple `config('plugin.webman.push.app.app_key')`.
5. Si le plugin a sa propre configuration de base de données, il est accédé de la manière suivante : `illuminate/database` en tant que `Db::connection('plugin.fabricant.nom du plugin.connexion spécifique')`, `thinkrom` comme `Db::connection('plugin.fabricant.nom du plugin.connexion spécifique')`.
6. Si le plugin doit placer des fichiers d'activité dans le répertoire `app/`, il doit s'assurer qu'ils ne entrent en conflit ni avec le projet utilisateur ni avec d'autres plugins.
7. Les plugins doivent éviter autant que possible de copier des fichiers ou des répertoires dans le projet principal. Par exemple, un plugin de traversée de domaine, à part le fichier de configuration à copier dans le projet principal, le fichier de programme middleware doit être placé dans `vendor/webman/cros/src`, sans nécessité de le copier dans le projet principal.
8. Il est recommandé de mettre en majuscules les espaces de noms des plugins, par exemple Webman/Console.

## Exemple

**Installation de la commande `webman/console`**

`composer require webman/console`

#### Création du plugin

Supposons que le plugin créé s'appelle `foo/admin` (le nom correspond au nom du projet à publier ultérieurement sur composer, il doit être en minuscules).
Exécuter la commande
`php webman plugin:create --name=foo/admin`

Après la création du plugin, un répertoire `vendor/foo/admin` sera généré pour stocker les fichiers associés au plugin et un `config/plugin/foo/admin` pour stocker la configuration du plugin.

> Remarque
> `config/plugin/foo/admin` prend en charge les configurations suivantes : app.php (configuration principale du plugin), bootstrap.php (configuration de démarrage du processus), route.php (configuration des routes), middleware.php (configuration du middleware), process.php (configuration des processus personnalisés), database.php (configurations de la base de données), redis.php (configurations de Redis), thinkorm.php (configurations de ThinkORM). Le format de configuration est identique à celui de webman et ces configurations sont automatiquement reconnues par webman.
Pour l'utiliser, ajoutez le préfixe `plugin`, par exemple config('plugin.foo.admin.app');

#### Exportation du plugin

Une fois le développement du plugin terminé, exécutez la commande suivante pour l'exporter :
`php webman plugin:export --name=foo/admin`

> Remarque
> Une fois exporté, le répertoire config/plugin/foo/admin sera copié dans src de vendor/foo/admin, et un fichier Install.php sera également créé. Install.php est utilisé pour exécuter des opérations automatiques lors de l'installation et de la désinstallation du plugin.
L'opération par défaut à l'installation consiste à copier les configurations de src de vendor/foo/admin dans config/plugin du projet actuel.
L'opération par défaut à la suppression consiste à supprimer les fichiers de configuration de config/plugin du projet actuel.
Vous pouvez modifier Install.php pour effectuer des opérations personnalisées à l'installation et à la désinstallation du plugin.

#### Soumission du plugin
* Supposons que vous ayez déjà un compte [github](https://github.com) et [packagist](https://packagist.org) 
* Créez un projet admin sur [github](https://github.com) et mettez-y le code, l'adresse du projet sera par exemple `https://github.com/yourusername/admin`
* Accédez à l'adresse `https://github.com/yourusername/admin/releases/new` pour publier une release, par exemple `v1.0.0`
* Accédez à [packagist](https://packagist.org), cliquez sur `Submit` dans la barre de navigation, soumettez l'URL de votre projet GitHub `https://github.com/yourusername/admin`, c'est ainsi que vous publiez un plugin

> **Conseil**
> Si vous rencontrez des conflits lors de la soumission du plugin dans `packagist`, vous pouvez changer le nom du fabricant, par exemple, de `foo/admin` à `myfoo/admin`

Par la suite, dès que votre projet de plugin est mis à jour, vous devez synchroniser le code sur github, republier une release à l'adresse `https://github.com/yourusername/admin/releases/new`, puis cliquer sur le bouton `Update` sur la page `https://packagist.org/packages/foo/admin` pour mettre à jour la version.
## Ajout de commandes au plugin
Parfois, nos plugins nécessitent des commandes personnalisées pour fournir des fonctionnalités d'assistance, par exemple, après l'installation du plugin `webman/redis-queue`, le projet ajoutera automatiquement une commande `redis-queue:consumer`, permettant à l'utilisateur de simplement exécuter `php webman redis-queue:consumer send-mail` pour générer une classe de consommateur SendMail.php dans le projet, ce qui contribue au développement rapide.

Supposons que le plugin `foo/admin` nécessite l'ajout de la commande `foo-admin:add`, suivez les étapes ci-dessous.

#### Créer une commande
**Créez un nouveau fichier de commande `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php
namespace Foo\Admin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'Ceci est une description de la ligne de commande';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Ajouter un nom');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Administrateur ajoute $name");
        return self::SUCCESS;
    }
}
```

> **Remarque**
> Pour éviter les conflits entre les commandes des plugins, il est recommandé de suivre le format de la ligne de commande `fabricant-nom du plugin:commande spécifique`, par exemple, toutes les commandes du plugin `foo/admin` doivent commencer par `foo-admin:`, par exemple `foo-admin:add`.

#### Ajouter une configuration
**Créez une configuration `config/plugin/foo/admin/command.php`**
```php
<?php
use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....vous pouvez ajouter plusieurs configurations...
];
```

> **Conseil**
> `command.php` est utilisé pour configurer des commandes personnalisées pour le plugin. Chaque élément du tableau correspond à un fichier de classe de commande, chaque classe correspondant à une commande. Lorsque l'utilisateur exécute une commande, `webman/console` chargera automatiquement toutes les commandes personnalisées configurées dans `command.php` de chaque plugin. Pour en savoir plus sur les lignes de commande, veuillez consulter le document [Commandes](console.md).

#### Effectuer l'exportation
Exécutez la commande `php webman plugin:export --name=foo/admin` pour exporter le plugin et le soumettre à `packagist`. Ainsi, lors de l'installation du plugin `foo/admin`, la commande `foo-admin:add` sera ajoutée. En exécutant `php webman foo-admin:add jerry`, le message `Administrateur ajoute jerry` sera imprimé.
