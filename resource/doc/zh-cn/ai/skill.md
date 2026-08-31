# 使用 Webman 开发 Skill

Webman 开发 Skill 为 Codex、Claude Code 和其它支持 Agent Skills 的 AI 编程工具提供按需读取的 Webman 知识。它覆盖常驻内存运行模型、控制器、路由、验证、数据访问、异步进程、协程、插件和生产部署，帮助 AI 根据项目实际安装的版本和配置选择正确做法。

完整 Skill 位于本仓库的 [skills/webman-development](https://github.com/webman-php/webman-manual/tree/master/skills/webman-development) 目录。为避免安装器下载体积较大的手册仓库，实际安装统一使用轻量的 [webman-php/skills](https://github.com/webman-php/skills) 发布仓库；其中的 `webman-development` 与本页面随手册维护的副本保持一致。

## 安装

以下方式**任选其一即可**。项目级安装命令应在实际需要使用 Skill 的 Webman 项目根目录执行。

### 方式一：让 AI 自动安装（推荐）

将下面的提示发给 Codex、Claude Code 或其它支持 Skill 的编码 Agent：

```text
请从 https://github.com/webman-php/skills 为当前项目安装
webman-development Skill。优先使用你内置的 Skill 安装能力，保留完整目录，
并在安装后确认 SKILL.md 已被发现；如需修改项目依赖，请先说明。
```

AI 应优先使用自身的 Skill 安装能力；不支持时，可以选择下面任一明确的安装方式。

### 方式二：Composer 安装

此方式只需要 PHP 和 Composer，不依赖 Node.js、PowerShell 或 `webman/console`：

```bash
composer config repositories.webman-skills vcs https://github.com/webman-php/skills
composer require --dev "webman/skills:~1.0"
php vendor/bin/webman-skills install --agents=codex,claude-code
```

第一条命令用于在 Packagist 尚未收录或镜像尚未同步时直接读取 GitHub 上的稳定版本；如果项目已经能从 Packagist 找到 `webman/skills`，可以省略。只安装 Codex 或 Claude Code 时，最后一条命令分别改为：

```bash
php vendor/bin/webman-skills install --agents=codex
php vendor/bin/webman-skills install --agents=claude-code
```

增加 `--global` 会安装到当前用户目录，而非当前项目。安装器不会覆盖来源不明的同名目录；应先检查该目录，确需替换时使用 `--force`，原目录会先保留为备份。

### 方式三：npx 安装

环境中已有 Node.js 时可以使用：

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

只使用一个 Agent 时保留对应的一个 `-a` 参数。希望使用独立副本而不是链接时增加 `--copy`。

### 方式四：手动安装

在 GitHub 下载 ZIP 后解压，或者克隆仓库：

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

将 `webman-skills/skills/webman-development/` 完整复制到以下任一目录：

| 范围 | Codex | Claude Code |
|---|---|---|
| 当前项目 | `.agents/skills/webman-development/` | `.claude/skills/webman-development/` |
| 当前用户 | `~/.agents/skills/webman-development/` | `~/.claude/skills/webman-development/` |

不要只复制 `SKILL.md`，`references/` 和其它随附文件也是 Skill 的一部分。

## 检查安装结果

项目级安装后，确认所用 Agent 的目标目录中同时存在 `SKILL.md` 和 `references/`。只使用一个 Agent 时无需同时创建两份；团队项目也应避免在项目级与用户级安装不同版本，以免加载来源不清楚。

## 使用

安装到 Codex 后，任务明显涉及 Webman 时可以自动加载，也可以显式调用：

```text
$webman-development 新增一个带类型参数绑定和验证的 POST 接口
```

可用 `/skills` 检查是否已发现；新建了顶层 `.agents/skills` 但列表未更新时，重启 Codex 后再检查。

Claude Code 同样会根据任务自动使用该 Skill，也可以输入：

```text
/webman-development
```

如果在 Claude Code 会话开始后才新建顶层 `.claude/skills`，需要重启会话后再检查。

## 更新

根据原安装方式选择对应命令：

```bash
# Composer
composer update webman/skills
php vendor/bin/webman-skills update --agents=codex,claude-code

# npx
npx skills update webman-development

# Git clone / 手动复制
git -C webman-skills pull --ff-only
```

手动更新源仓库后，只替换已确认属于本 Skill 的 `webman-development` 目录；不要删除项目的整个 `.agents` 或 `.claude` 目录。加载规则和个人范围安装请以 [Codex Skills 文档](https://developers.openai.com/codex/skills/) 与 [Claude Code Skills 文档](https://code.claude.com/docs/en/skills) 为准。
