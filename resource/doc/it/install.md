# Requisiti di sistema

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. Creare un progetto

```php
composer create-project workerman/webman
```

### 2. Esecuzione

Entra nella directory di webman   

#### Utenti Windows
Doppio clic su `windows.bat` oppure eseguire `php windows.php` per avviare

> **Nota**
> Se si verificano errori, è molto probabile che alcune funzioni siano disabilitate, fare riferimento a [Controllo delle funzioni disabilitate](others/disable-function-check.md) per rimuovere la disabilitazione

#### Utenti Linux
Esegui in modalità `debug` (per lo sviluppo e il debug)
 
```php
php start.php start
```

Esegui in modalità `daemon` (per l'ambiente di produzione)

```php
php start.php start -d
```

> **Nota**
> Se si verificano errori, è molto probabile che alcune funzioni siano disabilitate, fare riferimento a [Controllo delle funzioni disabilitate](others/disable-function-check.md) per rimuovere la disabilitazione

### 3. Accesso

Accedi tramite browser a `http://indirizzoip:8787`
