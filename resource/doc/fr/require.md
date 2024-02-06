# Conditions nécessaires

## Système Linux
Le système Linux nécessite les extensions `posix` et `pcntl`, qui sont des extensions intégrées à PHP et normalement elles ne nécessitent pas d'installation séparée.

Si vous êtes un utilisateur de Baota, il vous suffit de désactiver ou de supprimer les fonctions commençant par `pnctl_` dans Baota.

L'extension `event` n'est pas obligatoire, mais pour de meilleures performances, il est recommandé de l'installer.

## Système Windows
Webman peut fonctionner sur le système Windows, mais en raison de l'impossibilité de configurer des processus multiples, des processus en arrière-plan, etc., il est recommandé d'utiliser Windows uniquement comme environnement de développement, et d'utiliser le système Linux comme environnement de production.

Remarque : Sous le système Windows, les extensions `posix` et `pcntl` ne sont pas nécessaires.
