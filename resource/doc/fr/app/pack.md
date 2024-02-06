# Emballage

Par exemple, pour emballer l'application plugin foo :

* Définir le numéro de version dans `plugin/foo/config/app.php` (**important**)
* Supprimer les fichiers inutiles dans `plugin/foo`, en particulier les fichiers temporaires de test sous `plugin/foo/public`
* Supprimer les configurations de la base de données et de Redis. Si votre projet a ses propres configurations de base de données et de Redis, ces configurations doivent être déclenchées par un programme d'installation au premier accès à l'application (à implémenter vous-même), permettant à l'administrateur de saisir manuellement et de générer les configurations.
* Restaurer les autres fichiers qui doivent être restaurés à leur état d'origine
* Une fois ces opérations terminées, accédez au répertoire `{projet principal}/plugin/` et utilisez la commande `zip -r foo.zip foo` pour générer foo.zip
