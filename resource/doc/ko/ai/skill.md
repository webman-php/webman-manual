# Skills

Webman Development Skill은 Codex, Claude Code 및 Agent Skills를 지원하는 코딩 에이전트에 Webman 지침을 제공합니다. 자세한 내용은 [영문 Skills 가이드](https://webman.workerman.net/doc/en/ai/skill.html)를 참고하세요.

## 설치

한 가지 방법을 선택하고 Webman 프로젝트 루트에서 프로젝트 명령을 실행하세요.

### 1. 코딩 에이전트에게 설치 요청 (권장)

Codex 또는 Claude Code에게 현재 프로젝트에 `https://github.com/webman-php/skills`의 `webman-development`를 설치하고, 전체 디렉터리를 유지하며 `SKILL.md`가 검색되는지 확인하도록 요청하세요.

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. 수동 설치

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

`webman-skills/skills/webman-development/` 전체를 Codex의 `.agents/skills/webman-development/` 또는 Claude Code의 `.claude/skills/webman-development/`로 복사하세요. `SKILL.md`만 복사하지 말고 `references/`도 포함해야 합니다.

## 사용

Codex에서는 `$webman-development`, Claude Code에서는 `/webman-development`로 Skill을 직접 호출할 수 있습니다. Webman 작업에서는 자동으로 로드될 수도 있습니다.
