# Grundlegender Plugin-Generierungs- und Veröffentlichungsprozess

## Prinzip
1. Nehmen wir das Cross-Domain-Plugin als Beispiel. Das Plugin besteht aus drei Teilen: einer Cross-Domain-Middleware-Programmdatei, einer Middleware-Konfigurationsdatei middleware.php und einer Install.php, die durch Befehl automatisch generiert wird.
2. Wir verwenden Befehle, um die drei Dateien zu bündeln und in Composer zu veröffentlichen.
3. Wenn ein Benutzer das Cross-Domain-Plugin mit Composer installiert, wird Install.php die Cross-Domain-Middleware-Programmdatei und die Konfigurationsdatei in `{ Hauptprojekt }/config/plugin` kopieren, um von webman geladen zu werden. Dadurch werden die Cross-Domain-Middleware-Dateien automatisch konfiguriert und wirksam.
4. Wenn ein Benutzer das Plugin mit Composer entfernt, wird Install.php die entsprechenden Cross-Domain-Middleware-Programmdatei und Konfigurationsdatei löschen, um das Plugin automatisch zu deinstallieren.

## Spezifikation
1. Der Plugin-Name besteht aus zwei Teilen: `Hersteller` und `Plugin-Name`, z.B. `webman/push`, der dem Composer-Paketnamen entspricht.
2. Die Plugin-Konfigurationsdatei wird einheitlich unter `config/plugin/Hersteller/Plugin-Name/` (die Konsole erstellt automatisch das Konfigurationsverzeichnis) abgelegt. Wenn das Plugin keine Konfiguration benötigt, muss das automatisch erstellte Konfigurationsverzeichnis gelöscht werden.
3. Das Plugin-Konfigurationsverzeichnis unterstützt nur die Hauptkonfiguration app.php, die Startkonfiguration bootstrap.php, die Routenkonfiguration route.php, die Middleware-Konfiguration middleware.php, die benutzerdefinierte Prozesskonfiguration process.php, die Datenbankkonfiguration database.php, die Redis-Konfiguration redis.php, die ThinkORM-Konfiguration thinkorm.php. Diese Konfigurationen werden automatisch von webman erkannt.
4. Plugins können die Konfiguration mit der folgenden Methode abrufen: `config('plugin.Hersteller.Plugin-Name.Konfigurationsdatei.SpezifischesElement');`, z.B. `config('plugin.webman.push.app.app_key')`
5. Wenn ein Plugin eigene Datenbankkonfigurationen hat, kann darauf wie folgt zugegriffen werden: `illuminate/database` als `Db::connection('plugin.Hersteller.Plugin-Name.SpezifischeVerbindung')`, `thinkrom` als `Db::connct('plugin.Hersteller.Plugin-Name.SpezifischeVerbindung')`
6. Wenn ein Plugin Dateien im `app/`-Verzeichnis ablegen muss, muss sichergestellt werden, dass sie nicht mit den Dateien des Benutzerprojekts oder anderer Plugins in Konflikt geraten.
7. Plugins sollten darauf achten, Dateien oder Verzeichnisse nicht unnötigerweise in das Hauptprojekt zu kopieren. Zum Beispiel sollten Cross-Domain-Plugins neben der Konfigurationsdatei keine Dateien im Hauptprojekt kopieren, sondern die Middleware-Dateien sollten im Verzeichnis `vendor/webman/cros/src` sein und müssen nicht in das Hauptprojekt kopiert werden.
8. Es wird empfohlen, dass Plugin-Namespace in Großbuchstaben verwendet wird, z.B. Webman/Console.

## Beispiel

**Installation des Befehlszeilen-Plugins `webman/console`**

`composer require webman/console`

#### Erstellen eines Plugins

Angenommen, das Plugin heißt `foo/admin` (der Name entspricht dem später zu veröffentlichenden Projektnamen und muss in Kleinbuchstaben sein), führen Sie den Befehl aus:
`php webman plugin:create --name=foo/admin`

Nach dem Erstellen des Plugins wird das Verzeichnis `vendor/foo/admin` für die Speicherung von Plugin-spezifischen Dateien und `config/plugin/foo/admin` für die Speicherung von Plugin-spezifischen Konfigurationen generiert.

> Hinweis
> `config/plugin/foo/admin` unterstützt die folgenden Konfigurationen: app.php für die Hauptkonfiguration, bootstrap.php für die Startkonfiguration, route.php für die Routenkonfiguration, middleware.php für die Middleware-Konfiguration, process.php für die benutzerdefinierte Prozesskonfiguration, database.php für die Datenbankkonfiguration, redis.php für die Redis-Konfiguration, thinkorm.php für die ThinkORM-Konfiguration. Die Konfigurationen haben das gleiche Format wie Webman und werden automatisch von Webman erkannt und in die Konfiguration integriert.
Um auf die Konfiguration zuzugreifen, wird der Präfix `plugin` verwendet, z.B. config('plugin.foo.admin.app');


