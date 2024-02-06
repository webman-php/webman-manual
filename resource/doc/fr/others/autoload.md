# Chargement automatique

## Chargement des fichiers respectant la norme PSR-0 à l'aide de Composer
Webman suit la norme de chargement automatique `PSR-4`. Si votre application nécessite le chargement de bibliothèques respectant la norme `PSR-0`, veuillez suivre les étapes suivantes.

- Créez un répertoire `extend` pour stocker les bibliothèques respectant la norme `PSR-0`.
- Modifiez le fichier `composer.json` en ajoutant le contenu suivant sous `autoload` :

```js
"psr-0" : {
    "": "extend/"
}
```
Le résultat final ressemblera à ceci :
![](../../assets/img/psr0.png)

- Exécutez `composer dumpautoload`.
- Exécutez `php start.php restart` pour redémarrer webman (remarque : le redémarrage est nécessaire pour que les modifications prennent effet).

## Chargement de certains fichiers à l'aide de Composer

- Modifiez le fichier `composer.json` en ajoutant les fichiers à charger sous `autoload.files` :

```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- Exécutez `composer dumpautoload`.
- Exécutez `php start.php restart` pour redémarrer webman (remarque : le redémarrage est nécessaire pour que les modifications prennent effet).

> **Remarque**
> Les fichiers spécifiés dans la configuration `autoload.files` de `composer.json` sont chargés avant le démarrage de webman. Les fichiers chargés par le biais du fichier `config/autoload.php` du framework le sont après le démarrage de webman.
> Les modifications apportées aux fichiers chargés par `autoload.files` de `composer.json` nécessitent un redémarrage pour prendre effet, le rechargement ne suffit pas. En revanche, les fichiers chargés par le biais du fichier `config/autoload.php` du framework supportent le rechargement à chaud, ce qui permet aux modifications d'être prises en compte par un simple rechargement.

## Chargement de certains fichiers par le biais du framework
Certains fichiers peuvent ne pas être conformes à la norme SPR et ne peuvent donc pas être chargés automatiquement. Dans ce cas, nous pouvons les charger en configurant le fichier `config/autoload.php`, par exemple :

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
 > Nous remarquons que le fichier `autoload.php` est configuré pour charger les fichiers `support/Request.php` et `support/Response.php`, car il existe également des fichiers identiques dans `vendor/workerman/webman-framework/src/support/`. En configurant le fichier `autoload.php` de cette manière, nous priorisons le chargement des fichiers `support/Request.php` et `support/Response.php` situés à la racine du projet, ce qui nous permet de personnaliser le contenu de ces fichiers sans avoir à modifier les fichiers dans le répertoire `vendor`. Si vous n'avez pas besoin de les personnaliser, vous pouvez ignorer cette configuration.
