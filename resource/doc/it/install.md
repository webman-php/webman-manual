# Requisiti di ambiente

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. Creare un progetto

```php
composer create-project workerman/webman
```

### 2. Eseguire

Andare alla directory webman   

#### Utenti Windows
Fare doppio clic su `windows.bat` o eseguire `php windows.php` per avviare

> **Nota**
> Se si verificano errori, è probabile che alcune funzioni siano disabilitate. Consultare il [controllo delle funzioni disabilitate](others/disable-function-check.md) per sbloccarle

#### Utenti Linux
Eseguire in modalità `debug` (per lo sviluppo e il debug)

```php
php start.php start
```

Eseguire in modalità `daemon` (per l'ambiente di produzione)

```php
php start.php start -d
```

> **Nota**
> Se si verificano errori, è probabile che alcune funzioni siano disabilitate. Consultare il [controllo delle funzioni disabilitate](others/disable-function-check.md) per sbloccarle

### 3. Accesso

Accedere al browser a `http://indirizzo-ip:8787`
