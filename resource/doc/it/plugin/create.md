# Procedura di generazione e distribuzione di base dei plugin

## Principio
1. Prendiamo come esempio il plugin di cross-origin, il plugin è composto da tre parti: un file di programma del middleware di cross-origin, un file di configurazione del middleware middleware.php e un Install.php generato automaticamente tramite comando.
2. Utilizziamo un comando per comprimere i tre file e distribuirli tramite composer.
3. Quando un utente installa il plugin di cross-origin utilizzando composer, l'Install.php nel plugin copierà il file di programma del middleware di cross-origin e il file di configurazione in `{progetto principale}/config/plugin`, consentendo a webman di caricarli e attivarli in modo automatico.
4. Quando un utente rimuove il plugin utilizzando composer, l'Install.php eliminerà il file di programma del middleware di cross-origin e il file di configurazione corrispondente, permettendo così la disinstallazione automatica del plugin.

## Normativa
1. Il nome del plugin è composto da due parti, il `produttore` e il `nome del plugin`, ad esempio `webman/push`, che corrisponde al nome del pacchetto composer.
2. Il file di configurazione del plugin è collocato uniformemente in `config/plugin/produttore/nome del plugin/` (il comando della console creerà automaticamente la directory di configurazione). Se il plugin non richiede configurazioni, è necessario eliminare la directory di configurazione creata automaticamente.
3. La directory di configurazione del plugin supporta solo i seguenti file: app.php (configurazione principale del plugin), bootstrap.php (configurazione di avvio del processo), route.php (configurazione del percorso), middleware.php (configurazione middleware), process.php (configurazione del processo personalizzato), database.php (configurazione del database), redis.php (configurazione di redis), thinkorm.php (configurazione di thinkorm). Queste configurazioni verranno riconosciute automaticamente da webman.
4. Il plugin utilizza il seguente metodo per ottenere la configurazione: `config('plugin.produttore.nome del plugin.file di configurazione.configurazione specifica');`, ad esempio `config('plugin.webman.push.app.app_key')`.
5. Se il plugin ha una propria configurazione del database, questa può essere accessa nel seguente modo: `illuminate/database` diventa `Db::connection('plugin.produttore.nome del plugin.connessione specifica')`, `thinkrom` diventa `Db::connct('plugin.produttore.nome del plugin.connessione specifica')`.
6. Se il plugin ha bisogno di inserire file di business nella directory `app/`, è necessario assicurarsi che non entrino in conflitto con i progetti degli utenti e altri plugin.
7. I plugin dovrebbero evitare il più possibile di copiare file o directory nel progetto principale. Ad esempio, nel caso del plugin di cross-origin, oltre al file di configurazione che deve essere copiato nel progetto principale, il file del middleware dovrebbe essere posto in `vendor/webman/cros/src` e non copiato nel progetto principale.
8. Si consiglia di utilizzare lettere maiuscole per lo spazio dei nomi del plugin, ad esempio Webman/Console.

## Esempio

**Installazione del comando `webman/console`**

`composer require webman/console`

#### Creazione del plugin

Supponiamo che il plugin creato si chiami `foo/admin` (il nome è anche il nome del progetto da pubblicare con composer, il nome deve essere in minuscolo).
Esegui il comando
`php webman plugin:create --name=foo/admin`

Dopo la creazione del plugin verrà creata la directory `vendor/foo/admin` per memorizzare i file correlati al plugin e `config/plugin/foo/admin` per memorizzare le relative configurazioni del plugin.

>Nota
> `config/plugin/foo/admin` supporta le seguenti configurazioni: app.php (configurazione principale del plugin), bootstrap.php (configurazione di avvio del processo), route.php (configurazione del percorso), middleware.php (configurazione middleware), process.php (configurazione del processo personalizzato), database.php (configurazione del database), redis.php (configurazione di redis), thinkorm.php (configurazione di thinkorm). Il formato della configurazione è lo stesso di webman, queste configurazioni saranno automaticamente riconosciute e unite nella configurazione.
L'accesso avviene tramite prefisso `plugin`, ad esempio config('plugin.foo.admin.app');

#### Esportazione del plugin

