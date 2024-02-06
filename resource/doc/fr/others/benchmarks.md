# Test de stress

### Quels facteurs influencent les résultats des tests de stress ?
* La latence du réseau entre la machine de stress et le serveur (recommandé pour effectuer des tests en local ou sur le réseau interne)
* La bande passante entre la machine de stress et le serveur (recommandé pour effectuer des tests en local ou sur le réseau interne)
* La présence du HTTP keep-alive (recommandé de l'activer)
* Le nombre de connexions simultanées est-il suffisant (pour les tests sur le réseau externe, il est recommandé d'augmenter autant que possible le nombre de connexions simultanées)
* Le nombre de processus du serveur est-il approprié (le nombre de processus recommandé pour une activité basique comme "helloworld" devrait correspondre au nombre de processeurs disponibles, tandis que pour une activité de base de données, il est recommandé d'avoir quatre fois plus de processus que de processeurs)
* Les performances de l'application (par exemple, l'utilisation d'une base de données externe)

### Qu'est-ce que le HTTP keep-alive ?
Le mécanisme de HTTP Keep-Alive est une technique permettant d'envoyer et de recevoir plusieurs requêtes HTTP et réponses sur une seule connexion TCP. Il a un impact significatif sur les résultats des tests de performances, et désactiver le keep-alive peut diviser par deux les QPS (requêtes par seconde).
Actuellement, les navigateurs activent par défaut le keep-alive, ce qui signifie que lorsqu'un navigateur accède à une adresse http, la connexion est maintenue ouverte temporairement et réutilisée pour la prochaine requête, ce qui améliore les performances.
Il est recommandé d'activer le keep-alive lors des tests de stress.

### Comment activer le HTTP keep-alive pendant les tests de stress ?
Si vous utilisez le programme "ab" pour le test de stress, vous devez ajouter le paramètre -k, par exemple `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Apipost nécessite de retourner l'en-tête gzip pour activer le keep-alive (un bogue d'apipost, voir ci-dessous).
La plupart des autres programmes de test de stress activent généralement cette fonctionnalité par défaut.

### Pourquoi les QPS sont-ils très faibles lors des tests de stress sur le réseau externe ?
Les fortes latences du réseau externe entraînent une diminution des QPS, ce qui est un phénomène normal. Par exemple, il est possible que les QPS pour tester une page de Baidu ne soient que de quelques dizaines.
Il est recommandé de réaliser des tests en local ou sur le réseau interne pour éliminer l'impact de la latence du réseau.
Si des tests doivent absolument être réalisés sur le réseau externe, il est possible d'augmenter le nombre de connexions simultanées pour augmenter le débit (en vérifiant que la bande passante est adéquate).

### Pourquoi les performances diminuent-elles après la mise en place d'un proxy NGINX ?
L'exécution de NGINX consomme des ressources système. De plus, la communication entre NGINX et webman nécessite également des ressources. 
Cependant, les ressources système étant limitées, et webman ne pouvant pas accéder à toutes les ressources système, il est normal que les performances globales du système en pâtissent.
Pour réduire autant que possible l'impact des performances causé par le proxy NGINX, il est possible de envisager de désactiver les journaux de NGINX (`access_log off;`), et d'activer le keep-alive entre NGINX et webman, voir [proxy NGINX](nginx-proxy.md).

De plus, HTTPS consomme plus de ressources que HTTP, car il nécessite une poignée de main SSL/TLS, le chiffrement et le déchiffrement des données, et génère des paquets plus volumineux qui utilisent plus de bande passante, ce qui peut entraîner une diminution des performances.
Si des tests de stress sont effectués en utilisant des connexions courtes (sans activer le HTTP keep-alive), chaque requête nécessitera une nouvelle communication de poignée de main SSL/TLS, ce qui entraînera une diminution significative des performances. Il est recommandé d'activer le HTTP keep-alive lors des tests de stress en HTTPS.

### Comment savoir si le système a atteint ses limites en termes de performances ?
En général, lorsque le processeur atteint 100%, cela signifie que le système a atteint ses limites en termes de performances. Si le processeur n'est pas entièrement utilisé, cela signifie qu'il n'a pas encore atteint ses limites, et il est alors possible d'augmenter le nombre de connexions simultanées pour augmenter les QPS.
Si l'augmentation du nombre de connexions simultanées ne permet pas d'augmenter les QPS, il est possible que le nombre de processus webman soit insuffisant. Dans ce cas, il est recommandé d'augmenter le nombre de processus webman. Si cela ne fonctionne toujours pas, il est possible que la bande passante ne soit pas suffisante.

### Pourquoi les résultats de mes tests de stress montrent que les performances de webman sont inférieures à celles du framework gin en go ?
Les tests de [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) montrent que webman surpasse gin d'environ deux fois dans tous les indicateurs tels que le texte brut, les requêtes de base de données, les mises à jour de base de données, etc.
Si vos résultats sont différents, il est possible que vous utilisiez une ORM dans webman, causant ainsi une perte de performance significative. Vous pouvez essayer webman + PDO natif par rapport à gin + SQL natif pour comparer.

### Combien de performances perd-on en utilisant une ORM dans webman ?
Voici un ensemble de données de test

**Environnement**
Serveur cloud 4 cœurs 4 Go, une réponse JSON obtenue en tirant au sort une entrée parmi 100 000 enregistrements.

**En utilisant PDO natif**
QPS pour webman : 17 800

**En utilisant Db :: table () de Laravel**
QPS pour webman : 9 400

**En utilisant Model de Laravel**
QPS pour webman : 7 200

Les résultats sont similaires pour thinkORM, avec peu de différence.

> **Remarque**
> Bien que l'utilisation d'une ORM puisse entraîner une baisse de performances, elle est généralement suffisante pour la plupart des activités. Il est important de trouver un équilibre entre l'efficacité de développement, la maintenabilité et les performances, plutôt que de privilégier uniquement les performances.

### Pourquoi les tests de stress avec apipost affichent-ils des QPS très bas ?
Le module de test de stress d'apipost a un bug ; si le serveur ne renvoie pas l'en-tête gzip, le keep-alive ne peut pas être maintenu, ce qui entraîne une forte diminution des performances.
Pour résoudre ce problème, il faut comprimer les données en les renvoyant et ajouter l'en-tête gzip comme ceci :
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
De plus, dans certains cas, apipost ne peut pas fournir des performances satisfaisantes, ce qui se traduit par un QPS inférieur d'environ 50 % par rapport à ab.
Il est recommandé d'utiliser ab, wrk ou d'autres logiciels de test de stress professionnels au lieu d'apipost pour les tests de stress.

### Configuration du nombre de processus approprié
webman ouvre par défaut 4 fois le nombre de processeurs en termes de processus. En fait, pour un helloworld sans E/S réseau, le nombre de processus optimisé pour les tests de stress devrait correspondre au nombre de cœurs du CPU, car cela permet de réduire le surcoût lié au changement de processus.
Si l'application implique une E/S bloquante telle que l'accès à une base de données ou à Redis, le nombre de processus peut être défini entre 3 fois et 8 fois le nombre de cœurs du CPU, car cela nécessite plus de processus pour augmenter les connexions simultanées, et le surcoût lié au changement de processus peut être négligeable par rapport à l'E/S bloquante.

### Quelques gammes de référence pour les tests de stress

**Serveur cloud 4 cœurs 4 Go 16 processus, tests en local/réseau interne**

| - | Avec keep-alive | Sans keep-alive |
|--|-----|-----|
| helloworld | 80 000 - 160 000 QPS |  10 000 - 30 000 QPS |
| Requête unique à la base de données | 10 000 - 20 000 QPS | 10 000 QPS |

[**Données de test de stress tierces provenant de techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Exemples de commandes de test de stress

**ab**
```
# 100 000 requêtes 200 connexions simultanées, avec keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100 000 requêtes 200 connexions simultanées, sans keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# Test de stress pendant 10 secondes avec 200 connexions simultanées, avec keep-alive (par défaut)
wrk -c 200 -d 10s http://example.com
```
