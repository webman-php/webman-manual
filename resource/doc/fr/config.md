# Fichier de configuration

## Emplacement
Le fichier de configuration de webman est situé dans le répertoire `config/` et peut être obtenu dans le projet en utilisant la fonction `config()`.

## Obtenir la configuration

Obtenir toutes les configurations
```php
config();
```

Obtenir toutes les configurations dans `config/app.php`
```php
config('app');
```

Obtenir la configuration `debug` dans `config/app.php`
```php
config('app.debug');
```

Si la configuration est un tableau, vous pouvez obtenir la valeur des éléments internes du tableau en utilisant le point (`.`), par exemple
```php
config('file.key1.key2');
```

## Valeur par défaut
```php
config($key, $default);
```
La fonction `config` utilise le deuxième paramètre pour transmettre une valeur par défaut. Si la configuration n'existe pas, la valeur par défaut est renvoyée. Si aucune valeur par défaut n'est définie et que la configuration n'existe pas, elle renvoie `null`.

## Configuration personnalisée
Les développeurs peuvent ajouter leurs propres fichiers de configuration dans le répertoire `config/`, par exemple

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Utilisation lors de l'obtention de la configuration**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Modification de la configuration
webman ne prend pas en charge la modification dynamique de la configuration. Toute modification de la configuration doit être effectuée manuellement dans le fichier de configuration correspondant, suivi d'un rechargement (reload) ou d'un redémarrage (restart).

> **Remarque**
> La configuration du serveur `config/server.php` et la configuration des processus `config/process.php` ne prennent pas en charge le rechargement (reload) et nécessitent un redémarrage (restart) pour prendre effet.
