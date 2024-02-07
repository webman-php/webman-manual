# Componente di attività pianificata crontab

## workerman/crontab

### Descrizione

`workerman/crontab` è simile a crontab di Linux, con la differenza che `workerman/crontab` supporta la pianificazione al secondo.

Spiegazione del tempo:

```plaintext
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ giorno della settimana (0 - 6) (Domenica=0)
|   |   |   |   +------ mese (1 - 12)
|   |   |   +-------- giorno del mese (1 - 31)
|   |   +---------- ora (0 - 23)
|   +------------ minuto (0 - 59)
+-------------- secondi (0-59) [opzionale, se manca il 0, la minima unità di tempo è il minuto]
```

### Indirizzo del progetto

https://github.com/walkor/crontab

### Installazione

```php
composer require workerman/crontab
```

### Utilizzo

**Passo 1: Creare un file di processo `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Esegui ogni secondo
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Esegui ogni 5 secondi
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Esegui ogni minuto
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Esegui ogni 5 minuti
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Esegui al primo secondo di ogni minuto
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Esegui alle 7:50 ogni giorno, si noti che qui è stato omesso il campo dei secondi
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Passo 2: Configurare il file di processo per avviarsi con webman**

Apri il file di configurazione `config/process.php` e aggiungi la seguente configurazione

```php
return [
    ....altri configurazioni, qui omesse....

    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Passo 3: Riavvia webman**

> Nota: le attività pianificate non verranno eseguite immediatamente, ma partiranno al minuto successivo.

### Spiegazione

crontab non è asincrono, ad esempio se un processo di attività imposta due timer A e B, entrambi eseguiti ogni secondo, ma il compito A richiede 10 secondi, allora B dovrà aspettare che A venga completato prima di poter essere eseguito, causando un ritardo nell'esecuzione di B.
Se il business è molto sensibile all'intervallo di tempo, è necessario eseguire le attività cronologicamente sensibili in un processo separato per evitare di essere influenzati da altre attività programmate. Ad esempio, nel file `config/process.php` fare la seguente configurazione

```php
return [
    ....altri configurazioni, qui omesse....

    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```

Mettere le attività pianificate sensibili al tempo in `process/Task1.php` e le altre attività pianificate in `process/Task2.php`

### Ulteriori informazioni
Per ulteriori informazioni sulla configurazione di `config/process.php`, consultare [Processi personalizzati](../process.md)
