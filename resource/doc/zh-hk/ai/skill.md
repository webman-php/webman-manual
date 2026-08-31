# Skills

Webman Development Skill 為 Codex、Claude Code 及其他支援 Agent Skills 的編程代理提供 Webman 指引。完整說明請參閱[英文 Skills 指南](https://webman.workerman.net/doc/en/ai/skill.html)。

## 安裝

選擇一種方式，並在 Webman 專案根目錄執行專案級命令。

### 1. 讓編程代理自動安裝（推薦）

請 Codex 或 Claude Code 從 `https://github.com/webman-php/skills` 為目前專案安裝 `webman-development`，保留完整目錄，並確認能找到 `SKILL.md`。

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. 手動安裝

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

將 `webman-skills/skills/webman-development/` 完整複製到 Codex 的 `.agents/skills/webman-development/` 或 Claude Code 的 `.claude/skills/webman-development/`。不要只複製 `SKILL.md`，`references/` 也屬於 Skill 的一部分。

## 使用

在 Codex 使用 `$webman-development`，在 Claude Code 使用 `/webman-development` 明確呼叫 Skill。處理 Webman 任務時，代理也可以自動載入它。
