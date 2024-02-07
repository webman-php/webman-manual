# Grundlegender Plugin-Erstellungs- und Veröffentlichungsprozess

## Prinzip
1. Als Beispiel für ein Cross-Domain-Plugin ist das Plugin in drei Teile unterteilt: eine Cross-Domain-Middleware-Programmdatei, eine Middleware-Konfigurationsdatei middleware.php und eine durch Befehl automatisch generierte Install.php.
2. Wir verwenden einen Befehl, um die drei Dateien zu bündeln und auf Composer zu veröffentlichen.
3. Wenn ein Benutzer das Cross-Domain-Plugin mit Composer installiert, kopiert Install.php die Cross-Domain-Middleware-Programmdatei und die Konfigurationsdatei in `{Hauptprojekt}/config/plugin`, damit webman sie laden kann, um die automatische Konfiguration der Cross-Domain-Middleware-Datei zu ermöglichen.
4. Wenn ein Benutzer das Plugin mit Composer deinstalliert, löscht Install.php entsprechende Cross-Domain-Middleware-Programm- und Konfigurationsdateien, sodass das Plugin automatisch deinstalliert wird.

## Standard
1. Der Name des Plugins besteht aus zwei Teilen: `Hersteller` und `Plugin-Name`, z.B. `webman/push`, was dem Composer-Paketnamen entspricht.
2. Die Plugin-Konfigurationsdateien werden standardmäßig unter `config/plugin/Hersteller/Plugin-Name/` gespeichert (der Konsolebefehl erstellt automatisch das Konfigurationsverzeichnis). Wenn das Plugin keine Konfiguration benötigt, müssen Sie das automatisch erstellte Konfigurationsverzeichnis löschen.
3. Das Plugin-Konfigurationsverzeichnis unterstützt nur app.php (Hauptkonfiguration des Plugins), bootstrap.php (Prozessstartkonfiguration), route.php (Routing-Konfiguration), middleware.php (Middleware-Konfiguration), process.php (Benutzerdefinierte Prozesskonfiguration), database.php (Datenbankkonfiguration), redis.php (Redis-Konfiguration), thinkorm.php (ThinkORM-Konfiguration). Diese Konfigurationen werden automatisch von webman erkannt.
4. Das Plugin verwendet die folgende Methode, um auf Konfigurationen zuzugreifen: `config('plugin.Hersteller.Plugin-Name.Konfigurationsdatei.spezifische Konfiguration')`, z.B. `config('plugin.webman.push.app.app_key')`.
5. Wenn das Plugin eigene Datenbankkonfigurationen hat, erfolgt der Zugriff wie folgt: `illuminate/database` ist `Db::connection('plugin.Hersteller.Plugin-Name.spezifische Verbindung')`, `thinkrom` ist `Db::connect('plugin.Hersteller.Plugin-Name.spezifische Verbindung')`.
6. Wenn das Plugin Geschäftsdateien im `app/`-Verzeichnis ablegen muss, stellen Sie sicher, dass es nicht mit den Dateien des Benutzerprojekts oder anderen Plugins kollidiert.
7. Das Plugin sollte nach Möglichkeit das Kopieren von Dateien oder Verzeichnissen zum Hauptprojekt vermeiden. Zum Beispiel sollten Cross-Domain-Plugins Middleware-Dateien im `vendor/webman/cros/src`-Verzeichnis platzieren, ohne sie in das Hauptprojekt zu kopieren.
8. Der Namensraum des Plugins sollte idealerweise in Großbuchstaben sein, z.B. Webman/Console.

## Beispiel

**Installation des Befehlszeilenplugins "webman/console"**

`composer require webman/console`

#### Erstellen des Plugins

Angenommen, das zu erstellende Plugin heißt `foo/admin` (der Name ist auch der Name des Projekts, das später auf Composer veröffentlicht werden muss). Führen Sie den Befehl aus: 
`php webman plugin:create --name=foo/admin`

Nach Erstellung des Plugins wird ein Verzeichnis `vendor/foo/admin` zur Speicherung der Plugin-bezogenen Dateien und ein Verzeichnis `config/plugin/foo/admin` zur Speicherung der Plugin-Konfiguration erstellt.

> Beachten Sie
> `config/plugin/foo/admin` unterstützt die folgenden Konfigurationen: app.php (Hauptkonfiguration des Plugins), bootstrap.php (Prozessstartkonfiguration), route.php (Routing-Konfiguration), middleware.php (Middleware-Konfiguration), process.php (Benutzerdefinierte Prozesskonfiguration), database.php (Datenbankkonfiguration), redis.php (Redis-Konfiguration), thinkorm.php (ThinkORM-Konfiguration). Das Format der Konfigurationen entspricht webman, und diese Konfigurationen werden automatisch von webman erkannt und in die Konfiguration fusioniert. Sie können über den Prefix `plugin` darauf zugreifen, z.B. config('plugin.foo.admin.app').

