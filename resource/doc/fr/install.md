# Exigences de l'environnement

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. Créer un projet

```php
composer create-project workerman/webman
```

### 2. Exécution

Accédez au répertoire webman

#### Utilisateurs Windows
Double-cliquez sur `windows.bat` ou exécutez `php windows.php` pour démarrer

> **Remarque**
> En cas d'erreur, il est probable qu'une fonction soit désactivée. Consultez [Vérification des fonctions désactivées](others/disable-function-check.md) pour lever les restrictions

#### Utilisateurs Linux
Exécutez en mode `debug` (pour le développement et le débogage)

```php
php start.php start
```

Exécutez en mode `daemon` (pour l'environnement de production)

```php
php start.php start -d
```

> **Remarque**
> En cas d'erreur, il est probable qu'une fonction soit désactivée. Consultez [Vérification des fonctions désactivées](others/disable-function-check.md) pour lever les restrictions

### 3. Accès

Accédez depuis un navigateur à `http://adresseIP:8787`
