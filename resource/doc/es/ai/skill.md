# Skills

Webman Development Skill proporciona orientación de Webman para Codex, Claude Code y otros agentes compatibles con Agent Skills. Consulta la [guía completa de Skills en inglés](https://webman.workerman.net/doc/en/ai/skill.html).

## Instalación

Elige un método y ejecuta los comandos para el proyecto desde su directorio raíz de Webman.

### 1. Pide al agente que lo instale (recomendado)

Pide a Codex o Claude Code que instale `webman-development` desde `https://github.com/webman-php/skills` para el proyecto actual, conserve el directorio completo y compruebe que `SKILL.md` sea detectable.

### 2. Composer

```bash
composer require --dev "webman/skills:~1.0"
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Instalación manual

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Copia completamente `webman-skills/skills/webman-development/` a `.agents/skills/webman-development/` para Codex o `.claude/skills/webman-development/` para Claude Code. No copies solo `SKILL.md`; también se necesita `references/`.

## Uso

Invoca el Skill en Codex con `$webman-development` o en Claude Code con `/webman-development`. El agente también puede cargarlo automáticamente para tareas de Webman.
