# Exigences de l'environnement

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0

### 1. Créer un projet

```php
composer create-project workerman/webman
```

### 2. Exécuter

Aller dans le répertoire webman

#### Utilisateurs Windows
Double-cliquez sur `windows.bat` ou exécutez `php windows.php` pour démarrer

> **Remarque**
> En cas d'erreur, il est probable qu'une fonction soit désactivée, veuillez consulter [Vérification de la désactivation des fonctions](others/disable-function-check.md) pour lever la désactivation.

#### Utilisateurs Linux
Exécution en mode `debug` (pour le développement et le débogage)

```php
php start.php start
```

Exécution en mode `daemon` (pour l'environnement de production)

```php
php start.php start -d
```

> **Remarque**
> En cas d'erreur, il est probable qu'une fonction soit désactivée, veuillez consulter [Vérification de la désactivation des fonctions](others/disable-function-check.md) pour lever la désactivation.

### 3. Accès

Accéder via le navigateur à `http://adresse_IP:8787`
