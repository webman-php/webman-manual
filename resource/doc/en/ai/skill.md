# Use the Webman Development Skill

The Webman Development Skill gives Codex, Claude Code, and other Agent Skills-compatible editors on-demand Webman guidance. It covers the resident-process model, controllers, routing, validation, data access, asynchronous processes, coroutines, plugins, and production deployment so the agent can choose behavior based on the project's installed version and configuration.

The complete package is in [skills/webman-development](https://github.com/webman-php/webman-manual/tree/master/skills/webman-development). Keep the whole directory, including `references/`; copying `SKILL.md` alone is not sufficient.

For fast installation, use the lightweight [webman-php/skills](https://github.com/webman-php/skills) distribution repository instead of cloning the full manual. Its `webman-development` package is maintained in sync with the copy shipped in this manual.

## Recommended: project installation

Run the following in the root of the Webman project you are developing:

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

For only one editor, use one matching option:

```bash
npx skills add webman-php/skills --skill webman-development -a codex
npx skills add webman-php/skills --skill webman-development -a claude-code
```

Choose a linked installation when you want easy source updates, or add `--copy` to create an independent project copy.

## Codex

The project location is:

```text
<project-root>/.agents/skills/webman-development/
```

Codex can select the skill automatically for matching Webman work. Invoke it explicitly with, for example:

```text
$webman-development add a POST endpoint with typed parameter binding and validation
```

Use `/skills` to confirm discovery. If the top-level `.agents/skills` directory was created after Codex started, restart Codex and check again.

## Claude Code

The project location is:

```text
<project-root>/.claude/skills/webman-development/
```

Claude Code can also choose the skill automatically. Invoke it explicitly with:

```text
/webman-development
```

If you create the top-level `.claude/skills` directory after a session starts, restart the session and confirm it with `/skills`.

## Manual installation

If Node.js is unavailable, clone this repository and copy the complete directory. This PowerShell example stops if the target already exists:

```powershell
git clone --depth 1 https://github.com/webman-php/skills.git D:\tools\webman-skills

$source = 'D:\tools\webman-skills\skills\webman-development'
$projectRoot = 'D:\project\my-webman'
$parent = Join-Path $projectRoot '.agents\skills' # Use '.claude\skills' for Claude Code.
$target = Join-Path $parent 'webman-development'

if (-not (Test-Path -LiteralPath $source -PathType Container)) { throw "Skill source is missing: $source" }
if (Test-Path -LiteralPath $target) { throw "Skill target already exists: $target" }
New-Item -ItemType Directory -Path $parent -Force | Out-Null
Copy-Item -LiteralPath $source -Destination $parent -Recurse
```

## Update and troubleshoot

For installer-managed project skills:

```bash
npx skills update webman-development
npx skills list
```

For manual copies, verify the target is this skill before replacing it; never remove the whole `.agents` or `.claude` directory. Refer to the official [Codex Skills documentation](https://developers.openai.com/codex/skills/) and [Claude Code Skills documentation](https://docs.anthropic.com/en/docs/claude-code/skills) for current discovery and personal-scope rules.
