# Skills

Webman Development Skill предоставляет рекомендации по Webman для Codex, Claude Code и других агентов, совместимых с Agent Skills. Полное руководство доступно в [английской документации Skills](https://webman.workerman.net/doc/en/ai/skill.html).

## Установка

Выберите один способ и выполняйте команды проекта из корня проекта Webman.

### 1. Попросить агента выполнить установку (рекомендуется)

Попросите Codex или Claude Code установить `webman-development` из `https://github.com/webman-php/skills` для текущего проекта, сохранить весь каталог и проверить обнаружение `SKILL.md`.

### 2. Composer

```bash
composer require --dev "webman/skills:~1.0"
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. Ручная установка

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Полностью скопируйте `webman-skills/skills/webman-development/` в `.agents/skills/webman-development/` для Codex или `.claude/skills/webman-development/` для Claude Code. Нельзя копировать только `SKILL.md`: нужен также каталог `references/`.

## Использование

В Codex вызовите Skill через `$webman-development`, а в Claude Code — через `/webman-development`. Для задач Webman агент также может загрузить его автоматически.
