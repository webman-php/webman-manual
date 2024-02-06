# Les plugins de base

Les plugins de base sont généralement des composants généraux, généralement installés à l'aide de Composer et placés dans le répertoire vendor. Lors de l'installation, il est possible de copier automatiquement certaines configurations personnalisées (middlewares, processus, routes, etc.) dans le répertoire `{projet principal}config/plugin`, et Webman reconnaîtra automatiquement ces configurations et les fusionnera avec la configuration principale, permettant ainsi aux plugins d'intervenir à n'importe quel moment du cycle de vie de Webman.

Pour plus d'informations, consultez [Création de plugins de base](create.md)
