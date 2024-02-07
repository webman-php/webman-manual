# Qu'est-ce que webman

webman est un framework de service HTTP à haute performance basé sur [workerman](https://www.workerman.net). Il est utilisé pour remplacer l'architecture traditionnelle php-fpm et fournit des services HTTP extrêmement performants et évolutifs. Avec webman, vous pouvez développer des sites web, des interfaces HTTP ou des microservices.

En plus de cela, webman prend en charge les processus personnalisés, qui peuvent effectuer toutes les tâches possibles avec workerman, telles que les services de websocket, l'Internet des objets, les jeux, les services TCP, les services UDP, les services de socket Unix, etc.

# La philosophie de webman
**Fournir la plus grande extensibilité et la plus grande performance avec le noyau minimal.**

webman ne fournit que les fonctionnalités essentielles (route, middleware, session, interfaces de processus personnalisées). Toutes les autres fonctionnalités sont entièrement réutilisables via l'écosystème de composer. Cela signifie que vous pouvez utiliser les composants les plus familiers dans webman, par exemple dans le domaine de la base de données, les développeurs peuvent choisir d'utiliser `illuminate/database` de Laravel, ou `ThinkORM` de ThinkPHP, ou d'autres composants comme `Medoo`. Les intégrer dans webman est très simple.

# Caractéristiques de webman

1. Haute stabilité. Basée sur workerman, qui a toujours été un framework socket extrêmement stable avec très peu de bugs dans l'industrie.
2. Performances ultra-élevées. Les performances de webman sont de 10 à 100 fois supérieures à celles des frameworks traditionnels php-fpm, et environ deux fois supérieures à celles de frameworks similaires tels que gin et echo de go.
3. Haute réutilisabilité. Aucune modification nécessaire, la plupart des composants et bibliothèques de composer peuvent être réutilisés.
4. Grande extensibilité. Prend en charge les processus personnalisés, qui peuvent effectuer toutes les tâches possibles avec workerman.
5. Très simple et facile à utiliser, avec un coût d'apprentissage très faible et un code écrit sans différence par rapport aux frameworks traditionnels.
6. Utilisation de la licence MIT, la plus indulgente et conviviale.

# Adresse du projet
GitHub: https://github.com/walkor/webman **N'oubliez pas de donner une petite étoile** 

Gitee: https://gitee.com/walkor/webman **N'oubliez pas de donner une petite étoile** 

# Données de test de tierce partie

![](../assets/img/benchmark1.png)

Avec des requêtes de base de données, le débit en ligne unique de webman atteint 390 000 QPS, soit près de 80 fois plus élevé que le framework Laravel de l'architecture php-fpm traditionnelle.

![](../assets/img/benchmarks-go.png)

Avec des requêtes de base de données, les performances de webman sont environ deux fois supérieures à celles des frameworks web similaires en go.

Ces données proviennent de [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
