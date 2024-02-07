# Fichier de configuration

La configuration des plugins est similaire à celle des projets webman ordinaires, cependant la configuration des plugins affecte généralement uniquement le plugin actuel et n'a généralement aucun impact sur le projet principal.
Par exemple, la valeur de `plugin.foo.app.controller_suffix` n'a d'impact que sur le suffixe du contrôleur du plugin, sans effet sur le projet principal.
Par exemple, la valeur de `plugin.foo.app.controller_reuse` n'a d'impact que sur la réutilisation des contrôleurs du plugin, sans effet sur le projet principal.
Par exemple, la valeur de `plugin.foo.middleware` n'a d'impact que sur les middlewares du plugin, sans effet sur le projet principal.
Par exemple, la valeur de `plugin.foo.view` n'a d'impact que sur les vues utilisées par le plugin, sans effet sur le projet principal.
Par exemple, la valeur de `plugin.foo.container` n'a d'impact que sur le conteneur utilisé par le plugin, sans effet sur le projet principal.
Par exemple, la valeur de `plugin.foo.exception` n'a d'impact que sur la classe de gestion des exceptions du plugin, sans effet sur le projet principal.

Cependant, comme les routes sont globales, la configuration des routes des plugins affecte également l'ensemble du projet.

## Obtenir la configuration
Pour obtenir la configuration d'un plugin particulier, la méthode est `config('plugin.{plugin}.{configuration spécifique}')`. Par exemple, pour obtenir l'ensemble des configurations de `plugin/foo/config/app.php`, la méthode est `config('plugin.foo.app')`.
De même, le projet principal ou d'autres plugins peuvent également utiliser `config('plugin.foo.xxx')` pour obtenir la configuration du plugin foo.

## Configurations non prises en charge
Les applications de plugins ne prennent pas en charge la configuration server.php, session.php, et elles ne prennent pas en charge les configurations `app.request_class`, `app.public_path`, `app.runtime_path`.
