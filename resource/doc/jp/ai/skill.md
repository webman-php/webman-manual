# Skills

Webman Development Skill は、Codex、Claude Code、その他 Agent Skills 対応のコーディングエージェントに Webman の開発知識を提供します。詳しくは[英語の Skills ガイド](https://webman.workerman.net/doc/en/ai/skill.html)を参照してください。

## インストール

いずれか 1 つの方法を選び、Webman プロジェクトのルートで実行します。

### 1. コーディングエージェントにインストールを依頼（推奨）

Codex または Claude Code に、現在のプロジェクトへ `https://github.com/webman-php/skills` から `webman-development` をインストールし、完全なディレクトリを保持して `SKILL.md` が検出できることを確認するよう依頼します。

### 2. Composer

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

### 3. npx

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

### 4. 手動インストール

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

`webman-skills/skills/webman-development/` を Codex 用の `.agents/skills/webman-development/` または Claude Code 用の `.claude/skills/webman-development/` へ完全にコピーします。`SKILL.md` だけでなく `references/` も必要です。

## 使用方法

Codex では `$webman-development`、Claude Code では `/webman-development` で Skill を明示的に呼び出せます。Webman のタスクでは自動的に読み込まれる場合もあります。