Una volta completato lo sviluppo del plugin, eseguire il comando seguente per esportare il plugin
`php webman plugin:export --name=foo/admin`

> Spiegazione
> Dopo l'esportazione, la directory config/plugin/foo/admin verrà copiata in vendor/foo/admin/src e verrà generato automaticamente un file Install.php. Install.php viene utilizzato per eseguire operazioni automatiche durante l'installazione e la disinstallazione del plugin.
> L'operazione predefinita durante l'installazione è copiare le configurazioni dalla directory vendor/foo/admin/src nell'attuale directory config/plugin del progetto.
> L'operazione predefinita durante la rimozione è eliminare i file di configurazione dall'attuale directory config/plugin del progetto.
> È possibile modificare Install.php per eseguire operazioni personalizzate durante l'installazione e la disinstallazione del plugin.

#### Pubblicazione del plugin
* Supponiamo che tu abbia già un account [github](https://github.com) e [packagist](https://packagist.org)
* Crea un nuovo progetto admin su [github](https://github.com) e carica il codice, l'indirizzo del progetto dovrebbe essere `https://github.com/tuonome/admin`
* Vai all'indirizzo `https://github.com/tuonome/admin/releases/new` per pubblicare una release, ad esempio `v1.0.0`
* Vai su [packagist](https://packagist.org), clicca su `Submit` nella barra di navigazione e invia l'indirizzo del tuo progetto github `https://github.com/tuonome/admin`, così avrai completato la pubblicazione di un plugin.

> **Consiglio**
> Se la pubblicazione del plugin su `packagist` mostra un conflitto, è possibile scegliere un nuovo nome per il produttore, ad esempio `foo/admin` può diventare `miofoo/admin`.

In seguito, quando il codice del plugin subisce aggiornamenti, è necessario sincronizzare il codice su github, quindi tornare all'indirizzo `https://github.com/tuonome/admin/releases/new` per pubblicare nuovamente un release, quindi andare alla pagina `https://packagist.org/packages/foo/admin` e fare clic su `Update` per aggiornare la versione.
## Aggiungere comandi ai plugin
A volte i nostri plugin richiedono alcuni comandi personalizzati per fornire alcune funzionalità ausiliarie. Ad esempio, dopo aver installato il plugin `webman/redis-queue`, il progetto aggiungerà automaticamente un comando `redis-queue:consumer`. Gli utenti possono eseguire `php webman redis-queue:consumer send-mail` per generare una classe consumatrice SendMail.php nel progetto, il che è utile per lo sviluppo rapido.

Supponiamo che il plugin `foo/admin` debba aggiungere il comando `foo-admin:add`, seguire i passaggi seguenti.

#### Creare un nuovo comando
**Creare il file del comando `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Questa è la descrizione del comando';

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
        $output->writeln("Aggiunto admin $name");
        return self::SUCCESS;
    }
}
```

> **Nota**
> Per evitare conflitti tra comandi dei plugin, il formato della riga di comando è consigliato come `produttore-nomeplugin:comando`, ad esempio, tutti i comandi del plugin `foo/admin` dovrebbero avere il prefisso `foo-admin:`, come ad esempio `foo-admin:add`.

#### Aggiungere configurazioni
**Creare la configurazione `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....è possibile aggiungere più configurazioni...
];
```

> **Suggerimento**
> `command.php` viene utilizzato per configurare i comandi personalizzati dei plugin. Ogni elemento dell'array corrisponde a un file di classe dei comandi da riga di comando, e ogni file di classe corrisponde a un comando. Quando l'utente esegue un comando da riga di comando, `webman/console` caricherà automaticamente i comandi personalizzati configurati in `command.php` di ciascun plugin. Per ulteriori informazioni sui comandi da riga di comando, fare riferimento a [Riga di comando](console.md).

#### Eseguire l'esportazione
Eseguire il comando `php webman plugin:export --name=foo/admin` per esportare il plugin e caricarlo su `packagist`. In questo modo, una volta installato il plugin `foo/admin`, verrà aggiunto il comando `foo-admin:add`. Eseguendo `php webman foo-admin:add jerry` verrà visualizzato `Aggiunto admin jerry`.

