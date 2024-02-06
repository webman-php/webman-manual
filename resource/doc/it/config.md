# File di configurazione

## Posizione
Il file di configurazione di webman si trova nella cartella `config/` e nel progetto è possibile ottenere la configurazione corrispondente utilizzando la funzione `config()`.

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

Se la configurazione è un array, è possibile ottenere il valore interno dell'array utilizzando il punto, ad esempio
```php
config('file.key1.key2');
```

## Valore predefinito
```php
config($key, $default);
```
La funzione `config` accetta un valore predefinito come secondo argomento; se la configurazione non esiste, verrà restituito il valore predefinito.
Se la configurazione non esiste e nessun valore predefinito è stato impostato, verrà restituito null.

## Configurazioni personalizzate
Gli sviluppatori possono aggiungere i propri file di configurazione nella cartella `config/`, ad esempio

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
webman non supporta la modifica dinamica della configurazione; tutte le configurazioni devono essere modificate manualmente nei file di configurazione corrispondenti e quindi è necessario eseguire il comando reload o restart per rendere effettive le modifiche.

> **Nota**
> Le configurazioni del server in `config/server.php` e le configurazioni del processo in `config/process.php` non supportano il reload, è necessario eseguire il restart per renderle effettive.
