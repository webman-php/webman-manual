# Basic Plugin Generation and Publishing Process

## Principle
1. Taking a cross-domain plugin as an example, the plugin is divided into three parts: a cross-domain middleware program file, a middleware configuration file `middleware.php`, and an `Install.php` which is automatically generated through a command.
2. We use a command to package and publish the three files to composer.
3. When users install the cross-domain plugin using composer, the `Install.php` in the plugin will copy the cross-domain middleware program file and configuration file to `{primary project}/config/plugin` for webman to load, thereby automatically configuring the cross-domain middleware file to take effect.
4. When users remove the plugin using composer, the `Install.php` will delete the corresponding cross-domain middleware program file and configuration file, achieving automatic plugin uninstallation.

## Specifications
1. The plugin name consists of two parts, `vendor` and `plugin name`, for example, `webman/push`, which corresponds to the composer package name.
2. The plugin configuration file is uniformly placed under `config/plugin/vendor/plugin name/` (the console command will automatically create the configuration directory). If the plugin does not require configuration, the automatically created configuration directory needs to be deleted.
3. The plugin configuration directory only supports `app.php` for main configuration, `bootstrap.php` for process start configuration, `route.php` for route configuration, `middleware.php` for middleware configuration, `process.php` for custom process configuration, `database.php` for database configuration, `redis.php` for redis configuration, and `thinkorm.php` for thinkorm configuration. These configurations will be automatically recognized by webman.
4. Plugins use the following method to obtain configuration: `config('plugin.vendor.plugin name.config file.specific configuration item');`, for example, `config('plugin.webman.push.app.app_key')`
5. If the plugin has its own database configuration, it can be accessed as follows: `illuminate/database` is `Db::connection('plugin.vendor.plugin name.specific connection')`, and `thinkrom` is `Db::connect('plugin.vendor.plugin name.specific connection')`
6. If the plugin needs to place business files in the `app/` directory, it needs to ensure that there are no conflicts with the user's project and other plugins.
7. Plugins should try to avoid copying files or directories to the primary project. For example, in addition to the configuration file, the cross-domain plugin's middleware file should be placed under `vendor/webman/cros/src` and does not need to be copied to the primary project.
8. It is recommended to use uppercase for plugin namespaces, for example, `Webman/Console`.

## Example

**Install the `webman/console` command line**

`composer require webman/console`

#### Create a Plugin

Assuming the created plugin is named `foo/admin` (the name is also the project name to be published by composer, the name needs to be lowercase), execute the command:
`php webman plugin:create --name=foo/admin`

After creating the plugin, it will generate the directory `vendor/foo/admin` to store plugin-related files and `config/plugin/foo/admin` to store plugin-related configurations.

> Note
> `config/plugin/foo/admin` supports the following configurations: `app.php` for main configuration, `bootstrap.php` for process start configuration, `route.php` for route configuration, `middleware.php` for middleware configuration, `process.php` for custom process configuration, `database.php` for database configuration, `redis.php` for redis configuration, and `thinkorm.php` for thinkorm configuration. The format of the configurations is the same as webman's, and these configurations will be automatically recognized and merged into the configuration.
Use `plugin` as a prefix for access, for example, `config('plugin.foo.admin.app')`.

#### Export the Plugin

After developing the plugin, execute the following command to export the plugin:
`php webman plugin:export --name=foo/admin`

> Explanation
> After exporting, the `config/plugin/foo/admin` directory will be copied to `vendor/foo/admin/src`, and an `Install.php` will be automatically generated. The `Install.php` is used to perform certain operations when the plugin is automatically installed and uninstalled.
> The default operation during installation is to copy the configuration from `vendor/foo/admin/src` to the `config/plugin` in the current project.
> The removal default operation is to delete the configuration files from the `config/plugin` in the current project.
> You can modify `Install.php` to perform some custom operations during the installation and uninstallation of the plugin.

#### Submit the Plugin
* Assuming you already have a [GitHub](https://github.com) and [Packagist](https://packagist.org) account.
* Create an admin project on [GitHub](https://github.com) and upload the code. Suppose the project URL is `https://github.com/your-username/admin`.
* Go to the address `https://github.com/your-username/admin/releases/new` to publish a release, such as `v1.0.0`.
* Go to [Packagist](https://packagist.org), click on `Submit` in the navigation, and submit your GitHub project URL `https://github.com/your-username/admin`. This completes the publication of a plugin.

> **Tip**
> If there is a conflict when submitting the plugin on `Packagist`, consider using a different vendor name. For example, change `foo/admin` to `myfoo/admin`.

Subsequently, when there are updates to your plugin project code, you need to synchronize the code to GitHub, then go to the address `https://github.com/your-username/admin/releases/new` to republish a release, and then click the `Update` button on the `https://packagist.org/packages/foo/admin` page to update the version.

## Add Commands to the Plugin
Sometimes, our plugin needs some custom commands to provide auxiliary functions. For example, after installing the `webman/redis-queue` plugin, the project will automatically add a `redis-queue:consumer` command. Users only need to run `php webman redis-queue:consumer send-mail` to generate a `SendMail.php` consumer class in the project, which helps with rapid development.

Suppose the `foo/admin` plugin needs to add a `foo-admin:add` command, refer to the following steps.

#### Create a Command

**Create a new command file `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Here is the command description';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Add name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **Note**
> To avoid command conflicts between plugins, it is recommended to format the command line as `vendor-plugin name:specific command`, for example, all commands of the `foo/admin` plugin should have the `foo-admin:` prefix, for example, `foo-admin:add`.

#### Add Configuration
**Create a configuration `config/plugin/foo/admin/command.php`**

```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....more configurations can be added...
];
```

> **Tip**
> `command.php` is used to configure custom commands for the plugin. Each element in the array corresponds to a command class file, and each class file corresponds to a command. When users run a command line, `webman/console` will automatically load the custom commands set in `command.php` for each plugin. For more information related to command lines, please refer to [Commands](console.md).

#### Execute Export
Execute the command `php webman plugin:export --name=foo/admin` to export the plugin and submit it to `Packagist`. After installing the `foo/admin` plugin, a `foo-admin:add` command will be added. Running `php webman foo-admin:add jerry` will print `Admin add jerry`.
