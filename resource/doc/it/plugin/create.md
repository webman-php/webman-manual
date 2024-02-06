# Procedure di generazione e pubblicazione del plugin di base

## Principio
1. Prendendo come esempio il plugin di cross-origin, il plugin si compone di tre parti: un file di programma middleware di cross-origin, un file di configurazione middleware.php e un file Install.php generato automaticamente tramite comando.
2. Utilizziamo un comando per impacchettare i tre file e distribuirli tramite composer.
3. Quando un utente installa il plugin di cross-origin tramite composer, il file Install.php del plugin copierà il file di programma middleware di cross-origin e il file di configurazione in `{progetto principale}/config/plugin`, affinché webman li carichi. In questo modo si rende effettiva la configurazione automatica dei file middleware di cross-origin.
4. Quando un utente disinstalla il plugin usando composer, Install.php cancellerà i rispettivi file di programma middleware di cross-origin e di configurazione, garantendo così la disinstallazione automatica del plugin.

## Standard
1. Il nome del plugin è composto da due parti, il `produttore` e il `nome del plugin`, ad esempio `webman/push`, corrispondente al nome del pacchetto composer.
2. I file di configurazione del plugin sono tutti collocati in `config/plugin/produttore/nome del plugin/` (il comando della console crea automaticamente la directory di configurazione). Se il plugin non richiede configurazioni, quindi la directory di configurazione creata automaticamente va eliminata.
3. La directory di configurazione del plugin supporta solo i file app.php (la configurazione principale del plugin), bootstrap.php (la configurazione di avvio del processo), route.php (la configurazione del percorso), middleware.php (la configurazione del middleware), process.php (la configurazione del processo personalizzata), database.php (la configurazione del database), redis.php (la configurazione di redis), thinkorm.php (la configurazione di thinkorm). Queste configurazioni sono automaticamente riconosciute da webman.
4. Il plugin utilizza il seguente metodo per ottenere le configurazioni: `config('plugin.produttore.nome del plugin.file di configurazione.specifica configurazione');`, ad esempio `config('plugin.webman.push.app.app_key')`.
5. Se il plugin ha una sua configurazione del database, essa viene acceduta nel seguente modo: `illuminate/database` diventa `Db::connection('plugin.produttore.nome del plugin.connessione specifica')`, mentre `thinkorm` diventa `Db::connct('plugin.produttore.nome del plugin.connessione specifica')`.
6. Se il plugin deve collocare file di business sotto la directory `app/`, bisogna garantire che non entrino in conflitto con i file del progetto dell'utente o altri plugin.
7. Si consiglia che il plugin eviti il più possibile di copiare file o directory nel progetto principale. Ad esempio, eccetto i file di configurazione, il file del middleware dovrebbe essere collocato in `vendor/webman/cros/src`, e non deve essere copiato nel progetto principale.
8. Si consiglia di utilizzare la convenzione di denominazione delle classi del plugin. Ad esempio, Webman/Console per lo spazio dei nomi.

## Esempio

**Installa il comando di `webman/console`**

`composer require webman/console`

#### Creazione del plugin
Supponiamo che il plugin creato si chiami `foo/admin` (il nome corrisponderà al nome del progetto da pubblicare in seguito tramite composer e deve essere in minuscolo)
Esegui il comando
`php webman plugin:create --name=foo/admin`

Dopo la creazione del plugin, verranno create le directory `vendor/foo/admin` per conservare file relativi al plugin e `config/plugin/foo/admin` per memorizzare le configurazioni relative al plugin.

> Nota
> `config/plugin/foo/admin` supporta le seguenti configurazioni: app.php (configurazione principale del plugin), bootstrap.php (configurazione di avvio del processo), route.php (configurazione del percorso), middleware.php (configurazione del middleware), process.php (configurazione del processo personalizzata), database.php (configurazione del database), redis.php (configurazione di redis), thinkorm.php (configurazione di thinkorm). Il formato della configurazione corrisponde a quello di webman e sarà riconosciuto automaticamente da webman e unito alla configurazione.
Al momento dell'uso, si accede alla configurazione aggiungendo il prefisso `plugin`, ad esempio config('plugin.foo.admin.app').

