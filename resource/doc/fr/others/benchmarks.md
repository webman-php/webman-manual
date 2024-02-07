# Test de stress

### Quels sont les facteurs qui influent sur les résultats du test de stress?
* Latence réseau de la machine de stress vers le serveur (recommandé pour effectuer le test en réseau local ou sur la machine locale)
* Bande passante de la machine de stress vers le serveur (recommandé pour effectuer le test en réseau local ou sur la machine locale)
* Est-ce que le keep-alive HTTP est activé (recommandé de l'activer)
* Le nombre de connexions simultanées est-il suffisant (pour effectuer des tests en réseau externe, il est recommandé d'augmenter le nombre de connexions simultanées autant que possible)
* Est-ce que le nombre de processus côté serveur est approprié (le nombre de processus pour des tâches simples comme "helloworld" est recommandé d'être égal au nombre de processeurs disponibles, pour des tâches impliquant l'accès à la base de données, le nombre de processus est recommandé d'être au moins quatre fois supérieur au nombre de processeurs)
* Performances propres à l'entreprise (par exemple, est-ce qu'une base de données externe est utilisée)

### Qu'est-ce que le keep-alive HTTP ?
Le mécanisme de keep-alive HTTP est une technique utilisée pour envoyer plusieurs requêtes et réponses HTTP via une seule connexion TCP. Il a un impact significatif sur les résultats des tests de performance, et la désactivation du keep-alive peut entraîner une réduction significative des QPS. Actuellement, les navigateurs sont tous configurés par défaut pour activer le keep-alive, ce qui signifie qu'après une première requête à une URL HTTP, la connexion est maintenue ouverte temporairement, et est réutilisée pour les requêtes suivantes, améliorant ainsi les performances. Il est recommandé d'activer le keep-alive lors des tests de stress.

### Comment activer le keep-alive HTTP lors des tests de stress ?
Si vous utilisez le programme ab pour effectuer les tests de stress, vous devez ajouter le paramètre -k, par exemple `ab -n100000 -c200 -k http://127.0.0.1:8787/`. Pour apipost, vous devez renvoyer en-tête gzip dans la réponse pour activer le keep-alive (problème connu d'apipost, voir ci-dessous). La plupart des autres programmes de test de stress sont généralement configurés pour activer le keep-alive par défaut.

### Pourquoi les QPS sont-ils très bas lors des tests de stress en réseau externe ?
La latence élevée du réseau externe entraîne une faible QPS, ce qui est un phénomène normal. Par exemple, tester la page de baidu en réseau externe peut donner une QPS de seulement quelques dizaines. Il est recommandé d'effectuer les tests de stress en réseau local ou sur la machine locale pour exclure l'impact de la latence du réseau. Si vous devez effectuer des tests en réseau externe, vous devrez augmenter le nombre de connexions concurrentes pour augmenter le débit (en vous assurant que la bande passante est suffisante).

### Pourquoi les performances baissent après le passage par un proxy nginx ?
L'exécution de nginx nécessite des ressources système. De plus, la communication entre nginx et webman entraîne également une consommation de ressources. Cependant, les ressources système sont limitées et webman ne peut pas accéder à toutes les ressources du système, il est donc normal que les performances globales du système puissent être affectées. Pour réduire au maximum l'impact des performances induit par le proxy nginx, vous pouvez envisager de désactiver les journaux nginx (`access_log off;`), activer le keep-alive entre nginx et webman, voir [proxy nginx](nginx-proxy.md). De plus, HTTPS consomme plus de ressources que HTTP car il nécessite une poignée de main SSL/TLS, un chiffrement et un déchiffrement des données, et les paquets sont plus volumineux, ce qui peut entraîner des performances inférieures. Lors des tests de stress, si vous utilisez des connexions non persistantes (sans keep-alive HTTP), chaque requête nécessitera une poignée de main supplémentaire SSL/TLS, ce qui entraînera une diminution significative des performances. Il est recommandé d'activer le keep-alive HTTP lors des tests de stress en HTTPS.

### Comment savoir si le système a atteint sa limite de performances ?
En général, lorsque le processeur atteint 100 %, cela signifie que le système a atteint sa limite de performances. Si le processeur a encore de la capacité disponible, cela signifie que la limite n'a pas été atteinte, et dans ce cas, il est possible d'augmenter le nombre de connexions simultanées pour améliorer les QPS. Si l'augmentation du nombre de connexions simultanées ne permet pas d'améliorer les QPS, il est possible que le nombre de processus webman ne soit pas suffisant, il faut alors envisager d'augmenter le nombre de processus webman. Si les performances ne s'améliorent toujours pas, il convient de vérifier si la bande passante est adéquate.

### Pourquoi les résultats des tests de stress montrent que les performances de webman sont inférieures à celles du framework go gin ?
Les tests menés par [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) montrent que webman surpasse gin d'environ le double dans tous les indicateurs, que ce soit pour le texte brut, les requêtes de base de données, les mises à jour de base de données, etc. Si vos résultats diffèrent, il est possible que l'utilisation d'ORM dans webman entraîne une perte de performances significative. Vous pouvez essayer la comparaison entre webman avec PDO natif et gin avec SQL natif.

### De combien de performances la base de données ORM peut-elle réduire les performances dans webman ?
Voici un ensemble de données de test :
**Environnement** : Serveur Alibaba Cloud 4 cœurs 4 Go, retourne un enregistrement JSON aléatoire à partir de 100 000 enregistrements lors de la requête.

**Utilisation de PDO natif** : QPS de webman 17 800
**Utilisation de Db::table() de Laravel** : QPS de webman réduit à 9 400
**Utilisation de Model de Laravel** : QPS de webman réduit à 7 200

Les résultats de thinkORM sont similaires, avec peu de variations.
> **Remarque** : Bien que l'utilisation d'ORM puisse entraîner une certaine diminution des performances, elle est généralement suffisante pour la plupart des cas d'utilisation. Il est important de trouver un équilibre entre l'efficacité de développement, la maintenabilité, les performances, et d'autres critères, plutôt que de poursuivre aveuglément la performance seule.

### Pourquoi le QPS est-il si faible lors des tests de stress avec apipost ?
Le module de test de stress d'apipost a un bug : si le serveur ne renvoie pas d'en-tête gzip, le keep-alive ne peut pas être maintenu, ce qui entraîne une baisse significative des performances. La solution consiste à compresser les données lors du renvoi et à ajouter l'en-tête gzip, par exemple :
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
En dehors de cela, apipost peut également ne pas être en mesure de générer une pression satisfaisante dans certains cas. Cela se traduit par des QPS environ 50 % inférieurs à ceux d'ab, malgré un nombre équivalent de connexions simultanées. Il est recommandé d'utiliser ab, wrk ou tout autre logiciel de test de stress professionnel plutôt que apipost pour les tests de stress.

### Configuration du nombre de processus appropriée
webman est configuré par défaut pour démarrer 4 fois le nombre de cœurs CPU. En réalité, pour des tâches sans E/S réseau, le nombre de processus optimal pour les tests de stress pour des tâches simples comme "helloworld" est égal au nombre de cœurs CPU pour minimiser les coûts liés aux changements de processus. Si des tâches impliquant des accès à la base de données, une quantité de 3 à 8 fois le nombre de cœurs CPU est recommandée car elles nécessitent plus de processus pour augmenter le débit, et les coûts liés aux changements de processus peuvent être négligés par rapport à une E/S bloquante.


### Plages de référence pour les tests de stress

**Serveur Cloud 4 cœurs 4 Go avec 16 processus, test en local/réseau local**

| - | Keep-alive activé | Keep-alive désactivé |
|--|-----|-----|
| hello world | 80 000 à 160 000 QPS | 10 000 à 30 000 QPS |
| Requête unique à la base de données | 10 000 à 20 000 QPS | 10 000 QPS |

[**Données de test de tierce partie avec techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


### Exemples de commandes de test de stress

**ab**
```
# 100 000 requêtes avec 200 connexions simultanées et keep-alive activé
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100 000 requêtes avec 200 connexions simultanées et keep-alive désactivé
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# Test de stress pendant 10 secondes avec 200 connexions simultanées et keep-alive activé (par défaut)
wrk -c 200 -d 10s http://example.com
```
