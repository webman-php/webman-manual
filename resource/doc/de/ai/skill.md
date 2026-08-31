# Skills

Der Webman Development Skill liefert Codex, Claude Code und anderen Agent-Skills-kompatiblen Coding-Agenten Webman-Hinweise. Die vollständige Anleitung steht im [englischen Skills-Handbuch](https://webman.workerman.net/doc/en/ai/skill.html).

## Installation

Wähle eine Methode und führe projektbezogene Befehle im Stammverzeichnis des Webman-Projekts aus.

### 1. Den Coding-Agenten installieren lassen (empfohlen)

Bitte Codex oder Claude Code, `webman-development` aus `https://github.com/webman-php/skills` für das aktuelle Projekt zu installieren, das vollständige Verzeichnis zu behalten und die Erkennung von `SKILL.md` zu prüfen.

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Manuelle Installation

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Kopiere `webman-skills/skills/webman-development/` vollständig nach `.agents/skills/webman-development/` für Codex oder `.claude/skills/webman-development/` für Claude Code. Nur `SKILL.md` zu kopieren reicht nicht; `references/` gehört dazu.

## Verwendung

Rufe den Skill in Codex mit `$webman-development` oder in Claude Code mit `/webman-development` auf. Bei Webman-Aufgaben kann der Agent ihn auch automatisch laden.
