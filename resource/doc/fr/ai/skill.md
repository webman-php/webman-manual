# Skills

Webman Development Skill fournit des conseils Webman à Codex, Claude Code et aux agents compatibles avec Agent Skills. Consultez le [guide Skills complet en anglais](https://webman.workerman.net/doc/en/ai/skill.html).

## Installation

Choisissez une méthode et exécutez les commandes du projet depuis la racine de votre projet Webman.

### 1. Demander l’installation à l’agent (recommandé)

Demandez à Codex ou Claude Code d’installer `webman-development` depuis `https://github.com/webman-php/skills`, de conserver le dossier complet et de vérifier que `SKILL.md` est détecté.

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Installation manuelle

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Copiez entièrement `webman-skills/skills/webman-development/` vers `.agents/skills/webman-development/` pour Codex ou `.claude/skills/webman-development/` pour Claude Code. Ne copiez pas uniquement `SKILL.md` : `references/` est également requis.

## Utilisation

Appelez le Skill dans Codex avec `$webman-development` ou dans Claude Code avec `/webman-development`. Il peut aussi être chargé automatiquement pour les tâches Webman.
