# Use the Webman Development Skill

The Webman Development Skill gives Codex, Claude Code, and other Agent Skills-compatible coding agents on-demand Webman guidance. It covers the resident-process model, controllers, routing, validation, data access, asynchronous processes, coroutines, plugins, and production deployment, while adapting its recommendations to the project's installed versions and configuration.

The complete Skill is available in this repository at [skills/webman-development](https://github.com/webman-php/webman-manual/tree/master/skills/webman-development). For installation, use the lightweight [webman-php/skills](https://github.com/webman-php/skills) distribution repository instead of cloning the full manual. Its `webman-development` directory is maintained in sync with the copy shipped here.

## Install

Choose **one** method. Run project-scoped commands from the root of the Webman project that should use the Skill.

### Option 1: ask the coding agent to install it (recommended)

Send this prompt to Codex, Claude Code, or another Skill-aware coding agent:

```text
Install the webman-development Skill from https://github.com/webman-php/skills
for this project. Prefer your native Skill installation capability, keep the complete
directory, and verify that SKILL.md is discoverable. Explain before changing project dependencies.
```

The agent should prefer its native Skill installer. If that is unavailable, it can use any one of the explicit methods below.

### Option 2: Composer

This method needs only PHP and Composer; it does not require Node.js, PowerShell, or `webman/console`:

```bash
composer require --dev webman/skills
php vendor/bin/webman-skills install --agents=codex,claude-code
```

To target only one agent, use `--agents=codex` or `--agents=claude-code`.

Add `--global` to install in the current user's Skill directories instead of the current project. The installer refuses to overwrite an unmanaged same-name directory. Inspect that directory first; when replacement is intentional, `--force` preserves the old directory as a backup before installation.

### Option 3: npx

If Node.js is available:

```bash
npx skills add webman-php/skills --skill webman-development -a codex -a claude-code
```

Keep only the matching `-a` option when using one agent. Add `--copy` when an independent copy is preferable to a linked installation.

### Option 4: manual installation

Download and extract the ZIP from GitHub, or clone the repository:

```bash
git clone --depth 1 https://github.com/webman-php/skills.git webman-skills
```

Copy the complete `webman-skills/skills/webman-development/` directory to one of these locations:

| Scope | Codex | Claude Code |
|---|---|---|
| Current project | `.agents/skills/webman-development/` | `.claude/skills/webman-development/` |
| Current user | `~/.agents/skills/webman-development/` | `~/.claude/skills/webman-development/` |

Do not copy only `SKILL.md`; `references/` and the other bundled files are part of the Skill.

## Verify the installation

For a project-scoped installation, confirm that both `SKILL.md` and `references/` exist under the target directory for the agent you use. There is no need to create both Codex and Claude Code copies when only one is used. In a team project, avoid installing different versions at both project and user scope because that makes the loaded source ambiguous.

## Use

Codex can load the Skill automatically for a matching Webman task. You can also invoke it explicitly:

```text
$webman-development add a POST endpoint with typed parameter binding and validation
```

Use `/skills` to check discovery. If a newly created top-level `.agents/skills` directory does not appear, restart Codex and check again.

Claude Code can also select the Skill automatically. To invoke it explicitly, enter:

```text
/webman-development
```

If the top-level `.claude/skills` directory was created after the Claude Code session started, restart the session and check again.

## Update

Use the update path that matches the installation method:

```bash
# Composer
composer update webman/skills
php vendor/bin/webman-skills update --agents=codex,claude-code

# npx
npx skills update webman-development

# Git clone / manual copy
git -C webman-skills pull --ff-only
```

After updating a manually cloned source, replace only the verified `webman-development` directory; never delete the project's entire `.agents` or `.claude` directory. Refer to the official [Codex Skills documentation](https://developers.openai.com/codex/skills/) and [Claude Code Skills documentation](https://code.claude.com/docs/en/skills) for current discovery and user-scope rules.
