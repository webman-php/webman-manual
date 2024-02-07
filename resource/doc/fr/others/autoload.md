# Chargement automatique

## Chargement des fichiers respectant la spécification PSR-0 à l'aide de Composer
Webman suit la spécification de chargement automatique `PSR-4`. Si votre application nécessite le chargement de bibliothèques de code respectant la spécification `PSR-0`, veuillez suivre les étapes ci-dessous.

- Créez un répertoire `extend` pour stocker les bibliothèques de code respectant la spécification `PSR-0`.
- Modifiez le fichier `composer.json` et ajoutez le contenu suivant sous `autoload` :

```js
"psr-0" : {
    "": "extend/"
}
```
Le résultat final ressemblera à ceci :
![](../../assets/img/psr0.png)

- Exécutez la commande `composer dumpautoload`.
- Exécutez la commande `php start.php restart` pour redémarrer Webman (veuillez noter qu'un redémarrage est nécessaire pour que les changements prennent effet).

## Chargement de certains fichiers à l'aide de Composer

- Modifiez le fichier `composer.json` et ajoutez les fichiers à charger sous `autoload.files` :
```js
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Exécutez la commande `composer dumpautoload`.
- Exécutez la commande `php start.php restart` pour redémarrer Webman (veuillez noter qu'un redémarrage est nécessaire pour que les changements prennent effet).

> **Remarque**
> Les fichiers configurés dans `autoload.files` du fichier composer.json seront chargés avant le démarrage de Webman. Cependant, les fichiers chargés via le fichier `config/autoload.php` du framework seront chargés après le démarrage de Webman.
> Les modifications apportées aux fichiers chargés via `autoload.files` du fichier composer.json nécessiteront un redémarrage pour prendre effet, contrairement aux fichiers chargés via le fichier `config/autoload.php` qui supportent le rechargement direct.

## Chargement de certains fichiers via le framework
Certains fichiers peuvent ne pas être conformes à la spécification PSR et ne peuvent pas être chargés automatiquement. Dans ce cas, nous pouvons charger ces fichiers via la configuration `config/autoload.php`, comme suit :

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```
> **Remarque**
> Nous pouvons voir que `autoload.php` est configuré pour charger les fichiers `support/Request.php` et `support/Response.php` car ces mêmes fichiers existent également dans `vendor/workerman/webman-framework/src/support/`. En configurant `autoload.php`, nous priorisons le chargement des fichiers `support/Request.php` et `support/Response.php` se trouvant dans le répertoire principal du projet, ce qui nous permet de personnaliser le contenu de ces deux fichiers sans avoir à modifier les fichiers du dossier `vendor`. Si vous n'avez pas besoin de les personnaliser, vous pouvez ignorer ces deux configurations.
