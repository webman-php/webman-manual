# Skills

O Webman Development Skill fornece orientações de Webman para Codex, Claude Code e outros agentes compatíveis com Agent Skills. Consulte o [guia completo de Skills em inglês](https://webman.workerman.net/doc/en/ai/skill.html).

## Instalação

Escolha um método e execute os comandos do projeto na raiz do projeto Webman.

### 1. Peça ao agente para instalar (recomendado)

Peça ao Codex ou Claude Code para instalar `webman-development` de `https://github.com/webman-php/skills`, manter o diretório completo e verificar se `SKILL.md` foi detectado.

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Instalação manual

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Copie todo o diretório `webman-skills/skills/webman-development/` para `.agents/skills/webman-development/` no Codex ou `.claude/skills/webman-development/` no Claude Code. Não copie apenas `SKILL.md`; `references/` também é necessário.

## Uso

No Codex, use `$webman-development`; no Claude Code, use `/webman-development`. O agente também pode carregar o Skill automaticamente para tarefas Webman.
