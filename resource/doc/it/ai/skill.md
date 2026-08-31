# Skills

Webman Development Skill offre indicazioni su Webman per Codex, Claude Code e altri agenti compatibili con Agent Skills. Consulta la [guida completa di Skills in inglese](https://webman.workerman.net/doc/en/ai/skill.html).

## Installazione

Scegli un metodo ed esegui i comandi del progetto dalla directory principale del progetto Webman.

### 1. Chiedi all'agente di installarlo (consigliato)

Chiedi a Codex o Claude Code di installare `webman-development` da `https://github.com/webman-php/skills`, mantenere la directory completa e verificare che `SKILL.md` sia rilevabile.

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Installazione manuale

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Copia completamente `webman-skills/skills/webman-development/` in `.agents/skills/webman-development/` per Codex o `.claude/skills/webman-development/` per Claude Code. Non copiare solo `SKILL.md`: è necessaria anche `references/`.

## Utilizzo

Richiama lo Skill in Codex con `$webman-development` o in Claude Code con `/webman-development`. L'agente può caricarlo automaticamente per le attività Webman.