#### Esporta il plugin

Dopo aver sviluppato il plugin, esegui il seguente comando per esportarlo
`php webman plugin:export --name=foo/admin`

> Spiegazione
> Dopo l'esportazione, la directory config/plugin/foo/admin verrà copiata nella directory vendor/foo/admin/src, e verrà generato un file Install.php. Install.php serve ad eseguire delle operazioni automaticamente durante l'installazione e la disinstallazione del plugin. L'operazione predefinita durante l'installazione è copiare le configurazioni dalla directory vendor/foo/admin/src al progetto corrente in config/plugin, mentre durante la disinstallazione l'operazione predefinita è cancellare i file di configurazione dalla direcotry config/plugin del progetto corrente. È possibile modificare Install.php per aggiungere delle operazioni personalizzate durante l'installazione e la disinstallazione del plugin.

#### Pubblicazione del plugin
Supponendo che tu abbia già un account su [github](https://github.com) e [packagist](https://packagist.org)
Crea un progetto admin su [github](https://github.com) e carica il codice. Supponiamo che l'indirizzo del progetto sia `https://github.com/username/admin`.
Entra nell'indirizzo `https://github.com/username/admin/releases/new` e pubblica una release come `v1.0.0`
Entra in [packagist](https://packagist.org) e fai clic su `Submit` nella barra di navigazione, quindi invia l'indirizzo del tuo progetto github `https://github.com/username/admin`, e in questo modo hai pubblicato il plugin.

> Suggerimento
> Se durante la pubblicazione del plugin su `packagist` si verifica un conflitto di nomi, puoi cambiare il nome del produttore, ad esempio da `foo/admin` a `myfoo/admin`.

In futuro, quando aggiorni il codice del progetto del plugin, dovrai sincronizzare il codice su github e pubblicare nuovamente una release all'indirizzo `https://github.com/username/admin/releases/new`, quindi dalla pagina `https://packagist.org/packages/foo/admin` premi sul pulsante `Update` per aggiornare la versione.

## Aggiunta di comandi al plugin
A volte i plugin hanno bisogno di comandi personalizzati per fornire alcune funzionalità ausiliarie, ad esempio, dopo aver installato il plugin `webman/redis-queue`, verrà aggiunto automaticamente un comando `redis-queue:consumer` al progetto, in modo che l'utente possa eseguire il comando `php webman redis-queue:consumer send-mail` per generare una classe consumatori SendMail.php nel progetto, facilitando così lo sviluppo rapido.

Supponiamo che il plugin `foo/admin` debba aggiungere il comando `foo-admin:add`, segue i passaggi seguenti.

#### Creazione di un nuovo comando
**Crea il file di comando `FooAdminAddCommand.php` in `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Descrizione del comando qui';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Aggiungi nome');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Aggiungi admin $name");
        return self::SUCCESS;
    }

}
```

> Nota
> Per evitare conflitti tra i comandi dei plugin, si consiglia di utilizzare il formato `produttore-plugin:comando specifico`, ad esempio, tutti i comandi del plugin `foo/admin` dovrebbero avere il prefisso `foo-admin:`, come ad esempio `foo-admin:add`.

#### Aggiunta di configurazioni
**Crea il file di configurazione `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....aggiunta di più configurazioni...
];
```

> Suggerimento
> `command.php` è utilizzato per configurare i comandi personalizzati dei plugin. Ogni elemento dell'array corrisponde a un file di classe comando e ogni file di classe corrisponde a un comando. Quando un utente esegue un comando, `webman/console` caricherà automaticamente tutti i comandi personalizzati configurati nei file `command.php` di ciascun plugin. Per ulteriori informazioni sui comandi, consulta [qui](console.md).

#### Esecuzione dell'esportazione
Esegui il comando `php webman plugin:export --name=foo/admin` per esportare il plugin e caricarlo su `packagist`. In questo modo, quando un utente installa il plugin `foo/admin`, verrà aggiunto il comando `foo-admin:add`. Eseguendo `php webman foo-admin:add jerry` stamperà `Aggiungi admin jerry`.

