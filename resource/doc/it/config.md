# File di configurazione

## Posizione
Il file di configurazione di webman si trova nella directory `config/` e nel progetto è possibile ottenere la configurazione corrispondente tramite la funzione `config()`.

## Ottenere la configurazione

Ottenere tutte le configurazioni
```php
config();
```

Ottenere tutte le configurazioni in `config/app.php`
```php
config('app');
```

Ottenere la configurazione `debug` in `config/app.php`
```php
config('app.debug');
```

Se la configurazione è un array, è possibile accedere ai valori interni dell'array utilizzando il punto, ad esempio
```php
config('file.key1.key2');
```

## Valore predefinito
```php
config($key, $default);
```
La funzione `config` accetta un secondo parametro per passare un valore predefinito. Se la configurazione non esiste, verrà restituito il valore predefinito. Se la configurazione non esiste e non è stato impostato un valore predefinito, verrà restituito null.

## Configurazione personalizzata
Gli sviluppatori possono aggiungere i propri file di configurazione nella directory `config/`, ad esempio

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Utilizzo della configurazione**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Modifica della configurazione
webman non supporta la modifica dinamica della configurazione, tutte le configurazioni devono essere modificate manualmente nei file di configurazione corrispondenti e quindi eseguire un reload o un restart.

> **Nota**
> Le configurazioni del server in `config/server.php` e le configurazioni dei processi in `config/process.php` non supportano il reload e richiedono un restart per entrare in vigore.