#### Plugin exportieren

Sobald das Plugin entwickelt wurde, führen Sie den folgenden Befehl aus, um das Plugin zu exportieren
`php webman plugin:export --name=foo/admin`
Exportieren

> Erklärung
> Nach dem Export wird das Verzeichnis config/plugin/foo/admin nach vendor/foo/admin/src kopiert und automatisch eine Install.php generiert. Install.php wird verwendet, um bei der automatischen Installation und Deinstallation des Plugins bestimmte Operationen auszuführen.
Der Standardvorgang bei der Installation besteht darin, die Konfigurationen unter vendor/foo/admin/src in das Konfigurationsverzeichnis des aktuellen Projekts zu kopieren.
Beim Entfernen besteht der Standardvorgang darin, die Konfigurationsdateien im Konfigurationsverzeichnis des aktuellen Projekts zu löschen.
Sie können Install.php anpassen, um bei der Installation und Deinstallation des Plugins benutzerdefinierte Operationen durchzuführen.

#### Plugin veröffentlichen
* Angenommen Sie haben bereits ein [Github](https://github.com) und ein [Packagist](https://packagist.org) Konto
* Auf [Github](https://github.com) erstellen Sie ein Projekt namens admin und laden den Code hoch, z.B. ist die Projektadresse `https://github.com/yourusername/admin`
* Gehen Sie zur Adresse `https://github.com/yourusername/admin/releases/new` und veröffentlichen Sie ein Release, z.B. `v1.0.0`
* Gehen Sie zu [Packagist](https://packagist.org) und klicken Sie auf `Submit` in der Navigation. Geben Sie die Adresse Ihres Github-Projekts `https://github.com/yourusername/admin` ein, um das Plugin zu veröffentlichen.

> **Hinweis**
> Wenn bei der Einreichung des Plugins in `Packagist` ein Namenskonflikt angezeigt wird, können Sie einen anderen Herstellernamen verwenden, z.B. statt `foo/admin` ändern Sie es in `myfoo/admin`

Jedes Mal, wenn der Code Ihres Plugin-Projekts aktualisiert wird, müssen Sie den Code auf Github synchronisieren, erneut zur Adresse `https://github.com/yourusername/admin/releases/new` gehen, ein neues Release veröffentlichen und dann auf der Seite `https://packagist.org/packages/foo/admin` die Schaltfläche `Update` anklicken, um die Version zu aktualisieren.

## Hinzufügen eines Befehls zu einem Plugin
Manchmal benötigt unser Plugin benutzerdefinierte Befehle, um bestimmte Funktionen zu unterstützen. Zum Beispiel, nach der Installation des `webman/redis-queue`-Plugins wird ein Befehl `redis-queue:consumer` automatisch zum Projekt hinzugefügt. Benutzer müssen nur `php webman redis-queue:consumer send-mail` ausführen, um eine Verbraucherklasse SendMail.php im Projekt zu erstellen, was die schnelle Entwicklung unterstützt.

Angenommen, das `foo/admin`-Plugin muss den Befehl `foo-admin:add` hinzufügen, folgen Sie den Schritten unten.

#### Neuen Befehl erstellen
**Erstellen Sie die Befehlsdatei `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Hier steht die Beschreibung des Befehls';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Name hinzufügen');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin $name hinzufügen");
        return self::SUCCESS;
    }

}
```

> **Hinweis**
> Um Konflikte zwischen Plugin-Befehlen zu vermeiden, sollte das Befehlsformat `Hersteller-Plugin-Name:SpezifischerBefehl` empfohlen werden, z.B. sollte jedes Plugin-Befehl des Plugins `foo/admin` mit `foo-admin:` als Präfix beginnen, z.B. `foo-admin:add`.

#### Hinzufügen der Konfiguration
**Erstellen Sie die Konfiguration `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....Weitere Konfigurationen hinzufügen...
];
```

> **Hinweis**
> `command.php` wird zum Hinzufügen benutzerdefinierter Befehle für das Plugin verwendet. Jedes Element im Array entspricht einer Befehlszeilenklasse, und jede Klasse entspricht einem Befehl. Wenn Benutzer einen Befehl in der Befehlszeile ausführen, lädt `webman/console` automatisch jedes benutzerdefinierte Befehlsset aus `command.php` jeder Plugin. Für weitere Informationen zu Befehlszeilen lesen Sie bitte [Befehlszeilen](console.md)

#### Export ausführen
Führen Sie den Befehl `php webman plugin:export --name=foo/admin` aus, um das Plugin zu exportieren und bei `packagist` zu veröffentlichen. Nach der Installation des `foo/admin`-Plugins wird der Befehl `foo-admin:add` hinzugefügt. Durch die Ausführung von `php webman foo-admin:add jerry` wird "Admin hinzufügen jerry" ausgegeben.
