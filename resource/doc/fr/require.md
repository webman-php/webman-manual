# Configuration requise


## Système Linux
Le système Linux dépend des extensions `posix` et `pcntl`, qui sont des extensions intégrées à PHP et normalement inutiles à installer.

Si vous utilisez un panneau de contrôle tel que Baota, il suffit de désactiver ou supprimer les fonctions commençant par `pnctl_`.

L'extension `event` n'est pas obligatoire, mais son installation est recommandée pour des performances optimales.

## Système Windows
Webman peut être exécuté sur un système Windows, mais en raison de l'incapacité de configurer des processus multiples et des processus daemons, il est recommandé de ne l'utiliser que comme environnement de développement. Il est préférable d'utiliser un système Linux pour la production.

Remarque : Le système Windows n'a pas besoin des extensions `posix` et `pcntl`.
