# Plugins de base

Les plugins de base sont généralement des composants universels, généralement installés via composer et placés dans le dossier vendor. Lors de l'installation, certains paramètres personnalisés (middlewares, processus, routes, etc.) peuvent être automatiquement copiés dans le répertoire `{projet principal}config/plugin`, et webman reconnaîtra automatiquement cette configuration pour fusionner les paramètres dans la configuration principale, permettant ainsi aux plugins d'intervenir à n'importe quel moment du cycle de vie de webman.

Pour en savoir plus, consultez [Créer des plugins de base](create.md)
