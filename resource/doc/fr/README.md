# Qu'est-ce que webman

webman est un framework de service HTTP à haute performance basé sur [workerman](https://www.workerman.net). Il est conçu pour remplacer l'architecture traditionnelle php-fpm et offrir un service HTTP extrêmement performant et évolutif. Vous pouvez l'utiliser pour développer des sites web, des API HTTP ou des microservices.

En plus de cela, webman prend en charge les processus personnalisés, offrant la possibilité de faire tout ce que workerman peut faire, comme des services websocket, des applications IoT, des jeux, des services TCP, des services UDP, des services de socket unix, et bien d'autres.

# Philosophie de webman
**Fournir une extensibilité maximale et des performances optimales avec un noyau minimal.**

webman fournit uniquement les fonctionnalités essentielles (routing, middleware, session, interface de processus personnalisés). Toutes les autres fonctionnalités sont basées sur l'écosystème de composer. Cela signifie que vous pouvez utiliser les composants les plus familiers dans webman, tels que la gestion de base de données avec Laravel's `illuminate/database`, ThinkPHP's `ThinkORM`, ou d'autres composants tels que `Medoo`. Les intégrer dans webman est très facile.

# Caractéristiques de webman

1. Haute stabilité : Basé sur workerman, qui est un framework de socket extrêmement stable avec très peu de bugs.
2. Performance ultra-élevée : La performance de webman est 10 à 100 fois supérieure à celle des frameworks php-fpm traditionnels, et environ deux fois supérieure à des frameworks comme gin ou echo en Go.
3. Grande réutilisation : Aucune modification nécessaire, la plupart des composants et bibliothèques de composer peuvent être réutilisés.
4. Grande extensibilité : Supporte les processus personnalisés, capable de réaliser tout ce que workerman peut faire.
5. Très simple et facile à utiliser, avec un faible coût d'apprentissage et un code similaire aux frameworks traditionnels.
6. Utilise la licence MIT, qui est très permissive et conviviale.

# Adresse du projet
GitHub : https://github.com/walkor/webman **N'oubliez pas d'apporter votre petite étoile**

Gitee : https://gitee.com/walkor/webman **N'oubliez pas d'apporter votre petite étoile**

# Données d'évaluation tierces officielles

![](../assets/img/benchmark1.png)

Avec des requêtes de base de données, webman peut gérer jusqu'à 390 000 QPS sur une seule machine, ce qui est près de 80 fois plus élevé que le framework Laravel basé sur l'architecture php-fpm traditionnelle.

![](../assets/img/benchmarks-go.png)

Avec des requêtes de base de données, webman a une performance environ deux fois supérieure à un framework web similaire en Go.

Ces données sont issues de [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
