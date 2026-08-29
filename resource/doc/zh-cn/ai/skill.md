# 使用 Webman 开发 Skill

Webman 开发 Skill 为 Codex、Claude Code 和其它支持 Agent Skills 的 AI 编程工具提供按需读取的 Webman 知识。它覆盖常驻内存运行模型、控制器、路由、验证、数据访问、异步进程、协程、插件和生产部署，帮助 AI 根据项目实际安装的版本和配置选择正确做法。

完整 Skill 位于本仓库的 [skills/webman-development](https://github.com/webman-php/webman-manual/tree/master/skills/webman-development) 目录。安装时必须保留整个目录及其中的 `references/`，不要只复制 `SKILL.md`。

## 推荐安装：项目范围

先进入**实际要开发的 Webman 项目根目录**。安装器会把 Skill 放到对应编辑器的项目目录中，团队成员可按需要将这些目录提交到项目仓库。

同时安装到 Codex 与 Claude Code：

```bash
npx skills add webman-php/webman-manual --skill webman-development -a codex -a claude-code
```

只使用 Codex：

```bash
npx skills add webman-php/webman-manual --skill webman-development -a codex
```

只使用 Claude Code：

```bash
npx skills add webman-php/webman-manual --skill webman-development -a claude-code
```

首次运行时按安装器提示选择复制或链接。链接便于更新同一来源；使用 `--copy` 会创建独立副本，适合不支持链接或希望固定版本的项目。

## Codex

项目级 Skill 的标准目录是：

```text
<项目根目录>/.agents/skills/webman-development/
```

安装后，Codex 会在任务明显涉及 Webman 时自动选择它。也可以在提示中显式调用：

```text
$webman-development 新增一个带类型参数绑定和验证的 POST 接口
```

用 `/skills` 检查是否已发现；新建了顶层 `.agents/skills` 但列表未更新时，重启 Codex 后再检查。

## Claude Code

项目级 Skill 的标准目录是：

```text
<项目根目录>/.claude/skills/webman-development/
```

Claude Code 也会根据任务自动使用该 Skill。需要显式调用时输入：

```text
/webman-development
```

或者在请求中说明“使用 webman-development skill 实现此 Webman 功能”。如果在 Claude Code 会话开始后才创建顶层 `.claude/skills`，请重启会话后再通过 `/skills` 确认。

## 手动安装

没有 Node.js 或希望完全控制目录时，克隆本仓库后复制完整目录。以下 PowerShell 示例会在目标已存在时停止，避免覆盖来源不明的内容：

```powershell
git clone --depth 1 https://github.com/webman-php/webman-manual.git D:\tools\webman-manual

$source = 'D:\tools\webman-manual\skills\webman-development'
$projectRoot = 'D:\project\my-webman'
$parent = Join-Path $projectRoot '.agents\skills' # Codex；Claude Code 改为 '.claude\skills'
$target = Join-Path $parent 'webman-development'

if (-not (Test-Path -LiteralPath $source -PathType Container)) { throw "Skill source is missing: $source" }
if (Test-Path -LiteralPath $target) { throw "Skill target already exists: $target" }
New-Item -ItemType Directory -Path $parent -Force | Out-Null
Copy-Item -LiteralPath $source -Destination $parent -Recurse
```

## 更新与排查

使用安装器安装的项目可运行：

```bash
npx skills update webman-development
npx skills list
```

若使用手动复制，更新前先确认目标目录确实是此 Skill，再以新的完整目录替换；不要删除项目的整个 `.agents` 或 `.claude` 目录。加载规则和个人范围安装请以 [Codex Skills 文档](https://developers.openai.com/codex/skills/) 与 [Claude Code Skills 文档](https://docs.anthropic.com/en/docs/claude-code/skills) 为准。
