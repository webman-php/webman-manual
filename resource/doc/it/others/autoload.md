# Caricamento automatico

## Caricare i file conformi a PSR-0 utilizzando composer
webman segue la specifica di caricamento automatico `PSR-4`. Se hai bisogno di caricare librerie conformi a `PSR-0` per la tua attività, segui questi passaggi.

- Crea la directory `extend` per memorizzare le librerie conformi a `PSR-0`.
- Modifica `composer.json`, aggiungendo il seguente contenuto sotto `autoload`:

```js
"psr-0" : {
    "": "extend/"
}
```

Il risultato finale sarà simile a 
![](../../assets/img/psr0.png)

- Esegui `composer dumpautoload`.
- Esegui `php start.php restart` per riavviare webman (nota, è necessario riavviare affinché le modifiche abbiano effetto).

## Caricare determinati file utilizzando composer

- Modifica `composer.json`, aggiungendo i file da caricare sotto `autoload.files`:
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Esegui `composer dumpautoload`.
- Esegui `php start.php restart` per riavviare webman (nota, è necessario riavviare affinché le modifiche abbiano effetto).

> **Nota**
> I file configurati in `autoload.files` di `composer.json` vengono caricati prima dell'avvio di webman. I file caricati utilizzando il file `config/autoload.php` del framework vengono invece caricati dopo l'avvio di webman. 
> Le modifiche ai file caricati in `autoload.files` di `composer.json` richiedono un riavvio per avere effetto, mentre per i file caricati tramite `config/autoload.php` del framework, è sufficiente ricaricare per renderle effettive. 

## Caricare determinati file utilizzando il framework
Alcuni file potrebbero non essere conformi alle specifiche SPR e non possono essere caricati automaticamente. In questi casi, possiamo caricare questi file attraverso la configurazione di `config/autoload.php`. Ad esempio:
```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
> **Nota**
> Notiamo che in `autoload.php` sono impostati il caricamento dei file `support/Request.php` e `support/Response.php`. Questo perché nella directory  `vendor/workerman/webman-framework/src/support/` ci sono anche due file con lo stesso nome. Utilizzando `autoload.php`, carichiamo prioritariamente `support/Request.php` e `support/Response.php` dalla radice del progetto, consentendoci così di personalizzarne il contenuto senza dover modificare i file nella directory `vendor`. Se non è necessario personalizzarli, è possibile ignorare questa configurazione.
