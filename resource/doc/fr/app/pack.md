# Empaquetage

Par exemple, pour empaqueter l'application plugin "foo" : 

* Définir le numéro de version dans `plugin/foo/config/app.php` ( **important** )
* Supprimer les fichiers non nécessaires dans `plugin/foo`, en particulier les fichiers temporaires de test dans `plugin/foo/public`
* Supprimer la configuration de la base de données et de Redis. Si votre projet a sa propre configuration de base de données et de Redis, ces configurations devraient être déclenchées lors de la première visite de l'application (à implémenter manuellement), afin que l'administrateur puisse les remplir manuellement et les générer.
* Restaurer les autres fichiers nécessitant une restauration à leur état initial
* Une fois ces opérations effectuées, accéder au répertoire `{projet principal}/plugin/` et exécuter la commande `zip -r foo.zip foo` pour générer foo.zip
