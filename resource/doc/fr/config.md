# Fichier de configuration

## Emplacement
Le fichier de configuration de webman se trouve dans le répertoire `config/` et peut être obtenu dans le projet en utilisant la fonction `config()`.

## Obtenir la configuration

Obtenir toutes les configurations
```php
config();
```

Obtenir toutes les configurations de `config/app.php`
```php
config('app');
```

Obtenir la configuration `debug` de `config/app.php`
```php
config('app.debug');
```

Si la configuration est un tableau, vous pouvez obtenir la valeur des éléments internes du tableau en utilisant `.`, par exemple
```php
config('file.key1.key2');
```

## Valeur par défaut
```php
config($key, $default);
```
La fonction `config` permet de transmettre une valeur par défaut en tant que deuxième paramètre. Si la configuration n'existe pas, elle renverra la valeur par défaut. Si aucune valeur par défaut n'est définie et que la configuration n'existe pas, la fonction renverra `null`.

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
webman ne prend pas en charge la modification dynamique de la configuration, toutes les configurations doivent être modifiées manuellement dans les fichiers de configuration correspondants, puis rechargées ou redémarrées.

> **Remarque**
> Les configurations du serveur `config/server.php` et du processus `config/process.php` ne prennent pas en charge le rechargement ; un redémarrage est nécessaire pour qu'elles prennent effet.
