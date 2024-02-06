# Fichier de configuration

La configuration des plugins est similaire à celle des projets webman ordinaires, mais la configuration des plugins n'a généralement d'effet que sur le plugin en cours, sans incidence sur le projet principal.
Par exemple, la valeur de `plugin.foo.app.controller_suffix` n'a d'impact que sur le suffixe du contrôleur du plugin, sans incidence sur le projet principal.
De même, la valeur de `plugin.foo.app.controller_reuse` n'a d'impact que sur la réutilisation du contrôleur du plugin, sans aucune incidence sur le projet principal.
De même, la valeur de `plugin.foo.middleware` n'a d'impact que sur les middlewares du plugin, sans aucune incidence sur le projet principal.
De même, la valeur de `plugin.foo.view` n'a d'impact que sur les vues utilisées par le plugin, sans aucune incidence sur le projet principal.
De même, la valeur de `plugin.foo.container` n'a d'impact que sur le conteneur utilisé par le plugin, sans aucune incidence sur le projet principal.
De même, la valeur de `plugin.foo.exception` n'a d'impact que sur la classe de gestion des exceptions du plugin, sans aucune incidence sur le projet principal.

Cependant, étant donné que les routes sont globales, la configuration des routes des plugins influence également globalement.

## Obtenir la configuration
Pour obtenir la configuration d'un plugin spécifique, utilisez la méthode suivante : `config('plugin.{plugin}.{configuration spécifique}');`, par exemple pour obtenir l'ensemble de la configuration de `plugin/foo/config/app.php`, utilisez `config('plugin.foo.app')`
De même, le projet principal ou d'autres plugins peuvent utiliser `config('plugin.foo.xxx')` pour obtenir la configuration du plugin foo.

## Configurations non prises en charge
Les applications de plugins ne prennent pas en charge les configurations server.php, session.php, ni les configurations `app.request_class`, `app.public_path`, `app.runtime_path`.