#### Veröffentlichen des Plugins

Nachdem das Plugin entwickelt wurde, führen Sie den folgenden Befehl aus, um das Plugin zu veröffentlichen:
`php webman plugin:export --name=foo/admin`

Nach der Veröffentlichung werden die Dateien im Verzeichnis `config/plugin/foo/admin` in das Verzeichnis `vendor/foo/admin/src` kopiert, und gleichzeitig wird eine Datei Install.php automatisch generiert. Install.php wird verwendet, um bei der automatischen Installation und Deinstallation des Plugins bestimmte Aktionen auszuführen.
Die Standardaktion bei der Installation ist das Kopieren der Konfigurationen aus `vendor/foo/admin/src` in das Konfigurationsverzeichnis des aktuellen Projekts. Beim Entfernen erfolgt die Standardaktion durch das Löschen der Konfigurationsdateien im Konfigurationsverzeichnis des aktuellen Projekts. Sie können Install.php anpassen, um benutzerdefinierte Aktionen bei der Installation und Deinstallation des Plugins auszuführen.

#### Einreichen des Plugins
- Angenommen, Sie haben bereits ein Konto auf [GitHub](https://github.com) und [Packagist](https://packagist.org).
- Erstellen Sie auf [GitHub](https://github.com) ein Projekt mit dem Namen "admin" und laden Sie den Code hoch. Die Adresse des Projekts ist z.B. `https://github.com/your-username/admin`.
- Gehen Sie zu `https://github.com/your-username/admin/releases/new` und veröffentlichen Sie ein Release, z.B. `v1.0.0`.
- Gehen Sie zu [Packagist](https://packagist.org), klicken Sie auf `Submit` in der Navigation, und reichen Sie die Adresse Ihres GitHub-Projekts `https://github.com/your-username/admin` ein, um das Plugin zu veröffentlichen.

> **Hinweis**
> Falls bei der Einreichung des Plugins in `Packagist` ein Konflikt angezeigt wird, können Sie einen anderen Hersteller-Namen verwenden, z.B. `foo/admin` kann in `myfoo/admin` geändert werden.

Wenn Ihr Plugin-Projektcode später aktualisiert wird, müssen Sie den Code auf GitHub aktualisieren und erneut zu `https://github.com/your-username/admin/releases/new` gehen, um ein neues Release zu veröffentlichen. Anschließend klicken Sie auf die Schaltfläche `Update` auf der Seite `https://packagist.org/packages/foo/admin`, um die Version zu aktualisieren.

## Hinzufügen von Befehlen zu einem Plugin
Manchmal benötigt ein Plugin benutzerdefinierte Befehle, um zusätzliche Funktionen bereitzustellen. Wenn z.B. nach der Installation des Plugins `webman/redis-queue` automatisch ein `redis-queue:consumer`-Befehl im Projekt hinzugefügt wird, kann der Benutzer einfach `php webman redis-queue:consumer send-mail` ausführen, um eine SendMail.php-Verbraucherklasse im Projekt zu erzeugen, was die schnelle Entwicklung unterstützt.

Angenommen, das `foo/admin`-Plugin benötigt den Hinzufügen von `foo-admin:add`-Befehl, hier sind die Schritte dazu.

#### Hinzufügen eines Befehls
**Erstellen Sie eine Befehlsdatei in `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Hier ist die Beschreibung des Befehls';

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
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **Hinweis**
> Um Konflikte zwischen Plugin-Befehlen zu vermeiden, sollte das Format des Befehls `Hersteller-Plugin-Name:konkreter Befehl` sein, z.B. alle Befehle des Plugins `foo/admin` sollten das Präfix `foo-admin:` haben, z.B. `foo-admin:add`.

#### Hinzufügen von Konfigurationen
**Erstellen Sie eine Konfiguration `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ...mehrere Konfigurationen können hinzugefügt werden...
];
```

> **Hinweis**
> `command.php` wird verwendet, um benutzerdefinierte Befehle für das Plugin zu konfigurieren. Jedes Element des Arrays entspricht einer Befehlsklassen-Datei, und jede Klassen-Datei entspricht einem Befehl. Wenn ein Benutzer einen Befehl ausführt, werden automatisch alle benutzerdefinierten Befehle aus `command.php` jedes Plugins von `webman/console` geladen. Weitere Informationen zu Befehlszeilen finden Sie im [Befehlszeilen-Dokument](console.md).

#### Durchführen der Exportierung
Führen Sie den Befehl `php webman plugin:export --name=foo/admin` aus, um das Plugin zu exportieren und bei `packagist` einzureichen. Nach der Installation des `foo/admin`-Plugins wird ein `foo-admin:add`-Befehl hinzugefügt. Durch Ausführen von `php webman foo-admin:add jerry` wird "Admin add jerry" angezeigt.
